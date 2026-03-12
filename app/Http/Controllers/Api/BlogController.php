<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Http\Requests\BlogRequest;

class BlogController extends BaseController
{
    public function index()
    {
        $blogs = Blog::latest()->get();

        return $this->success($blogs, "Blog list fetched successfully");
    }

    public function store(BlogRequest $request)
    {
        $blog = Blog::create($request->validated());

        return $this->success($blog, "Blog created successfully");
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

        $blog->update($request->validated());

        return $this->success($blog, "Blog updated successfully");
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return $this->error("Blog not found", 404);
        }

        $blog->delete();

        return $this->success(null, "Blog deleted successfully");
    }
}
