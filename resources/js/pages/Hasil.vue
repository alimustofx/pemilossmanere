<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';

const props = defineProps({
    election: {
        type: Object,
        required: true,
    },
    initialResults: {
        type: Array,
        default: () => [],
    },
});

const results = ref(props.initialResults);
const menuOpen = ref(false);

// === REALTIME ECHO ===
const channelName = `election.${props.election.type}`;

useEchoPublic(channelName, '.vote.recorded', (event) => {
    console.log('📊 Vote baru:', event);
    results.value = event.results;
});

// === COMPUTED ===
const totalVotes = computed(() => {
    return results.value.reduce((sum, r) => sum + r.total, 0);
});

const sortedResults = computed(() => {
    return [...results.value].sort((a, b) => b.total - a.total);
});

const candidateCount = computed(() => {
    return results.value.length;
});

// === NAVIGATION ===
function toggleMenu() {
    menuOpen.value = !menuOpen.value;
}

function closeMenu() {
    menuOpen.value = false;
}

function handleKeydown(event) {
    if (event.key === 'Escape') {
        menuOpen.value = false;
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

// === ROUTING ===
function goToHome() {
    router.visit('/');
}

function goToOsis() {
    router.visit('/hasil/osis');
}

function goToMpk() {
    router.visit('/hasil/mpk');
}

// === LABEL ===
const electionLabel = computed(() => {
    return props.election.type === 'osis' ? 'OSIS' : 'MPK';
});

const electionTitle = computed(() => {
    return props.election.type === 'osis'
        ? 'Hasil Pemilihan Ketua & Wakil Ketua OSIS'
        : 'Hasil Pemilihan Ketua & Wakil Ketua MPK';
});
</script>

<template>
    <Head :title="`Hasil ${electionLabel} — Pemilos`" />

    <div class="page">
        <!-- =====================================================
         NAVIGATION
    ====================================================== -->

        <header class="nav">
            <a href="/" class="nav__logo" @click="closeMenu"> Pemilos </a>

            <div class="nav__right">
                <button
                    type="button"
                    class="nav__menu-button"
                    :class="{ 'is-open': menuOpen }"
                    :aria-expanded="menuOpen"
                    :aria-label="
                        menuOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi'
                    "
                    @click="toggleMenu"
                >
                    <span class="nav__menu-label">Menu</span>
                    <span class="nav__hamburger" aria-hidden="true">
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <!-- MENU PANEL -->
            <Transition name="menu-fade">
                <div v-if="menuOpen" class="nav__menu">
                    <div class="nav__menu-header">
                        <span>Navigasi</span>
                        <button
                            type="button"
                            class="nav__menu-close"
                            aria-label="Tutup menu"
                            @click="closeMenu"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            >
                                <path d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>
                    </div>

                    <nav class="nav__menu-links">
                        <a href="/" class="nav__menu-link" @click="closeMenu">
                            <span class="nav__menu-number">01</span>
                            <span class="nav__menu-text">Beranda</span>
                            <span class="nav__menu-arrow">↗</span>
                        </a>

                        <a
                            href="/pengumuman"
                            class="nav__menu-link"
                            @click="closeMenu"
                        >
                            <span class="nav__menu-number">02</span>
                            <span class="nav__menu-text">Pengumuman</span>
                            <span class="nav__menu-arrow">↗</span>
                        </a>

                        <a
                            href="/hasil/osis"
                            class="nav__menu-link"
                            @click="closeMenu"
                        >
                            <span class="nav__menu-number">03</span>
                            <span class="nav__menu-text">Hasil OSIS</span>
                            <span class="nav__menu-arrow">↗</span>
                        </a>

                        <a
                            href="/hasil/mpk"
                            class="nav__menu-link"
                            @click="closeMenu"
                        >
                            <span class="nav__menu-number">04</span>
                            <span class="nav__menu-text">Hasil MPK</span>
                            <span class="nav__menu-arrow">↗</span>
                        </a>

                        <a
                            href="/vote/access"
                            class="nav__menu-link"
                            @click="closeMenu"
                        >
                            <span class="nav__menu-number">05</span>
                            <span class="nav__menu-text">Mulai memilih</span>
                            <span class="nav__menu-arrow">↗</span>
                        </a>
                    </nav>

                    <div class="nav__menu-footer">
                        <span>Pemilos 2026/2027</span>
                        <span>OSIS &amp; MPK</span>
                    </div>
                </div>
            </Transition>

            <!-- OVERLAY -->
            <Transition name="overlay-fade">
                <button
                    v-if="menuOpen"
                    type="button"
                    class="nav__overlay"
                    aria-label="Tutup menu"
                    @click="closeMenu"
                ></button>
            </Transition>
        </header>

        <!-- =====================================================
         MAIN
    ====================================================== -->

        <main class="result">
            <div class="result__inner">
                <!-- META -->
                <div class="result__meta">
                    <span class="result__step">
                        <span class="result__step-number">03</span>
                        Hasil {{ electionLabel }}
                    </span>
                    <span class="result__year"> Pemilos 2026/2027 </span>
                </div>

                <!-- HEADING -->
                <section class="result__heading">
                    <p class="result__eyebrow">
                        {{ electionLabel }} — Realtime
                    </p>

                    <h1 class="result__title">
                        {{ electionTitle }}
                    </h1>

                    <p class="result__desc">
                        Perolehan suara diperbarui secara langsung setiap kali
                        ada pemilih yang memberikan suara.
                    </p>
                </section>

                <!-- STATISTIK — HANYA DATA YANG TERSEDIA -->
                <section class="result__stats">
                    <div class="stat-card">
                        <span class="stat-card__label">Total Suara</span>
                        <span class="stat-card__number">{{ totalVotes }}</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-card__label">Jumlah Kandidat</span>
                        <span class="stat-card__number">{{
                            candidateCount
                        }}</span>
                    </div>
                    <div class="stat-card stat-card--empty">
                        <span class="stat-card__label">Status</span>
                        <span
                            class="stat-card__number stat-card__number--small"
                        >
                            <span class="result__status-dot"></span>
                            Live
                        </span>
                    </div>
                </section>

                <!-- BAR CHART -->
                <section class="result__chart">
                    <div
                        v-for="r in sortedResults"
                        :key="r.candidate_group_id"
                        class="result-bar"
                    >
                        <div class="result-bar__header">
                            <span class="result-bar__number">
                                {{
                                    String(
                                        r.candidate_group?.nomor_urut || '—',
                                    ).padStart(2, '0')
                                }}
                            </span>
                            <span class="result-bar__name">
                                {{
                                    r.candidate_group?.nama_kelompok ||
                                    'Tidak diketahui'
                                }}
                            </span>
                            <span class="result-bar__votes">
                                {{ r.total }} suara
                            </span>
                            <span class="result-bar__percent">
                                {{
                                    totalVotes > 0
                                        ? Math.round(
                                              (r.total / totalVotes) * 100,
                                          )
                                        : 0
                                }}%
                            </span>
                        </div>

                        <div class="result-bar__track">
                            <div
                                class="result-bar__fill"
                                :style="{
                                    width:
                                        totalVotes > 0
                                            ? `${(r.total / totalVotes) * 100}%`
                                            : '0%',
                                }"
                            >
                                <span class="result-bar__fill-inner"></span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- STATUS REALTIME -->
                <div class="result__status">
                    <span class="result__status-dot"></span>
                    <span class="result__status-text">
                        Data diperbarui secara langsung
                    </span>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="result__actions">
                    <button
                        type="button"
                        class="result__btn result__btn--secondary"
                        @click="goToHome"
                    >
                        <span>←</span>
                        Kembali ke Beranda
                    </button>

                    <div class="result__btn-group">
                        <button
                            type="button"
                            class="result__btn result__btn--outline"
                            @click="goToOsis"
                            :class="{
                                'result__btn--active': election.type === 'osis',
                            }"
                        >
                            Hasil OSIS
                        </button>

                        <button
                            type="button"
                            class="result__btn result__btn--outline"
                            @click="goToMpk"
                            :class="{
                                'result__btn--active': election.type === 'mpk',
                            }"
                        >
                            Hasil MPK
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- =====================================================
         FOOTER
    ====================================================== -->

        <footer class="footer">
            <span>Panitia Pemilihan OSIS &amp; MPK</span>
            <span>{{ new Date().getFullYear() }}</span>
        </footer>
    </div>
