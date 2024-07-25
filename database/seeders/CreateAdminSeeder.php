<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create supper admin
        $admin = Admin::create([
            'name' => 'mustafa mahmoud',
            'email' => 'mustafa45salem@gmail.com',
            'password' => bcrypt('123456789'),
            'birth_date' => '2002-03-15',
            'phone' => '01025824895',
            'ssn' => '182929299229929',
            'gender' => 'gender'
        ]);

        //create supper role
        $role = Role::create(
            [
                'name' => 'supper-admin'
            ]
        );

        //get all permission
        $permissions = Permission::get();

        //supper admin role take permissions
        $role->syncPermissions($permissions);

        //admin take supper admin role
        $admin->assignRole([$role->id]);
    }
}
