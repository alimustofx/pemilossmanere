<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    voters: Object,
    filters: Object,
});

const fileInput = ref(null);

const form = useForm({
    file: null,
});

function handleFile(e) {
    form.file = e.target.files[0];
}

function submitImport() {
    form.post('/admin/voters/import', {
        onSuccess: () => {
            form.reset();
            if (fileInput.value) fileInput.value.value = '';
        },
    });
}

function toggleStatus(voter) {
    form.patch(`/admin/voters/${voter.id}/toggle`);
}

function deleteVoter(voter) {
    if (confirm(`Hapus pemilih ${voter.name}?`)) {
        form.delete(`/admin/voters/${voter.id}`);
    }
}

// === COMPUTED ===
const totalVoters = computed(() => props.voters?.total ?? 0);

const osisVoted = computed(() => {
    if (!props.voters?.data) return 0;
    return props.voters.data.filter((v) => v.osis_voted_at).length;
});

const mpkVoted = computed(() => {
    if (!props.voters?.data) return 0;
    return props.voters.data.filter((v) => v.mpk_voted_at).length;
});

const currentPage = computed(() => props.voters?.current_page ?? 1);
const lastPage = computed(() => props.voters?.last_page ?? 1);

// === SEARCH & FILTER (server-side, cocok untuk ribuan data) ===
const searchQuery = ref(props.filters?.search ?? '');
const perPage = ref(props.filters?.per_page ?? 20);

let searchTimeout = null;

function applyFilters() {
    router.get(
        '/admin/voters',
        {
            search: searchQuery.value || undefined,
            per_page: perPage.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 400);
});

watch(perPage, () => {
    applyFilters();
});

function clearSearch() {
    searchQuery.value = '';
}
</script>

