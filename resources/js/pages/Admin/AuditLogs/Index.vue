<script setup>
import { computed } from 'vue';

const props = defineProps({
    logs: Object,
});

// === FORMAT TANGGAL ===
function formatDate(dateString) {
    if (!dateString) return '—';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatTime(dateString) {
    if (!dateString) return '—';
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
}

// === LABEL AKSI (uppercase -> readable) ===
function actionLabel(action) {
    if (!action) return '—';

    const map = {
        'vote.opened': 'Buka Voting',
        'vote.closed': 'Tutup Voting',
        'result.published': 'Publikasi Hasil',
        'candidate.created': 'Tambah Kandidat',
        'candidate.updated': 'Edit Kandidat',
        'candidate.deleted': 'Hapus Kandidat',
        'voter.imported': 'Import Pemilih',
        'voter.toggled': 'Ubah Status Pemilih',
        'voter.deleted': 'Hapus Pemilih',
        'announcement.created': 'Tambah Pengumuman',
        'announcement.updated': 'Edit Pengumuman',
        'announcement.deleted': 'Hapus Pengumuman',
        'user.login': 'Login',
        'user.logout': 'Logout',
    };

    if (map[action]) return map[action];

    // Fallback: ubah snake_case/dot notation jadi readable
    return action
        .replace(/[._]/g, ' ')
        .replace(/\b\w/g, (c) => c.toUpperCase());
}

// === KATEGORI AKSI (untuk warna) ===
function actionCategory(action) {
    if (!action) return 'default';

    if (action.startsWith('vote.')) return 'vote';
    if (action.startsWith('result.')) return 'result';
    if (action.startsWith('candidate.')) return 'candidate';
    if (action.startsWith('voter.')) return 'voter';
    if (action.startsWith('announcement.')) return 'announcement';
    if (action.startsWith('user.')) return 'user';

    return 'default';
}

// === STATISTIK ===
const totalLogs = computed(() => props.logs?.total ?? 0);
const currentPage = computed(() => props.logs?.current_page ?? 1);
const lastPage = computed(() => props.logs?.last_page ?? 1);
</script>

<template>
    <div class="audit">
        <!-- =====================================================
         HEADER
    ====================================================== -->

        <header class="audit__header">
            <div class="audit__header-left">
                <p class="audit__eyebrow">Panel Admin</p>

                <h1 class="audit__title">Audit <em>Log.</em></h1>

                <p class="audit__desc">
                    Catatan lengkap seluruh aktivitas penting yang dilakukan
                    oleh panitia dan administrator sistem.
                </p>
            </div>
        </header>

        <!-- =====================================================
         META ROW
    ====================================================== -->

        <div class="audit__meta">
            <span class="audit__meta-step">
                <span class="audit__meta-number">06</span>
                Audit Log
            </span>
            <span class="audit__meta-count"> {{ totalLogs }} catatan </span>
        </div>

        <!-- =====================================================
         TABLE SECTION — CARD
    ====================================================== -->

        <section class="table">
            <div class="table__head">
                <div>
                    <p class="table__eyebrow">Riwayat Aktivitas</p>
                    <h2 class="table__title">Log <em>Sistem</em></h2>
                </div>

                <div class="table__stats">
                    <div class="table__stat">
                        <span class="table__stat-label">Halaman</span>
                        <span class="table__stat-value">
                            {{ currentPage }} / {{ lastPage }}
                        </span>
                    </div>
                    <div class="table__stat">
                        <span class="table__stat-label">Total</span>
                        <span
                            class="table__stat-value table__stat-value--accent"
                        >
                            {{ totalLogs }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- EMPTY STATE -->
            <div v-if="!logs.data?.length" class="table__empty">
                <span class="table__empty-icon">📋</span>
                <h3 class="table__empty-title">Belum ada catatan</h3>
                <p class="table__empty-text">
                    Aktivitas admin akan muncul di sini setelah sistem
                    digunakan.
                </p>
            </div>

            <!-- TABLE SCROLL -->
            <div v-else class="table__scroll">
                <table class="table__grid">
                    <thead>
                        <tr>
                            <th class="table__th table__th--num">#</th>
                            <th class="table__th table__th--waktu">Waktu</th>
                            <th class="table__th table__th--user">User</th>
                            <th class="table__th table__th--aksi">Aksi</th>
                            <th class="table__th table__th--ip">IP Address</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(log, index) in logs.data"
                            :key="log.id"
                            class="table__row"
                        >
                            <td class="table__td table__td--num">
                                {{
                                    String(
                                        (currentPage - 1) *
                                            (logs.per_page ?? 15) +
                                            index +
                                            1,
                                    ).padStart(3, '0')
                                }}
                            </td>

                            <td class="table__td table__td--waktu">
                                <span class="audit-date">
                                    <span class="audit-date__day">
                                        {{ formatDate(log.created_at) }}
                                    </span>
                                    <span class="audit-date__time">
                                        {{ formatTime(log.created_at) }}
                                    </span>
                                </span>
                            </td>

                            <td class="table__td table__td--user">
                                {{ log.user?.name ?? '—' }}
                            </td>

                            <td class="table__td table__td--aksi">
                                <span
                                    class="action-pill"
                                    :class="`action-pill--${actionCategory(log.action)}`"
                                >
                                    <span class="action-pill__dot"></span>
                                    {{ actionLabel(log.action) }}
                                </span>
                            </td>

                            <td class="table__td table__td--ip">
                                {{ log.ip_address ?? '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div v-if="lastPage > 1" class="pagination">
                <span class="pagination__info">
                    Halaman {{ currentPage }} dari {{ lastPage }}
                </span>

                <div class="pagination__links">
                    <component
                        :is="link.url ? 'a' : 'span'"
                        v-for="(link, i) in logs.links"
                        :key="i"
                        :href="link.url || undefined"
                        class="pagination__link"
                        :class="{
                            'pagination__link--active': link.active,
                            'pagination__link--disabled': !link.url,
                        }"
                        v-html="link.label"
                    ></component>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
/* =========================================================
   ROOT — TEMA EDITORIAL
========================================================= */

.audit {
    --ink: #181716;
    --ink-muted: #77736d;
    --ink-faint: #a09b93;
    --accent: #4b1f63;
    --accent-soft: #7c3a9e;
    --green: #2d8a52;
    --red: #a74a3d;
    --blue: #3b6ea5;
    --amber: #a57a2d;
    --paper: #f6f3ee;
    --paper-dark: #ebe7df;
    --white: #fffdf9;
    --line: #d9d4cb;

    width: 100%;
    max-width: 1180px;
    min-width: 0;

    margin-left: auto;
    margin-right: auto;

    padding: 32px 40px 64px;

    color: var(--ink);

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    -webkit-font-smoothing: antialiased;
}

.audit *,
.audit *::before,
.audit *::after {
    box-sizing: border-box;
}

.audit button,
.audit a {
    -webkit-tap-highlight-color: transparent;
}

/* =========================================================
   HEADER
========================================================= */

.audit__header {
    display: grid;
    grid-template-columns: minmax(0, 1fr);

    gap: 32px;

    padding: 8px 0 32px;

    border-bottom: 1px solid var(--ink);
}

.audit__header-left {
    max-width: 640px;
}

.audit__eyebrow {
    margin: 0 0 14px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.audit__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(36px, 5vw, 52px);
    font-weight: 600;
    line-height: 1;
    letter-spacing: -0.04em;

    color: var(--ink);
}

.audit__title em {
    font-weight: 400;
}

.audit__desc {
    margin: 16px 0 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.75;

    max-width: 540px;
}

/* =========================================================
   META ROW
========================================================= */

.audit__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 20px 0 28px;

    border-bottom: 1px solid var(--line);
}

.audit__meta-step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.audit__meta-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.audit__meta-count {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   TABLE SECTION — CARD
========================================================= */

.table {
    display: block;
    width: 100%;
    min-width: 0;

    margin-top: 32px;

    padding: 28px 0 0;

    background: var(--white);

    border: 1px solid var(--line);
    border-top: 2px solid var(--ink);
}

.table__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 20px;

    padding: 0 28px 20px;
    margin-bottom: 22px;

    border-bottom: 1px solid var(--line);

    flex-wrap: wrap;
}

.table__eyebrow {
    margin: 0 0 6px;

    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.table__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 24px;
    font-weight: 600;
    line-height: 1.1;
    letter-spacing: -0.03em;

    color: var(--ink);
}

.table__title em {
    font-weight: 400;
}

.table__stats {
    display: flex;
    gap: 20px;

    flex-wrap: wrap;
}

.table__stat {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.table__stat-label {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.table__stat-value {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 15px;
    font-weight: 600;
}

.table__stat-value--accent {
    color: var(--accent);
}

/* =========================================================
   TABLE EMPTY
========================================================= */

.table__empty {
    display: flex;
    flex-direction: column;
    align-items: center;

    margin: 0 28px 28px;
    padding: 60px 24px;

    background: var(--paper);

    border: 1px solid var(--line);

    text-align: center;
}

.table__empty-icon {
    font-size: 2.6rem;
    margin-bottom: 14px;
}

.table__empty-title {
    margin: 0 0 6px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
    font-weight: 600;
    letter-spacing: -0.02em;
}

.table__empty-text {
    margin: 0;

    color: var(--ink-muted);

    font-size: 12px;
    line-height: 1.6;

    max-width: 340px;
}

/* =========================================================
   TABLE SCROLL — versi bersih, tanpa trik gradient
========================================================= */

.table__scroll {
    display: block;
    width: 100%;
    min-width: 0;

    overflow-x: auto;
    overflow-y: hidden;

    -webkit-overflow-scrolling: touch;

    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);

    background: var(--white);

    scrollbar-width: thin;
    scrollbar-color: var(--line) transparent;
}

.table__scroll::-webkit-scrollbar {
    height: 8px;
}

.table__scroll::-webkit-scrollbar-track {
    background: var(--paper);
}

.table__scroll::-webkit-scrollbar-thumb {
    background: var(--line);
    border-radius: 4px;
}

.table__scroll::-webkit-scrollbar-thumb:hover {
    background: var(--ink-faint);
}

.table__grid {
    width: 100%;

    min-width: 760px;

    border-collapse: collapse;

    font-size: 12px;
}

.table__th,
.table__td {
    white-space: nowrap;
}

/* ----- KOLOM LEBAR FLEKSIBEL ----- */

.table__th--num,
.table__td--num {
    width: 52px;
    text-align: center;
}

.table__th--waktu {
    width: 140px;
}

.table__th--user,
.table__td--user {
    width: 160px;
}

.table__th--aksi,
.table__td--aksi {
    width: auto;
    min-width: 200px;
}

.table__th--ip {
    width: 130px;
}

/* =========================================================
   TABLE HEADER
========================================================= */

.table__th {
    padding: 14px 16px;

    border-bottom: 1px solid var(--ink);

    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;

    text-align: left;

    background: var(--paper);
}

.table__th--num {
    padding-left: 28px;
}

.table__th:last-child {
    padding-right: 28px;
}

/* =========================================================
   TABLE ROWS
========================================================= */

.table__row {
    border-bottom: 1px solid var(--line);

    transition: background 150ms ease;
}

.table__row:last-child {
    border-bottom: 0;
}

.table__row:hover {
    background: rgba(75, 31, 99, 0.02);
}

.table__td {
    padding: 14px 16px;

    color: var(--ink);

    vertical-align: middle;
}

.table__td--num {
    padding-left: 28px;

    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 13px;
    font-weight: 500;

    text-align: center;
}

.table__td:last-child {
    padding-right: 28px;
}

/* ----- WAKTU ----- */

.audit-date {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.audit-date__day {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 13px;
    font-weight: 500;
    letter-spacing: -0.01em;
}

.audit-date__time {
    color: var(--ink-faint);

    font-family: 'JetBrains Mono', 'Menlo', monospace;

    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.02em;
}

/* ----- USER ----- */

.table__td--user {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 14px;
    font-weight: 500;
    letter-spacing: -0.01em;
}

/* ----- IP ----- */

.table__td--ip {
    color: var(--ink-muted);

    font-family: 'JetBrains Mono', 'Menlo', monospace;

    font-size: 11px;
    font-weight: 500;
}

/* =========================================================
   ACTION PILL
========================================================= */

.action-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 5px 12px;

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;

    white-space: nowrap;
}

.action-pill__dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    flex: 0 0 auto;
}

/* ----- VARIANTS ----- */

.action-pill--vote {
    background: rgba(45, 138, 82, 0.08);
    color: var(--green);
}

.action-pill--vote .action-pill__dot {
    background: var(--green);
}

.action-pill--result {
    background: rgba(75, 31, 99, 0.08);
    color: var(--accent);
}

.action-pill--result .action-pill__dot {
    background: var(--accent);
}

.action-pill--candidate {
    background: rgba(59, 110, 165, 0.08);
    color: var(--blue);
}

.action-pill--candidate .action-pill__dot {
    background: var(--blue);
}

.action-pill--voter {
    background: rgba(165, 122, 45, 0.08);
    color: var(--amber);
}

.action-pill--voter .action-pill__dot {
    background: var(--amber);
}

.action-pill--announcement {
    background: rgba(124, 58, 158, 0.08);
    color: var(--accent-soft);
}

.action-pill--announcement .action-pill__dot {
    background: var(--accent-soft);
}

.action-pill--user {
    background: var(--paper);
    color: var(--ink-muted);
}

.action-pill--user .action-pill__dot {
    background: var(--ink-muted);
}

.action-pill--default {
    background: var(--paper);
    color: var(--ink-muted);
}

.action-pill--default .action-pill__dot {
    background: var(--ink-faint);
}

/* =========================================================
   PAGINATION
========================================================= */

.pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 20px 28px 26px;

    border-top: 1px solid var(--line);

    flex-wrap: wrap;
}

.pagination__info {
    color: var(--ink-faint);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.pagination__links {
    display: flex;
    gap: 4px;

    flex-wrap: wrap;
}

.pagination__link {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 32px;
    height: 32px;

    padding: 0 8px;

    border: 1px solid var(--line);

    color: var(--ink-muted);

    font-size: 11px;
    font-weight: 600;

    text-decoration: none;

    transition:
        background 150ms ease,
        border-color 150ms ease,
        color 150ms ease;
}

.pagination__link:hover:not(.pagination__link--disabled):not(
        .pagination__link--active
    ) {
    border-color: var(--ink);
    color: var(--ink);
    background: var(--paper);
}

.pagination__link--active {
    background: var(--ink);
    border-color: var(--ink);
    color: var(--paper);
}

.pagination__link--disabled {
    opacity: 0.35;
    cursor: not-allowed;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1024px) {
    .audit {
        padding: 28px 32px 56px;
    }
}

@media (max-width: 760px) {
    .audit {
        padding: 24px 24px 48px;
    }

    .audit__title {
        font-size: clamp(30px, 9vw, 44px);
    }

    .audit__desc {
        font-size: 12px;
    }

    .audit__meta {
        padding: 14px 0 20px;
    }

    .audit__meta-count {
        display: none;
    }

    .table {
        padding: 22px 0 0;
        margin-top: 24px;
    }

    .table__title {
        font-size: 20px;
    }

    .table__head {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        padding: 0 20px 16px;
        margin-bottom: 18px;
    }

    .table__stats {
        gap: 16px;
    }

    .table__stat-value {
        font-size: 14px;
    }

    /* Mobile: paksa lebar minimum tabel supaya bisa digeser */
    .table__grid {
        min-width: 720px;
    }

    .table__th--num,
    .table__td--num {
        padding-left: 20px;
    }

    .table__th:last-child,
    .table__td:last-child {
        padding-right: 20px;
    }

    .table__empty {
        margin: 0 20px 22px;
        padding: 44px 20px;
    }

    .pagination {
        padding: 16px 20px 22px;
        gap: 14px;
    }
}

@media (max-width: 480px) {
    .audit {
        padding: 20px 18px 40px;
    }

    .audit__title {
        font-size: 30px;
    }

    .audit__eyebrow {
        font-size: 9px;
    }

    .table {
        padding: 20px 0 0;
    }

    .table__head {
        padding: 0 16px 14px;
        margin-bottom: 16px;
    }

    .table__grid {
        min-width: 640px;
        font-size: 11px;
    }

    .table__th,
    .table__td {
        padding: 12px 12px;
    }

    .table__th--num,
    .table__td--num {
        padding-left: 16px;
    }

    .table__th:last-child,
    .table__td:last-child {
        padding-right: 16px;
    }

    .table__td--user {
        font-size: 13px;
    }

    .audit-date__day {
        font-size: 12px;
    }

    .audit-date__time {
        font-size: 9px;
    }

    .action-pill {
        padding: 4px 9px;
        font-size: 9px;
    }

    .table__empty {
        margin: 0 16px 18px;
        padding: 36px 16px;
    }

    .pagination {
        padding: 14px 16px 18px;
    }

    .pagination__link {
        min-width: 30px;
        height: 30px;
        font-size: 10px;
    }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .table__row,
    .pagination__link {
        transition: none !important;
    }
}
</style>
