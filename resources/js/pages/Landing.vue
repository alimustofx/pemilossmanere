<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
    elections: {
        type: Array,
        default: () => [],
    },

    latestAnnouncement: {
        type: Object,
        default: null,
    },
})

/*
|--------------------------------------------------------------------------
| NAVIGATION
|--------------------------------------------------------------------------
*/

const menuOpen = ref(false)

function toggleMenu() {
    menuOpen.value = !menuOpen.value
}

function closeMenu() {
    menuOpen.value = false
}

function handleEscape(event) {
    if (event.key === 'Escape') {
        closeMenu()
    }
}

/*
|--------------------------------------------------------------------------
| HERO SLIDES
|--------------------------------------------------------------------------
*/

const heroSlides = [
    '/images/candidate-1.svg',
    '/images/candidate-2.svg',
    '/images/candidate-3.svg',
    '/images/candidate-4.svg',
    '/images/candidate-5.svg',
    '/images/candidate-6.svg',
]

const activeSlide = ref(0)
const ready = ref(false)

let slideInterval = null

const statusLabel = {
    draft: 'Belum dimulai',
    scheduled: 'Terjadwal',
    open: 'Sedang berlangsung',
    closed: 'Ditutup',
    result_published: 'Hasil diumumkan',
}

const typeLabel = {
    osis: 'Ketua & Wakil Ketua OSIS',
    mpk: 'Ketua & Wakil Ketua MPK',
}

const currentSlide = computed(() => {
    return heroSlides[activeSlide.value]
})

const announcementImage = computed(() => {
    if (!props.latestAnnouncement?.image) {
        return null
    }

    return `/storage/${props.latestAnnouncement.image}`
})

const announcementExcerpt = computed(() => {
    const content = props.latestAnnouncement?.content ?? ''

    if (content.length <= 180) {
        return content
    }

    return `${content.substring(0, 180).trim()}…`
})

onMounted(() => {
    window.addEventListener('keydown', handleEscape)

    requestAnimationFrame(() => {
        ready.value = true
    })

    /*
     * Ganti foto setiap 5 detik.
     * Kalau hanya ada 1 foto, slider tidak dijalankan.
     */
    if (heroSlides.length > 1) {
        slideInterval = window.setInterval(() => {
            activeSlide.value =
                (activeSlide.value + 1) % heroSlides.length
        }, 5000)
    }
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleEscape)

    if (slideInterval) {
        window.clearInterval(slideInterval)
        slideInterval = null
    }
})
</script>

