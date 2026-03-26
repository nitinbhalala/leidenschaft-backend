<?php

namespace App\Http\Controllers\Api;

use App\Models\Policy;
use Illuminate\Support\Str;
use App\Http\Requests\PolicyRequest;

class PolicyController extends BaseController
{
    public function index()
    {
        return $this->success(Policy::latest()->get(), "Policy list");
    }

    public function store(PolicyRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        $policy = Policy::create($data);

        return $this->success($policy, "Created");
    }

    public function show(string $slug)
    {
        $policy = Policy::where('slug', $slug)->first();

        if (!$policy) {
            return $this->error("Policy not found", 404);
        }

        return $this->success($policy, "Fetched");
    }

    public function update(PolicyRequest $request, string $slug)
    {
        $policy = Policy::where('slug', $slug)->first();

        if (!$policy) {
            // Auto-create if not exists
            $data = $request->validated();
            $data['slug'] = $slug;
            $policy = Policy::create($data);

            return $this->success($policy, "Created");
        }

        $data = $request->validated();
        $data['slug'] = $slug;

        $policy->update($data);

        return $this->success($policy->fresh(), "Updated");
    }

    public function destroy(string $slug)
    {
        $policy = Policy::where('slug', $slug)->first();

        if (!$policy) {
            return $this->error("Policy not found", 404);
        }

        $policy->delete();

        return $this->success(null, "Deleted");
    }
}
