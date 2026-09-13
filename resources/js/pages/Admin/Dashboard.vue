<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { dashboard } from '@/routes';

type CandidateResult = {
    candidate_group_id: number;
    total: number;
    candidate_group?: {
        nama_kelompok: string;
    } | null;
};

type Stats = {
    total_voters: number;
    fully_voted: number;
    belum_memilih: number;
    participation: number;
};

defineProps<{
    stats: Stats;
    osisResult: CandidateResult[];
    mpkResult: CandidateResult[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

// === HELPERS ===
function totalVotes(list: CandidateResult[]): number {
    return list.reduce((sum, r) => sum + r.total, 0);
}

function maxVotes(list: CandidateResult[]): number {
    if (list.length === 0) return 1;
    return Math.max(...list.map((r) => r.total), 1);
}

function percentOf(list: CandidateResult[], item: CandidateResult): number {
    const total = totalVotes(list);
    if (total === 0) return 0;
    return Math.round((item.total / total) * 100);
}

function sortedByTotal(list: CandidateResult[]): CandidateResult[] {
    return [...list].sort((a, b) => b.total - a.total);
}
</script>

<template>
    <div class="admin">
        <!-- =====================================================
         HEADER
    ====================================================== -->

        <header class="admin__header">
            <div class="admin__header-left">
                <p class="admin__eyebrow">Panel Admin</p>

                <h1 class="admin__title">Dashboard <em>Pemilos.</em></h1>

                <p class="admin__desc">
                    Monitoring realtime pemilihan Ketua &amp; Wakil Ketua OSIS
                    serta MPK periode 2026/2027.
                </p>
            </div>

            <div class="admin__header-right">
                <a href="/admin/dashboard/export-results" class="admin__export">
                    <span class="admin__export-icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                            />
                            <path d="M7 10l5 5 5-5" />
                            <path d="M12 15V3" />
                        </svg>
                    </span>
                    <span class="admin__export-label">Export Hasil CSV</span>
                </a>
            </div>
        </header>

        <!-- =====================================================
         META ROW
    ====================================================== -->

        <div class="admin__meta">
            <span class="admin__meta-step">
                <span class="admin__meta-number">01</span>
                Ringkasan Pemilihan
            </span>
            <span class="admin__meta-year"> Pemilos 2026/2027 </span>
        </div>

        <!-- =====================================================
         STATISTIK
    ====================================================== -->

        <section class="stats">
            <div class="stats__grid">
                <!-- TOTAL PEMILIH -->
                <div class="stat-card">
                    <span class="stat-card__label">Total Pemilih</span>
                    <span class="stat-card__number">{{
                        stats.total_voters
                    }}</span>
                    <span class="stat-card__sub">Terdaftar aktif</span>
                </div>

                <!-- SUDAH MEMILIH -->
                <div class="stat-card stat-card--accent">
                    <span class="stat-card__label stat-card__label--accent">
                        Sudah Memilih
                    </span>
                    <span class="stat-card__number stat-card__number--accent">
                        {{ stats.fully_voted }}
                    </span>
                    <span class="stat-card__sub stat-card__sub--accent">
                        Suara tercatat
                    </span>
                </div>

                <!-- BELUM MEMILIH -->
                <div class="stat-card">
                    <span class="stat-card__label">Belum Memilih</span>
                    <span class="stat-card__number">{{
                        stats.belum_memilih
                    }}</span>
                    <span class="stat-card__sub">Menunggu partisipasi</span>
                </div>

                <!-- PARTISIPASI -->
                <div class="stat-card stat-card--participation">
                    <span class="stat-card__label stat-card__label--green">
                        Partisipasi
                    </span>
                    <span class="stat-card__number stat-card__number--green">
                        {{ stats.participation }}%
                    </span>
                    <div class="stat-card__bar">
                        <div
                            class="stat-card__bar-fill"
                            :style="{
                                width: `${Math.min(stats.participation, 100)}%`,
                            }"
                        ></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
         HASIL VOTING
    ====================================================== -->

        <section class="results">
            <!-- OSIS -->
            <div class="results__card">
                <div class="results__head">
                    <div>
                        <p class="results__eyebrow">Realtime Count</p>
                        <h2 class="results__title">Hasil <em>OSIS</em></h2>
                    </div>

                    <span class="results__total">
                        {{ totalVotes(osisResult) }} suara
                    </span>
                </div>

                <div v-if="osisResult.length === 0" class="results__empty">
                    <span class="results__empty-icon">📭</span>
                    <p>Belum ada suara masuk.</p>
                </div>

                <div v-else class="results__list">
                    <div
                        v-for="result in sortedByTotal(osisResult)"
                        :key="result.candidate_group_id"
                        class="result-bar"
                    >
                        <div class="result-bar__header">
                            <span class="result-bar__name">
                                {{
                                    result.candidate_group?.nama_kelompok ??
                                    'Tidak diketahui'
                                }}
                            </span>

                            <div class="result-bar__meta">
                                <span class="result-bar__total">
                                    {{ result.total }}
                                </span>
                                <span class="result-bar__percent">
                                    {{ percentOf(osisResult, result) }}%
                                </span>
                            </div>
                        </div>

                        <div class="result-bar__track">
                            <div
                                class="result-bar__fill"
                                :style="{
                                    width: `${(result.total / maxVotes(osisResult)) * 100}%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MPK -->
            <div class="results__card">
                <div class="results__head">
                    <div>
                        <p class="results__eyebrow">Realtime Count</p>
                        <h2 class="results__title">Hasil <em>MPK</em></h2>
                    </div>

                    <span class="results__total">
                        {{ totalVotes(mpkResult) }} suara
                    </span>
                </div>

                <div v-if="mpkResult.length === 0" class="results__empty">
                    <span class="results__empty-icon">📭</span>
                    <p>Belum ada suara masuk.</p>
                </div>

                <div v-else class="results__list">
                    <div
                        v-for="result in sortedByTotal(mpkResult)"
                        :key="result.candidate_group_id"
                        class="result-bar"
                    >
                        <div class="result-bar__header">
                            <span class="result-bar__name">
                                {{
                                    result.candidate_group?.nama_kelompok ??
                                    'Tidak diketahui'
                                }}
                            </span>

                            <div class="result-bar__meta">
                                <span class="result-bar__total">
                                    {{ result.total }}
                                </span>
                                <span class="result-bar__percent">
                                    {{ percentOf(mpkResult, result) }}%
                                </span>
                            </div>
                        </div>

                        <div class="result-bar__track">
                            <div
                                class="result-bar__fill"
                                :style="{
                                    width: `${(result.total / maxVotes(mpkResult)) * 100}%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =====================================================
         NOTE
    ====================================================== -->

        <p class="admin__note">
            Data diperbarui secara otomatis. Halaman ini hanya dapat diakses
            oleh panitia dan administrator pemilihan.
        </p>
    </div>
</template>

<style scoped>
/* =========================================================
   ROOT — TEMA EDITORIAL
   (dengan jarak dari sidebar)
========================================================= */

.admin {
    --ink: #181716;
    --ink-muted: #77736d;
    --ink-faint: #a09b93;
    --accent: #4b1f63;
    --accent-soft: #7c3a9e;
    --green: #2d8a52;
    --paper: #f6f3ee;
    --paper-dark: #ebe7df;
    --white: #fffdf9;
    --line: #d9d4cb;

    width: 100%;
    max-width: 1180px;

    /* ✅ Jarak dari sidebar + tepi layar */
    margin-left: auto;
    margin-right: auto;

    padding: 32px 40px 64px;

    color: var(--ink);

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    -webkit-font-smoothing: antialiased;
}

.admin *,
.admin *::before,
.admin *::after {
    box-sizing: border-box;
}

.admin a {
    -webkit-tap-highlight-color: transparent;
}

/* =========================================================
   HEADER
========================================================= */

.admin__header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;

    gap: 32px;

    padding: 8px 0 32px;

    border-bottom: 1px solid var(--ink);
}

