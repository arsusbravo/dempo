<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'category_slug' => 'dimsum',
                'name' => 'Dimsum Kukus',
                'slug' => 'dimsum-kukus',
                'description' => 'Dimsum lembut dengan isian pilihan, disajikan hangat.',
                'price' => 85000,
                'source_image' => 'dimsums.png',
                'sort_order' => 1,
            ],
            [
                'category_slug' => 'gorengan-risol',
                'name' => 'Risol Renyah',
                'slug' => 'risol-renyah',
                'description' => 'Risol crispy dengan isian gurih di setiap gigitan.',
                'price' => 65000,
                'source_image' => 'risol.png',
                'sort_order' => 1,
            ],
            [
                'category_slug' => 'dessert',
                'name' => 'Mini Tiramisu',
                'slug' => 'mini-tiramisu',
                'description' => 'Dessert manis mini yang cantik dan menggoda.',
                'price' => 95000,
                'source_image' => 'tiramisu.png',
                'sort_order' => 1,
            ],
        ];

        foreach ($menus as $menu) {
            $category = MenuCategory::where('slug', $menu['category_slug'])->first();

            Menu::updateOrCreate(
                ['slug' => $menu['slug']],
                [
                    'menu_category_id' => $category?->id,
                    'name' => $menu['name'],
                    'description' => $menu['description'],
                    'price' => $menu['price'],
                    'image' => $this->copySeedImage($menu['source_image']),
                    'is_featured' => true,
                    'is_active' => true,
                    'sort_order' => $menu['sort_order'],
                ]
            );
        }
    }

    /**
     * Copy an existing static asset into the "menus" storage folder, if present.
     */
    private function copySeedImage(string $filename): ?string
    {
        $source = public_path("assets/images/{$filename}");

        if (! File::exists($source)) {
            return null;
        }

        $destination = "menus/{$filename}";

        Storage::disk('public')->put($destination, File::get($source));

        return $destination;
    }
}
