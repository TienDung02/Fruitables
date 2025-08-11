<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductMediaController extends Controller
{
    /**
     * Upload media files for a Products.
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov|max:10240', // 10MB max
            'alt_texts.*' => 'nullable|string|max:255',
        ]);

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $uploadedFile = $this->uploadFile($file, $product, $request->input("alt_texts.{$index}"));
                $uploadedFiles[] = $uploadedFile;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully!',
            'files' => $uploadedFiles
        ]);
    }

    /**
     * Set primary image for Products.
     */
    public function setPrimary(ProductMedia $media): JsonResponse
    {
        // Remove primary flag from other media
        ProductMedia::where('product_id', $media->product_id)
            ->where('id', '!=', $media->id)
            ->update(['is_primary' => false]);

        // Set this media as primary
        $media->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image set successfully!'
        ]);
    }

    /**
     * Update media information.
     */
    public function update(Request $request, ProductMedia $media): JsonResponse
    {
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $media->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Media updated successfully!',
            'media' => $media
        ]);
    }

    /**
     * Delete media file.
     */
    public function destroy(ProductMedia $media): JsonResponse
    {
        // Delete physical files
        if (Storage::exists($media->file_path)) {
            Storage::delete($media->file_path);
        }

        // Delete thumbnail if exists
        $pathInfo = pathinfo($media->file_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        if (Storage::exists($thumbnailPath)) {
            Storage::delete($thumbnailPath);
        }

        // Delete database record
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully!'
        ]);
    }

    /**
     * Reorder media files.
     */
    public function reorder(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:product_media,id'
        ]);

        foreach ($request->media_ids as $index => $mediaId) {
            ProductMedia::where('id', $mediaId)
                ->where('product_id', $product->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Media reordered successfully!'
        ]);
    }

    /**
     * Upload and process a single file.
     */
    private function uploadFile($file, Product $product, ?string $altText = null): ProductMedia
    {
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = "products/{$product->id}/{$fileName}";

        // Store the file
        Storage::putFileAs("products/{$product->id}", $file, $fileName);

        // Create thumbnail for images
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $this->createThumbnail($filePath, $product->id);
        }

        // Determine file type
        $fileType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'video';

        // Check if this is the first media (make it primary)
        $isPrimary = $product->media()->count() === 0;

        return ProductMedia::create([
            'product_id' => $product->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'alt_text' => $altText,
            'sort_order' => $product->media()->count(),
            'is_primary' => $isPrimary
        ]);
    }

    /**
     * Create thumbnail for image.
     */
    private function createThumbnail(string $filePath, int $productId): void
    {
        try {
            $fullPath = Storage::path($filePath);
            $pathInfo = pathinfo($filePath);
            $thumbnailDir = "products/{$productId}/thumbnails";
            $thumbnailPath = $thumbnailDir . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

            // Create thumbnail directory if not exists
            Storage::makeDirectory($thumbnailDir);

            // Create and save thumbnail (300x300)
            $thumbnail = Image::make($fullPath)
                ->fit(300, 300, function ($constraint) {
                    $constraint->upsize();
                })
                ->save(Storage::path($thumbnailPath), 85);

        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Log::error('Failed to create thumbnail: ' . $e->getMessage());
        }
    }
}