.admin__header-left {
    max-width: 640px;
}

.admin__eyebrow {
    margin: 0 0 14px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.admin__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(36px, 5vw, 52px);
    font-weight: 600;
    line-height: 1;
    letter-spacing: -0.04em;

    color: var(--ink);
}

.admin__title em {
    font-weight: 400;
}

.admin__desc {
    margin: 16px 0 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.75;

    max-width: 520px;
}

.admin__header-right {
    display: flex;
    align-items: flex-end;
}

/* ----- EXPORT BUTTON (prominent) ----- */

.admin__export {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    min-height: 48px;

    padding: 0 22px;

    background: var(--ink);
    color: var(--paper);

    border: 0;

    text-decoration: none;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.04em;

    white-space: nowrap;

    box-shadow: 0 6px 18px rgba(24, 23, 22, 0.08);

    transition:
        background 180ms ease,
        transform 180ms ease,
        box-shadow 180ms ease;
}

.admin__export:hover {
    background: #302e2b;
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(24, 23, 22, 0.15);
}

.admin__export-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 18px;
    height: 18px;
}

.admin__export-icon svg {
    width: 100%;
    height: 100%;
}

.admin__export-label {
    white-space: nowrap;
}

/* =========================================================
   META ROW
========================================================= */

.admin__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 20px 0 28px;

    border-bottom: 1px solid var(--line);
}