</template>

<style scoped>
/* =========================================================
   ROOT — SAMA PERSIS DENGAN OSIS.VUE
========================================================= */

.page {
    --ink: #181716;
    --ink-muted: #77736d;
    --ink-faint: #a09b93;
    --accent: #4b1f63;
    --paper: #f6f3ee;
    --paper-dark: #ebe7df;
    --white: #fffdf9;
    --line: #d9d4cb;

    min-height: 100vh;
    background: var(--paper);
    color: var(--ink);

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    -webkit-font-smoothing: antialiased;
}

.page *,
.page *::before,
.page *::after {
    box-sizing: border-box;
}

.page button,
.page a {
    -webkit-tap-highlight-color: transparent;
}

/* =========================================================
   NAV — SAMA PERSIS
========================================================= */

.nav {
    position: relative;
    z-index: 100;

    width: min(1180px, calc(100% - 48px));
    min-height: 76px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav__logo {
    position: relative;
    z-index: 102;

    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
    font-weight: 600;
    letter-spacing: -0.025em;

    text-decoration: none;
}

.nav__right {
    position: relative;
    z-index: 102;

    display: flex;
    align-items: center;
}

.nav__menu-button {
    min-height: 38px;

    display: inline-flex;
    align-items: center;
    gap: 11px;

    padding: 0 13px;

    border: 1px solid var(--line);
    border-radius: 999px;

    background: transparent;
    color: var(--ink);

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    transition:
        background 180ms ease,
        border-color 180ms ease,
        color 180ms ease;
}

.nav__menu-button:hover,
.nav__menu-button.is-open {
    border-color: var(--ink);
    background: var(--ink);
    color: var(--paper);
}

.nav__menu-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.nav__hamburger {
    width: 17px;
    height: 14px;

    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
}

.nav__hamburger span {
    display: block;

    width: 17px;
    height: 1px;

    background: currentColor;

    transition:
        transform 180ms ease,
        width 180ms ease;
}

.nav__menu-button.is-open .nav__hamburger span:first-child {
    transform: translateY(3px) rotate(45deg);
}

.nav__menu-button.is-open .nav__hamburger span:last-child {
    transform: translateY(-3px) rotate(-45deg);
}

.nav__overlay {
    position: fixed;
    inset: 0;

    z-index: 99;

    width: 100%;
    height: 100%;

    padding: 0;

    border: 0;

    background: rgba(24, 23, 22, 0.18);

    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);

    cursor: default;
}

