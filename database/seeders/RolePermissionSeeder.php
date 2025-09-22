<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage',
            
            // Order management
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.update',
            'orders.delete',
            
            // Product management
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            
            // Customer management
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            
            // Shipment management
            'shipments.view',
            'shipments.create',
            'shipments.edit',
            'shipments.manage',
            
            // Invoice management
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.manage',
            
            // Settings management
            'settings.manage',
            
            // Dashboard access
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::create(['name' => 'SuperAdmin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'users.view',
            'users.create',
            'users.edit',
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.update',
            'products.view',
            'products.create',
            'products.edit',
            'customers.view',
            'customers.create',
            'customers.edit',
            'shipments.view',
            'shipments.create',
            'shipments.edit',
            'shipments.manage',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.manage',
            'dashboard.view',
        ]);

        $operator = Role::create(['name' => 'Operator']);
        $operator->givePermissionTo([
            'orders.view',
            'orders.edit',
            'orders.update',
            'products.view',
            'customers.view',
            'shipments.view',
            'shipments.create',
            'shipments.edit',
            'invoices.view',
            'invoices.create',
            'dashboard.view',
        ]);

        $viewer = Role::create(['name' => 'Viewer']);
        $viewer->givePermissionTo([
            'orders.view',
            'products.view',
            'customers.view',
            'shipments.view',
            'invoices.view',
            'dashboard.view',
        ]);
    }
}
