<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'

const form = useForm({
  class_name: '',
  nis: '',
  tanggal_lahir: '',
})

const menuOpen = ref(false)

const kelas = [
  ...Array.from({ length: 12 }, (_, i) => `X-${String.fromCharCode(65 + i)}`),
  ...Array.from({ length: 12 }, (_, i) => `XI-${String.fromCharCode(65 + i)}`),
  ...Array.from({ length: 12 }, (_, i) => `XII-${String.fromCharCode(65 + i)}`),
]

function submit() {
  form.post('/vote/access')
}

function toggleMenu() {
  menuOpen.value = !menuOpen.value
}

function closeMenu() {
  menuOpen.value = false
}

function handleKeydown(event) {
  if (event.key === 'Escape') {
    menuOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <Head title="Mulai Memilih — Pemilos" />

  <div class="page">
    <!-- =====================================================
         NAVIGATION
    ====================================================== -->
    <header class="nav">
      <a href="/" class="nav__logo" @click="closeMenu">
        Pemilos
      </a>

      <div class="nav__right">
        <a
          href="/"
          class="nav__back"
          @click="closeMenu"
        >
        </a>

        <!-- HAMBURGER -->
        <button
          type="button"
          class="nav__menu-button"
          :class="{ 'is-open': menuOpen }"
          :aria-expanded="menuOpen"
          aria-label="Buka menu navigasi"
          @click="toggleMenu"
        >
          <span class="nav__menu-label">
            Menu
          </span>

          <span class="nav__hamburger">
            <span></span>
            <span></span>
          </span>
        </button>
      </div>

      <!-- MENU PANEL -->
      <Transition name="menu-fade">
        <div
          v-if="menuOpen"
          class="nav__menu"
        >
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
            <a
              href="/"
              class="nav__menu-link"
              @click="closeMenu"
            >
              <span class="nav__menu-number">01</span>

              <span class="nav__menu-text">
                Beranda
              </span>

              <span class="nav__menu-arrow">↗</span>
            </a>

            <a
              href="/pengumuman"
              class="nav__menu-link"
              @click="closeMenu"
            >
              <span class="nav__menu-number">02</span>

              <span class="nav__menu-text">
                Pengumuman
              </span>

              <span class="nav__menu-arrow">↗</span>
            </a>

            <a
              href="/hasil/osis"
              class="nav__menu-link"
              @click="closeMenu"
            >
              <span class="nav__menu-number">03</span>

              <span class="nav__menu-text">
                Hasil OSIS
              </span>

              <span class="nav__menu-arrow">↗</span>
            </a>

            <a
              href="/hasil/mpk"
              class="nav__menu-link"
              @click="closeMenu"
            >
              <span class="nav__menu-number">04</span>

              <span class="nav__menu-text">
                Hasil MPK
              </span>

              <span class="nav__menu-arrow">↗</span>
            </a>

            <a
              href="/vote/access"
              class="nav__menu-link nav__menu-link--active"
              @click="closeMenu"
            >
              <span class="nav__menu-number">05</span>

              <span class="nav__menu-text">
                Mulai memilih
              </span>

              <span class="nav__menu-arrow">↗</span>
            </a>
          </nav>

          <div class="nav__menu-footer">
            <span>
              Pemilos 2026/2027
            </span>

            <span>
              OSIS &amp; MPK
            </span>
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
    <main class="access">
      <div class="access__inner">
        <!-- STEP -->
        <div class="access__meta">
          <span class="access__step">
            <span class="access__step-number">01</span>
            Verifikasi pemilih
          </span>

          <span class="access__year">
            Pemilos 2026/2027
          </span>
        </div>

        <!-- TITLE -->
        <section class="access__heading">
          <p class="access__eyebrow">
            Pemilihan OSIS &amp; MPK
          </p>

          <h1 class="access__title">
            Satu suara dimulai
            <em>dari sini.</em>
          </h1>

          <p class="access__desc">
            Masukkan kelas, NIS, dan tanggal lahir untuk memverifikasi
            hak suara sebelum masuk ke bilik pemilihan.
          </p>
        </section>

        <!-- SECURITY -->
        <div class="security">
          <div class="security__icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.6"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M12 3l7 3v5c0 4.6-2.9 8.1-7 10-4.1-1.9-7-5.4-7-10V6l7-3z" />
              <path d="M8.5 12l2.2 2.2 4.8-5" />
            </svg>
          </div>

          <div class="security__content">
            <strong>Data kamu tetap rahasia.</strong>

            <p>
              Data hanya digunakan untuk memvalidasi hak suara dan
              memastikan setiap siswa dapat memilih secara sah.
            </p>
          </div>
        </div>

        <!-- FORM -->
        <form
          @submit.prevent="submit"
          class="form-card"
        >
          <!-- KELAS -->
          <div class="field">
            <label for="class_name">
              Kelas
            </label>

            <div class="input-wrapper">
              <svg
                class="input-icon"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 10l9-5 9 5" />
                <path d="M5 10v9M9 10v9M15 10v9M19 10v9" />
                <path d="M3 19h18M2 10h20" />
              </svg>

              <select
                id="class_name"
                v-model="form.class_name"
                :class="{ 'has-error': form.errors.class_name }"
              >
                <option value="" disabled>
                  Pilih kelas
                </option>

                <optgroup label="Kelas X">
                  <option
                    v-for="item in kelas.filter(item => item.startsWith('X-'))"
                    :key="item"
                    :value="item"
                  >
                    {{ item }}
                  </option>
                </optgroup>

                <optgroup label="Kelas XI">
                  <option
                    v-for="item in kelas.filter(item => item.startsWith('XI-'))"
                    :key="item"
                    :value="item"
                  >
                    {{ item }}
                  </option>
                </optgroup>

                <optgroup label="Kelas XII">
                  <option
                    v-for="item in kelas.filter(item => item.startsWith('XII-'))"
                    :key="item"
                    :value="item"
                  >
                    {{ item }}
                  </option>
                </optgroup>
              </select>

              <svg
                class="select-arrow"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="m6 9 6 6 6-6" />
              </svg>
            </div>

            <p
              v-if="form.errors.class_name"
              class="error"
            >
              {{ form.errors.class_name }}
            </p>
          </div>

          <!-- NIS -->
          <div class="field">
            <label for="nis">
              Nomor Induk Siswa (NIS)
            </label>

            <div class="input-wrapper">
              <svg
                class="input-icon"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="4" y="3" width="16" height="18" rx="2" />
                <circle cx="12" cy="9" r="2.5" />
                <path d="M8 16c1-1.3 2.3-2 4-2s3 .7 4 2" />
              </svg>

              <input
                id="nis"
                v-model="form.nis"
                type="text"
                inputmode="numeric"
                autocomplete="off"
                placeholder="Masukkan NIS"
                :class="{ 'has-error': form.errors.nis }"
              />
            </div>

            <p
              v-if="form.errors.nis"
              class="error"
            >
              {{ form.errors.nis }}
            </p>
          </div>

          <!-- TANGGAL LAHIR -->
          <div class="field">
            <label for="tanggal_lahir">
              Tanggal Lahir
            </label>

            <div class="input-wrapper">
              <svg
                class="input-icon"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="5" width="18" height="16" rx="2" />
                <path d="M16 3v4M8 3v4M3 10h18" />
                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01" />
              </svg>

              <input
                id="tanggal_lahir"
                v-model="form.tanggal_lahir"
                type="date"
                :class="{ 'has-error': form.errors.tanggal_lahir }"
              />
            </div>

            <p
              v-if="form.errors.tanggal_lahir"
              class="error"
            >
              {{ form.errors.tanggal_lahir }}
            </p>
          </div>

          <!-- SUBMIT -->
          <button
            type="submit"
            :disabled="form.processing"
            class="form-card__submit"
          >
            <span>
              {{ form.processing ? 'Memvalidasi...' : 'Mulai memilih' }}
            </span>

            <span class="form-card__arrow">
              ↗
            </span>
          </button>

          <p class="form-card__note">
            Pastikan data yang kamu masukkan sudah benar sebelum melanjutkan.
          </p>
        </form>

        <!-- BACK -->
        <a
          href="/"
          class="back-link"
        >
          <span>←</span>
          Kembali ke halaman awal
        </a>
      </div>
    </main>

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
  position: relative;

  width: min(1180px, calc(100% - 48px));
  min-height: 76px;

  margin: 0 auto;

  display: flex;
  align-items: center;
  justify-content: space-between;

  z-index: 100;
}

.nav__logo {
  position: relative;
  z-index: 102;

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

.nav__right {
  position: relative;
  z-index: 102;

  display: flex;
  align-items: center;
  gap: 20px;
}

.nav__back {
  display: inline-flex;
  align-items: center;
  gap: 8px;

  color: var(--ink-muted);

  font-size: 12px;
  font-weight: 600;

  text-decoration: none;

  transition:
    color 180ms ease,
    transform 180ms ease;
}

.nav__back:hover {
  color: var(--ink);
  transform: translateX(-2px);
}

.nav__back-arrow {
  font-size: 16px;
}

/* =========================================================
   MENU BUTTON
========================================================= */

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

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

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

/* =========================================================
   MENU OVERLAY
========================================================= */

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

/* =========================================================
   MENU PANEL
========================================================= */

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

/* =========================================================
   MENU LINKS
========================================================= */

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

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 13px;
}

.nav__menu-text {
  font-family:
    'Fraunces',
    Georgia,
    serif;

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

.nav__menu-link--active {
  background: rgba(75, 31, 99, 0.045);
}

.nav__menu-link--active .nav__menu-text {
  color: var(--accent);
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

/* =========================================================
   MENU TRANSITION
========================================================= */

.menu-fade-enter-active,
.menu-fade-leave-active {
  transition:
    opacity 180ms ease,
    transform 180ms cubic-bezier(.22, 1, .36, 1);
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
   ACCESS
========================================================= */

.access {
  width: min(1180px, calc(100% - 48px));

  margin: 0 auto;

  padding: 72px 0 90px;
}

.access__inner {
  width: min(100%, 620px);

  margin: 0 auto;
}

/* =========================================================
   META
========================================================= */

.access__meta {
  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 20px;

  margin-bottom: 38px;
  padding-bottom: 16px;

  border-bottom: 1px solid var(--line);
}

.access__step {
  display: inline-flex;
  align-items: center;
  gap: 10px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;

  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.access__step-number {
  color: var(--ink-faint);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 17px;
  font-weight: 500;

  letter-spacing: 0;
}

.access__year {
  color: var(--ink-faint);

  font-size: 9px;
  font-weight: 700;

  letter-spacing: 0.1em;
  text-transform: uppercase;
}

/* =========================================================
   HEADING
========================================================= */

.access__heading {
  margin-bottom: 35px;
}

.access__eyebrow {
  margin: 0 0 20px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;

  letter-spacing: 0.13em;
  text-transform: uppercase;
}

.access__title {
  margin: 0;

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: clamp(45px, 7vw, 66px);
  font-weight: 600;

  line-height: 0.98;
  letter-spacing: -0.045em;
}

.access__title em {
  font-weight: 400;
}

.access__desc {
  max-width: 510px;

  margin: 25px 0 0;

  color: var(--ink-muted);

  font-size: 14px;
  line-height: 1.75;
}

/* =========================================================
   SECURITY
========================================================= */

.security {
  display: flex;
  align-items: flex-start;

  gap: 15px;

  margin-bottom: 24px;
  padding: 17px 18px;

  border: 1px solid rgba(75, 31, 99, 0.16);

  background: rgba(75, 31, 99, 0.045);
}

.security__icon {
  width: 40px;
  height: 40px;

  flex: 0 0 auto;

  display: flex;
  align-items: center;
  justify-content: center;

  background: rgba(75, 31, 99, 0.09);

  color: var(--accent);
}

.security__icon svg {
  width: 21px;
  height: 21px;
}

.security__content {
  padding-top: 1px;
}

.security__content strong {
  display: block;

  color: var(--ink);

  font-size: 12px;
  font-weight: 700;
}

.security__content p {
  max-width: 470px;

  margin: 5px 0 0;

  color: var(--ink-muted);

  font-size: 11px;
  line-height: 1.65;
}

/* =========================================================
   FORM CARD
========================================================= */

.form-card {
  padding: 34px;

  background: var(--white);

  border-top: 1px solid var(--ink);
  border-bottom: 1px solid var(--line);

  box-shadow:
    0 14px 35px rgba(24, 23, 22, 0.035);
}

/* =========================================================
   FIELD
========================================================= */

.field {
  margin-bottom: 22px;
}

.field label {
  display: block;

  margin-bottom: 9px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;

  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;

  left: 16px;
  top: 50%;

  width: 19px;
  height: 19px;

  color: var(--ink-faint);

  transform: translateY(-50%);

  pointer-events: none;
}

.input-wrapper input,
.input-wrapper select {
  width: 100%;
  min-height: 52px;

  padding: 0 46px 0 48px;

  border: 1px solid var(--line);
  border-radius: 0;

  background: var(--paper);
  color: var(--ink);

  outline: none;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 13px;
  font-weight: 500;

  transition:
    border-color 180ms ease,
    background 180ms ease,
    box-shadow 180ms ease;
}

.input-wrapper input::placeholder {
  color: var(--ink-faint);
}

.input-wrapper input:focus,
.input-wrapper select:focus {
  border-color: var(--ink);

  background: var(--white);

  box-shadow:
    0 0 0 3px rgba(24, 23, 22, 0.055);
}

.input-wrapper input.has-error,
.input-wrapper select.has-error {
  border-color: #a74a3d;
}

.input-wrapper select {
  appearance: none;
  -webkit-appearance: none;

  cursor: pointer;
}

.select-arrow {
  position: absolute;

  right: 17px;
  top: 50%;

  width: 15px;
  height: 15px;

  color: var(--ink-faint);

  transform: translateY(-50%);

  pointer-events: none;
}


/* =========================================================
   DATE INPUT
========================================================= */

.input-wrapper input[type='date']::-webkit-calendar-picker-indicator {
  opacity: 0.45;
  cursor: pointer;
}

/* =========================================================
   ERROR
========================================================= */

.error {
  margin: 7px 0 0;

  color: #a74a3d;

  font-size: 10px;
  font-weight: 600;
}

/* =========================================================
   SUBMIT
========================================================= */

.form-card__submit {
  width: 100%;
  min-height: 52px;

  margin-top: 5px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 19px 0 21px;

  border: 0;
  border-radius: 0;

  background: var(--ink);
  color: var(--paper);

  cursor: pointer;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 13px;
  font-weight: 600;

  transition:
    transform 180ms ease,
    background 180ms ease;
}

.form-card__submit:hover {
  background: #302e2b;
  transform: translateY(-2px);
}

.form-card__submit:disabled {
  cursor: not-allowed;
  opacity: 0.55;
  transform: none;
}

.form-card__arrow {
  font-size: 18px;
}

.form-card__note {
  margin: 13px 0 0;

  color: var(--ink-faint);

  font-size: 9px;
  line-height: 1.6;

  text-align: center;
}

/* =========================================================
   BACK LINK
========================================================= */

.back-link {
  display: flex;
  align-items: center;
  justify-content: center;

  gap: 7px;

  width: fit-content;

  margin: 27px auto 0;

  color: var(--ink-muted);

  font-size: 11px;
  font-weight: 600;

  text-decoration: none;

  border-bottom: 1px solid var(--line);

  padding-bottom: 3px;

  transition:
    color 180ms ease,
    border-color 180ms ease;
}

.back-link:hover {
  color: var(--ink);
  border-color: var(--ink);
}

.back-link span {
  font-size: 15px;
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

@media (max-width: 680px) {
  .nav {
    width: calc(100% - 32px);
    min-height: 68px;
  }

  .nav__back {
    font-size: 11px;
  }

  .nav__back-arrow {
    font-size: 15px;
  }

  .nav__right {
    gap: 11px;
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

  .nav__menu-footer {
    min-height: 45px;
  }

  .access {
    width: calc(100% - 32px);

    padding: 50px 0 65px;
  }

  .access__meta {
    margin-bottom: 32px;
  }

  .access__year {
    display: none;
  }

  .access__title {
    font-size: clamp(43px, 13vw, 58px);
  }

  .access__desc {
    font-size: 13px;
  }

  .security {
    padding: 15px;
  }

  .form-card {
    padding: 25px 20px;
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
  .nav__back {
    display: none;
  }

  .nav__menu-button {
    min-height: 35px;
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

  .access {
    padding-top: 42px;
  }

  .access__title {
    font-size: 41px;
  }

  .form-card {
    padding: 23px 17px;
  }

  .footer {
    font-size: 9px;
  }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
  .menu-fade-enter-active,
  .menu-fade-leave-active,
  .overlay-fade-enter-active,
  .overlay-fade-leave-active {
    transition: none !important;
  }

  .nav__menu-link,
  .nav__menu-button,
  .nav__menu-close,
  .nav__hamburger span,
  .nav__back {
    transition: none !important;
  }
}
</style>