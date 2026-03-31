<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Storage;
use Exception;

class CategoryController extends BaseController
{
    public function index()
    {
        try {
            $query = Category::whereNull('parent_id')
                ->with('children')
                ->withCount('products')
                ->withCount('children');

            $admin = request()->attributes->get('admin');

            if (!$admin) {
                $query->where('status', 1);
            }

            $categories = $query->latest()->get();

            return $this->success($categories, 'Categories fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function subCategories($id = null)
    {
        try {
            $query = Category::whereNotNull('parent_id')
                ->with('parent:id,name')
                ->withCount('subCategoryProducts as products_count');

            $admin = request()->attributes->get('admin');

            if (!$admin) {
                $query->where('status', 1);
            }

            if ($id) {
                $category = Category::find($id);

                if (!$category) {
                    return $this->error('Category not found', 404);
                }

                $query->where('parent_id', $id);
            }

            $subCategories = $query->latest()->get();

            return $this->success($subCategories, 'Sub categories fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can create categories.', 403);
            }

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            if (!empty($data['parent_id'])) {
                $parent = Category::find($data['parent_id']);

                if (!$parent) {
                    return $this->error('Parent category not found', 404);
                }
            }

            $category = Category::create($data);
            $category->load(['parent', 'children']);

            return $this->success($category, 'Category created successfully', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show(Category $category)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin && !$category->status) {
                return $this->error('Category not found', 404);
            }

            $category->load(['parent', 'children.children', 'products']);
            $category->loadCount('products');

            return $this->success($category, 'Category fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update categories.', 403);
            }

            $data = $request->validated();

            $category = Category::find($id);

            if ($request->hasFile('image')) {

                $rawImage = $category->getRawOriginal('image');

                if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                    Storage::disk('public')->delete($rawImage);
                }

                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            if (isset($data['parent_id'])) {
                if ($data['parent_id'] == $category->id) {
                    return $this->error('Category cannot be its own parent', 422);
                }

                if ($data['parent_id']) {
                    $childIds = $this->getAllChildIds($category);

                    if (in_array($data['parent_id'], $childIds)) {
                        return $this->error('Circular reference not allowed', 422);
                    }

                    $parent = Category::find($data['parent_id']);

                    if (!$parent) {
                        return $this->error('Parent category not found', 404);
                    }
                }
            }

            $category->update($data);
            $category->load(['parent', 'children']);

            return $this->success($category, 'Category updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update category status.', 403);
            }

            $category = Category::find($id);

            $category->update([
                'status' => !$category->status
            ]);

            return $this->success($category, 'Category status updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can delete categories.', 403);
            }

            $category = Category::find($id);

            if (!$category) {
                return $this->error('Category not found', 404);
            }

            if ($category->children()->count() > 0) {
                return $this->error('Delete subcategories first.', 422);
            }

            if ($category->products()->count() > 0) {
                return $this->error('Remove products first.', 422);
            }

            $rawImage = $category->getRawOriginal('image');

            if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                Storage::disk('public')->delete($rawImage);
            }

            $category->delete();

            return $this->success(null, 'Category deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    private function getAllChildIds(Category $category): array
    {
        $ids = [];

        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildIds($child));
        }

        return $ids;
    }
}
