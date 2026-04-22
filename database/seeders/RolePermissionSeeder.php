<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Users / Admins
            'users.view', 'users.create', 'users.update', 'users.delete',

            // Roles & Permissions
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.update', 'permissions.delete',

            // Products
            'products.view', 'products.create', 'products.update', 'products.delete',

            // Categories
            'categories.view', 'categories.create', 'categories.update', 'categories.delete',

            // Orders
            'orders.view', 'orders.create', 'orders.update', 'orders.delete',

            // Customers
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',

            // Payments
            'payments.view', 'payments.refund', 'payments.delete',

            // Inventory
            'inventory.view', 'inventory.update',

            // Blogs
            'blogs.view', 'blogs.create', 'blogs.update', 'blogs.delete',

            // FAQs
            'faqs.view', 'faqs.create', 'faqs.update', 'faqs.delete',

            // Policies
            'policies.view', 'policies.create', 'policies.update', 'policies.delete',

            // Settings
            'settings.view', 'settings.create', 'settings.update', 'settings.delete',

            // Contacts
            'contacts.view', 'contacts.update', 'contacts.delete',

            // Notifications
            'notifications.view', 'notifications.create', 'notifications.update', 'notifications.delete',

            // Email Templates
            'email-templates.view', 'email-templates.create', 'email-templates.update', 'email-templates.delete',

            // Offers
            'offers.view', 'offers.create', 'offers.update', 'offers.delete',

            // Error Logs
            'error-logs.view', 'error-logs.resolve', 'error-logs.delete',

            // Reviews
            'reviews.view', 'reviews.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin — all except roles/permissions management
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(
            Permission::whereNotIn('name', [
                'roles.create', 'roles.update', 'roles.delete',
                'permissions.create', 'permissions.update', 'permissions.delete',
                'users.delete',
            ])->get()
        );

        // Manager — operational only
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'dashboard.view',
            'products.view', 'products.create', 'products.update',
            'categories.view', 'categories.create', 'categories.update',
            'orders.view', 'orders.update',
            'customers.view',
            'inventory.view', 'inventory.update',
            'reviews.view', 'reviews.delete',
        ]);

        // Viewer — read only
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(
            Permission::where('name', 'like', '%.view')->get()
        );

        // Assign super-admin role to the first user if exists
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('super-admin')) {
            $firstUser->assignRole('super-admin');
        }
    }
}