<template>
    <div class="voters">
        <!-- =====================================================
         HEADER
    ====================================================== -->

        <header class="voters__header">
            <div class="voters__header-left">
                <p class="voters__eyebrow">Panel Admin</p>

                <h1 class="voters__title">Manajemen <em>Pemilih.</em></h1>

                <p class="voters__desc">
                    Kelola daftar pemilih aktif — impor massal, pantau status
                    pemungutan suara OSIS dan MPK, serta ekspor data kapan saja.
                </p>
            </div>

            <div class="voters__header-right">
                <a href="/admin/voters/export" class="voters__export">
                    <span class="voters__export-icon">
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
                    <span class="voters__export-label">Export CSV</span>
                </a>
            </div>
        </header>

        <!-- =====================================================
         META ROW
    ====================================================== -->

        <div class="voters__meta">
            <span class="voters__meta-step">
                <span class="voters__meta-number">03</span>
                Daftar Pemilih
            </span>
            <span class="voters__meta-count">
                {{ totalVoters }} pemilih terdaftar
            </span>
        </div>

        <!-- =====================================================
         FLASH MESSAGE
    ====================================================== -->

        <div v-if="$page.props.flash?.success" class="voters__flash">
            <span class="voters__flash-dot"></span>
            {{ $page.props.flash.success }}
        </div>

        <!-- =====================================================
         IMPORT SECTION
    ====================================================== -->

        <section class="import">
            <div class="import__head">
                <div>
                    <p class="import__eyebrow">Import Massal</p>
                    <h2 class="import__title">Unggah <em>CSV</em> Pemilih</h2>
                </div>

                <span class="import__badge">Format CSV</span>
            </div>

            <div class="import__hint">
                <span class="import__hint-label">Format kolom</span>
                <code class="import__hint-code">
                    nis, nama, kelas, tanggal_lahir (YYYY-MM-DD)
                </code>
                <span class="import__hint-note">
                    Baris pertama = header. Pastikan encoding UTF-8.
                </span>
            </div>

            <form class="import__form" @submit.prevent="submitImport">
                <label class="import__drop">
                    <input
                        ref="fileInput"
                        type="file"
                        accept=".csv"
                        class="import__input"
                        @change="handleFile"
                    />

                    <span class="import__drop-icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                            />
                            <path d="M17 8l-5-5-5 5" />
                            <path d="M12 3v12" />
                        </svg>
                    </span>

                    <span class="import__drop-label">
                        {{
                            form.file
                                ? form.file.name
                                : 'Pilih file CSV atau klik untuk memilih'
                        }}
                    </span>
                </label>

                <button
                    type="submit"
                    class="import__submit"
                    :disabled="form.processing || !form.file"
                >
                    <span>
                        {{
                            form.processing ? 'Mengimpor...' : 'Upload & Import'
                        }}
                    </span>
                    <span class="import__submit-icon">↗</span>
                </button>
            </form>

            <p v-if="form.errors.file" class="import__error">
                {{ form.errors.file }}
            </p>
        </section>

        <!-- =====================================================
         TABLE SECTION — EDGE-TO-EDGE, SEJAJAR DENGAN IMPORT
    ====================================================== -->

        <section class="table">
            <div class="table__head">
                <div>
                    <p class="table__eyebrow">Data Realtime</p>
                    <h2 class="table__title">Daftar <em>Pemilih</em></h2>
                </div>

                <div class="table__stats">
                    <div class="table__stat">
                        <span class="table__stat-label">Halaman</span>
                        <span class="table__stat-value">
                            {{ currentPage }} / {{ lastPage }}
                        </span>
                    </div>
                    <div class="table__stat">
                        <span class="table__stat-label">OSIS</span>
                        <span
                            class="table__stat-value table__stat-value--accent"
                        >
                            {{ osisVoted }} / {{ totalVoters }}
                        </span>
                    </div>
                    <div class="table__stat">
                        <span class="table__stat-label">MPK</span>
                        <span
                            class="table__stat-value table__stat-value--accent"
                        >
                            {{ mpkVoted }} / {{ totalVoters }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- FILTER BAR: search + per-page -->
            <div class="filter-bar">
                <div class="filter-bar__search">
                    <svg
                        class="filter-bar__search-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle cx="11" cy="11" r="7" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>

                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama atau NIS..."
                        class="filter-bar__search-input"
                    />

                    <button
                        v-if="searchQuery"
                        type="button"
                        class="filter-bar__search-clear"
                        aria-label="Bersihkan pencarian"
                        @click="clearSearch"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        >
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>

                <div class="filter-bar__perpage">
                    <span class="filter-bar__perpage-label">Tampilkan</span>
                    <select
                        v-model.number="perPage"
                        class="filter-bar__perpage-select"
                    >
                        <option :value="10">10</option>
                        <option :value="20">20</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                    <span class="filter-bar__perpage-label">per halaman</span>
                </div>
            </div>

            <!-- EMPTY STATE -->
            <div v-if="!voters.data?.length" class="table__empty">
                <span class="table__empty-icon">📭</span>
                <h3 class="table__empty-title">
                    {{ searchQuery ? 'Tidak ditemukan' : 'Belum ada pemilih' }}
                </h3>
                <p class="table__empty-text">
                    {{
                        searchQuery
                            ? `Tidak ada pemilih yang cocok dengan pencarian "${searchQuery}".`
                            : 'Unggah file CSV di atas untuk mulai menambahkan data pemilih.'
                    }}
                </p>
            </div>

            <!-- TABLE SCROLL — full-bleed, edge-to-edge, bisa digeser di mobile -->
            <div v-else class="table__scroll">
                <table class="table__grid">
                    <thead>
                        <tr>
                            <th class="table__th table__th--num">#</th>
                            <th class="table__th">NIS</th>
                            <th class="table__th">Nama</th>
                            <th class="table__th">Kelas</th>
                            <th class="table__th table__th--center">OSIS</th>
                            <th class="table__th table__th--center">MPK</th>
                            <th class="table__th">Status</th>
                            <th class="table__th table__th--right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(voter, index) in voters.data"
                            :key="voter.id"
                            class="table__row"
                        >
                            <td class="table__td table__td--num">
                                {{
                                    String(
                                        (currentPage - 1) *
                                            (voters.per_page ?? 15) +
                                            index +
                                            1,
                                    ).padStart(3, '0')
                                }}
                            </td>
                            <td class="table__td table__td--mono">
                                {{ voter.nis }}
                            </td>
                            <td class="table__td table__td--name">
                                {{ voter.name }}
                            </td>
                            <td class="table__td">
                                {{ voter.class_name }}
                            </td>
                            <td class="table__td table__td--center">
                                <span
                                    class="vote-pill"
                                    :class="
                                        voter.osis_voted_at
                                            ? 'vote-pill--done'
                                            : 'vote-pill--pending'
                                    "
                                >
                                    {{ voter.osis_voted_at ? '✓' : '—' }}
                                </span>
                            </td>
                            <td class="table__td table__td--center">
                                <span
                                    class="vote-pill"
                                    :class="
                                        voter.mpk_voted_at
                                            ? 'vote-pill--done'
                                            : 'vote-pill--pending'
                                    "
                                >
                                    {{ voter.mpk_voted_at ? '✓' : '—' }}
                                </span>
                            </td>
                            <td class="table__td">
                                <span
                                    class="status-pill"
                                    :class="
                                        voter.status
                                            ? 'status-pill--active'
                                            : 'status-pill--inactive'
                                    "
                                >
                                    <span class="status-pill__dot"></span>
                                    {{ voter.status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="table__td table__td--right">
                                <div class="table__actions">
                                    <button
                                        type="button"
                                        class="table__btn table__btn--toggle"
                                        @click="toggleStatus(voter)"
                                    >
                                        {{
                                            voter.status
                                                ? 'Nonaktifkan'
                                                : 'Aktifkan'
                                        }}
                                    </button>

                                    <button
                                        type="button"
                                        class="table__btn table__btn--delete"
                                        @click="deleteVoter(voter)"
                                    >
                                        Hapus
                                    </button>
                                </div>
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
                        v-for="(link, i) in voters.links"
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

.voters {
    --ink: #181716;
    --ink-muted: #77736d;
    --ink-faint: #a09b93;
    --accent: #4b1f63;
    --accent-soft: #7c3a9e;
    --green: #2d8a52;
    --red: #a74a3d;
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

.voters *,
.voters *::before,
.voters *::after {
    box-sizing: border-box;
}

.voters button,
.voters a {
    -webkit-tap-highlight-color: transparent;
}

/* =========================================================
   HEADER
========================================================= */

.voters__header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;

    gap: 32px;

    padding: 8px 0 32px;

    border-bottom: 1px solid var(--ink);
}

.voters__header-left {
    max-width: 640px;
}

.voters__eyebrow {
    margin: 0 0 14px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.voters__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(36px, 5vw, 52px);
    font-weight: 600;
    line-height: 1;
    letter-spacing: -0.04em;

    color: var(--ink);
}

.voters__title em {
    font-weight: 400;
}

.voters__desc {
    margin: 16px 0 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.75;

    max-width: 540px;
}

.voters__header-right {
    display: flex;
    align-items: flex-end;
}

/* ----- EXPORT ----- */

.voters__export {
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

.voters__export:hover {
    background: #302e2b;
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(24, 23, 22, 0.15);
}

.voters__export-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 18px;
    height: 18px;
}

.voters__export-icon svg {
    width: 100%;
    height: 100%;
}

.voters__export-label {
    white-space: nowrap;
}

/* =========================================================
   META ROW
========================================================= */

.voters__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 20px 0 28px;

    border-bottom: 1px solid var(--line);
}

.voters__meta-step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.voters__meta-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.voters__meta-count {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   FLASH
========================================================= */

.voters__flash {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-top: 24px;
    padding: 14px 18px;

    border: 1px solid rgba(45, 138, 82, 0.22);

    background: rgba(45, 138, 82, 0.045);

    color: var(--green);

    font-size: 12px;
    font-weight: 600;

    line-height: 1.5;
}

.voters__flash-dot {
    width: 7px;
    height: 7px;

    flex: 0 0 auto;

    border-radius: 50%;

    background: var(--green);
}

/* =========================================================
   IMPORT SECTION
========================================================= */

.import {
    margin-top: 36px;

    padding: 28px 28px 26px;

    background: var(--white);

    border: 1px solid var(--line);
    border-top: 2px solid var(--ink);
}

.import__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 20px;

    padding-bottom: 20px;
    margin-bottom: 22px;

    border-bottom: 1px solid var(--line);
}

.import__eyebrow {
    margin: 0 0 6px;

    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.import__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 24px;
    font-weight: 600;
    line-height: 1.1;
    letter-spacing: -0.03em;

    color: var(--ink);
}

.import__title em {
    font-weight: 400;
}

.import__badge {
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

.import__hint {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;

    margin-bottom: 20px;

    padding: 12px 14px;

    border: 1px solid var(--line);

    background: var(--paper);
}

.import__hint-label {
    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.import__hint-code {
    padding: 3px 8px;

    background: var(--ink);
    color: var(--paper);

    font-family: 'JetBrains Mono', 'Menlo', monospace;

    font-size: 11px;
    font-weight: 500;

    letter-spacing: 0.02em;
}

.import__hint-note {
    color: var(--ink-faint);

    font-size: 10px;
    font-style: italic;
}

.import__form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;

    gap: 12px;

    align-items: stretch;
}

/* ----- DROP ZONE / FILE INPUT ----- */

.import__drop {
    display: flex;
    align-items: center;
    gap: 14px;

    min-height: 60px;

    padding: 0 20px;

    border: 1px dashed var(--line);

    background: var(--white);

    cursor: pointer;

    transition:
        border-color 180ms ease,
        background 180ms ease;
}

.import__drop:hover {
    border-color: var(--accent);
    background: rgba(75, 31, 99, 0.02);
}

.import__input {
    position: absolute;

    width: 1px;
    height: 1px;

    opacity: 0;

    pointer-events: none;
}

.import__drop-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 24px;
    height: 24px;

    color: var(--accent);
}

.import__drop-icon svg {
    width: 100%;
    height: 100%;
}

.import__drop-label {
    color: var(--ink-muted);

    font-size: 12px;
    font-weight: 500;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.import__submit {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;

    min-height: 60px;

    padding: 0 24px;

    border: 0;

    background: var(--ink);
    color: var(--paper);

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.04em;

    white-space: nowrap;

    transition:
        background 180ms ease,
        transform 180ms ease;
}

.import__submit:hover:not(:disabled) {
    background: #302e2b;
    transform: translateY(-2px);
}

.import__submit:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
}

.import__submit-icon {
    font-size: 17px;
}

.import__error {
    margin: 12px 0 0;

    color: var(--red);

    font-size: 11px;
    font-weight: 500;
}

/* =========================================================
   TABLE SECTION — EDGE-TO-EDGE, SEJAJAR DENGAN IMPORT
========================================================= */

.table {
    display: block;
    width: 100%;
    min-width: 0;

    margin-top: 24px;

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
   FILTER BAR — search + per-page (baru, mengikuti tema)
========================================================= */

.filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 16px;

    padding: 0 28px 20px;
    margin-bottom: 4px;

    flex-wrap: wrap;
}

.filter-bar__search {
    position: relative;

    flex: 1;
    min-width: 220px;
    max-width: 380px;
}

.filter-bar__search-icon {
    position: absolute;

    left: 13px;
    top: 50%;

    width: 15px;
    height: 15px;

    color: var(--ink-faint);

    transform: translateY(-50%);

    pointer-events: none;
}

.filter-bar__search-input {
    width: 100%;
    min-height: 38px;

    padding: 0 34px 0 36px;

    border: 1px solid var(--line);

    background: var(--paper);
    color: var(--ink);

    outline: none;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 12px;
    font-weight: 500;

    transition:
        border-color 180ms ease,
        background 180ms ease;
}

.filter-bar__search-input::placeholder {
    color: var(--ink-faint);
}

.filter-bar__search-input:focus {
    border-color: var(--ink);
    background: var(--white);
}

.filter-bar__search-clear {
    position: absolute;

    right: 8px;
    top: 50%;

    width: 20px;
    height: 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;
    border: 0;

    background: transparent;
    color: var(--ink-faint);

    cursor: pointer;

    transform: translateY(-50%);

    transition: color 150ms ease;
}

.filter-bar__search-clear:hover {
    color: var(--ink);
}

.filter-bar__search-clear svg {
    width: 12px;
    height: 12px;
}

.filter-bar__perpage {
    display: flex;
    align-items: center;
    gap: 8px;

    flex: 0 0 auto;
}

.filter-bar__perpage-label {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;

    white-space: nowrap;
}

.filter-bar__perpage-select {
    min-height: 34px;

    padding: 0 10px;

    border: 1px solid var(--line);

    background: var(--white);
    color: var(--ink);

    cursor: pointer;
    outline: none;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 13px;
    font-weight: 500;

    transition: border-color 180ms ease;
}

.filter-bar__perpage-select:focus {
    border-color: var(--ink);
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
   TABLE SCROLL — full-bleed edge-to-edge, geser di mobile
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

    min-width: 820px;

    border-collapse: collapse;

    font-size: 12px;
}

/* ----- HEADER ----- */

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

    white-space: nowrap;
}

.table__th--num {
    width: 52px;
    text-align: center;
    padding-left: 20px;
}

.table__th:first-child {
    padding-left: 28px;
}

.table__th:last-child {
    padding-right: 28px;
}

.table__th--center {
    text-align: center;
}

.table__th--right {
    text-align: right;
}

/* ----- ROWS ----- */

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

    white-space: nowrap;
}

