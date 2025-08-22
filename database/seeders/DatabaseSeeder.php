<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password'),
        // ]);
        Employee::factory(10)->create();
        
        // $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // $managerRole = Role::firstOrCreate(['name' => 'manager']);
        // $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // User::factory(10)->create()->each(function ($user) use ($employeeRole){
        //     $user->assignRole($employeeRole);
        // });

        // $admin = User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password'),
        // ]);
        // $admin->assignRole($adminRole);

        // $manager = User::factory()->create([
        //     'name' => 'Manager User',
        //     'email' => 'manager@example.com',
        //     'password' => bcrypt('password'),
        // ]);
        // $manager->assignRole($managerRole);

        // $employee = User::factory()->create([
        //     'name' => 'Employee User',
        //     'email' => 'employee@example.com',
        //     'password' => bcrypt('password'),
        // ]);
        // $employee->assignRole($employeeRole);
        
        // Jalankan RolePermissionSeeder terlebih dahulu
        $this->call(RolePermissionSeeder::class);
        
        // Kemudian buat user dan assign role
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->syncRoles(['admin']); // Gunakan nama role, bukan objek

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
        ]);
        $manager->syncRoles(['manager']);

        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ]);
        $employee->syncRoles(['employee']);
    }

}
