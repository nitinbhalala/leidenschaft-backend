<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreSceneRequest;
use App\Http\Requests\UpdateSceneRequest;
use App\Models\Scene;
use Exception;
use Illuminate\Support\Facades\Storage;

class SceneController extends BaseController
{
    public function index()
    {
        try {
            $scenes = Scene::withCount('pins')->latest()->get();

            return $this->success($scenes, 'Scenes fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(StoreSceneRequest $request)
    {
        try {
            $path = $request->file('image')->store('scenes', 'public');

            $scene = Scene::create([
                'title'     => $request->title,
                'image_url' => Storage::url($path),
                'status'    => $request->status ?? 'draft',
            ]);

            if ($request->has('pins')) {
                $pins = json_decode($request->input('pins'), true);
                $scene->pins()->insert(
                    collect($pins)->map(fn($p, $i) => [
                        'scene_id'   => $scene->id,
                        'product_id' => $p['product_id'],
                        'x_percent'  => $p['x_percent'],
                        'y_percent'  => $p['y_percent'],
                        'sort_order' => $p['sort_order'] ?? $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->toArray()
                );
            }

            return $this->success($scene->load('pins.product'), 'Scene created successfully', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show(Scene $scene)
    {
        try {
            return $this->success($scene->load('pins.product'), 'Scene fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(UpdateSceneRequest $request, Scene $scene)
    {
        try {
            if ($request->hasFile('image')) {
                $old = str_replace('/storage/', '', $scene->image_url);
                Storage::disk('public')->delete($old);
                $path = $request->file('image')->store('scenes', 'public');
                $scene->image_url = Storage::url($path);
            }

            $scene->fill($request->only(['title', 'status']))->save();

            if ($request->has('pins')) {
                $pins = json_decode($request->input('pins'), true);

                $incomingIds = collect($pins)->pluck('id')->filter()->values()->toArray();

                // Delete pins that are not in incoming list
                $scene->pins()->whereNotIn('id', $incomingIds)->delete();

                foreach ($pins as $i => $p) {
                    $data = [
                        'product_id' => $p['product_id'],
                        'x_percent'  => $p['x_percent'],
                        'y_percent'  => $p['y_percent'],
                        'sort_order' => $p['sort_order'] ?? $i,
                    ];

                    if (!empty($p['id'])) {
                        // Update existing pin
                        $scene->pins()->where('id', $p['id'])->update($data);
                    } else {
                        // Create new pin
                        $scene->pins()->create($data);
                    }
                }
            }

            return $this->success($scene->load('pins.product'), 'Scene updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy(Scene $scene)
    {
        try {
            $scene->pins()->delete();

            $path = str_replace('/storage/', '', $scene->image_url);
            Storage::disk('public')->delete($path);

            $scene->delete();

            return $this->success(null, 'Scene deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update get the look items.', 403);
            }

            $item = Scene::find($id);

            if (!$item) {
                return $this->error('Item not found', 404);
            }

            $item->update(['status' => $item->status == 1 ? 0 : 1]);

            return $this->success($item->fresh(), 'Status updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