.nav__menu {
    position: absolute;

    top: 58px;
    right: 0;

    z-index: 101;

    width: min(390px, calc(100vw - 32px));

    background: var(--white);

    border: 1px solid var(--line);
    border-top: 1px solid var(--ink);

    box-shadow:
        0 22px 60px rgba(24, 23, 22, 0.13),
        0 5px 15px rgba(24, 23, 22, 0.05);
}

.nav__menu-header {
    min-height: 55px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 18px;

    border-bottom: 1px solid var(--line);

    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.11em;
    text-transform: uppercase;
}

.nav__menu-close {
    width: 30px;
    height: 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid var(--line);
    border-radius: 50%;

    background: transparent;
    color: var(--ink-muted);

    cursor: pointer;

    transition:
        background 180ms ease,
        color 180ms ease,
        border-color 180ms ease;
}

.nav__menu-close:hover {
    border-color: var(--ink);
    background: var(--ink);
    color: var(--paper);
}

.nav__menu-close svg {
    width: 14px;
    height: 14px;
}

.nav__menu-links {
    display: flex;
    flex-direction: column;
}

.nav__menu-link {
    min-height: 69px;

    display: grid;
    grid-template-columns: 34px minmax(0, 1fr) 22px;
    align-items: center;
    gap: 10px;

    padding: 0 19px;

    border-bottom: 1px solid var(--line);

    color: var(--ink);

    text-decoration: none;

    transition:
        background 180ms ease,
        padding 180ms ease;
}

.nav__menu-link:hover {
    padding-left: 24px;
    background: var(--paper);
}

.nav__menu-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 13px;
}

.nav__menu-text {
    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
    font-weight: 500;
    letter-spacing: -0.025em;
}

.nav__menu-arrow {
    color: var(--ink-muted);
    font-size: 16px;

    transition: transform 180ms ease;
}

.nav__menu-link:hover .nav__menu-arrow {
    transform: translate(2px, -2px);
}

.nav__menu-footer {
    min-height: 50px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 0 19px;

    color: var(--ink-faint);

    font-size: 8px;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
}

.menu-fade-enter-active,
.menu-fade-leave-active {
    transition:
        opacity 180ms ease,
        transform 180ms cubic-bezier(0.22, 1, 0.36, 1);
}

