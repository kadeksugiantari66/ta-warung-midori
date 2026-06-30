<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::firstOrCreate(
            ['name' => 'Makanan'],
            ['description' => 'Daftar menu makanan']
        );

        $minuman = Category::firstOrCreate(
            ['name' => 'Minuman'],
            ['description' => 'Daftar menu minuman']
        );

        $jus = Category::firstOrCreate(
            ['name' => 'Jus'],
            ['description' => 'Daftar menu jus buah']
        );

        $products = [
            // Minuman
            ['id_category' => $minuman->id_category, 'name' => 'Air Mineral', 'price' => 5000, 'description' => 'Air mineral dingin/segar.', 'image' => $this->productImage('air-mineral.jpg')],
            ['id_category' => $minuman->id_category, 'name' => 'Jeruk Hangat', 'price' => 8000, 'description' => 'Minuman jeruk hangat.', 'image' => $this->productImage('jeruk.jpg')],
            ['id_category' => $minuman->id_category, 'name' => 'Es Jeruk', 'price' => 8000, 'description' => 'Minuman jeruk dingin.', 'image' => $this->productImage('jeruk.jpg')],
            ['id_category' => $minuman->id_category, 'name' => 'Es Teh / Teh Hangat', 'price' => 6000, 'description' => 'Pilihan es teh atau teh hangat.', 'image' => $this->productImage('es-teh.jpg')],
            ['id_category' => $minuman->id_category, 'name' => 'Kopi Bali', 'price' => 5000, 'description' => 'Kopi bali hangat.', 'image' => $this->productImage('kopi-bali.jpg')],

            // Jus
            ['id_category' => $jus->id_category, 'name' => 'Alpukat', 'price' => 10000, 'description' => 'Jus alpukat segar.', 'image' => $this->productImage('jus-alpukat.jpg')],
            ['id_category' => $jus->id_category, 'name' => 'Sirsak', 'price' => 10000, 'description' => 'Jus sirsak segar.', 'image' => $this->productImage('jus-sirsak.jpg')],
            ['id_category' => $jus->id_category, 'name' => 'Mangga', 'price' => 9000, 'description' => 'Jus mangga segar.', 'image' => $this->productImage('jus-mangga.jpg')],
            ['id_category' => $jus->id_category, 'name' => 'Wortel', 'price' => 9000, 'description' => 'Jus wortel segar.', 'image' => $this->productImage('jus-wortel.jpg')],
            ['id_category' => $jus->id_category, 'name' => 'Tomat', 'price' => 9000, 'description' => 'Jus tomat segar.', 'image' => $this->productImage('jus-tomat.jpg')],
            ['id_category' => $jus->id_category, 'name' => 'Melon', 'price' => 9000, 'description' => 'Jus melon segar.', 'image' => $this->productImage('jus-melon.jpg')],
            ['id_category' => $jus->id_category, 'name' => 'Semangka', 'price' => 9000, 'description' => 'Jus semangka segar.', 'image' => $this->productImage('jus-semangka.jpg')],

            // Makanan
            ['id_category' => $makanan->id_category, 'name' => 'Mujair Nyat-Nyat + Nasi', 'price' => 28000, 'description' => 'Mujair nyat-nyat dengan nasi.', 'image' => $this->productImage('mujair-nyat-nyat.jpg')],
            ['id_category' => $makanan->id_category, 'name' => 'Mujair Plecing + Nasi', 'price' => 23000, 'description' => 'Mujair plecing dengan nasi.', 'image' => $this->productImage('mujair-plecing.jpg')],
            ['id_category' => $makanan->id_category, 'name' => 'Mujair Sambal Matah + Nasi', 'price' => 23000, 'description' => 'Mujair sambal matah dengan nasi.', 'image' => $this->productImage('mujair-sambal-matah.jpg')],
            ['id_category' => $makanan->id_category, 'name' => 'Ayam Nyat-Nyat + Nasi', 'price' => 27000, 'description' => 'Ayam nyat-nyat dengan nasi.', 'image' => $this->productImage('ayam-nyat-nyat.jpg')],
            ['id_category' => $makanan->id_category, 'name' => 'Ayam Plecing + Nasi', 'price' => 23000, 'description' => 'Ayam plecing dengan nasi.', 'image' => $this->productImage('ayam-plecing.jpg')],
            ['id_category' => $makanan->id_category, 'name' => 'Kentang Goreng', 'price' => 14000, 'description' => 'Kentang goreng renyah.', 'image' => $this->productImage('kentang-goreng.jpg')],
            ['id_category' => $makanan->id_category, 'name' => 'Sosis Goreng', 'price' => 14000, 'description' => 'Sosis goreng gurih.', 'image' => $this->productImage('sosis-goreng.jpg')],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                array_merge($product, ['is_available' => true])
            );
        }

        $officialMenuNames = array_column($products, 'name');

        Product::whereNotIn('name', $officialMenuNames)->delete();
    }

    private function productImage(string $filename): string
    {
        $source = database_path("seeders/assets/products/{$filename}");
        $target = "products/{$filename}";

        if (is_file($source)) {
            Storage::disk('public')->put($target, file_get_contents($source));
        }

        return $target;
    }
}