<template>
    <div class="page">

        <!-- =====================================================
             NAVIGATION
        ====================================================== -->
        <header class="nav">

            <!-- LOGO -->
            <a
                href="/"
                class="nav__logo"
                @click="closeMenu"
            >
                Pemilos
            </a>

            <!-- DESKTOP NAVIGATION -->
            <nav class="nav__links">
                <a href="/pengumuman">
                    Pengumuman
                </a>

                <a href="/hasil/osis">
                    Hasil OSIS
                </a>

                <a href="/hasil/mpk">
                    Hasil MPK
                </a>

                <a
                    href="/vote/access"
                    class="nav__cta"
                >
                    Mulai memilih
                </a>
            </nav>

            <!-- HAMBURGER BUTTON -->
            <button
                type="button"
                class="nav__menu-button"
                :class="{ 'is-open': menuOpen }"
                :aria-expanded="menuOpen"
                aria-label="Buka menu navigasi"
                @click="toggleMenu"
            >
                <span class="nav__menu-icon">
                    <span></span>
                    <span></span>
                </span>

                <span class="nav__menu-label">
                    Menu
                </span>
            </button>
        </header>

        <!-- =====================================================
             MOBILE / TABLET MENU
        ====================================================== -->
        <Transition name="menu">
            <div
                v-if="menuOpen"
                class="mobile-menu"
            >

                <!-- OVERLAY -->
                <button
                    type="button"
                    class="mobile-menu__overlay"
                    aria-label="Tutup menu"
                    @click="closeMenu"
                ></button>

                <!-- MENU PANEL -->
                <nav class="mobile-menu__panel">

                    <!-- MENU HEADER -->
                    <div class="mobile-menu__top">
                        <span>
                            Navigasi
                        </span>

                        <button
                            type="button"
                            class="mobile-menu__close"
                            aria-label="Tutup menu"
                            @click="closeMenu"
                        >
                            <span></span>
                            <span></span>
                        </button>
                    </div>

                    <!-- MENU LINKS -->
                    <div class="mobile-menu__links">

                        <a
                            href="/pengumuman"
                            @click="closeMenu"
                        >
                            <span class="mobile-menu__number">
                                01
                            </span>

                            <span class="mobile-menu__text">
                                Pengumuman
                            </span>

                            <span class="mobile-menu__arrow">
                                ↗
                            </span>
                        </a>

                        <a
                            href="/hasil/osis"
                            @click="closeMenu"
                        >
                            <span class="mobile-menu__number">
                                02
                            </span>

                            <span class="mobile-menu__text">
                                Hasil OSIS
                            </span>

                            <span class="mobile-menu__arrow">
                                ↗
                            </span>
                        </a>

                        <a
                            href="/hasil/mpk"
                            @click="closeMenu"
                        >
                            <span class="mobile-menu__number">
                                03
                            </span>

                            <span class="mobile-menu__text">
                                Hasil MPK
                            </span>

                            <span class="mobile-menu__arrow">
                                ↗
                            </span>
                        </a>

                        <a
                            href="/vote/access"
                            class="mobile-menu__vote"
                            @click="closeMenu"
                        >
                            <span class="mobile-menu__number">
                                04
                            </span>

                            <span class="mobile-menu__text">
                                Mulai memilih
                            </span>

                            <span class="mobile-menu__arrow">
                                ↗
                            </span>
                        </a>
                    </div>

                    <!-- MENU FOOTER -->
                    <div class="mobile-menu__footer">
                        <span>
                            Pemilos 2026/2027
                        </span>

                        <span>
                            OSIS &amp; MPK
                        </span>
                    </div>
                </nav>
            </div>
        </Transition>

        <!-- =====================================================
             HERO
        ====================================================== -->
        <section
            class="hero"
            :class="{ 'is-ready': ready }"
        >

            <!-- LEFT -->
            <div class="hero__content">

                <p class="hero__eyebrow">
                    Pemilihan OSIS & MPK

                    <span>
                        2026/2027
                    </span>
                </p>

                <h1 class="hero__title">
                    Satu suara menentukan
                    <em>arah</em>
                    sekolah kita.
                </h1>

                <p class="hero__desc">
                    Masukkan kelas, NIS, dan tanggal lahir untuk memilih.
                    Prosesnya kurang dari dua menit, dan pilihanmu tetap rahasia.
                </p>

                <div class="hero__actions">

                    <a
                        href="/vote/access"
                        class="hero__cta"
                    >
                        <span>
                            Mulai memilih
                        </span>

                        <span class="hero__cta-arrow">
                            ↗
                        </span>
                    </a>

                </div>
            </div>

            <!-- RIGHT / PHOTO SLIDESHOW -->
            <div class="hero__visual">

                <div class="hero__photo">

                    <Transition name="photo-fade">
                        <img
                            :key="currentSlide"
                            :src="currentSlide"
                            :alt="`Foto kandidat ${activeSlide + 1}`"
                        />
                    </Transition>

                </div>

                <div class="hero__photo-caption">
                    <span>
                        Pemilos 2026/2027
                    </span>

                    <span>
                        {{ String(activeSlide + 1).padStart(2, '0') }}
                        /
                        {{ String(heroSlides.length).padStart(2, '0') }}
                    </span>
                </div>
            </div>
        </section>

        <!-- =====================================================
             CURRENT STATUS
        ====================================================== -->
        <section
            v-if="elections?.length"
            class="ballot"
            id="pemilihan"
        >
            <div
                v-for="(election, i) in elections"
                :key="election.id"
                class="ballot__row"
            >
                <span class="ballot__num">
                    {{ String(i + 1).padStart(2, '0') }}
                </span>

                <div class="ballot__info">
                    <span class="ballot__type">
                        {{ typeLabel[election.type] ?? election.name }}
                    </span>

                    <span class="ballot__name">
                        {{ election.name }}
                    </span>
                </div>

                <span class="ballot__status">
                    <span
                        class="ballot__dot"
                        :class="`is-${election.status}`"
                    ></span>

                    {{ statusLabel[election.status] }}
                </span>
            </div>
        </section>

        <!-- =====================================================
             ANNOUNCEMENT
        ====================================================== -->
        <section
            v-if="latestAnnouncement"
            class="announce"
        >
            <div class="announce__heading">

                <div>
                    <span class="announce__number">
                        02
                    </span>

                    <h2>
                        Pengumuman
                    </h2>
                </div>

                <a
                    href="/pengumuman"
                    class="announce__all"
                >
                    Semua pengumuman
                </a>
            </div>

            <article class="announce__body">

                <div
                    v-if="announcementImage"
                    class="announce__img-wrapper"
                >
                    <img
                        :src="announcementImage"
                        :alt="latestAnnouncement.title"
                        class="announce__img"
                    />
                </div>

                <div class="announce__content">

                    <span class="announce__label">
                        Pengumuman terbaru
                    </span>

                    <h3 class="announce__title">
                        {{ latestAnnouncement.title }}
                    </h3>

                    <p class="announce__text">
                        {{ announcementExcerpt }}
                    </p>

                    <a
                        href="/pengumuman"
                        class="announce__link"
                    >
                        Baca selengkapnya

                        <span>
                            ↗
                        </span>
                    </a>
                </div>
            </article>
        </section>

        <!-- =====================================================
             FINAL CTA
        ====================================================== -->
        <section class="closing">

            <span class="closing__number">
                03
            </span>

            <div class="closing__content">

                <p class="closing__eyebrow">
                    Hak suara
                </p>

                <h2>
                    Pilih dengan
                    <em>pertimbangan.</em>
                </h2>

                <p>
                    Luangkan waktu untuk mengenal pilihanmu.
                    Kemudian gunakan hak suara dengan bertanggung jawab.
                </p>

                <a
                    href="/vote/access"
                    class="closing__cta"
                >
                    Mulai memilih

                    <span>
                        ↗
                    </span>
                </a>
            </div>
        </section>

        <!-- =====================================================
             FOOTER
        ====================================================== -->
        <footer class="footer">
            <span>
                Panitia Pemilihan OSIS &amp; MPK
            </span>

            <span>
                {{ new Date().getFullYear() }}
            </span>
        </footer>
    </div>
