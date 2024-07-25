<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    //in system have three role to admin 1-supper admin 2-media admin 3-system admin

    //1-supper admin: can make all cases
    //2-media admin: can make 1-show user 2-manage user 3-show post 4-manage-post
    //3-system admin: can make 1-manage social 2-show social 3-manage gender 4-show gender 5-manage call 6-show call 7-manage address 8-show address 9-manage relationship 10-show relationship
    public function run(): void
    {
        $permissions = [
            'manage-social',
            'show-social',
            'manage-gender',
            'show-gender',
            'manage-call',
            'show-call',
            'manage-address',
            'show-address',
            'manage-relationship',
            'show-relationship',
            'show-user',
            'manage-user',
            'show-admin',
            'show-post',
            'manage-post',
            'manage-admin'
        ];

        foreach ($permissions as $permission) {

            Permission::create([
                'name' => $permission
            ]);
        }
    }

}
