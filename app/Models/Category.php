<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'page',
        'image',
        'name',
        'slug',
        'description',
        'show_in_navbar',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'parent_id' => 'integer',
        'show_in_navbar' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if ($category->isDirty('name') || !$category->slug) {
                $category->slug = self::generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (
            self::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subCategoryProducts()
    {
        return $this->hasMany(Product::class, 'sub_category_id');
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function isChild(): bool
    {
        return !is_null($this->parent_id);
    }

    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
