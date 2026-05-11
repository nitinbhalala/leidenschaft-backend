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
                'image_url' => $path,
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
            $scene->load(['pins.product' => fn($q) => $q->select('id', 'name')]);

            return $this->success($scene, 'Scene fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(UpdateSceneRequest $request, Scene $scene)
    {
        try {
            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($scene->getRawOriginal('image_url'));
                $path = $request->file('image')->store('scenes', 'public');
                $scene->image_url = $path;
            }

            $scene->fill($request->only(['status']))->save();

            if ($request->has('pins')) {
                $pins = json_decode($request->input('pins'), true);

                $incomingIds = collect($pins)->pluck('id')->filter()->values()->toArray();

                $scene->pins()->whereNotIn('id', $incomingIds)->delete();

                foreach ($pins as $i => $p) {
                    $data = [
                        'product_id' => $p['product_id'],
                        'x_percent'  => $p['x_percent'],
                        'y_percent'  => $p['y_percent'],
                        'sort_order' => $p['sort_order'] ?? $i,
                    ];

                    if (!empty($p['id'])) {
                        $scene->pins()->where('id', $p['id'])->update($data);
                    } else {
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

            Storage::disk('public')->delete($scene->getRawOriginal('image_url'));

            $scene->delete();

            return $this->success(null, 'Scene deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function customerIndex()
    {
        try {
            $scenes = Scene::where('status', 1)
                ->with(['pins.product' => function ($query) {
                    $query->select('id', 'name', 'slug', 'price')
                        ->with(['images' => fn($q) => $q->select('product_id', 'image')->limit(1)]);
                }])
                ->latest()
                ->get()
                ->map(function ($scene) {
                    $data        = $scene->toArray();
                    $data['pins'] = $scene->pins->map(function ($pin) {
                        return [
                            'id'         => $pin->id,
                            'x_percent'  => $pin->x_percent,
                            'y_percent'  => $pin->y_percent,
                            'product'    => $pin->product ? [
                                'id'    => $pin->product->id,
                                'name'  => $pin->product->name,
                                'slug'  => $pin->product->slug,
                                'price' => $pin->product->price,
                                'image' => $pin->product->images->first()?->image,
                            ] : null,
                        ];
                    });
                    return $data;
                });

            return $this->success($scenes, 'Scenes fetched successfully');
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
