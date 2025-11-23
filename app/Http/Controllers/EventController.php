<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('creator');

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $events = $query->active()
                        ->orderBy('event_date')
                        ->orderBy('event_time')
                        ->get()
                        ->map(function ($event) {
                            return [
                                'id' => $event->id,
                                'title' => $event->title,
                                'description' => $event->description,
                                'location' => $event->location,
                                'event_date' => $event->event_date->format('Y-m-d'),
                                'event_time' => $event->event_time ? $event->event_time->format('H:i:s') : null,
                                'end_date' => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                                'end_time' => $event->end_time ? $event->end_time->format('H:i:s') : null,
                                'category' => $event->category,
                                'max_participants' => $event->max_participants,
                                'current_participants' => $event->current_participants,
                                'is_active' => $event->is_active,
                                'created_by' => $event->created_by,
                                'creator_name' => $event->creator ? $event->creator->name : null,
                                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                            ];
                        });

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function store(Request $request)
    {
        // Only admins can create events
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date|after:today',
            'event_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:event_date',
            'end_time' => 'nullable|date_format:H:i',
            'category' => 'required|string|max:100',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'category' => $request->category,
            'max_participants' => $request->max_participants,
            'current_participants' => 0,
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_event',
            'entity_type' => 'event',
            'entity_id' => $event->id,
            'description' => 'Event created: ' . $request->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'event_date' => $event->event_date->format('Y-m-d'),
                'event_time' => $event->event_time ? $event->event_time->format('H:i:s') : null,
                'category' => $event->category,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $event = Event::with('creator')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'location' => $event->location,
                'event_date' => $event->event_date->format('Y-m-d'),
                'event_time' => $event->event_time ? $event->event_time->format('H:i:s') : null,
                'end_date' => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                'end_time' => $event->end_time ? $event->end_time->format('H:i:s') : null,
                'category' => $event->category,
                'max_participants' => $event->max_participants,
                'current_participants' => $event->current_participants,
                'is_active' => $event->is_active,
                'created_by' => $event->created_by,
                'creator_name' => $event->creator ? $event->creator->name : null,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Only admins can update events
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:event_date',
            'end_time' => 'nullable|date_format:H:i',
            'category' => 'required|string|max:100',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'category' => $request->category,
            'max_participants' => $request->max_participants,
            'is_active' => $request->is_active ?? $event->is_active,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_event',
            'entity_type' => 'event',
            'entity_id' => $event->id,
            'description' => 'Event updated: ' . $request->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'event_date' => $event->event_date->format('Y-m-d'),
                'event_time' => $event->event_time ? $event->event_time->format('H:i:s') : null,
                'category' => $event->category,
                'is_active' => $event->is_active,
                'updated_at' => $event->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Only admins can delete events
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $event->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_event',
            'entity_type' => 'event',
            'entity_id' => $id,
            'description' => 'Event deleted: ' . $event->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}
