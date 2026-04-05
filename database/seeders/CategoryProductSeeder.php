<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::firstOrCreate(['name' => 'Makanan'], ['description' => 'Menu makanan utama']);
        $minuman = Category::firstOrCreate(['name' => 'Minuman'], ['description' => 'Minuman segar']);
        $camilan = Category::firstOrCreate(['name' => 'Camilan'], ['description' => 'Makanan ringan & pelengkap']);
        $paket   = Category::firstOrCreate(['name' => 'Paket Hemat'], ['description' => 'Paket lengkap lebih hemat']);

        $products = [
            // ── Makanan ──────────────────────────────────────────────────
            ['category_id' => $makanan->id, 'name' => 'Ikan Mujair Goreng',       'price' => 35000, 'description' => 'Ikan mujair segar digoreng crispy khas Bangli, disajikan dengan sambal matah dan lalapan.'],
            ['category_id' => $makanan->id, 'name' => 'Ikan Mujair Bakar',        'price' => 38000, 'description' => 'Ikan mujair bakar bumbu kecap manis, aroma harum khas arang.'],
            ['category_id' => $makanan->id, 'name' => 'Ikan Mujair Nyat-nyat',    'price' => 42000, 'description' => 'Ikan mujair dimasak bumbu genep Bali, kuah kuning kaya rempah.'],
            ['category_id' => $makanan->id, 'name' => 'Ikan Mujair Pepes',        'price' => 38000, 'description' => 'Ikan mujair dibungkus daun pisang dengan bumbu rempah pilihan, dikukus hingga meresap.'],
            ['category_id' => $makanan->id, 'name' => 'Nasi Putih',               'price' => 5000,  'description' => 'Nasi putih pulen, porsi standar.'],
            ['category_id' => $makanan->id, 'name' => 'Nasi Goreng Ikan',         'price' => 25000, 'description' => 'Nasi goreng dengan potongan ikan mujair, telur, dan sayuran segar.'],
            ['category_id' => $makanan->id, 'name' => 'Ayam Goreng Bumbu Bali',   'price' => 30000, 'description' => 'Ayam kampung goreng dengan bumbu base genep khas Bali.'],
            ['category_id' => $makanan->id, 'name' => 'Ayam Bakar Taliwang',      'price' => 32000, 'description' => 'Ayam bakar pedas manis bumbu taliwang, disajikan dengan plecing kangkung.'],
            ['category_id' => $makanan->id, 'name' => 'Plecing Kangkung',         'price' => 12000, 'description' => 'Kangkung rebus dengan sambal tomat pedas segar khas Bali.'],
            ['category_id' => $makanan->id, 'name' => 'Lawar Nangka',             'price' => 15000, 'description' => 'Lawar nangka muda dengan bumbu kelapa parut dan rempah Bali.'],
            ['category_id' => $makanan->id, 'name' => 'Sate Lilit Ikan',          'price' => 20000, 'description' => 'Sate lilit dari daging ikan mujair cincang dengan bumbu khas Bali, 5 tusuk.'],

            // ── Minuman ───────────────────────────────────────────────────
            ['category_id' => $minuman->id, 'name' => 'Es Teh Manis',             'price' => 5000,  'description' => 'Teh manis dingin segar.'],
            ['category_id' => $minuman->id, 'name' => 'Teh Hangat',               'price' => 4000,  'description' => 'Teh hangat manis atau tawar.'],
            ['category_id' => $minuman->id, 'name' => 'Es Jeruk',                 'price' => 8000,  'description' => 'Jeruk peras segar dengan es batu.'],
            ['category_id' => $minuman->id, 'name' => 'Jus Alpukat',              'price' => 15000, 'description' => 'Jus alpukat segar dengan susu kental manis.'],
            ['category_id' => $minuman->id, 'name' => 'Jus Semangka',             'price' => 12000, 'description' => 'Jus semangka segar tanpa gula tambahan.'],
            ['category_id' => $minuman->id, 'name' => 'Es Kelapa Muda',           'price' => 15000, 'description' => 'Kelapa muda segar langsung dari buahnya.'],
            ['category_id' => $minuman->id, 'name' => 'Air Mineral',              'price' => 4000,  'description' => 'Air mineral botol 600ml.'],
            ['category_id' => $minuman->id, 'name' => 'Kopi Hitam',               'price' => 8000,  'description' => 'Kopi hitam robusta Kintamani, diseduh panas.'],
            ['category_id' => $minuman->id, 'name' => 'Es Kopi Susu',             'price' => 15000, 'description' => 'Kopi susu dingin dengan gula aren, kopi Kintamani.'],

            // ── Camilan ───────────────────────────────────────────────────
            ['category_id' => $camilan->id, 'name' => 'Kerupuk',                  'price' => 3000,  'description' => 'Kerupuk renyah pelengkap makan.'],
            ['category_id' => $camilan->id, 'name' => 'Tempe Goreng',             'price' => 8000,  'description' => 'Tempe goreng crispy, 5 potong.'],
            ['category_id' => $camilan->id, 'name' => 'Tahu Goreng',              'price' => 8000,  'description' => 'Tahu goreng kuning, 4 potong.'],
            ['category_id' => $camilan->id, 'name' => 'Sambal Matah',             'price' => 5000,  'description' => 'Sambal matah segar dengan irisan bawang, cabai, dan serai.'],
            ['category_id' => $camilan->id, 'name' => 'Sambal Tomat',             'price' => 5000,  'description' => 'Sambal tomat pedas segar.'],

            // ── Paket Hemat ───────────────────────────────────────────────
            ['category_id' => $paket->id,   'name' => 'Paket Midori 1',           'price' => 45000, 'description' => 'Ikan Mujair Goreng + Nasi Putih + Es Teh Manis. Hemat Rp 3.000.'],
            ['category_id' => $paket->id,   'name' => 'Paket Midori 2',           'price' => 48000, 'description' => 'Ikan Mujair Bakar + Nasi Putih + Es Jeruk. Hemat Rp 3.000.'],
            ['category_id' => $paket->id,   'name' => 'Paket Keluarga',           'price' => 150000,'description' => 'Ikan Mujair Goreng 2 ekor + Nasi Putih 4 porsi + Plecing Kangkung + 4 Es Teh. Untuk 4 orang.'],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                array_merge($product, ['is_available' => true])
            );
        }
    }
}
