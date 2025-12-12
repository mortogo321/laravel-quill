<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        if (!config('quill.uploads.enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Image uploads are disabled.',
            ], 403);
        }

        try {
            $request->validate([
                'image' => [
                    'required',
                    'image',
                    'max:' . config('quill.uploads.max_size', 2048),
                    'mimes:jpeg,png,gif,webp',
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['image'][0] ?? 'Invalid image.',
            ], 422);
        }

        $file = $request->file('image');
        $disk = config('quill.uploads.disk', 'public');
        $path = config('quill.uploads.path', 'quill-uploads');

        // Generate unique filename
        $filename = sprintf(
            '%s_%s.%s',
            date('Y-m-d_H-i-s'),
            Str::random(10),
            $file->getClientOriginalExtension()
        );

        // Store file
        $filePath = $file->storeAs($path, $filename, $disk);

        if (!$filePath) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image.',
            ], 500);
        }

        // Get public URL
        $url = Storage::disk($disk)->url($filePath);

        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $filePath,
            'filename' => $filename,
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        if (!config('quill.uploads.enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Image management is disabled.',
            ], 403);
        }

        $request->validate([
            'path' => 'required|string',
        ]);

        $disk = config('quill.uploads.disk', 'public');
        $basePath = config('quill.uploads.path', 'quill-uploads');
        $path = $request->input('path');

        // Security: Ensure the path is within the uploads directory
        if (!Str::startsWith($path, $basePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file path.',
            ], 403);
        }

        if (!Storage::disk($disk)->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        $deleted = Storage::disk($disk)->delete($path);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'File deleted successfully.' : 'Failed to delete file.',
        ]);
    }
}
