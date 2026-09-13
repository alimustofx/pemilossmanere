<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    announcements: {
        type: Array,
        default: () => [],
    },
});

const menuOpen = ref(false);

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

// === FORMAT TANGGAL ===
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Pengumuman — Pemilos" />

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

        <main class="announcement-page">
            <div class="announcement-page__inner">
                <!-- META -->
                <div class="announcement-page__meta">
                    <span class="announcement-page__step">
                        <span class="announcement-page__step-number">02</span>
                        Pengumuman
                    </span>
                    <span class="announcement-page__year">
                        Pemilos 2026/2027
                    </span>
                </div>

                <!-- HEADING -->
                <section class="announcement-page__heading">
                    <p class="announcement-page__eyebrow">Informasi Resmi</p>

                    <h1 class="announcement-page__title">
                        Pengumuman <em>Pemilos</em>
                    </h1>

                    <p class="announcement-page__desc">
                        Semua pengumuman resmi terkait pemilihan OSIS dan MPK
                        akan dipublikasikan di sini.
                    </p>
                </section>

                <!-- LIST PENGUMUMAN -->
                <section class="announcement-list">
                    <!-- KOSONG -->
                    <div
                        v-if="!announcements.length"
                        class="announcement-empty"
                    >
                        <span class="announcement-empty__icon">📢</span>
                        <h3>Belum ada pengumuman</h3>
                        <p>
                            Pengumuman resmi akan muncul di sini setelah
                            diterbitkan oleh panitia.
                        </p>
                    </div>

                    <!-- LIST -->
                    <article
                        v-for="(a, index) in announcements"
                        :key="a.id"
                        class="announcement-card"
                        :style="{ animationDelay: `${index * 100}ms` }"
                    >
                        <!-- GAMBAR -->
                        <div v-if="a.image" class="announcement-card__image">
                            <img
                                :src="`/storage/${a.image}`"
                                :alt="a.title"
                                class="announcement-card__img"
                            />
                            <span class="announcement-card__badge">
                                {{ formatDate(a.created_at) }}
                            </span>
                        </div>

                        <!-- KONTEN -->
                        <div
                            class="announcement-card__body"
                            :class="{
                                'announcement-card__body--no-image': !a.image,
                            }"
                        >
                            <div class="announcement-card__top">
                                <span class="announcement-card__date">
                                    <span class="announcement-card__date-day">
                                        {{ formatDate(a.created_at) }}
                                    </span>
                                    <span class="announcement-card__date-time">
                                        {{ formatTime(a.created_at) }}
                                    </span>
                                </span>
                                <span class="announcement-card__number">
                                    {{ String(index + 1).padStart(2, '0') }}
                                </span>
                            </div>

                            <h2 class="announcement-card__title">
                                {{ a.title }}
                            </h2>

                            <p class="announcement-card__content">
                                {{ a.content }}
                            </p>
                        </div>
                    </article>
                </section>

                <!-- ACTION BUTTONS -->
                <div class="announcement-page__actions">
                    <button
                        type="button"
                        class="announcement-page__btn announcement-page__btn--secondary"
                        @click="goToHome"
                    >
                        <span>←</span>
                        Kembali ke Beranda
                    </button>

                    <div class="announcement-page__btn-group">
                        <button
                            type="button"
                            class="announcement-page__btn announcement-page__btn--outline"
                            @click="goToOsis"
                        >
                            Hasil OSIS
                        </button>

                        <button
                            type="button"
                            class="announcement-page__btn announcement-page__btn--outline"
                            @click="goToMpk"
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
   ROOT — SAMA PERSIS DENGAN HALAMAN LAIN
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
   ANNOUNCEMENT PAGE
========================================================= */

.announcement-page {
    width: min(1180px, calc(100% - 48px));

    margin: 0 auto;

    padding: 72px 0 90px;
}

.announcement-page__inner {
    width: min(100%, 820px);

    margin: 0 auto;
}

/* =========================================================
   META
========================================================= */

.announcement-page__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 38px;
    padding-bottom: 16px;

    border-bottom: 1px solid var(--line);
}

