<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

const menuOpen = ref(false);
const showContent = ref(false);

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
    // Tampilkan konten dengan animasi setelah mount
    setTimeout(() => {
        showContent.value = true;
    }, 100);
});

// cleanup
import { onUnmounted } from 'vue';
onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <Head title="Pemilihan Selesai — Pemilos" />

    <div class="page">
        <!-- =====================================================
         NAVIGATION (sama persis)
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

        <main class="done">
            <div class="done__inner">
                <!-- =================================================
             PAGE META
        ================================================== -->

                <div class="done__meta">
                    <span class="done__step">
                        <span class="done__step-number">✓</span>
                        Selesai
                    </span>
                    <span class="done__year">Pemilos 2026/2027</span>
                </div>

                <!-- =================================================
             CONTENT
        ================================================== -->

                <div
                    class="done__content"
                    :class="{ 'is-visible': showContent }"
                >
                    <!-- IKON / CHECKMARK dengan animasi -->
                    <div class="done__icon-wrapper">
                        <div class="done__icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>
                    </div>

                    <h1 class="done__title">Pemilihan Selesai</h1>

                    <p class="done__desc">
                        Suara Anda telah berhasil dicatat.
                        <br />
                        Terima kasih telah berpartisipasi dalam pemilihan OSIS
                        dan MPK.
                    </p>

                    <!-- TOMBOL AKSI -->
                    <div class="done__actions">
                        <a href="/" class="done__btn done__btn--primary">
                            <span>←</span>
                            Kembali ke Beranda
                        </a>

                        <a
                            href="/hasil/osis"
                            class="done__btn done__btn--secondary"
                        >
                            Lihat Hasil OSIS
                            <span>→</span>
                        </a>

                        <a
                            href="/hasil/mpk"
                            class="done__btn done__btn--secondary"
                        >
                            Lihat Hasil MPK
                            <span>→</span>
                        </a>
                    </div>

                    <p class="done__note">
                        Suaramu adalah masa depan sekolah. Terima kasih sudah
                        berpartisipasi.
                    </p>
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
   ROOT (sama persis dengan osis.vue)
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
   NAV (sama persis)
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

/* TRANSISI MENU */
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
   DONE
========================================================= */

.done {
    width: min(1180px, calc(100% - 48px));

    margin: 0 auto;

    padding: 72px 0 90px;
}

.done__inner {
    width: min(100%, 820px);

    margin: 0 auto;
}

/* META */
.done__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 38px;
    padding-bottom: 16px;

    border-bottom: 1px solid var(--line);
}

.done__step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.done__step-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.done__year {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* CONTENT */
.done__content {
    max-width: 580px;
    margin: 60px auto 0;

    text-align: center;

    opacity: 0;
    transform: translateY(30px) scale(0.98);
    transition:
        opacity 800ms cubic-bezier(0.22, 1, 0.36, 1),
        transform 800ms cubic-bezier(0.22, 1, 0.36, 1);
}

.done__content.is-visible {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* IKON CHECKMARK DENGAN ANIMASI */
.done__icon-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 32px;
}

.done__icon {
    width: 88px;
    height: 88px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--accent);
    color: var(--paper);

    box-shadow: 0 12px 32px rgba(75, 31, 99, 0.2);

    animation: icon-pop 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.3s both;
}

.done__icon svg {
    width: 48px;
    height: 48px;
    stroke-width: 2.5;

    stroke-dasharray: 80;
    stroke-dashoffset: 80;
    animation: check-draw 0.7s ease 0.5s forwards;
}

@keyframes icon-pop {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    60% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes check-draw {
    to {
        stroke-dashoffset: 0;
    }
}

/* TITLE & DESC */
.done__title {
    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(40px, 6vw, 64px);
    font-weight: 600;
    letter-spacing: -0.04em;

    margin: 0 0 16px;

    animation: fade-up 0.7s ease 0.6s both;
}

.done__desc {
    font-size: 16px;
    line-height: 1.7;
    color: var(--ink-muted);

    margin: 0 0 40px;

    animation: fade-up 0.7s ease 0.7s both;
}

@keyframes fade-up {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ACTIONS */
.done__actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;

    animation: fade-up 0.7s ease 0.8s both;
}

.done__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 12px 24px;
    border-radius: 999px;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 13px;
    font-weight: 600;
    text-decoration: none;

    transition:
        transform 180ms ease,
        background 180ms ease,
        border-color 180ms ease,
        box-shadow 180ms ease;
}

.done__btn--primary {
    background: var(--ink);
    color: var(--paper);
    box-shadow: 0 4px 12px rgba(24, 23, 22, 0.12);
}

.done__btn--primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(24, 23, 22, 0.16);
}

.done__btn--secondary {
    background: transparent;
    color: var(--ink);
    border: 1px solid var(--line);
}

.done__btn--secondary:hover {
    border-color: var(--ink);
    background: rgba(24, 23, 22, 0.03);
    transform: translateY(-2px);
}

.done__btn span {
    font-size: 18px;
    line-height: 1;
}

.done__note {
    margin: 32px 0 0;

    color: var(--ink-faint);

    font-size: 12px;
    line-height: 1.6;

    animation: fade-up 0.7s ease 0.9s both;
}

/* =========================================================
   FOOTER (sama)
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
   RESPONSIVE (sama dengan osis.vue)
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

    .done {
        width: calc(100% - 32px);
        padding: 50px 0 65px;
    }

    .done__meta {
        margin-bottom: 32px;
    }

    .done__year {
        display: none;
    }

    .done__content {
        margin-top: 40px;
    }

    .done__icon {
        width: 72px;
        height: 72px;
    }

    .done__icon svg {
        width: 38px;
        height: 38px;
    }

    .done__title {
        font-size: 38px;
    }

    .done__desc {
        font-size: 14px;
    }

    .done__actions {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .done__btn {
        width: 100%;
        justify-content: center;
        padding: 14px 20px;
    }

    .footer {
        width: calc(100% - 32px);
        padding-bottom: 30px;
        gap: 14px;
    }
}

@media (max-width: 390px) {
    .nav__menu-button {
        width: 36px;
        height: 36px;
        padding: 0;
        justify-content: center;
    }

    .nav__menu-label {
        display: none;
    }

    .nav__menu {
        top: 60px;
        right: 16px;
        width: calc(100vw - 32px);
    }

    .nav__menu-link {
        grid-template-columns: 30px minmax(0, 1fr) 20px;
        padding: 0 16px;
    }

    .nav__menu-link:hover {
        padding-left: 20px;
    }

    .nav__menu-text {
        font-size: 18px;
    }

    .done__title {
        font-size: 34px;
    }
}

/* REDUCED MOTION */
@media (prefers-reduced-motion: reduce) {
    .done__content,
    .done__title,
    .done__desc,
    .done__actions,
    .done__note {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .done__icon {
        animation: none !important;
    }

    .done__icon svg {
        animation: none !important;
        stroke-dashoffset: 0;
    }

    .menu-fade-enter-active,
    .menu-fade-leave-active,
    .overlay-fade-enter-active,
    .overlay-fade-leave-active {
        transition: none !important;
    }

    .nav__menu-button,
    .nav__menu-close,
    .nav__hamburger span,
    .nav__menu-link,
    .done__btn {
        transition: none !important;
    }
}
</style>