</template>

<style scoped>

/* =========================================================
   ROOT
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

    font-family:
        'Inter',
        'Plus Jakarta Sans',
        system-ui,
        sans-serif;

    -webkit-font-smoothing: antialiased;
}

.page *,
.page *::before,
.page *::after {
    box-sizing: border-box;
}

.page a {
    -webkit-tap-highlight-color: transparent;
}


/* =========================================================
   NAV
========================================================= */

.nav {
    width: min(1180px, calc(100% - 48px));
    min-height: 76px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav__logo {
    color: var(--ink);

    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: 20px;
    font-weight: 600;

    letter-spacing: -0.025em;

    text-decoration: none;
}

.nav__links {
    display: flex;
    align-items: center;
    gap: 30px;
}

.nav__links a {
    position: relative;

    color: var(--ink-muted);

    font-size: 13px;
    font-weight: 500;

    text-decoration: none;

    transition: color 180ms ease;
}

.nav__links a:not(.nav__cta)::after {
    content: '';

    position: absolute;
    left: 0;
    bottom: -5px;

    width: 0;
    height: 1px;

    background: var(--ink);

    transition: width 180ms ease;
}

.nav__links a:not(.nav__cta):hover {
    color: var(--ink);
}

.nav__links a:not(.nav__cta):hover::after {
    width: 100%;
}

.nav__cta {
    padding: 10px 17px;

    border: 1px solid var(--ink);
    border-radius: 999px;

    color: var(--ink) !important;

    transition:
        background 180ms ease,
        color 180ms ease;
}

.nav__cta:hover {
    background: var(--ink);
    color: var(--paper) !important;
}


/* =========================================================
   HAMBURGER BUTTON
========================================================= */

.nav__menu-button {
    display: none;

    align-items: center;
    gap: 10px;

    padding: 8px 0;

    border: 0;
    background: transparent;

    color: var(--ink);

    cursor: pointer;

    font-family: inherit;
}

.nav__menu-label {
    font-size: 11px;
    font-weight: 700;

    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.nav__menu-icon {
    position: relative;

    width: 22px;
    height: 16px;

    display: flex;
    flex-direction: column;
    justify-content: center;

    gap: 5px;
}

.nav__menu-icon span {
    display: block;

    width: 22px;
    height: 1px;

    background: var(--ink);

    transform-origin: center;

    transition:
        transform 220ms ease,
        width 220ms ease;
}

.nav__menu-button.is-open .nav__menu-icon span:first-child {
    transform: translateY(3px) rotate(45deg);
}

.nav__menu-button.is-open .nav__menu-icon span:last-child {
    transform: translateY(-3px) rotate(-45deg);
}


/* =========================================================
   MOBILE / TABLET MENU
========================================================= */

.mobile-menu {
    position: fixed;
    inset: 0;

    z-index: 1000;
}

.mobile-menu__overlay {
    position: absolute;
    inset: 0;

    width: 100%;
    height: 100%;

    padding: 0;

    border: 0;

    background: rgba(24, 23, 22, 0.35);

    cursor: pointer;
}

.mobile-menu__panel {
    position: absolute;

    top: 0;
    right: 0;

    width: min(430px, 88vw);
    min-height: 100%;

    display: flex;
    flex-direction: column;

    padding: 28px 28px 25px;

    background: var(--paper);

    border-left: 1px solid var(--line);

    box-shadow:
        -20px 0 50px rgba(24, 23, 22, 0.08);
}

.mobile-menu__top {
    min-height: 48px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    border-bottom: 1px solid var(--line);
}

.mobile-menu__top > span {
    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.mobile-menu__close {
    position: relative;

    width: 34px;
    height: 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 0;

    background: transparent;

    cursor: pointer;
}

.mobile-menu__close span {
    position: absolute;

    width: 20px;
    height: 1px;

    background: var(--ink);
}

.mobile-menu__close span:first-child {
    transform: rotate(45deg);
}

.mobile-menu__close span:last-child {
    transform: rotate(-45deg);
}

.mobile-menu__links {
    display: flex;
    flex-direction: column;

    margin-top: 30px;
}

.mobile-menu__links a {
    min-height: 82px;

    display: grid;

    grid-template-columns:
        38px
        minmax(0, 1fr)
        auto;

    align-items: center;

    gap: 14px;

    border-bottom: 1px solid var(--line);

    color: var(--ink);

    text-decoration: none;

    transition:
        padding 180ms ease,
        background 180ms ease;
}

.mobile-menu__links a:hover {
    padding-left: 8px;
    padding-right: 8px;

    background: rgba(255, 253, 249, 0.65);
}

.mobile-menu__number {
    color: var(--ink-faint);

    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: 13px;
}

.mobile-menu__text {
    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: 26px;
    font-weight: 500;

    letter-spacing: -0.025em;
}

.mobile-menu__arrow {
    color: var(--ink-muted);

    font-size: 18px;

    transition: transform 180ms ease;
}

.mobile-menu__links a:hover .mobile-menu__arrow {
    transform: translate(2px, -2px);
}

.mobile-menu__vote {
    margin-top: 18px;

    padding-left: 17px;
    padding-right: 17px;

    background: var(--ink);

    border-bottom: 0 !important;

    color: var(--paper) !important;
}

.mobile-menu__vote:hover {
    background: #302e2b !important;
}

.mobile-menu__vote .mobile-menu__number {
    color: #77736d;
}

.mobile-menu__vote .mobile-menu__arrow {
    color: var(--paper);
}

.mobile-menu__footer {
    margin-top: auto;

    padding-top: 25px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;

    letter-spacing: 0.08em;
    text-transform: uppercase;
}


/* =========================================================
   MENU TRANSITION
========================================================= */

.menu-enter-active,
.menu-leave-active {
    transition: opacity 220ms ease;
}

.menu-enter-active .mobile-menu__panel,
.menu-leave-active .mobile-menu__panel {
    transition:
        transform 300ms cubic-bezier(.22, 1, .36, 1);
}

.menu-enter-from,
.menu-leave-to {
    opacity: 0;
}

.menu-enter-from .mobile-menu__panel,
.menu-leave-to .mobile-menu__panel {
    transform: translateX(100%);
}


/* =========================================================
   HERO
========================================================= */

.hero {
    width: min(1180px, calc(100% - 48px));

    min-height: 650px;

    margin: 0 auto;

    padding: 82px 0 94px;

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(360px, 0.72fr);

    align-items: center;

    gap: 90px;
}

.hero__content {
    max-width: 650px;
}


/* =========================================================
   HERO TEXT
========================================================= */

.hero__eyebrow {
    margin: 0 0 24px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.13em;
    text-transform: uppercase;

    opacity: 0;
    transform: translateY(10px);
}

.hero__eyebrow span {
    margin-left: 8px;

    color: var(--ink);
}

.hero__title {
    max-width: 680px;

    margin: 0;

    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: clamp(48px, 6vw, 78px);

    font-weight: 600;

    line-height: 0.98;

    letter-spacing: -0.045em;

    opacity: 0;
    transform: translateY(10px);
}

.hero__title em {
    font-weight: 400;
}

.hero__desc {
    max-width: 470px;

    margin: 30px 0 0;

    color: var(--ink-muted);

    font-size: 15px;

    line-height: 1.7;

    opacity: 0;
    transform: translateY(10px);
}

.hero__actions {
    display: flex;
    align-items: center;

    gap: 24px;

    margin-top: 38px;

    opacity: 0;
    transform: translateY(10px);
}

.hero__cta {
    min-height: 48px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 20px;

    padding: 0 20px 0 23px;

    background: var(--ink);

    color: var(--paper);

    font-size: 13px;
    font-weight: 600;

    text-decoration: none;

    transition:
        transform 180ms ease,
        background 180ms ease;
}

.hero__cta:hover {
    background: #302e2b;

    transform: translateY(-2px);
}

.hero__cta-arrow {
    font-size: 17px;
}

.hero__link {
    color: var(--ink-muted);

    font-size: 13px;
    font-weight: 600;

    text-decoration: none;

    border-bottom: 1px solid var(--line);

    padding-bottom: 3px;

    transition:
        color 180ms ease,
        border-color 180ms ease;
}

.hero__link:hover {
    color: var(--ink);

    border-color: var(--ink);
}


/* =========================================================
   HERO PHOTO SLIDESHOW
========================================================= */

.hero__visual {
    width: 100%;
    max-width: 440px;

    justify-self: end;

    opacity: 0;

    transform: translateY(15px);

    transition:
        opacity 700ms cubic-bezier(.22, 1, .36, 1),
        transform 700ms cubic-bezier(.22, 1, .36, 1);
}

.hero__photo {
    position: relative;

    width: 100%;

    aspect-ratio: 0.78;

    overflow: hidden;

    background: var(--paper-dark);
}

.hero__photo img {
    position: absolute;

    inset: 0;

    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    filter: saturate(0.88);

    will-change: opacity, transform;
}


/* =========================================================
   PHOTO TRANSITION
========================================================= */

.photo-fade-enter-active,
.photo-fade-leave-active {
    transition:
        opacity 700ms cubic-bezier(.22, 1, .36, 1),
        transform 900ms cubic-bezier(.22, 1, .36, 1);
}

.photo-fade-enter-from {
    opacity: 0;

    transform: scale(1.025);
}

.photo-fade-enter-to {
    opacity: 1;

    transform: scale(1);
}

.photo-fade-leave-from {
    opacity: 1;

    transform: scale(1);
}

.photo-fade-leave-to {
    opacity: 0;

    transform: scale(0.99);
}


/* =========================================================
   PHOTO CAPTION
========================================================= */

.hero__photo-caption {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding-top: 11px;

    border-top: 1px solid var(--line);

    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 700;

    letter-spacing: 0.09em;
    text-transform: uppercase;
}


/* =========================================================
   HERO ENTRANCE
========================================================= */

.hero.is-ready .hero__eyebrow {
    animation:
        rise
        600ms
        cubic-bezier(.22, 1, .36, 1)
        0.05s
        forwards;
}

.hero.is-ready .hero__title {
    animation:
        rise
        650ms
        cubic-bezier(.22, 1, .36, 1)
        0.12s
        forwards;
}

.hero.is-ready .hero__desc {
    animation:
        rise
        650ms
        cubic-bezier(.22, 1, .36, 1)
        0.22s
        forwards;
}

.hero.is-ready .hero__actions {
    animation:
        rise
        650ms
        cubic-bezier(.22, 1, .36, 1)
        0.32s
        forwards;
}

.hero.is-ready .hero__visual {
    opacity: 1;

    transform: translateY(0);
}

@keyframes rise {
    to {
        opacity: 1;

        transform: translateY(0);
    }
}


/* =========================================================
   BALLOT
========================================================= */

.ballot {
    width: min(1180px, calc(100% - 48px));

    margin: 0 auto;

    border-top: 1px solid var(--line);
}

.ballot__row {
    min-height: 112px;

    display: grid;

    grid-template-columns:
        70px
        minmax(0, 1fr)
        190px
        20px;

    align-items: center;

    gap: 25px;

    border-bottom: 1px solid var(--line);

    transition:
        padding 180ms ease,
        background 180ms ease;
}

.ballot__row:hover {
    padding-left: 10px;
    padding-right: 10px;

    background: rgba(255, 253, 249, 0.55);
}

.ballot__num {
    color: var(--ink-faint);

    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: 17px;
}

.ballot__info {
    display: flex;
    flex-direction: column;

    gap: 5px;
}

.ballot__type {
    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.09em;
    text-transform: uppercase;
}

.ballot__name {
    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: 23px;
    font-weight: 500;

    letter-spacing: -0.025em;
}

.ballot__status {
    display: flex;
    align-items: center;

    gap: 8px;

    color: var(--ink-muted);

    font-size: 11px;
    font-weight: 600;

    white-space: nowrap;
}

.ballot__dot {
    width: 6px;
    height: 6px;

    flex: 0 0 auto;

    border-radius: 50%;

    background: #aaa59e;
}

.ballot__dot.is-open {
    background: #315c3b;
}

.ballot__dot.is-closed {
    background: #934a3d;
}

.ballot__dot.is-result_published {
    background: var(--ink);
}

.ballot__dot.is-scheduled {
    background: #8b7144;
}


/* =========================================================
   ANNOUNCEMENT
========================================================= */

.announce {
    width: min(1180px, calc(100% - 48px));

    margin: 0 auto;

    padding: 105px 0;
}

.announce__heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    gap: 30px;

    margin-bottom: 42px;
}

.announce__heading > div {
    display: flex;
    align-items: baseline;

    gap: 18px;
}

.announce__number {
    color: #9c978f;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.1em;
}

.announce__heading h2 {
    margin: 0;

    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: 34px;
    font-weight: 600;

    line-height: 1;

    letter-spacing: -0.035em;
}

.announce__all {
    color: var(--ink-muted);

    font-size: 12px;
    font-weight: 600;

    text-decoration: none;

    border-bottom: 1px solid var(--line);

    padding-bottom: 3px;
}

.announce__all:hover {
    color: var(--ink);

    border-color: var(--ink);
}

.announce__body {
    display: grid;

    grid-template-columns:
        minmax(280px, 0.9fr)
        minmax(0, 1fr);

    min-height: 360px;

    border-top: 1px solid var(--ink);
    border-bottom: 1px solid var(--line);
}

.announce__img-wrapper {
    min-height: 360px;

    overflow: hidden;

    background: var(--paper-dark);
}

.announce__img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    filter: saturate(0.82);
}

.announce__content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;

    padding: 55px;
}

