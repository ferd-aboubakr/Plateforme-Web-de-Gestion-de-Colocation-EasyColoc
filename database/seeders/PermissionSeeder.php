<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $permissions=['view_statistics','ban_users','disable_users','create_colocation','join_colocation'];
        
        foreach ($permissions as $permission) {Permission::firstOrCreate(['name' => $permission]);}
    }
}
