<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Services\ProductImageService;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ProductImageManager extends Component
{
    use WithFileUploads;

    public Product $product;

    /** @var array<int, TemporaryUploadedFile> */
    public array $newImages = [];

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'newImages.*' => ['image', 'max:5120'],
        ];
    }

    public function updatedNewImages(ProductImageService $service): void
    {
        $this->validate();

        foreach ($this->newImages as $file) {
            $service->addImage($this->product, $file);
        }

        $this->reset('newImages');
    }

    public function setPrimary(int $imageId, ProductImageService $service): void
    {
        $service->setPrimary($this->product, $this->product->images()->findOrFail($imageId));
    }

    public function moveUp(int $imageId, ProductImageService $service): void
    {
        $service->moveUp($this->product->images()->findOrFail($imageId));
    }

    public function moveDown(int $imageId, ProductImageService $service): void
    {
        $service->moveDown($this->product->images()->findOrFail($imageId));
    }

    public function deleteImage(int $imageId, ProductImageService $service): void
    {
        $service->delete($this->product->images()->findOrFail($imageId));
    }

    public function render()
    {
        return view('livewire.admin.product-image-manager', [
            'images' => $this->product->images()->orderBy('position')->get(),
        ]);
    }
}
