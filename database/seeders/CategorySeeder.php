<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Catégories globales partagées par TOUTES les colocations
        // Aucun lien avec une colocation spécifique
        $categories = [
            'Loyer',
            'Courses / Nourriture',
            'Électricité / Eau / Gaz',
            'Internet / Téléphone',
            'Nettoyage',
            'Autre',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
