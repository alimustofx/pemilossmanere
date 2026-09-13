<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voter;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoterController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $voters = Voter::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->orderBy('class_name')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Voters/Index', [
            'voters' => $voters,
            'filters' => [
                'search' => $request->input('search', ''),
                'per_page' => $perPage,
            ],
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Gagal membuka file CSV.');
        }

        $header = fgetcsv($handle); // baris pertama = header, dilewati
        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                $skipped++;

                continue;
            }

            [$nis, $nama, $kelas, $tanggalLahir] = array_map(
                static fn (?string $value): string => trim($value ?? ''),
                $row
            );

            if (empty($nis) || empty($nama) || empty($kelas) || empty($tanggalLahir)) {
                $skipped++;

                continue;
            }

            Voter::updateOrCreate(
                ['nis' => $nis],
                [
                    'name' => $nama,
                    'class_name' => $kelas,
                    'tanggal_lahir' => $tanggalLahir,
                    'status' => true,
                ]
            );

            $imported++;
        }

        fclose($handle);

        AuditLogger::log(
            "Import pemilih: {$imported} berhasil, {$skipped} dilewati"
        );

        return back()->with(
            'success',
            "Berhasil import {$imported} pemilih. {$skipped} baris dilewati (data tidak lengkap)."
        );
    }

    public function export(): StreamedResponse
    {
        $voters = Voter::orderBy('class_name')->orderBy('name')->get();

        $callback = function () use ($voters): void {
            $file = fopen('php://output', 'w');

            if ($file === false) {
                throw new \RuntimeException('Gagal membuka output CSV.');
            }

            fputcsv($file, ['NIS', 'Nama', 'Kelas', 'Status', 'OSIS Voted', 'MPK Voted']);

            foreach ($voters as $v) {
                fputcsv($file, [
                    $v->nis,
                    $v->name,
                    $v->class_name,
                    $v->status ? 'Aktif' : 'Nonaktif',
                    $v->osis_voted_at ? 'Ya' : 'Belum',
                    $v->mpk_voted_at ? 'Ya' : 'Belum',
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload(
            $callback,
            'rekap-pemilih.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function toggleStatus(Voter $voter): RedirectResponse
    {
        $voter->update([
            'status' => ! $voter->status,
        ]);

        AuditLogger::log(
            "Mengubah status pemilih: {$voter->name} menjadi ".
            ($voter->status ? 'aktif' : 'nonaktif')
        );

        return back();
    }

    public function destroy(Voter $voter): RedirectResponse
    {
        AuditLogger::log("Menghapus pemilih: {$voter->name} ({$voter->nis})");

        $voter->delete();

        return back();
    }
}
