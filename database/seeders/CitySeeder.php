<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tentukan path file secara absolut menggunakan storage_path()
        $filePath = storage_path('app/data/cities.json');

        if (!file_exists($filePath)) {
            $this->command->error("File cities.json not found in: {$filePath}");
            return;
        }

        // Membaca file JSON
        $json = file_get_contents($filePath);

        $data = json_decode($json, true);
        if (!$data) {
            $this->command->error("JSON Data not valid!");
            return;
        }

        $cities = [];
        foreach ($data as $province) {
            // Pastikan 'kota' ada dalam data provinsi
            if (isset($province['kota'])) {
                foreach ($province['kota'] as $city) {
                    $cities[] = [
                        'city_name'  => $city,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert data ke tabel cities
        DB::table('cities')->insert($cities);
        $this->command->info('Data cities inserted successfuly');
    }
}
