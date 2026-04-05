<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        $comments = [
            5 => ['Enak banget!', 'Mantap, pasti balik lagi', 'Recommended!', 'Porsinya pas, rasanya juara'],
            4 => ['Lumayan enak', 'Cukup memuaskan', 'Suka rasanya', 'Enak, tapi agak lama'],
            3 => ['Biasa aja', 'Standar', 'Cukup lah', 'Bisa lebih baik'],
            2 => ['Kurang sesuai ekspektasi', 'Agak hambar', 'Tidak terlalu suka'],
            1 => ['Kurang enak', 'Tidak sesuai selera'],
        ];

        foreach ($products as $product) {
            // 3–8 ulasan per produk
            $reviewCount = rand(3, 8);
            for ($i = 0; $i < $reviewCount; $i++) {
                // Distribusi rating: lebih banyak 4-5
                $rating  = collect([5,5,5,4,4,4,3,2,1])->random();
                $comment = collect($comments[$rating])->random();

                Review::create([
                    'product_id' => $product->id,
                    'rating'     => $rating,
                    'comment'    => $comment,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