.menu-fade-enter-from,
.menu-fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

.overlay-fade-enter-active,
.overlay-fade-leave-active {
    transition: opacity 180ms ease;
}

.overlay-fade-enter-from,
.overlay-fade-leave-to {
    opacity: 0;
}

/* =========================================================
   RESULT
========================================================= */

.result {
    width: min(1180px, calc(100% - 48px));

    margin: 0 auto;

    padding: 72px 0 90px;
}

.result__inner {
    width: min(100%, 820px);

    margin: 0 auto;
}

/* =========================================================
   META
========================================================= */

.result__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 38px;
    padding-bottom: 16px;

    border-bottom: 1px solid var(--line);
}

.result__step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.result__step-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.result__year {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   HEADING
========================================================= */

.result__heading {
    margin-bottom: 35px;
}

.result__eyebrow {
    margin: 0 0 20px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.result__title {
    max-width: 760px;

    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(38px, 6vw, 58px);
    font-weight: 600;
    line-height: 0.97;
    letter-spacing: -0.05em;
}

.result__desc {
    max-width: 590px;

    margin: 25px 0 0;

    color: var(--ink-muted);

    font-size: 14px;
    line-height: 1.75;
}

/* =========================================================
   STATS — HANYA DATA YANG TERSEDIA
========================================================= */

.result__stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);

    gap: 12px;

    margin-bottom: 40px;
}

.stat-card {
    display: flex;
    flex-direction: column;

    padding: 18px 20px;

    border: 1px solid var(--line);

    background: var(--white);
}

.stat-card--empty {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 12px;
}

.stat-card--empty .stat-card__label {
    margin-bottom: 0;
}

