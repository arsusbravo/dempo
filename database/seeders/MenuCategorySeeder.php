<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Dimsum', 'slug' => 'dimsum', 'sort_order' => 1],
            ['name' => 'Gorengan & Risol', 'slug' => 'gorengan-risol', 'sort_order' => 2],
            ['name' => 'Dessert', 'slug' => 'dessert', 'sort_order' => 3],
        ];

        foreach ($categories as $category) {
            MenuCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
