<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MeetingRecord;
use App\Models\BlogDocument;
use App\Models\Document;

class FileDownloadController extends Controller
{
    public function download($type, $id)
    {
        switch ($type) {
            case 'meeting':
                $record = MeetingRecord::find($id);
                $path = $record?->uploaded_document;
                $name = basename($path);
                break;

            case 'blog':
                $record = BlogDocument::find($id);
                $path = $record?->BlogDocumentFile;
                $name = basename($path);
                break;

            case 'general':
                $record = Document::find($id);
                $path = $record?->file;
                $name = $record?->file_name ?? basename($path);
                break;

            default:
                return response()->json(['message' => 'Invalid file type.'], 400);
        }

        if (!$path || !Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->download(
            storage_path("app/public/{$path}"),
            $name,
            ['Content-Type' => 'application/octet-stream']
        );
    }
}
