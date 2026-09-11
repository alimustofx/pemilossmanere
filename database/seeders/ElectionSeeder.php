<?php

namespace Database\Seeders;

use App\Models\Election;
use App\Models\CandidateGroup;
use App\Models\Candidate;
use App\Models\CandidateProgram;
use Illuminate\Database\Seeder;

class ElectionSeeder extends Seeder
{
    public function run(): void
    {
        // Election OSIS
        $osis = Election::create([
            'name' => 'Pemilihan Ketua & Wakil Ketua OSIS',
            'type' => 'osis',
            'status' => 'open',
            'starts_at' => now(),
            'ends_at' => now()->addDays(3),
        ]);

        $osisCandidates = [
            ['nomor_urut' => 1, 'nama_kelompok' => 'Ahmad & Budi', 'ketua' => 'Ahmad', 'wakil' => 'Budi'],
            ['nomor_urut' => 2, 'nama_kelompok' => 'Citra & Dinda', 'ketua' => 'Citra', 'wakil' => 'Dinda'],
            ['nomor_urut' => 3, 'nama_kelompok' => 'Eko & Fajar', 'ketua' => 'Eko', 'wakil' => 'Fajar'],
        ];

        foreach ($osisCandidates as $item) {
            $group = CandidateGroup::create([
                'election_id' => $osis->id,
                'nomor_urut' => $item['nomor_urut'],
                'nama_kelompok' => $item['nama_kelompok'],
                'model' => 'pair',
                'visi' => 'Menjadikan OSIS lebih aktif, transparan, dan dekat dengan siswa.',
                'misi' => 'Mengadakan program kerja yang melibatkan seluruh siswa.',
            ]);

            Candidate::create(['candidate_group_id' => $group->id, 'name' => $item['ketua'], 'role' => 'ketua']);
            Candidate::create(['candidate_group_id' => $group->id, 'name' => $item['wakil'], 'role' => 'wakil']);

            CandidateProgram::create([
                'candidate_group_id' => $group->id,
                'title' => 'Program Unggulan 1',
                'description' => 'Contoh program kerja unggulan pasangan ini.',
                'order' => 1,
            ]);
        }

        // Election MPK
        $mpk = Election::create([
            'name' => 'Pemilihan Ketua & Wakil Ketua MPK',
            'type' => 'mpk',
            'status' => 'open',
            'starts_at' => now(),
            'ends_at' => now()->addDays(3),
        ]);

        $mpkCandidates = [
            ['nomor_urut' => 1, 'nama_kelompok' => 'Gilang & Hana', 'ketua' => 'Gilang', 'wakil' => 'Hana'],
            ['nomor_urut' => 2, 'nama_kelompok' => 'Indra & Joko', 'ketua' => 'Indra', 'wakil' => 'Joko'],
        ];

        foreach ($mpkCandidates as $item) {
            $group = CandidateGroup::create([
                'election_id' => $mpk->id,
                'nomor_urut' => $item['nomor_urut'],
                'nama_kelompok' => $item['nama_kelompok'],
                'model' => 'pair',
                'visi' => 'Mengawasi jalannya program OSIS secara adil dan aktif.',
                'misi' => 'Menjadi jembatan aspirasi siswa ke pihak sekolah.',
            ]);

            Candidate::create(['candidate_group_id' => $group->id, 'name' => $item['ketua'], 'role' => 'ketua']);
            Candidate::create(['candidate_group_id' => $group->id, 'name' => $item['wakil'], 'role' => 'wakil']);
        }
    }
}