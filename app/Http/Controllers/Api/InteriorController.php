<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\InteriorRequest;
use App\Models\Interior;
use Illuminate\Support\Facades\Storage;

class InteriorController extends BaseController
{
    public function index()
    {
        $settings = Interior::firstOrCreate(['id' => 1]);
        return $this->success($settings, 'Interior fetched successfully');
    }

    public function update(InteriorRequest $request)
    {
        $settings = Interior::firstOrCreate(['id' => 1]);

        $data = $request->validated();

        foreach (['section_1_image', 'section_2_image'] as $field) {
            if ($request->hasFile($field)) {
                $oldPath = $settings->getRawOriginal($field);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                $data[$field] = $request->file($field)->store('interior', 'public');
            } else {
                unset($data[$field]);
            }
        }

        if ($request->has('section_2_items')) {
            $decoded = json_decode($request->input('section_2_items'), true);
            $data['section_2_items'] = is_array($decoded) ? $decoded : [];
        }

        if ($request->has('section_2_process_steps')) {
            $decoded = json_decode($request->input('section_2_process_steps'), true);
            $data['section_2_process_steps'] = is_array($decoded) ? $decoded : [];
        }

        if ($request->has('section_5_points')) {
            $decoded = json_decode($request->input('section_5_points'), true);
            $data['section_5_points'] = is_array($decoded) ? $decoded : [];
        }

        if ($request->has('section_3_items')) {
            $metaItems = json_decode($request->input('section_3_items'), true);

            if (is_array($metaItems)) {
                $finalItems = [];

                foreach ($metaItems as $itemIdx => $meta) {
                    $existingImages = (isset($meta['images']) && is_array($meta['images']))
                        ? $meta['images']
                        : [];

                    $newImageCount  = isset($meta['newImageCount']) ? (int) $meta['newImageCount'] : 0;
                    $uploadedImages = [];

                    for ($fileIdx = 0; $fileIdx < $newImageCount; $fileIdx++) {
                        $fileKey = "section_3_item_{$itemIdx}_image_{$fileIdx}";

                        if ($request->hasFile($fileKey)) {
                            $path           = $request->file($fileKey)->store('interior/gallery', 'public');
                            $uploadedImages[] = asset('storage/' . $path);
                        }
                    }

                    $finalItems[] = [
                        'title'       => $meta['title']       ?? '',
                        'description' => $meta['description'] ?? '',
                        'images'      => array_merge($existingImages, $uploadedImages),
                    ];
                }

                $data['section_3_items'] = $finalItems;
            }
        }

        if ($request->has('section_4_items')) {
            $metaItems = json_decode($request->input('section_4_items'), true);

            if (is_array($metaItems)) {
                $finalItems = [];

                foreach ($metaItems as $itemIdx => $meta) {
                    $existingImages = (isset($meta['images']) && is_array($meta['images']))
                        ? $meta['images']
                        : [];

                    $newImageCount  = isset($meta['newImageCount']) ? (int) $meta['newImageCount'] : 0;
                    $uploadedImages = [];

                    for ($fileIdx = 0; $fileIdx < $newImageCount; $fileIdx++) {
                        $fileKey = "section_4_item_{$itemIdx}_image_{$fileIdx}";

                        if ($request->hasFile($fileKey)) {
                            $path           = $request->file($fileKey)->store('interior/showcase', 'public');
                            $uploadedImages[] = asset('storage/' . $path);
                        }
                    }

                    $finalItems[] = [
                        'title'       => $meta['title']       ?? '',
                        'description' => $meta['description'] ?? '',
                        'side_text'   => $meta['side_text']   ?? '',
                        'images'      => array_merge($existingImages, $uploadedImages),
                    ];
                }

                $data['section_4_items'] = $finalItems;
            }
        }

        $settings->update($data);

        return $this->success($settings, 'Interior updated successfully');
    }

    public function toggleStatus($section)
    {
        $admin = request()->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized. Only admin can toggle section status.', 403);
        }

        $field    = "section_{$section}_status";
        $settings = Interior::firstOrCreate(['id' => 1]);

        if (!array_key_exists($field, $settings->getCasts())) {
            return $this->error('Invalid section number.', 422);
        }

        $settings->update([$field => !$settings->$field]);

        return $this->success($settings, "Section {$section} status toggled successfully");
    }
}
