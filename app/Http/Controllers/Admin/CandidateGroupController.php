<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateGroup;
use App\Models\CandidateProgram;
use App\Models\Election;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CandidateGroupController extends Controller
{
    public function index(): Response
    {
        $groups = CandidateGroup::with(['election', 'candidates', 'programs'])
            ->orderBy('election_id')
            ->orderBy('nomor_urut')
            ->get();

        return Inertia::render('Admin/Candidates/Index', [
            'groups' => $groups,
            'elections' => Election::select('id', 'name', 'type')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'nomor_urut' => 'required|integer|min:1',
            'nama_kelompok' => 'required|string|max:255',
            'model' => 'required|in:pair,individual',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'ketua_name' => 'required|string|max:255',
            'ketua_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'wakil_name' => 'nullable|string|max:255',
            'wakil_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'programs' => 'nullable|array',
            'programs.*.title' => 'required|string|max:255',
            'programs.*.description' => 'nullable|string',
        ]);

        $group = CandidateGroup::create([
            'election_id' => $validated['election_id'],
            'nomor_urut' => $validated['nomor_urut'],
            'nama_kelompok' => $validated['nama_kelompok'],
            'model' => $validated['model'],
            'visi' => $validated['visi'] ?? null,
            'misi' => $validated['misi'] ?? null,
        ]);

        $ketuaPhotoPath = $request->hasFile('ketua_photo')
            ? $request->file('ketua_photo')->store('candidates', 'public')
            : null;

        Candidate::create([
            'candidate_group_id' => $group->id,
            'name' => $validated['ketua_name'],
            'role' => 'ketua',
            'photo' => $ketuaPhotoPath,
        ]);

        if ($validated['model'] === 'pair' && ! empty($validated['wakil_name'])) {
            $wakilPhotoPath = $request->hasFile('wakil_photo')
                ? $request->file('wakil_photo')->store('candidates', 'public')
                : null;

            Candidate::create([
                'candidate_group_id' => $group->id,
                'name' => $validated['wakil_name'],
                'role' => 'wakil',
                'photo' => $wakilPhotoPath,
            ]);
        }

        foreach ($validated['programs'] ?? [] as $i => $program) {
            CandidateProgram::create([
                'candidate_group_id' => $group->id,
                'title' => $program['title'],
                'description' => $program['description'] ?? null,
                'order' => $i + 1,
            ]);
        }

        AuditLogger::log(
            "Menambahkan kandidat: {$group->nama_kelompok} untuk election ID {$group->election_id}"
        );

        return redirect()
            ->route('admin.candidates.index')
            ->with('success', 'Kandidat berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        CandidateGroup $candidateGroup
    ): RedirectResponse {
        $validated = $request->validate([
            'nomor_urut' => 'required|integer|min:1',
            'nama_kelompok' => 'required|string|max:255',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'ketua_name' => 'required|string|max:255',
            'ketua_photo' => 'nullable|image|max:2048',
            'wakil_name' => 'nullable|string|max:255',
            'wakil_photo' => 'nullable|image|max:2048',
            'programs' => 'nullable|array',
            'programs.*.title' => 'required|string|max:255',
            'programs.*.description' => 'nullable|string',
        ]);

        $candidateGroup->update([
            'nomor_urut' => $validated['nomor_urut'],
            'nama_kelompok' => $validated['nama_kelompok'],
            'visi' => $validated['visi'] ?? null,
            'misi' => $validated['misi'] ?? null,
        ]);

        $ketua = $candidateGroup->candidates()->where('role', 'ketua')->first();
        $ketuaPhoto = $request->hasFile('ketua_photo')
            ? $request->file('ketua_photo')->store('candidates', 'public')
            : $ketua?->photo;

        if ($ketua) {
            $ketua->update([
                'name' => $validated['ketua_name'],
                'photo' => $ketuaPhoto,
            ]);
        } else {
            Candidate::create([
                'candidate_group_id' => $candidateGroup->id,
                'name' => $validated['ketua_name'],
                'role' => 'ketua',
                'photo' => $ketuaPhoto,
            ]);
        }

        if ($candidateGroup->model === 'pair' && ! empty($validated['wakil_name'])) {
            $wakil = $candidateGroup->candidates()->where('role', 'wakil')->first();
            $wakilPhoto = $request->hasFile('wakil_photo')
                ? $request->file('wakil_photo')->store('candidates', 'public')
                : $wakil?->photo;

            if ($wakil) {
                $wakil->update([
                    'name' => $validated['wakil_name'],
                    'photo' => $wakilPhoto,
                ]);
            } else {
                Candidate::create([
                    'candidate_group_id' => $candidateGroup->id,
                    'name' => $validated['wakil_name'],
                    'role' => 'wakil',
                    'photo' => $wakilPhoto,
                ]);
            }
        }

        $candidateGroup->programs()->delete();

        foreach ($validated['programs'] ?? [] as $i => $program) {
            CandidateProgram::create([
                'candidate_group_id' => $candidateGroup->id,
                'title' => $program['title'],
                'description' => $program['description'] ?? null,
                'order' => $i + 1,
            ]);
        }

        AuditLogger::log(
            "Memperbarui kandidat: {$candidateGroup->nama_kelompok}"
        );

        return redirect()
            ->route('admin.candidates.index')
            ->with('success', 'Kandidat berhasil diperbarui.');
    }

    public function destroy(CandidateGroup $candidateGroup): RedirectResponse
    {
        AuditLogger::log(
            "Menghapus kandidat: {$candidateGroup->nama_kelompok}"
        );

        $candidateGroup->delete(); // cascade ke candidates & programs via FK

        return back()->with('success', 'Kandidat berhasil dihapus.');
    }
}
