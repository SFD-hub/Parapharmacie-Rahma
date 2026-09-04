<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

class ImageOptimizerService
{
    public function __construct(private readonly ImageManager $manager) {}

    /**
     * Resize and re-encode the given upload, store it on the public disk and
     * return the publicly accessible URL.
     */
    public function storeOptimized(UploadedFile $file, string $directory, int $maxWidth = 1200, int $quality = 82): string
    {
        $path = trim($directory, '/').'/'.Str::uuid().'.jpg';

        $encoded = $this->manager->decode($file)
            ->scaleDown(width: $maxWidth)
            ->encode(new JpegEncoder(quality: $quality));

        Storage::disk('public')->put($path, $encoded->toString());

        return Storage::disk('public')->url($path);
    }

    /**
     * Delete a previously optimized image from the public disk, given its
     * public URL. Silently ignores URLs that are not managed by this disk
     * (e.g. seeded placeholder assets living in /public/images).
     */
    public function delete(?string $url): void
    {
        if (! $url) {
            return;
        }

        $prefix = Storage::disk('public')->url('');

        if (! str_starts_with($url, $prefix)) {
            return;
        }

        Storage::disk('public')->delete(Str::after($url, $prefix));
    }

    /**
     * Resolve a public-disk URL back to its absolute local filesystem path,
     * for contexts that need to read the file directly rather than fetch it
     * over HTTP (e.g. embedding a logo in a DomPDF-rendered document).
     * Returns null for empty/non-managed URLs, same as delete().
     */
    public function localPath(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $prefix = Storage::disk('public')->url('');

        if (! str_starts_with($url, $prefix)) {
            return null;
        }

        return Storage::disk('public')->path(Str::after($url, $prefix));
    }
}
