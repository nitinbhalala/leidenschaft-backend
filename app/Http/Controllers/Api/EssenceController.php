<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EssenceRequest;
use App\Models\Essence;
use Illuminate\Support\Facades\Storage;

class EssenceController extends BaseController
{
    public function index()
    {
        $settings = Essence::firstOrCreate(['id' => 1]);
        return $this->success($settings, 'Essence fetched successfully');
    }

    public function update(EssenceRequest $request)
    {
        $settings = Essence::firstOrCreate(['id' => 1]);
        $data = $request->validated();

        $imageFields = [
            'section_1_image',
            'section_2_image',
            'section_3_image',
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $oldPath = $settings->getRawOriginal($field);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                $data[$field] = $request->file($field)->store('essence', 'public');
            } else {
                unset($data[$field]);
            }
        }

        $settings->update($data);

        return $this->success($settings, 'Essence updated successfully');
    }

    public function toggleStatus($section)
    {
        $admin = request()->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized. Only admin can toggle section status.', 403);
        }

        $field = "section_{$section}_status";
        $settings = Essence::firstOrCreate(['id' => 1]);

        if (!array_key_exists($field, $settings->getCasts())) {
            return $this->error('Invalid section number.', 422);
        }

        $settings->update([$field => !$settings->$field]);

        return $this->success($settings, "Section {$section} status toggled successfully");
    }
}
