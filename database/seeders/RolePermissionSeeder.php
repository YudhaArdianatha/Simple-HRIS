<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::where('guard_name', 'web')->delete();
        Role::where('guard_name', 'web')->delete();
        
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',

            'view employees',
            'create employees',
            'edit employees',
            'delete employees',

            'view attendances',
            'create attendances',
            'edit attendances',
            'delete attendances',

            'view leaves',
            'create leaves',
            'edit leaves',
            'delete leaves',

            'view self',
            'view self attendance',
            'view self leaves'
        ];

        foreach($permissions as $permission){
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum' 
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'sanctum' 
        ]);
        
        $manager = Role::firstOrCreate([
            'name' => 'manager', 
            'guard_name' => 'sanctum' 
        ]);
        
        $employee = Role::firstOrCreate([
            'name' => 'employee',
            'guard_name' => 'sanctum' 
        ]);

        $admin->givePermissionTo(Permission::all());
        
        $manager->givePermissionTo([
            'view employees',
            'view attendances', 
            'view leaves'
        ]);
        
        $employee->givePermissionTo([
            'view self',
            'view self attendance',
            'view self leaves',
            'create leaves'
        ]);
    }
}