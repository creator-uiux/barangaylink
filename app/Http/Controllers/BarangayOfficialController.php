<?php

namespace App\Http\Controllers;

use App\Models\BarangayOfficial;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangayOfficialController extends Controller
{
    public function index(Request $request)
    {
        $officials = BarangayOfficial::active()
                                    ->ordered()
                                    ->get()
                                    ->map(function ($official) {
                                        return [
                                            'id' => $official->id,
                                            'name' => $official->name,
                                            'position' => $official->position,
                                            'position_order' => $official->position_order,
                                            'email' => $official->email,
                                            'phone' => $official->phone,
                                            'photo_url' => $official->photo_url,
                                            'description' => $official->description,
                                            'is_active' => $official->is_active,
                                            'created_at' => $official->created_at->format('Y-m-d H:i:s'),
                                        ];
                                    });

        return response()->json([
            'success' => true,
            'data' => $officials
        ]);
    }

    public function store(Request $request)
    {
        // Only admins can create officials
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'position_order' => 'required|integer|min:0',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'photo_url' => 'nullable|url',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $official = BarangayOfficial::create([
            'name' => $request->name,
            'position' => $request->position,
            'position_order' => $request->position_order,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo_url' => $request->photo_url,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_official',
            'entity_type' => 'barangay_official',
            'entity_id' => $official->id,
            'description' => 'Barangay official created: ' . $request->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barangay official created successfully',
            'data' => [
                'id' => $official->id,
                'name' => $official->name,
                'position' => $official->position,
                'position_order' => $official->position_order,
                'is_active' => $official->is_active,
                'created_at' => $official->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $official = BarangayOfficial::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $official->id,
                'name' => $official->name,
                'position' => $official->position,
                'position_order' => $official->position_order,
                'email' => $official->email,
                'phone' => $official->phone,
                'photo_url' => $official->photo_url,
                'description' => $official->description,
                'is_active' => $official->is_active,
                'created_at' => $official->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $official = BarangayOfficial::findOrFail($id);

        // Only admins can update officials
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'position_order' => 'required|integer|min:0',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'photo_url' => 'nullable|url',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $official->update([
            'name' => $request->name,
            'position' => $request->position,
            'position_order' => $request->position_order,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo_url' => $request->photo_url,
            'description' => $request->description,
            'is_active' => $request->is_active ?? $official->is_active,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_official',
            'entity_type' => 'barangay_official',
            'entity_id' => $official->id,
            'description' => 'Barangay official updated: ' . $request->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barangay official updated successfully',
            'data' => [
                'id' => $official->id,
                'name' => $official->name,
                'position' => $official->position,
                'position_order' => $official->position_order,
                'is_active' => $official->is_active,
                'updated_at' => $official->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $official = BarangayOfficial::findOrFail($id);

        // Only admins can delete officials
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $official->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_official',
            'entity_type' => 'barangay_official',
            'entity_id' => $id,
            'description' => 'Barangay official deleted: ' . $official->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barangay official deleted successfully'
        ]);
    }
}
