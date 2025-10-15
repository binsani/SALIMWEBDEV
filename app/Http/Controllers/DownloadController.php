<?php

namespace App\Http\Controllers;

use App\Models\OrderDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Download digital product.
     */
    public function download($token)
    {
        $download = OrderDownload::where('download_token', $token)->firstOrFail();

        // Check if download belongs to authenticated user
        if ($download->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this download.');
        }

        // Check if download has expired
        if ($download->isExpired()) {
            return back()->with('error', 'This download link has expired.');
        }

        // Check if download limit reached
        if ($download->limitReached()) {
            return back()->with('error', 'Download limit has been reached for this file.');
        }

        // Get the file path
        $filePath = $download->orderItem->digital_file_path;

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        // Record the download
        $download->recordDownload();

        // Return file download response
        return Storage::disk('local')->download(
            $filePath,
            $download->orderItem->product_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION)
        );
    }
}