.admin__meta-step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.admin__meta-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.admin__meta-year {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   STATS GRID
========================================================= */

.stats {
    margin-top: 36px;
}

.stats__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));

    gap: 0;

    border-top: 1px solid var(--ink);
    border-left: 1px solid var(--line);
}

.stat-card {
    display: flex;
    flex-direction: column;

    gap: 10px;

    padding: 24px 22px;

    background: var(--white);

    border-right: 1px solid var(--line);
    border-bottom: 1px solid var(--line);

    transition: background 180ms ease;
}

.stat-card:hover {
    background: #fffefb;
}

.stat-card__label {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.stat-card__number {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 40px;
    font-weight: 500;
    line-height: 1;
    letter-spacing: -0.04em;
}

.stat-card__sub {
    color: var(--ink-faint);

    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.02em;
}

/* ----- SUDAH MEMILIH — FULL ACCENT (tidak abu-abu) ----- */

.stat-card--accent {
    background: linear-gradient(
        135deg,
        rgba(75, 31, 99, 0.035),
        rgba(124, 58, 158, 0.02)
    );
}

.stat-card--accent:hover {
    background: linear-gradient(
        135deg,
        rgba(75, 31, 99, 0.06),
        rgba(124, 58, 158, 0.03)
    );
}

.stat-card__label--accent {
    color: var(--accent) !important;
    font-weight: 800;
}

.stat-card__number--accent {
    color: var(--accent) !important;
}

.stat-card__sub--accent {
    color: var(--accent) !important;
    opacity: 0.7;
}

/* ----- PARTISIPASI — GREEN ----- */

.stat-card__label--green {
    color: var(--green) !important;
    font-weight: 800;
}

.stat-card__number--green {
    color: var(--green) !important;
}

.stat-card__bar {
    margin-top: 6px;

    width: 100%;
    height: 3px;

    background: var(--paper-dark);

    overflow: hidden;
}

.stat-card__bar-fill {
    height: 100%;

    background: var(--green);

    transition: width 800ms cubic-bezier(0.22, 1, 0.36, 1);
}

/* =========================================================
   RESULTS
========================================================= */

.results {
    margin-top: 36px;

    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 24px;
}

.results__card {
    background: var(--white);

    border: 1px solid var(--line);
    border-top: 2px solid var(--ink);

    padding: 28px 28px 24px;
}

.results__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 20px;

    padding-bottom: 20px;
    margin-bottom: 22px;

    border-bottom: 1px solid var(--line);
}

