<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Http\Requests\BlogRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $query = Blog::query();

            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            }

            if ($request->status && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $perPage = $request->per_page ?? 10;
            $blogs   = $query->latest()->paginate($perPage);

            return $this->success($blogs, "Blog list fetched successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(BlogRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $data['image'] = $path;
        }

        $blog = Blog::create($data);

        return $this->success($blog, "Blog created successfully", 201);
    }

    public function show($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return $this->error("Blog not found", 404);
        }

        return $this->success($blog, "Blog fetched successfully");
    }

    public function update(BlogRequest $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return $this->error("Blog not found", 404);
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($blog->getRawOriginal('image') && $blog->getRawOriginal('image') !== 'default') {
                Storage::disk('public')->delete($blog->getRawOriginal('image'));
            }

            $path = $request->file('image')->store("blogs/{$blog->id}", 'public');
            $data['image'] = $path;
        }

        $blog->update($data);

        return $this->success($blog->fresh(), "Blog updated successfully");
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return $this->error("Blog not found", 404);
        }

        if ($blog->getRawOriginal('image') && $blog->getRawOriginal('image') !== 'default') {
            Storage::disk('public')->delete($blog->getRawOriginal('image'));
            Storage::disk('public')->deleteDirectory("blogs/{$blog->id}");
        }

        $blog->delete();

        return $this->success(null, "Blog deleted successfully");
    }
}
