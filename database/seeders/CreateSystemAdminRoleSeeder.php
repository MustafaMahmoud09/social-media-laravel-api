<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateSystemAdminRoleSeeder extends Seeder
{

    public function run(): void
    {
        //create system admin role
        $role = Role::create(
            [
                'name' => 'system-admin'
            ]
        );

        //get permission to system admin role
        $permissions = Permission::where('id','<=',10)->get();

        //store data in pivot table
        $role->syncPermissions($permissions);
    }
}
