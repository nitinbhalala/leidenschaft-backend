<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Anyone authenticated can view products list
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone authenticated can view single product
     */
    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Only admin can create product
     */
    public function create(User $user): bool
    {
        return $user->is_admin === 1;
    }

    /**
     * Only admin can update product
     */
    public function update(User $user, Product $product): bool
    {
        return $user->is_admin === 1;
    }

    /**
     * Only admin can delete product
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->is_admin === 1;
    }

    /**
     * Only admin can restore product
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->is_admin === 1;
    }

    /**
     * Only admin can permanently delete product
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->is_admin === 1;
    }
}
