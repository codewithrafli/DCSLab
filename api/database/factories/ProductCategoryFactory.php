<?php

namespace Database\Factories;

use App\Enums\ProductCategoryTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $productNames = [
        'Baju', 'Celana', 'Sepatu', 'Topi', 'Kacamata', 'Jam',
        'Gelang', 'Pensil', 'Penghapus', 'Buku', 'Rautan', 'Penggaris',
        'Kertas', 'Spidol', 'Tinta', 'Pulpen', 'Tas', 'Celengan',
        'Baterai', 'Kabel', 'Kipas', 'Lampu', 'Kulkas', 'Mesin Cuci',
        'TV', 'Radio', 'Kompor', 'Headset', 'Keyboard', 'Mouse', 'Monitor',
        'Powerbank', 'Charger', 'Adaptor', 'Kamera', 'Tripod', 'Stabilizer',
        'Obeng', 'Tang', 'Palu', 'Meteran', 'Gunting', 'Pisau',
        'Wajan', 'Panci', 'Talenan', 'Gelas', 'Piring', 'Sendok', 'Garpu',
        'Ember', 'Sapu', 'Pel', 'Gayung', 'Dispenser', 'Blender',
        'Microwave', 'Oven', 'Rice Cooker', 'Vacuum Cleaner',
        'Sabun', 'Sampo', 'Pasta Gigi', 'Sikat Gigi', 'Tisu', 'Detergen',
        'Parfum', 'Deodoran', 'Lotion', 'Masker', 'Hand Sanitizer',
        'Dompet', 'Koper', 'Ransel', 'Toples', 'Botol Minum', 'Kotak Makan',
        'Flashdisk', 'Harddisk', 'Memory Card', 'Printer', 'Scanner',
    ];

    protected $serviceNames = [
        'Jasa Cuci', 'Jasa Perbaikan', 'Jasa Konsultasi', 'Jasa Kebersihan', 'Jasa Pengiriman',
        'Jasa Pindahan', 'Jasa Servis AC', 'Jasa Las', 'Jasa Potong Rambut', 'Jasa Pijat',
        'Jasa Laundry', 'Jasa Penitipan Anak', 'Jasa Tukang Bangunan', 'Jasa Fotografi',
        'Jasa Videografi', 'Jasa Make Up', 'Jasa Desain Grafis', 'Jasa Cetak Undangan',
        'Jasa Percetakan', 'Jasa Servis Komputer', 'Jasa Instalasi Listrik', 'Jasa Catering',
        'Jasa Sewa Mobil', 'Jasa Sewa Motor', 'Jasa Sewa Tenda', 'Jasa Sewa Kamera',
        'Jasa Antar Jemput', 'Jasa Bersih Rumah', 'Jasa Pembuatan Website', 'Jasa SEO',
        'Jasa Digital Marketing', 'Jasa Editing Video', 'Jasa Voice Over', 'Jasa Penerjemah',
        'Jasa Penulisan Artikel', 'Jasa Konsultan Pajak', 'Jasa Konsultan Hukum',
        'Jasa Akuntansi', 'Jasa Pembuatan Aplikasi', 'Jasa Interior Design', 'Jasa Arsitektur',
        'Jasa Jahit Baju', 'Jasa Reparasi Sepeda', 'Jasa Sablon Kaos', 'Jasa Pengaspalan',
        'Jasa Penitipan Hewan', 'Jasa Dog Grooming', 'Jasa Cuci Karpet', 'Jasa Fogging Nyamuk',
        'Jasa Reparasi HP', 'Jasa Branding Produk', 'Jasa Packing Barang', 'Jasa Sablon Mug',
        'Jasa Survey', 'Jasa Pendakian Gunung', 'Jasa Travel Wisata', 'Jasa Tukang Ledeng',
        'Jasa Tambal Ban', 'Jasa Kalibrasi Alat', 'Jasa Uji Laboratorium',
    ];

    public function definition(): array
    {
        $productNames = $this->productNames;
        $serviceNames = $this->serviceNames;
        $type = fake()->randomElement(ProductCategoryTypeEnum::toArrayEnum());

        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'name' => (function () use ($type, $productNames, $serviceNames) {
                switch ($type) {
                    case ProductCategoryTypeEnum::PRODUCT:
                        return fake()->randomElement($productNames);
                    case ProductCategoryTypeEnum::SERVICE:
                        return fake()->randomElement($serviceNames);
                }
            }),
            'type' => $type,
        ];
    }

    public function forProduct()
    {
        return $this->state([
            'name' => fake()->randomElement($this->productNames),
            'type' => ProductCategoryTypeEnum::PRODUCT,
        ]);
    }

    public function forService()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->randomElement($this->serviceNames),
                'type' => ProductCategoryTypeEnum::SERVICE,
            ];
        });
    }

    public function insertStringInName(string $str)
    {
        return $this->state(function (array $attributes) use ($str) {
            return [
                'name' => $this->craftName($str),
            ];
        });
    }

    private function craftName(string $str)
    {
        $text = fake()->randomElement(['Buku', 'Elektronik', 'Tas']);

        return substr_replace($text, $str, random_int(0, strlen($text) - 1), 0);
    }
}
