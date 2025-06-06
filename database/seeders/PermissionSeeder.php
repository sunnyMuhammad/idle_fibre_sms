<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [
            'list-user',
            'create-user',
            'update-user',
            'delete-user',
            'list-role',
            'create-role',
            'update-role',
            'delete-role',
            'list-product',
            'create-product',
            'update-product',
            'delete-product',
            'list-category',
            'create-category',
            'update-category',
            'delete-category',
            'list-vendor',
            'create-vendor',
            'update-vendor',
            'delete-vendor',
            'list-purchase',
            'create-purchase',
            'update-purchase',
            'delete-purchase',
            'list-issue-product',
            'list-damage-product',
            'list-minimum-product',
            'list-requisition',
            'create-requisition',
            'approve-floor-receive',
            'approve-requisition-receive',
            'receive-requisition',
            'receive-floor-receive',
            'issue-product',
            'delete-requisition',
        ];

        foreach($permissions as $permission){
            Permission::create([
                'name' => $permission
            ]);
        }

    }
}