.results__eyebrow {
    margin: 0 0 6px;

    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.results__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 24px;
    font-weight: 600;
    line-height: 1.1;
    letter-spacing: -0.03em;

    color: var(--ink);
}

.results__title em {
    font-weight: 400;
}

.results__total {
    padding: 5px 11px;

    background: var(--paper);

    border: 1px solid var(--line);

    color: var(--ink);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;

    white-space: nowrap;
}

.results__empty {
    display: flex;
    flex-direction: column;
    align-items: center;

    padding: 36px 20px;

    color: var(--ink-muted);

    text-align: center;
}

.results__empty-icon {
    font-size: 1.8rem;
    margin-bottom: 10px;

    opacity: 0.6;
}

.results__empty p {
    margin: 0;

    font-size: 12px;
    font-weight: 500;
}

.results__list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* =========================================================
   RESULT BAR
========================================================= */

.result-bar {
    display: flex;
    flex-direction: column;
    gap: 9px;
}

.result-bar__header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;

    gap: 12px;
}

.result-bar__name {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 16px;
    font-weight: 500;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.result-bar__meta {
    display: flex;
    align-items: baseline;
    gap: 10px;

    white-space: nowrap;
}

.result-bar__total {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 15px;
    font-weight: 600;
}

.result-bar__percent {
    color: var(--ink-faint);

    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.04em;
}

.result-bar__track {
    width: 100%;
    height: 5px;

    background: var(--paper-dark);

    overflow: hidden;
}

.result-bar__fill {
    height: 100%;

    background: linear-gradient(90deg, var(--accent), var(--accent-soft));

    transition: width 800ms cubic-bezier(0.22, 1, 0.36, 1);
}

/* =========================================================
   NOTE
========================================================= */

.admin__note {
    margin: 36px 0 0;

    padding-top: 22px;

    border-top: 1px solid var(--line);

    color: var(--ink-faint);

    font-size: 10px;
    line-height: 1.7;

    text-align: center;

    max-width: 600px;

    margin-left: auto;
    margin-right: auto;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1024px) {
    .admin {
        padding: 28px 32px 56px;
    }

    .stats__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .results {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    .admin {
        padding: 24px 24px 48px;
    }

    .admin__header {
        grid-template-columns: 1fr;

        gap: 20px;

        padding: 4px 0 24px;
    }

    .admin__header-right {
        align-items: stretch;
    }

    .admin__export {
        width: 100%;
        justify-content: center;
    }

    .admin__title {
        font-size: clamp(30px, 9vw, 44px);
    }

    .admin__desc {
        font-size: 12px;
    }

    .admin__meta {
        padding: 14px 0 20px;
    }

    .admin__meta-year {
        display: none;
    }

    .stats {
        margin-top: 24px;
    }

    .stat-card {
        padding: 18px 16px;
        gap: 8px;
    }

    .stat-card__number {
        font-size: 32px;
    }

    .results {
        margin-top: 24px;
        gap: 16px;
    }

    .results__card {
        padding: 22px 20px 20px;
    }

    .results__title {
        font-size: 20px;
    }

    .result-bar__name {
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .admin {
        padding: 20px 18px 40px;
    }

    .stats__grid {
        grid-template-columns: 1fr 1fr;
    }

    .stat-card {
        padding: 16px 14px;
    }

    .stat-card__number {
        font-size: 28px;
    }

    .stat-card__sub {
        font-size: 9px;
    }

    .results__card {
        padding: 20px 18px 18px;
    }

    .results__head {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .result-bar__header {
        flex-wrap: wrap;
    }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .admin__export,
    .stat-card__bar-fill,
    .result-bar__fill {
        transition: none !important;
    }

    .admin__export:hover {
        transform: none;
    }
}
</style>
