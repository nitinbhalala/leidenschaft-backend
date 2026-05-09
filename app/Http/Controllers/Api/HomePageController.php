<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\HomePageRequest;
use App\Models\HomePageSetting;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class HomePageController extends BaseController
{
    public function index()
    {
        $settings = HomePageSetting::firstOrCreate(['id' => 1]);

        $data = $settings->toArray();

        $position = $settings->section_2_product_position ?? [];

        $data['section_2_products'] = Product::with(['images'])
            ->whereIn('id', $settings->section_2_product_ids ?? [])
            ->where('status', 1)
            ->get(['id', 'name', 'price'])
            ->map(function ($product) use ($position) {
                return [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => $product->price,
                    'image'    => $product->images->first()?->image ?? null,
                    'position' => $position[(string) $product->id] ?? null,
                ];
            });

        return $this->success($data, 'Home page settings fetched successfully');
    }

    public function update(HomePageRequest $request)
    {
        $settings = HomePageSetting::firstOrCreate(['id' => 1]);
        $data = $request->validated();

        $imageFields = [
            'section_1_image',
            'section_2_image',
            'section_7_img_1',
            'section_7_img_2',
            'section_7_img_3',
            'section_7_img_4',
            'section_8_img_1',
            'section_8_img_2',
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $oldPath = $settings->getRawOriginal($field);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                $data[$field] = $request->file($field)->store('home-page', 'public');
            } else {
                unset($data[$field]);
            }
        }

        $settings->update($data);

        return $this->success($settings, 'Home page settings updated successfully');
    }

    public function toggleStatus($section)
    {
        $admin = request()->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized. Only admin can toggle section status.', 403);
        }

        $field = "section_{$section}_status";
        $settings = HomePageSetting::firstOrCreate(['id' => 1]);

        if (!array_key_exists($field, $settings->getCasts())) {
            return $this->error('Invalid section number.', 422);
        }

        $settings->update([$field => !$settings->$field]);

        return $this->success($settings, "Section {$section} status toggled successfully");
    }
}
