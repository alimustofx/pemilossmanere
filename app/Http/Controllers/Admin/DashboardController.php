<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(): InertiaResponse
    {
        $totalVoters = Voter::where('status', true)->count();

        $osisVoted = Voter::where('status', true)->whereNotNull('osis_voted_at')->count();
        $mpkVoted = Voter::where('status', true)->whereNotNull('mpk_voted_at')->count();

        // "Sudah memilih" dianggap selesai kalau sudah vote di kedua pemilihan
        $fullyVoted = Voter::where('status', true)
            ->whereNotNull('osis_voted_at')
            ->whereNotNull('mpk_voted_at')
            ->count();

        $participation = $totalVoters > 0
            ? round(($fullyVoted / $totalVoters) * 100, 2)
            : 0;

        $osisResult = Vote::where('election_id', Election::where('type', 'osis')->value('id'))
            ->selectRaw('candidate_group_id, count(*) as total')
            ->groupBy('candidate_group_id')
            ->with('candidateGroup:id,nama_kelompok,nomor_urut')
            ->get();

        $mpkResult = Vote::where('election_id', Election::where('type', 'mpk')->value('id'))
            ->selectRaw('candidate_group_id, count(*) as total')
            ->groupBy('candidate_group_id')
            ->with('candidateGroup:id,nama_kelompok,nomor_urut')
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_voters' => $totalVoters,
                'osis_voted' => $osisVoted,
                'mpk_voted' => $mpkVoted,
                'fully_voted' => $fullyVoted,
                'belum_memilih' => $totalVoters - $fullyVoted,
                'participation' => $participation,
            ],
            'osisResult' => $osisResult,
            'mpkResult' => $mpkResult,
        ]);
    }

    public function exportResults(): StreamedResponse
    {
        /** @var Collection<int, Vote> $data */
        $data = Vote::selectRaw('election_id, candidate_group_id, count(*) as total')
            ->groupBy('election_id', 'candidate_group_id')
            ->with(['election:id,name,type', 'candidateGroup:id,nama_kelompok'])
            ->get();

        $callback = function () use ($data): void {
            $file = fopen('php://output', 'w');

            if ($file === false) {
                throw new \RuntimeException('Gagal membuka output CSV.');
            }

            fputcsv($file, ['Pemilihan', 'Kandidat', 'Total Suara']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->election->name,
                    $row->candidateGroup->nama_kelompok,
                    (int) $row->getAttribute('total'),
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload(
            $callback,
            'rekap-hasil.csv',
            ['Content-Type' => 'text/csv']
        );
    }
}
