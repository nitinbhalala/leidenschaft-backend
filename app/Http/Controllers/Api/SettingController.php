<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\SettingRequest;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    public function index()
    {
        try {
            $settings = Setting::all()->map(function ($setting) {
                return [
                    'key'   => $setting->key,
                    'value' => $setting->value,
                ];
            });

            return $this->success($settings, 'Settings fetched successfully');
        } catch (\Exception $e) {
            return $this->error('Error fetching settings', 500, $e->getMessage());
        }
    }

    public function store(SettingRequest $request)
    {
        try {
            $setting = Setting::updateOrCreate(
                ['key'   => $request->validated()['key']],
                ['value' => $request->validated()['value']]
            );

            return $this->success([
                'key'   => $setting->key,
                'value' => $setting->value,
            ], 'Setting saved successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Error saving setting', 500, $e->getMessage());
        }
    }

    public function getByKey($key)
    {
        try {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                return $this->error('Setting not found', 404);
            }

            return $this->success([
                'key'   => $setting->key,
                'value' => $setting->value,
            ]);
        } catch (\Exception $e) {
            return $this->error('Error fetching setting', 500, $e->getMessage());
        }
    }

    public function update(Request $request, $key)
    {
        try {
            $request->validate([
                'value' => ['nullable', 'string'],
            ]);

            $setting = Setting::updateOrCreate(
                ['key'   => $key],
                ['value' => $request->input('value', '')]
            );

            return $this->success([
                'key'   => $setting->key,
                'value' => $setting->value,
            ], 'Setting updated successfully');
        } catch (\Exception $e) {
            return $this->error('Error updating setting', 500, $e->getMessage());
        }
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'settings'         => ['required', 'array', 'min:1'],
            'settings.*.key'   => ['required', 'string', 'max:255'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        try {
            $upserted = [];

            foreach ($request->input('settings') as $item) {
                $setting = Setting::updateOrCreate(
                    ['key'   => $item['key']],
                    ['value' => $item['value'] ?? '']
                );

                $upserted[] = [
                    'key'   => $setting->key,
                    'value' => $setting->value,
                ];
            }

            return $this->success($upserted, 'Settings saved successfully');
        } catch (\Exception $e) {
            return $this->error('Error saving settings', 500, $e->getMessage());
        }
    }

    public function destroy($key)
    {
        try {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                return $this->error('Setting not found', 404);
            }

            $setting->delete();

            return $this->success(null, 'Setting deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Error deleting setting', 500, $e->getMessage());
        }
    }
}
