<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the user's attachments.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $attachments = Attachment::where('user_id', $request->user()->id)
                ->get()
                ->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'name' => $attachment->original_name,
                    'mime' => $attachment->mime_type,
                    'size' => $attachment->size,
                    'url' => $attachment->getSignedUrl(),
                ]);

            return response()->json($attachments, Response::HTTP_OK);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'Failed to retrieve attachments',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly uploaded attachment.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,png,pdf,doc,docx|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('attachments', config('filesystems.default_private_disk', 'private'));

            $attachment = Attachment::create([
                'user_id' => $request->user()->id,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'path' => $path,
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'id' => $attachment->id,
                'name' => $attachment->original_name,
                'mime' => $attachment->mime_type,
                'size' => $attachment->size,
                'url' => $attachment->getSignedUrl(),
            ], Response::HTTP_CREATED);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'Upload failed',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified attachment's details.
     *
     * @param Request $request
     * @param Attachment $attachment
     * @return JsonResponse
     */
    public function show(Request $request, Attachment $attachment): JsonResponse
    {
        try {
            if ($request->user()->id !== $attachment->user_id) {
                return response()->json([
                    'error' => 'Unauthorized',
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'id' => $attachment->id,
                'name' => $attachment->original_name,
                'mime' => $attachment->mime_type,
                'size' => $attachment->size,
                'url' => $attachment->getSignedUrl(),
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'Failed to retrieve attachment',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Download the specified attachment.
     *
     * @param Request $request
     * @param Attachment $attachment
     * @return BinaryFileResponse
     */
    public function download(Request $request, Attachment $attachment): BinaryFileResponse
    {
        try {
            if ($request->user()->id !== $attachment->user_id) {
                abort(Response::HTTP_FORBIDDEN, 'Unauthorized');
            }

            $disk = Storage::disk(config('filesystems.default_private_disk', 'private'));
            if (!$disk->exists($attachment->path)) {
                abort(Response::HTTP_NOT_FOUND, 'File not found');
            }

            return response()->file(
                $disk->path($attachment->path),
                [
                    'Content-Type' => $attachment->mime_type,
                    'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
                ]
            );
        } catch (Throwable $e) {
            report($e);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Download error');
        }
    }

    /**
     * Remove the specified attachment and its file.
     *
     * @param Request $request
     * @param Attachment $attachment
     * @return JsonResponse
     */
    public function destroy(Request $request, Attachment $attachment): JsonResponse
    {
        try {
            if ($request->user()->id !== $attachment->user_id) {
                return response()->json([
                    'error' => 'Unauthorized',
                ], Response::HTTP_FORBIDDEN);
            }

            $disk = Storage::disk(config('filesystems.default_private_disk', 'private'));
            if ($disk->exists($attachment->path)) {
                $disk->delete($attachment->path);
            }

            $attachment->delete();

            return response()->json([
                'message' => 'Attachment deleted successfully',
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'Deletion failed',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