.stat-card--empty .stat-card__number {
    margin-top: 0;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-card__label {
    color: var(--ink-faint);

    font-size: 8px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.stat-card__number {
    margin-top: 7px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 28px;
    font-weight: 600;
    letter-spacing: -0.03em;

    color: var(--ink);
}

.stat-card__number--small {
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* =========================================================
   BAR CHART
========================================================= */

.result__chart {
    margin-bottom: 28px;

    border-top: 1px solid var(--ink);
    border-left: 1px solid var(--line);
}

.result-bar {
    border-right: 1px solid var(--line);
    border-bottom: 1px solid var(--line);

    background: var(--white);
}

.result-bar__header {
    display: grid;
    grid-template-columns: 44px minmax(0, 1fr) 72px 52px;

    gap: 12px;

    align-items: center;

    padding: 14px 18px;

    font-size: 11px;
}

.result-bar__number {
    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
    font-weight: 500;
    letter-spacing: -0.03em;

    color: var(--accent);
}

.result-bar__name {
    font-weight: 600;
    color: var(--ink);

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.result-bar__votes {
    color: var(--ink-muted);
    text-align: right;
}

.result-bar__percent {
    color: var(--ink-faint);
    text-align: right;
}

.result-bar__track {
    height: 4px;

    margin: 0 18px 14px;

    background: var(--paper-dark);

    border-radius: 2px;

    overflow: hidden;
}

.result-bar__fill {
    height: 100%;

    background: var(--accent);

    border-radius: 2px;

    transition: width 800ms cubic-bezier(0.22, 1, 0.36, 1);

    position: relative;
}

.result-bar__fill-inner {
    display: block;

    width: 100%;
    height: 100%;

    background: linear-gradient(90deg, var(--accent), #7c3a9e);

    border-radius: 2px;

    animation: barPulse 2s ease-in-out infinite;
}

@keyframes barPulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* =========================================================
   STATUS REALTIME
========================================================= */

.result__status {
    display: flex;
    align-items: center;
    gap: 10px;

    padding: 14px 18px;

    border: 1px solid rgba(75, 31, 99, 0.12);

    background: rgba(75, 31, 99, 0.04);
}

.result__status-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: #2fae6b;

    animation: dotPulse 1.6s ease-in-out infinite;
}

@keyframes dotPulse {
    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.4;
        transform: scale(0.7);
    }
}

.result__status-text {
    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

/* =========================================================
   ACTIONS
========================================================= */

.result__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;

    gap: 16px;

    margin-top: 32px;
    padding-top: 28px;

    border-top: 1px solid var(--line);
}

.result__btn-group {
    display: flex;
    gap: 8px;
}

.result__btn {
    min-height: 44px;

    display: inline-flex;
    align-items: center;

    padding: 0 18px;

    border: 1px solid var(--line);

    background: transparent;
    color: var(--ink-muted);

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.04em;

    transition:
        background 180ms ease,
        border-color 180ms ease,
        color 180ms ease;
}

.result__btn:hover {
    border-color: var(--ink);
    color: var(--ink);
}

.result__btn--secondary {
    gap: 8px;
}

.result__btn--secondary span {
    font-size: 15px;
}

.result__btn--outline {
    border-color: var(--line);
}

.result__btn--outline:hover {
    border-color: var(--ink);
}

.result__btn--active {
    border-color: var(--accent);
    background: var(--accent);
    color: var(--white);
}

.result__btn--active:hover {
    border-color: var(--accent);
    background: #3d1a52;
    color: var(--white);
}

/* =========================================================
   FOOTER
========================================================= */

.footer {
    width: min(1180px, calc(100% - 48px));

    margin: 0 auto;

    padding: 24px 0 40px;

    border-top: 1px solid var(--line);

    display: flex;
    align-items: center;
    justify-content: space-between;

    color: #8b867e;

    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 760px) {
    .result__stats {
        grid-template-columns: 1fr 1fr;
    }

    .stat-card--empty {
        grid-column: span 1;
    }

    .result-bar__header {
        grid-template-columns: 36px minmax(0, 1fr) 56px 40px;

        gap: 8px;

        font-size: 10px;
        padding: 12px 14px;
    }

    .result-bar__number {
        font-size: 17px;
    }

    .result__actions {
        flex-direction: column;
        align-items: stretch;
    }

    .result__btn-group {
        justify-content: center;
    }
}

@media (max-width: 680px) {
    .nav {
        width: calc(100% - 32px);
        min-height: 68px;
    }

    .nav__menu-button {
        min-height: 36px;
        padding: 0 11px;
        gap: 9px;
    }

    .nav__menu-label {
        font-size: 9px;
    }

    .nav__menu {
        position: fixed;
        top: 64px;
        right: 16px;
        width: calc(100vw - 32px);
        max-height: calc(100vh - 82px);
        overflow-y: auto;
    }

    .nav__menu-link {
        min-height: 64px;
    }

    .nav__menu-text {
        font-size: 19px;
    }

    .result {
        width: calc(100% - 32px);
        padding: 50px 0 65px;
    }

    .result__meta {
        margin-bottom: 32px;
    }

    .result__year {
        display: none;
    }

    .result__title {
        font-size: clamp(36px, 12vw, 48px);
    }

    .result__desc {
        font-size: 13px;
    }

    .result__stats {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
    }

    .stat-card {
        padding: 14px 16px;
    }

    .stat-card__number {
        font-size: 22px;
    }

    .stat-card--empty .stat-card__number {
        font-size: 12px;
    }

    .result-bar__header {
        grid-template-columns: 32px minmax(0, 1fr) 48px 36px;

        gap: 6px;

        font-size: 9px;
        padding: 10px 12px;
    }

    .result-bar__number {
        font-size: 15px;
    }

    .result-bar__track {
        margin: 0 12px 10px;
    }

    .result__status {
        padding: 12px 14px;
    }

    .result__actions {
        margin-top: 24px;
        padding-top: 20px;
    }

    .result__btn {
        font-size: 9px;
        min-height: 40px;
        padding: 0 14px;
    }

    .footer {
        width: calc(100% - 32px);
        padding-bottom: 30px;
        gap: 14px;
    }
}

@media (max-width: 480px) {
    .result__stats {
        grid-template-columns: 1fr 1fr;
    }

    .stat-card {
        padding: 12px 14px;
    }

    .stat-card__number {
        font-size: 20px;
    }

    .result-bar__header {
        grid-template-columns: 28px minmax(0, 1fr) 40px 30px;

        gap: 4px;

        font-size: 8px;
        padding: 8px 10px;
    }

    .result-bar__number {
        font-size: 13px;
    }

    .result__btn-group {
        flex-wrap: wrap;
        justify-content: center;
    }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .result-bar__fill {
        transition: none !important;
    }

    .result-bar__fill-inner {
        animation: none !important;
    }

    .result__status-dot {
        animation: none !important;
    }

    .menu-fade-enter-active,
    .menu-fade-leave-active,
    .overlay-fade-enter-active,
    .overlay-fade-leave-active {
        transition: none !important;
    }
}
</style>
