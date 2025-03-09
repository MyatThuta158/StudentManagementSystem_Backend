<?php
namespace App\Http\Controllers;

use App\Models\MeetingRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeetingRecordController extends Controller
{
    /**
     * Create a new meeting record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data.
        $validatedData = $request->validate([
            'arrange_id'        => 'required|integer|exists:arrangings,id',
            'meeting_note'      => 'required|string',
            'uploaded_document' => 'nullable|file',
        ]);

        // Handle file upload if present.
        if ($request->hasFile('uploaded_document')) {
            $path                               = $request->file('uploaded_document')->store('uploads', 'public');
            $validatedData['uploaded_document'] = $path;
        }

        // Create the meeting record.
        $meetingRecord = MeetingRecord::create($validatedData);

        return response()->json($meetingRecord, 201);
    }

    /**
     * Show the specified meeting record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $meetingRecord = MeetingRecord::findOrFail($id);
        return response()->json($meetingRecord);
    }

    /**
     * Update the specified meeting record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $meetingRecord = MeetingRecord::findOrFail($id);

        // Validate the request data.
        $validatedData = $request->validate([
            'arrange_id'        => 'sometimes|required|integer|exists:arrangings,id',
            'meeting_note'      => 'sometimes|required|string',
            'uploaded_document' => 'nullable|file',
            'remove_file'       => 'nullable|boolean',
        ]);

        // Check if user wants to remove the file without uploading a new one.
        if ($request->has('remove_file') && $request->input('remove_file')) {
            if ($meetingRecord->uploaded_document && Storage::disk('public')->exists($meetingRecord->uploaded_document)) {
                Storage::disk('public')->delete($meetingRecord->uploaded_document);
            }
            $validatedData['uploaded_document'] = null;
        }

        // If a new file is uploaded, delete the old file first.
        if ($request->hasFile('uploaded_document')) {
            if ($meetingRecord->uploaded_document && Storage::disk('public')->exists($meetingRecord->uploaded_document)) {
                Storage::disk('public')->delete($meetingRecord->uploaded_document);
            }
            $path                               = $request->file('uploaded_document')->store('uploads', 'public');
            $validatedData['uploaded_document'] = $path;
        }

        // Update the meeting record.
        $meetingRecord->update($validatedData);

        return response()->json($meetingRecord);
    }

    /**
     * Soft delete the specified meeting record and remove the associated file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $meetingRecord = MeetingRecord::findOrFail($id);

        // Delete the file if it exists.
        if ($meetingRecord->uploaded_document && Storage::disk('public')->exists($meetingRecord->uploaded_document)) {
            Storage::disk('public')->delete($meetingRecord->uploaded_document);
        }

        // Soft delete the record.
        $meetingRecord->delete();

        return response()->json(['message' => 'Meeting record deleted successfully.']);
    }
}
