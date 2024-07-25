<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateMediaAdminRoleSeeder extends Seeder
{

    public function run(): void
    {
        //create media admin role
        $role = Role::create(
            [
                'name' => 'media-admin'
            ]
        );

        //get permission to media admin role
        $permissions = Permission::where(

            function ($query) {
                $query->where('id', '>=', 11)->where('id', '<=', 12);
            }
        )->orWhere(

            function ($query) {
                $query->where('id', '>=', 14)->where('id', '<=', 15);
            }
        )->get();
        //store data in pivot table
        $role->syncPermissions($permissions);
    }
}
