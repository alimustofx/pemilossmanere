<?php

namespace Database\Seeders;

use App\Models\Voter;
use Illuminate\Database\Seeder;

class VoterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nis' => '12345', 'name' => 'Ahmad Rizki', 'class_name' => 'X-A', 'tanggal_lahir' => '2008-05-12'],
            ['nis' => '12346', 'name' => 'Budi Santoso', 'class_name' => 'X-A', 'tanggal_lahir' => '2008-07-23'],
            ['nis' => '12347', 'name' => 'Citra Lestari', 'class_name' => 'X-B', 'tanggal_lahir' => '2008-09-01'],
            ['nis' => '12348', 'name' => 'Dinda Permata', 'class_name' => 'X-B', 'tanggal_lahir' => '2008-03-15'],
            ['nis' => '12349', 'name' => 'Eko Prasetyo', 'class_name' => 'X-C', 'tanggal_lahir' => '2008-11-20'],
        ];

        foreach ($data as $item) {
            Voter::create([
                'nis' => $item['nis'],
                'name' => $item['name'],
                'class_name' => $item['class_name'],
                'tanggal_lahir' => $item['tanggal_lahir'],
                'status' => true,
            ]);
        }
    }
}