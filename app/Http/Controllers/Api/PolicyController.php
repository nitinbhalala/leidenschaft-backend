<?php

namespace App\Http\Controllers\Api;

use App\Models\Policy;
use Illuminate\Support\Str;
use App\Http\Requests\PolicyRequest;
use Exception;

class PolicyController extends BaseController
{
    public function index()
    {
        try {
            $query = Policy::latest();

            $admin = request()->attributes->get('admin');

            if (!$admin) {
                $query->where('status', 1);
            }

            $policies = $query->get();

            return $this->success($policies, "Policy list");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(PolicyRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error("Unauthorized. Only admin can create policy.", 403);
            }

            $data = $request->validated();
            $data['slug'] = Str::slug($data['title']);

            $policy = Policy::create($data);

            return $this->success($policy, "Created");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show(string $slug)
    {
        try {
            $policy = Policy::where('slug', $slug)->first();

            if (!$policy) {
                return $this->error("Policy not found", 404);
            }

            $admin = request()->attributes->get('admin');

            if (!$admin && isset($policy->status) && !$policy->status) {
                return $this->error("Policy not found", 404);
            }

            return $this->success($policy, "Fetched");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(PolicyRequest $request, string $slug)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error("Unauthorized. Only admin can update policy.", 403);
            }

            $policy = Policy::where('slug', $slug)->first();

            if (!$policy) {
                return $this->error("Policy not found", 404);
            }

            $data = $request->validated();
            $data['slug'] = $slug;

            $policy->update($data);

            return $this->success($policy->fresh(), "Updated");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy(string $slug)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error("Unauthorized. Only admin can delete policy.", 403);
            }

            $policy = Policy::where('slug', $slug)->first();

            if (!$policy) {
                return $this->error("Policy not found", 404);
            }

            $policy->delete();

            return $this->success(null, "Deleted");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