.announce__label {
    margin-bottom: 18px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.11em;
    text-transform: uppercase;
}

.announce__title {
    max-width: 520px;

    margin: 0;

    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: clamp(30px, 4vw, 46px);

    font-weight: 500;

    line-height: 1.02;

    letter-spacing: -0.035em;
}

.announce__text {
    max-width: 500px;

    margin: 22px 0 28px;

    color: var(--ink-muted);

    font-size: 14px;

    line-height: 1.7;
}

.announce__link {
    display: inline-flex;
    align-items: center;

    gap: 7px;

    color: var(--ink);

    font-size: 13px;
    font-weight: 600;

    text-decoration: none;

    border-bottom: 1px solid var(--ink);

    padding-bottom: 3px;
}


/* =========================================================
   CLOSING
========================================================= */

.closing {
    width: min(1180px, calc(100% - 48px));

    margin: 0 auto 90px;

    padding: 90px 80px;

    display: grid;

    grid-template-columns: 100px 1fr;

    gap: 40px;

    background: var(--ink);

    color: var(--paper);
}

.closing__number {
    color: #77736d;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.1em;
}

.closing__content {
    max-width: 680px;
}

.closing__eyebrow {
    margin: 0 0 24px;

    color: #8f8a83;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.closing h2 {
    margin: 0;

    font-family:
        'Fraunces',
        Georgia,
        serif;

    font-size: clamp(40px, 5vw, 62px);

    font-weight: 500;

    line-height: 1;

    letter-spacing: -0.04em;
}