.table__td:first-child {
    padding-left: 28px;
}

.table__td:last-child {
    padding-right: 28px;
}

.table__td--num {
    width: 52px;

    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 13px;
    font-weight: 500;

    text-align: center;
}

.table__td--mono {
    font-family: 'JetBrains Mono', 'Menlo', monospace;

    font-size: 11px;
    font-weight: 500;

    color: var(--ink-muted);
}

.table__td--name {
    font-family: 'Fraunces', Georgia, serif;

    font-size: 14px;
    font-weight: 500;
    letter-spacing: -0.01em;
}

.table__td--center {
    text-align: center;
}

.table__td--right {
    text-align: right;
}

/* ----- VOTE PILL ----- */

.vote-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 26px;
    height: 26px;

    border-radius: 50%;

    font-size: 12px;
    font-weight: 700;
}

.vote-pill--done {
    background: rgba(45, 138, 82, 0.1);
    color: var(--green);
}

.vote-pill--pending {
    background: var(--paper-dark);
    color: var(--ink-faint);
}

/* ----- STATUS PILL ----- */

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 4px 10px;

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;

    white-space: nowrap;
}

.status-pill__dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;
}

.status-pill--active {
    background: rgba(45, 138, 82, 0.08);
    color: var(--green);
}

.status-pill--active .status-pill__dot {
    background: var(--green);
}

