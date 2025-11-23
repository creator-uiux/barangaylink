<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['user', 'processor']);

        // Non-admin users can only see their own documents
        if (!$request->user() || !$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $documents = $query->orderBy('created_at', 'desc')
                          ->get()
                          ->map(function ($document) {
                              return [
                                  'id' => $document->id,
                                  'user_id' => $document->user_id,
                                  'user_name' => $document->user->name,
                                  'document_type' => $document->document_type,
                                  'purpose' => $document->purpose,
                                  'quantity' => $document->quantity,
                                  'notes' => $document->notes,
                                  'status' => $document->status,
                                  'admin_notes' => $document->admin_notes,
                                  'processed_by' => $document->processed_by,
                                  'processor_name' => $document->processor ? $document->processor->name : null,
                                  'created_at' => $document->created_at->format('Y-m-d H:i:s'),
                                  'updated_at' => $document->updated_at->format('Y-m-d H:i:s'),
                              ];
                          });

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string|max:255',
            'purpose' => 'required|string',
            'quantity' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $document = Document::create([
            'user_id' => $request->user()->id,
            'document_type' => $request->document_type,
            'purpose' => $request->purpose,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_document',
            'entity_type' => 'document',
            'entity_id' => $document->id,
            'description' => 'Document request created: ' . $request->document_type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document request submitted successfully',
            'data' => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'purpose' => $document->purpose,
                'quantity' => $document->quantity,
                'status' => $document->status,
                'created_at' => $document->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $document = Document::with(['user', 'processor'])->findOrFail($id);

        // Users can only view their own documents, admins can view all
        if (!$request->user() || (!$request->user()->isAdmin() && $request->user()->id != $document->user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $document->id,
                'user_id' => $document->user_id,
                'user_name' => $document->user->name,
                'document_type' => $document->document_type,
                'purpose' => $document->purpose,
                'quantity' => $document->quantity,
                'notes' => $document->notes,
                'status' => $document->status,
                'admin_notes' => $document->admin_notes,
                'processed_by' => $document->processed_by,
                'processor_name' => $document->processor ? $document->processor->name : null,
                'created_at' => $document->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $document->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Only admins can update document status
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,approved,rejected,ready,claimed',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldStatus = $document->status;
        $document->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_by' => $request->user()->id,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_document',
            'entity_type' => 'document',
            'entity_id' => $document->id,
            'description' => 'Document status changed from ' . $oldStatus . ' to ' . $request->status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'data' => [
                'id' => $document->id,
                'status' => $document->status,
                'admin_notes' => $document->admin_notes,
                'processed_by' => $document->processed_by,
                'updated_at' => $document->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Only admins can delete documents, and only if they're still pending
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($document->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete processed documents'
            ], 400);
        }

        $document->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_document',
            'entity_type' => 'document',
            'entity_id' => $id,
            'description' => 'Document request deleted',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }
}
