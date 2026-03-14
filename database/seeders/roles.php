<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class roles extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name'=>'user']);
        
        $adminRole->givePermissionTo([
            'view_statistics',
            'ban_users',
            'disable_users',
            'create_colocation',
            'join_colocation'
        ]);

        $userRole->givePermissionTo([
            'create_colocation',
            'join_colocation'
        ]);
    }
}