.closing h2 em {
    font-weight: 400;
}

.closing__content > p:not(.closing__eyebrow) {
    max-width: 480px;

    margin: 24px 0 32px;

    color: #aaa59e;

    font-size: 14px;

    line-height: 1.7;
}

.closing__cta {
    min-height: 48px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 20px;

    padding: 0 20px 0 23px;

    background: var(--paper);

    color: var(--ink);

    font-size: 13px;
    font-weight: 600;

    text-decoration: none;

    transition:
        transform 180ms ease,
        background 180ms ease;
}

.closing__cta:hover {
    background: var(--white);

    transform: translateY(-2px);
}

.closing__cta span {
    font-size: 17px;
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
   TABLET
========================================================= */

@media (max-width: 900px) {

    /* HIDE DESKTOP NAV */
    .nav__links {
        display: none;
    }

    /* SHOW HAMBURGER */
    .nav__menu-button {
        display: flex;
    }

    .hero {
        grid-template-columns:
            minmax(0, 1fr)
            minmax(300px, 0.7fr);

        gap: 50px;

        padding-top: 65px;
        padding-bottom: 75px;
    }

    .hero__title {
        font-size: clamp(43px, 7vw, 62px);
    }

    .ballot__row {
        grid-template-columns:
            55px
            minmax(0, 1fr)
            160px
            20px;

        gap: 16px;
    }

    .announce__content {
        padding: 40px;
    }

    .closing {
        padding: 65px 50px;
    }
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 680px) {

    .nav {
        width: calc(100% - 32px);

        min-height: 64px;
    }

    .nav__menu-label {
        display: none;
    }

    .nav__menu-button {
        gap: 0;
    }

    .mobile-menu__panel {
        width: min(430px, 92vw);

        padding:
            22px
            22px
            25px;
    }

    .mobile-menu__text {
        font-size: 23px;
    }

    .hero {
        width: calc(100% - 32px);

        display: flex;
        flex-direction: column;
        align-items: stretch;

        gap: 48px;

        padding: 60px 0 55px;

        min-height: auto;
    }

    .hero__title {
        font-size: clamp(43px, 13vw, 62px);

        line-height: 0.96;
    }

    .hero__desc {
        margin-top: 24px;

        font-size: 14px;

        line-height: 1.65;
    }

    .hero__actions {
        margin-top: 30px;

        align-items: flex-start;

        flex-direction: column;

        gap: 18px;
    }

    .hero__cta {
        width: 100%;
    }

    .hero__visual {
        width: 100%;

        max-width: none;
    }

    .hero__photo {
        aspect-ratio: 0.86;
    }

    .ballot {
        width: calc(100% - 32px);
    }

    .ballot__row {
        min-height: 130px;

        grid-template-columns:
            38px
            minmax(0, 1fr)
            20px;

        gap: 12px;

        position: relative;
    }

    .ballot__info {
        grid-column: 2;
        grid-row: 1;
    }

    .ballot__status {
        grid-column: 2;
        grid-row: 2;

        margin-top: -5px;
    }

    .ballot__num {
        grid-column: 1;
        grid-row: 1 / span 2;
    }

    .ballot__row > .ballot__status {
        align-self: start;
    }

    .ballot__name {
        font-size: 21px;
    }

    .announce {
        width: calc(100% - 32px);

        padding: 72px 0;
    }

    .announce__heading {
        align-items: flex-start;

        flex-direction: column;

        gap: 18px;

        margin-bottom: 30px;
    }

    .announce__heading > div {
        gap: 13px;
    }

    .announce__heading h2 {
        font-size: 30px;
    }

    .announce__body {
        display: flex;

        flex-direction: column;
    }

    .announce__img-wrapper {
        min-height: 260px;

        aspect-ratio: 1.25;
    }

    .announce__content {
        padding: 35px 24px 40px;
    }

    .announce__title {
        font-size: 34px;
    }

    .announce__text {
        font-size: 13px;
    }

    .closing {
        width: calc(100% - 32px);

        margin-bottom: 60px;

        padding: 45px 24px;

        display: block;
    }

    .closing__number {
        display: block;

        margin-bottom: 45px;
    }

    .closing h2 {
        font-size: 43px;
    }

    .closing__content > p:not(.closing__eyebrow) {
        font-size: 13px;
    }

    .closing__cta {
        width: 100%;
    }

    .footer {
        width: calc(100% - 32px);

        padding-bottom: 30px;

        gap: 14px;
    }
}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 390px) {

    .hero {
        padding-top: 48px;
    }

    .hero__title {
        font-size: 42px;
    }

    .hero__photo {
        aspect-ratio: 0.82;
    }

    .ballot__name {
        font-size: 19px;
    }

    .announce__title {
        font-size: 30px;
    }

    .closing h2 {
        font-size: 38px;
    }

    .mobile-menu__text {
        font-size: 21px;
    }
}


/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {

    .hero__eyebrow,
    .hero__title,
    .hero__desc,
    .hero__actions,
    .hero__visual {
        animation: none !important;

        opacity: 1 !important;

        transform: none !important;
    }

    .photo-fade-enter-active,
    .photo-fade-leave-active,
    .menu-enter-active,
    .menu-leave-active,
    .menu-enter-active .mobile-menu__panel,
    .menu-leave-active .mobile-menu__panel {
        transition: none !important;
    }
}
</style>