.announcement-page__step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.announcement-page__step-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.announcement-page__year {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   HEADING
========================================================= */

.announcement-page__heading {
    margin-bottom: 40px;
}

.announcement-page__eyebrow {
    margin: 0 0 20px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.announcement-page__title {
    max-width: 760px;

    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(38px, 6vw, 58px);
    font-weight: 600;
    line-height: 0.97;
    letter-spacing: -0.05em;
}

.announcement-page__title em {
    font-weight: 400;
}

.announcement-page__desc {
    max-width: 590px;

    margin: 25px 0 0;

    color: var(--ink-muted);

    font-size: 14px;
    line-height: 1.75;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.announcement-empty {
    display: flex;
    flex-direction: column;
    align-items: center;

    padding: 60px 20px;

    border: 1px solid var(--line);

    background: var(--white);

    text-align: center;
}

.announcement-empty__icon {
    font-size: 2.8rem;
    margin-bottom: 16px;
}

.announcement-empty h3 {
    margin: 0 0 8px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
    font-weight: 600;
}

.announcement-empty p {
    margin: 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.6;

    max-width: 360px;
}

/* =========================================================
   ANNOUNCEMENT LIST
========================================================= */

.announcement-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* =========================================================
   ANNOUNCEMENT CARD
========================================================= */

.announcement-card {
    background: var(--white);

    border: 1px solid var(--line);

    overflow: hidden;

    opacity: 0;
    transform: translateY(12px);
    animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes cardIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.announcement-card__image {
    position: relative;

    width: 100%;
    height: 220px;

    overflow: hidden;

    background: var(--paper-dark);
}

.announcement-card__img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    transition: transform 400ms ease;
}

.announcement-card:hover .announcement-card__img {
    transform: scale(1.02);
}

.announcement-card__badge {
    position: absolute;

    bottom: 14px;
    left: 14px;

    padding: 4px 12px;

    background: var(--ink);
    color: var(--white);

    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.announcement-card__body {
    padding: 28px 30px;
}

.announcement-card__body--no-image {
    padding-top: 32px;
}

.announcement-card__top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 14px;
}

.announcement-card__date {
    display: flex;
    align-items: center;
    gap: 8px;
}

.announcement-card__date-day {
    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.04em;
}

.announcement-card__date-time {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 500;
}

.announcement-card__number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 16px;
    font-weight: 500;
}

.announcement-card__title {
    margin: 0 0 12px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 24px;
    font-weight: 600;
    line-height: 1.2;
    letter-spacing: -0.02em;

    color: var(--ink);
}

.announcement-card__content {
    margin: 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.75;

    white-space: pre-line;
}

/* =========================================================
   ACTIONS
========================================================= */

.announcement-page__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;

    gap: 16px;

    margin-top: 40px;
    padding-top: 28px;

    border-top: 1px solid var(--line);
}

.announcement-page__btn-group {
    display: flex;
    gap: 8px;
}

.announcement-page__btn {
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

.announcement-page__btn:hover {
    border-color: var(--ink);
    color: var(--ink);
}

.announcement-page__btn--secondary {
    gap: 8px;
}

.announcement-page__btn--secondary span {
    font-size: 15px;
}

.announcement-page__btn--outline {
    border-color: var(--line);
}

.announcement-page__btn--outline:hover {
    border-color: var(--ink);
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

    .announcement-page {
        width: calc(100% - 32px);
        padding: 50px 0 65px;
    }

    .announcement-page__meta {
        margin-bottom: 32px;
    }

    .announcement-page__year {
        display: none;
    }

    .announcement-page__title {
        font-size: clamp(32px, 10vw, 44px);
    }

    .announcement-page__desc {
        font-size: 13px;
    }

    .announcement-card__image {
        height: 180px;
    }

    .announcement-card__body {
        padding: 22px 20px;
    }

    .announcement-card__title {
        font-size: 20px;
    }

    .announcement-card__content {
        font-size: 12px;
    }

    .announcement-page__actions {
        flex-direction: column;
        align-items: stretch;
    }

    .announcement-page__btn-group {
        justify-content: center;
    }

    .announcement-empty {
        padding: 40px 16px;
    }

    .footer {
        width: calc(100% - 32px);
        padding-bottom: 30px;
        gap: 14px;
    }
}

@media (max-width: 400px) {
    .announcement-card__image {
        height: 150px;
    }

    .announcement-card__body {
        padding: 18px 16px;
    }

    .announcement-card__title {
        font-size: 18px;
    }

    .announcement-card__top {
        flex-wrap: wrap;
        gap: 6px;
    }

    .announcement-page__btn-group {
        flex-wrap: wrap;
    }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .announcement-card {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .announcement-card__img {
        transition: none !important;
    }

    .menu-fade-enter-active,
    .menu-fade-leave-active,
    .overlay-fade-enter-active,
    .overlay-fade-leave-active {
        transition: none !important;
    }
}
</style>
