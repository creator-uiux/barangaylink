<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('creator');

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by priority if provided
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        $announcements = $query->active()
                              ->orderBy('created_at', 'desc')
                              ->get()
                              ->map(function ($announcement) {
                                  return [
                                      'id' => $announcement->id,
                                      'title' => $announcement->title,
                                      'content' => $announcement->content,
                                      'category' => $announcement->category,
                                      'priority' => $announcement->priority,
                                      'is_active' => $announcement->is_active,
                                      'created_by' => $announcement->created_by,
                                      'creator_name' => $announcement->creator ? $announcement->creator->name : null,
                                      'created_at' => $announcement->created_at->format('Y-m-d H:i:s'),
                                      'updated_at' => $announcement->updated_at->format('Y-m-d H:i:s'),
                                  ];
                              });

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    public function store(Request $request)
    {
        // Only admins can create announcements
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,normal,high,urgent',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'priority' => $request->priority,
            'is_active' => $request->is_active ?? true,
            'created_by' => $request->user()->id,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_announcement',
            'entity_type' => 'announcement',
            'entity_id' => $announcement->id,
            'description' => 'Announcement created: ' . $request->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully',
            'data' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'category' => $announcement->category,
                'priority' => $announcement->priority,
                'is_active' => $announcement->is_active,
                'created_at' => $announcement->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $announcement = Announcement::with('creator')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'category' => $announcement->category,
                'priority' => $announcement->priority,
                'is_active' => $announcement->is_active,
                'created_by' => $announcement->created_by,
                'creator_name' => $announcement->creator ? $announcement->creator->name : null,
                'created_at' => $announcement->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $announcement->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        // Only admins can update announcements
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,normal,high,urgent',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'priority' => $request->priority,
            'is_active' => $request->is_active ?? $announcement->is_active,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_announcement',
            'entity_type' => 'announcement',
            'entity_id' => $announcement->id,
            'description' => 'Announcement updated: ' . $request->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
            'data' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'category' => $announcement->category,
                'priority' => $announcement->priority,
                'is_active' => $announcement->is_active,
                'updated_at' => $announcement->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        // Only admins can delete announcements
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $announcement->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_announcement',
            'entity_type' => 'announcement',
            'entity_id' => $id,
            'description' => 'Announcement deleted: ' . $announcement->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ]);
    }
}
