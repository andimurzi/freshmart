<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeder dibuat idempoten (updateOrCreate) sehingga AMAN dijalankan
     * berulang kali — misalnya pada setiap deploy di Railway.
     */
    public function run(): void
    {
        // ===== Akun =====
        User::updateOrCreate(
            ['email' => 'admin@freshmart.test'],
            [
                'name'     => 'Admin FreshMart',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
                'phone'    => '081234567890',
                'gender'   => 'L',
            ]
        );

        User::updateOrCreate(
            ['email' => 'budi@example.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role'     => 'customer',
                'phone'    => '081298765432',
                'address'  => 'Jl. Melati No. 12, Bandung',
                'gender'   => 'L',
            ]
        );

        // ===== Kategori =====
        $categories = [
            ['name' => 'Buah Segar',    'icon' => '🍎', 'description' => 'Buah lokal & impor dipetik saat matang sempurna.'],
            ['name' => 'Sayuran',       'icon' => '🥦', 'description' => 'Sayur panen pagi langsung dari petani mitra.'],
            ['name' => 'Daging & Ikan', 'icon' => '🍗', 'description' => 'Protein segar dengan rantai dingin terjaga.'],
            ['name' => 'Susu & Telur',  'icon' => '🥛', 'description' => 'Produk peternakan segar setiap hari.'],
            ['name' => 'Roti & Kue',    'icon' => '🍞', 'description' => 'Dipanggang fresh setiap pagi.'],
            ['name' => 'Minuman',       'icon' => '🧃', 'description' => 'Jus segar dan minuman sehat.'],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            $model = Category::updateOrCreate(['slug' => $slug], $cat + ['slug' => $slug]);
            $catIds[$cat['name']] = $model->id;
        }

        // ===== Produk =====
        // [nama, kategori, harga, stok, unit, unggulan, deskripsi]
        $products = [
            ['Apel Fuji Premium', 'Buah Segar', 35000, 50, 'kg', true,
                'Apel Fuji impor dengan rasa manis renyah dan kadar air tinggi. Cocok untuk camilan sehat keluarga maupun bahan salad buah.'],
            ['Pisang Cavendish', 'Buah Segar', 22000, 60, 'sisir', true,
                'Pisang Cavendish matang pohon, manis legit dan kaya kalium. Sumber energi alami sebelum beraktivitas.'],
            ['Jeruk Mandarin', 'Buah Segar', 30000, 45, 'kg', false,
                'Jeruk mandarin segar tanpa biji, mudah dikupas, dengan rasa manis menyegarkan dan vitamin C tinggi.'],
            ['Semangka Merah', 'Buah Segar', 9000, 30, 'kg', false,
                'Semangka merah tanpa biji, juicy dan manis. Pelepas dahaga terbaik di cuaca panas.'],

            ['Brokoli Hijau', 'Sayuran', 18000, 40, 'pcs', true,
                'Brokoli hijau segar kaya serat dan antioksidan. Dipanen pagi hari agar tetap renyah saat dimasak.'],
            ['Bayam Hijau Organik', 'Sayuran', 6000, 80, 'ikat', true,
                'Bayam organik bebas pestisida, daun lebar dan segar. Sempurna untuk sayur bening dan tumisan.'],
            ['Wortel Berastagi', 'Sayuran', 14000, 55, 'kg', false,
                'Wortel Berastagi manis dan renyah, kaya beta-karoten untuk kesehatan mata. Cocok untuk sup, jus, dan MPASI.'],
            ['Tomat Merah Segar', 'Sayuran', 12000, 70, 'kg', false,
                'Tomat merah matang sempurna dengan rasa seimbang manis-asam. Wajib ada di dapur untuk sambal dan masakan harian.'],

            ['Dada Ayam Fillet', 'Daging & Ikan', 48000, 35, 'kg', true,
                'Dada ayam fillet tanpa tulang dan kulit, tinggi protein rendah lemak. Higienis dengan rantai dingin terjaga.'],
            ['Salmon Fillet Premium', 'Daging & Ikan', 75000, 20, 'pack', true,
                'Salmon fillet premium 250g kaya omega-3. Tekstur lembut, cocok dipanggang, ditim, atau untuk MPASI.'],
            ['Daging Sapi Sirloin', 'Daging & Ikan', 65000, 25, 'pack', false,
                'Daging sapi sirloin 500g empuk dan juicy. Pilihan tepat untuk steak rumahan dan tumisan istimewa.'],

            ['Susu UHT Full Cream', 'Susu & Telur', 19000, 90, 'liter', false,
                'Susu sapi UHT full cream 1 liter, gurih alami tanpa pengawet. Sumber kalsium untuk seluruh keluarga.'],
            ['Telur Ayam Negeri', 'Susu & Telur', 28000, 65, 'kg', true,
                'Telur ayam negeri segar dari peternakan mitra, dikemas hati-hati. Protein lengkap untuk menu sehari-hari.'],

            ['Roti Tawar Gandum', 'Roti & Kue', 16000, 40, 'pcs', false,
                'Roti tawar gandum utuh dipanggang setiap pagi. Lembut, kaya serat, sarapan praktis bergizi.'],

            ['Jus Jeruk Segar 500ml', 'Minuman', 15000, 50, 'pcs', true,
                'Jus jeruk peras asli tanpa gula tambahan, diproses dingin agar vitamin tetap utuh. Segar maksimal!'],
        ];

        foreach ($products as [$name, $catName, $price, $stock, $unit, $featured, $desc]) {
            $slug = Str::slug($name);
            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $catIds[$catName],
                    'name'        => $name,
                    'description' => $desc,
                    'price'       => $price,
                    'stock'       => $stock,
                    'unit'        => $unit,
                    'image'       => 'images/products/' . $slug . '.svg',
                    'is_featured' => $featured,
                    'is_active'   => true,
                ]
            );
        }
    }
}
