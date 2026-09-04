<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;

class ProductImageService
{
    public function __construct(private readonly ImageOptimizerService $optimizer) {}

    public function addImage(Product $product, UploadedFile $file): ProductImage
    {
        $url = $this->optimizer->storeOptimized($file, "products/{$product->id}");

        $nextPosition = ((int) $product->images()->max('position')) + 1;
        $isFirst = $product->images()->doesntExist();

        return $product->images()->create([
            'path' => $url,
            'is_primary' => $isFirst,
            'position' => $nextPosition,
        ]);
    }

    public function setPrimary(Product $product, ProductImage $image): void
    {
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
    }

    public function delete(ProductImage $image): void
    {
        $this->optimizer->delete($image->path);

        $product = $image->product;
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $product->images()->orderBy('position')->first()?->update(['is_primary' => true]);
        }
    }

    public function moveUp(ProductImage $image): void
    {
        $previous = $image->product->images()
            ->where('position', '<', $image->position)
            ->orderByDesc('position')
            ->first();

        $this->swapPositions($image, $previous);
    }

    public function moveDown(ProductImage $image): void
    {
        $next = $image->product->images()
            ->where('position', '>', $image->position)
            ->orderBy('position')
            ->first();

        $this->swapPositions($image, $next);
    }

    private function swapPositions(ProductImage $a, ?ProductImage $b): void
    {
        if (! $b) {
            return;
        }

        [$positionA, $positionB] = [$a->position, $b->position];

        $a->update(['position' => $positionB]);
        $b->update(['position' => $positionA]);
    }
}