.status-pill--inactive {
    background: rgba(167, 74, 61, 0.08);
    color: var(--red);
}

.status-pill--inactive .status-pill__dot {
    background: var(--red);
}

/* ----- ROW ACTIONS ----- */

.table__actions {
    display: inline-flex;
    gap: 8px;

    justify-content: flex-end;
}

.table__btn {
    min-height: 30px;

    padding: 0 12px;

    border: 1px solid var(--line);

    background: transparent;

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;

    white-space: nowrap;

    transition:
        background 150ms ease,
        border-color 150ms ease,
        color 150ms ease;
}

.table__btn--toggle {
    color: var(--ink-muted);
}

.table__btn--toggle:hover {
    border-color: var(--ink);
    color: var(--ink);
    background: var(--paper);
}

.table__btn--delete {
    color: var(--red);
}

.table__btn--delete:hover {
    border-color: var(--red);
    background: rgba(167, 74, 61, 0.06);
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
    .voters {
        padding: 28px 32px 56px;
    }

    .import__form {
        grid-template-columns: 1fr;
    }

    .import__submit {
        justify-content: center;
    }
}

@media (max-width: 760px) {
    .voters {
        padding: 24px 24px 48px;
    }

    .voters__header {
        grid-template-columns: 1fr;

        gap: 20px;

        padding: 4px 0 24px;
    }

    .voters__header-right {
        align-items: stretch;
    }

    .voters__export {
        width: 100%;
        justify-content: center;
    }

    .voters__title {
        font-size: clamp(30px, 9vw, 44px);
    }

    .voters__desc {
        font-size: 12px;
    }

    .voters__meta {
        padding: 14px 0 20px;
    }

    .voters__meta-count {
        display: none;
    }

    .import {
        padding: 22px 20px 20px;
        margin-top: 28px;
    }

    .import__title {
        font-size: 20px;
    }

    .import__head {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .table {
        padding: 22px 0 0;
        margin-top: 20px;
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

    .filter-bar {
        flex-direction: column;
        align-items: stretch;

        padding: 0 20px 16px;
    }

    .filter-bar__search {
        max-width: none;
    }

    .filter-bar__perpage {
        justify-content: flex-start;
    }

    .table__th:first-child,
    .table__td:first-child {
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
    .voters {
        padding: 20px 18px 40px;
    }

    .voters__title {
        font-size: 30px;
    }

    .import {
        padding: 20px 16px 18px;
    }

    .import__hint {
        padding: 10px 12px;
        gap: 6px;
    }

    .import__hint-code {
        font-size: 10px;
        padding: 2px 6px;
    }

    .table {
        padding: 20px 0 0;
    }

    .table__head {
        padding: 0 16px 14px;
        margin-bottom: 16px;
    }

    .filter-bar {
        padding: 0 16px 14px;
    }

    .table__grid {
        font-size: 11px;
        min-width: 720px;
    }

    .table__th,
    .table__td {
        padding: 12px 12px;
    }

    .table__th:first-child,
    .table__td:first-child {
        padding-left: 16px;
    }

    .table__th:last-child,
    .table__td:last-child {
        padding-right: 16px;
    }

    .table__td--name {
        font-size: 13px;
    }

    .table__btn {
        font-size: 8px;
        padding: 0 10px;
        min-height: 28px;
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
    .voters__export,
    .import__drop,
    .import__submit,
    .table__row,
    .table__btn,
    .pagination__link {
        transition: none !important;
    }

    .voters__export:hover,
    .import__submit:hover:not(:disabled) {
        transform: none;
    }
}
</style>
