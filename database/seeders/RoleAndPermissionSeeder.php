<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage users',
            'create invoices',
            'edit invoices',
            'delete invoices',
            'receive invoices',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles and assign existing permissions
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::findOrCreate('manager', 'web');
        $managerRole->givePermissionTo([
            'create invoices',
            'edit invoices',
            'receive invoices',
            'view reports',
        ]);

        $employeeRole = Role::findOrCreate('employee', 'web');
        $employeeRole->givePermissionTo([
            'receive invoices',
        ]);

        // New Role: office (المكتب)
        $officeRole = Role::findOrCreate('office', 'web');
        $officeRole->givePermissionTo([
            'create invoices',
            'edit invoices',
            'delete invoices',
            'view reports',
        ]);

        // New Role: store (المخزن)
        $storeRole = Role::findOrCreate('store', 'web');
        $storeRole->givePermissionTo([
            'receive invoices',
            'view reports',
        ]);

        // Create default admin if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@apgroup.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole($adminRole);

        // Assign 'admin' role to all existing users so they don't lose access
        $users = User::all();
        foreach ($users as $user) {
            $user->assignRole($adminRole);
        }
    }
}
