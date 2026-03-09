<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Http\Resources\SettingResource;

class SettingController extends Controller
{
    public function index()
    {
        try {

            $settings = Setting::latest()->paginate(10);

            return SettingResource::collection($settings);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error fetching settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(SettingRequest $request)
    {
        try {

            $setting = Setting::create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Setting created successfully',
                'data' => new SettingResource($setting)
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error creating setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {

            $setting = Setting::findOrFail($id);

            return new SettingResource($setting);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Setting not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(SettingRequest $request, $id)
    {
        try {

            $setting = Setting::findOrFail($id);

            $setting->update($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Setting updated successfully',
                'data' => new SettingResource($setting)
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error updating setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $setting = Setting::findOrFail($id);

            $setting->delete();

            return response()->json([
                'status' => true,
                'message' => 'Setting deleted successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error deleting setting',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
