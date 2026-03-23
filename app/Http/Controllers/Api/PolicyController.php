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

    public function show(Policy $policy)
    {
        return $this->success($policy, "Fetched");
    }

    public function update(PolicyRequest $request, Policy $policy)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        $policy->update($data);

        return $this->success($policy, "Updated");
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();

        return $this->success(null, "Deleted");
    }
}
