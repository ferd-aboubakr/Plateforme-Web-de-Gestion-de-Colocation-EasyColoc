<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,  // 1er : crée les permissions
            roles::class,             // 2ème : crée les rôles et assigne les permissions
            CategorySeeder::class,    // 3ème : crée les catégories globales
        ]);

        // Admin (1er user) - seulement s'il n'existe pas
        if (!\App\Models\User::where('email', 'admin@easycoloc.fr')->exists()) {
            $admin = \App\Models\User::factory()->create([
                'name'  => 'Admin EasyColoc',
                'email' => 'admin@easycoloc.fr',
            ]);
            $admin->assignRole('admin');
        }

        // Créer un wallet pour chaque user existant
        \App\Models\User::all()->each(function ($user) {
            \App\Models\Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0.00]
            );
        });
    }
}
