<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  election: Object,
})

const selectedGroup = ref(null)
const showConfirm = ref(false)
const menuOpen = ref(false)

const form = useForm({
  candidate_group_id: null,
})

function selectGroup(group) {
  selectedGroup.value = group
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
}

function backToList() {
  selectedGroup.value = null
  showConfirm.value = false

  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
}

function askConfirm() {
  showConfirm.value = true
}

function closeConfirm() {
  showConfirm.value = false
}

function submitVote() {
  form.candidate_group_id = selectedGroup.value.id
  form.post('/vote/mpk')
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
    showConfirm.value = false
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
})

const ketua = computed(() =>
  selectedGroup.value?.candidates.find((c) => c.role === 'ketua')
)

const wakil = computed(() =>
  selectedGroup.value?.candidates.find((c) => c.role === 'wakil')
)
</script>

<template>
  <Head title="Pemilihan MPK — Pemilos" />

  <div class="page">

    <!-- =====================================================
         NAVIGATION
    ====================================================== -->

    <header class="nav">
      <a
        href="/"
        class="nav__logo"
        @click="closeMenu"
      >
        Pemilos
      </a>

      <div class="nav__right">

        <!-- HAMBURGER -->
        <button
          type="button"
          class="nav__menu-button"
          :class="{ 'is-open': menuOpen }"
          :aria-expanded="menuOpen"
          :aria-label="menuOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi'"
          @click="toggleMenu"
        >
          <span class="nav__menu-label">
            Menu
          </span>

          <span class="nav__hamburger" aria-hidden="true">
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
              class="nav__menu-link"
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

    <main class="vote">

      <div class="vote__inner">

        <!-- =================================================
             PAGE META
        ================================================== -->

        <div class="vote__meta">
          <span class="vote__step">
            <span class="vote__step-number">02</span>
            Pemilihan MPK
          </span>

          <span class="vote__year">
            Pemilos 2026/2027
          </span>
        </div>


        <!-- =================================================
             LIST CANDIDATES
        ================================================== -->

        <template v-if="!selectedGroup">

          <section class="vote__heading">
            <p class="vote__eyebrow">
              Pemilihan Ketua &amp; Wakil Ketua MPK
            </p>

            <h1 class="vote__title">
              Pilih pasangan
              <em>pilihanmu.</em>
            </h1>

            <p class="vote__desc">
              Kenali setiap pasangan calon sebelum menentukan
              satu suara. Pilihanmu akan dicatat sebagai suara
              yang sah dan tidak dapat diubah setelah dikirim.
            </p>
          </section>


          <!-- INFORMATION -->
          <div class="vote__notice">
            <div class="vote__notice-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="9" />
                <path d="M12 10v6" />
                <path d="M12 7h.01" />
              </svg>
            </div>

            <div>
              <strong>
                Pilih dengan cermat.
              </strong>

              <p>
                Klik pasangan calon untuk melihat visi, misi,
                program unggulan, serta profil ketua dan wakil.
              </p>
            </div>
          </div>


          <!-- CANDIDATE LIST -->
          <section class="candidate-list">

            <article
              v-for="group in election.candidate_groups"
              :key="group.id"
              class="candidate-card"
              @click="selectGroup(group)"
            >

              <div class="candidate-card__top">
                <span class="candidate-card__label">
                  Nomor Urut
                </span>

                <span class="candidate-card__number">
                  {{ String(group.nomor_urut).padStart(2, '0') }}
                </span>
              </div>

              <div class="candidate-card__body">
                <p class="candidate-card__eyebrow">
                  Pasangan Calon
                </p>

                <h2 class="candidate-card__name">
                  {{ group.nama_kelompok }}
                </h2>

                <div class="candidate-card__people">
                  <div>
                    <span>Ketua</span>
                    <strong>
                      {{
                        group.candidates.find(
                          (c) => c.role === 'ketua'
                        )?.name || '—'
                      }}
                    </strong>
                  </div>

                  <div>
                    <span>Wakil</span>
                    <strong>
                      {{
                        group.candidates.find(
                          (c) => c.role === 'wakil'
                        )?.name || '—'
                      }}
                    </strong>
                  </div>
                </div>
              </div>

              <div class="candidate-card__bottom">
                <span>
                  Lihat visi &amp; misi
                </span>

                <span class="candidate-card__arrow">
                  ↗
                </span>
              </div>

            </article>

          </section>

        </template>


        <!-- =================================================
             DETAIL CANDIDATE
        ================================================== -->

        <template v-else>

          <section class="detail">

            <!-- BACK -->
            <button
              type="button"
              class="detail__back"
              @click="backToList"
            >
              <span>←</span>
              Kembali ke daftar pasangan
            </button>


            <!-- DETAIL HEADER -->
            <div class="detail__header">

              <div class="detail__number">
                <span>Nomor Urut</span>

                <strong>
                  {{
                    String(selectedGroup.nomor_urut).padStart(2, '0')
                  }}
                </strong>
              </div>

              <div class="detail__heading">
                <p>
                  Pasangan Calon
                </p>

                <h1>
                  {{ selectedGroup.nama_kelompok }}
                </h1>
              </div>

            </div>


            <!-- PEOPLE -->
            <div class="detail__people">

              <div class="person">
                <span class="person__role">
                  Ketua
                </span>

                <strong class="person__name">
                  {{ ketua?.name || '—' }}
                </strong>
              </div>

              <div class="person">
                <span class="person__role">
                  Wakil
                </span>

                <strong class="person__name">
                  {{ wakil?.name || '—' }}
                </strong>
              </div>

            </div>


            <!-- VISI -->
            <section class="detail__section">
              <div class="detail__section-label">
                <span>01</span>
                Visi
              </div>

              <div class="detail__section-content">
                <p>
                  {{ selectedGroup.visi || 'Belum tersedia.' }}
                </p>
              </div>
            </section>


            <!-- MISI -->
            <section class="detail__section">
              <div class="detail__section-label">
                <span>02</span>
                Misi
              </div>

              <div class="detail__section-content">
                <p>
                  {{ selectedGroup.misi || 'Belum tersedia.' }}
                </p>
              </div>
            </section>


            <!-- PROGRAM -->
            <section
              v-if="selectedGroup.programs?.length"
              class="detail__section"
            >
              <div class="detail__section-label">
                <span>03</span>
                Program Unggulan
              </div>

              <div class="detail__section-content">
                <ol class="program-list">
                  <li
                    v-for="(program, index) in selectedGroup.programs"
                    :key="program.id"
                  >
                    <span>
                      {{ String(index + 1).padStart(2, '0') }}
                    </span>

                    <p>
                      {{ program.title }}
                    </p>
                  </li>
                </ol>
              </div>
            </section>


            <!-- ACTION -->
            <div class="detail__action">

              <button
                type="button"
                class="detail__secondary"
                @click="backToList"
              >
                Pilih pasangan lain
              </button>

              <button
                type="button"
                class="detail__primary"
                @click="askConfirm"
              >
                <span>
                  Pilih pasangan ini
                </span>

                <span>
                  ↗
                </span>
              </button>

            </div>


            <p class="detail__note">
              Pastikan kamu sudah membaca visi, misi, dan program
              sebelum memberikan suara.
            </p>

          </section>

        </template>

      </div>
    </main>


    <!-- =====================================================
         CONFIRMATION MODAL
    ====================================================== -->

    <Transition name="modal-fade">
      <div
        v-if="showConfirm"
        class="modal"
        @click.self="closeConfirm"
      >

        <div class="modal__card">

          <div class="modal__top">
            <span class="modal__eyebrow">
              Konfirmasi pilihan
            </span>

            <button
              type="button"
              class="modal__close"
              aria-label="Tutup konfirmasi"
              @click="closeConfirm"
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

          <div class="modal__body">

            <div class="modal__number">
              {{
                String(selectedGroup.nomor_urut).padStart(2, '0')
              }}
            </div>

            <p class="modal__question">
              Kamu akan memilih:
            </p>

            <h2 class="modal__candidate">
              {{ selectedGroup.nama_kelompok }}
            </h2>

            <div class="modal__people">
              <span>
                {{ ketua?.name || '—' }}
              </span>

              <span class="modal__separator">
                &amp;
              </span>

              <span>
                {{ wakil?.name || '—' }}
              </span>
            </div>

            <div class="modal__warning">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M12 3l9 17H3L12 3z" />
                <path d="M12 9v5" />
                <path d="M12 17h.01" />
              </svg>

              <p>
                Suara yang telah diberikan tidak dapat diubah.
                Pastikan pilihanmu sudah benar.
              </p>
            </div>

          </div>

          <div class="modal__actions">

            <button
              type="button"
              class="modal__cancel"
              @click="closeConfirm"
            >
              Batalkan
            </button>

            <button
              type="button"
              class="modal__confirm"
              :disabled="form.processing"
              @click="submitVote"
            >
              <span>
                {{
                  form.processing
                    ? 'Mengirim suara...'
                    : 'Ya, saya yakin'
                }}
              </span>

              <span>↗</span>
            </button>

          </div>

        </div>
      </div>
    </Transition>


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

.page button,
.page a {
  -webkit-tap-highlight-color: transparent;
}


/* =========================================================
   NAV
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
   OVERLAY
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
   VOTE
========================================================= */

.vote {
  width: min(1180px, calc(100% - 48px));

  margin: 0 auto;

  padding: 72px 0 90px;
}

.vote__inner {
  width: min(100%, 820px);

  margin: 0 auto;
}


/* =========================================================
   META
========================================================= */

.vote__meta {
  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 20px;

  margin-bottom: 38px;
  padding-bottom: 16px;

  border-bottom: 1px solid var(--line);
}

.vote__step {
  display: inline-flex;
  align-items: center;
  gap: 10px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.vote__step-number {
  color: var(--ink-faint);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 17px;
  font-weight: 500;
  letter-spacing: 0;
}

.vote__year {
  color: var(--ink-faint);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}


/* =========================================================
   HEADING
========================================================= */

.vote__heading {
  margin-bottom: 35px;
}

.vote__eyebrow {
  margin: 0 0 20px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.13em;
  text-transform: uppercase;
}

.vote__title {
  max-width: 760px;

  margin: 0;

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: clamp(48px, 7vw, 72px);
  font-weight: 600;
  line-height: 0.97;
  letter-spacing: -0.05em;
}

.vote__title em {
  font-weight: 400;
}

.vote__desc {
  max-width: 590px;

  margin: 25px 0 0;

  color: var(--ink-muted);

  font-size: 14px;
  line-height: 1.75;
}


/* =========================================================
   NOTICE
========================================================= */

.vote__notice {
  display: flex;
  align-items: flex-start;
  gap: 15px;

  margin-bottom: 28px;
  padding: 17px 18px;

  border: 1px solid rgba(75, 31, 99, 0.16);

  background: rgba(75, 31, 99, 0.045);
}

.vote__notice-icon {
  width: 40px;
  height: 40px;

  flex: 0 0 auto;

  display: flex;
  align-items: center;
  justify-content: center;

  background: rgba(75, 31, 99, 0.09);

  color: var(--accent);
}

.vote__notice-icon svg {
  width: 21px;
  height: 21px;
}

.vote__notice strong {
  display: block;

  color: var(--ink);

  font-size: 12px;
  font-weight: 700;
}

.vote__notice p {
  max-width: 620px;

  margin: 5px 0 0;

  color: var(--ink-muted);

  font-size: 11px;
  line-height: 1.65;
}


/* =========================================================
   CANDIDATE LIST
========================================================= */

.candidate-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));

  border-top: 1px solid var(--ink);
  border-left: 1px solid var(--line);
}

.candidate-card {
  position: relative;

  min-height: 340px;

  display: flex;
  flex-direction: column;

  background: var(--white);

  border-right: 1px solid var(--line);
  border-bottom: 1px solid var(--line);

  cursor: pointer;

  transition:
    background 180ms ease,
    transform 180ms ease,
    box-shadow 180ms ease;
}

.candidate-card:hover {
  z-index: 2;

  background: #ffffff;

  transform: translateY(-4px);

  box-shadow:
    0 18px 38px rgba(24, 23, 22, 0.08);
}

.candidate-card__top {
  min-height: 72px;

  display: flex;
  align-items: flex-start;
  justify-content: space-between;

  padding: 17px 20px;

  border-bottom: 1px solid var(--line);
}

.candidate-card__label {
  color: var(--ink-faint);

  font-size: 8px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.candidate-card__number {
  color: var(--accent);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 34px;
  font-weight: 500;
  line-height: 0.8;

  letter-spacing: -0.05em;
}

.candidate-card__body {
  flex: 1;

  padding: 27px 22px 25px;
}

.candidate-card__eyebrow {
  margin: 0 0 10px;

  color: var(--ink-faint);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.candidate-card__name {
  margin: 0;

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 29px;
  font-weight: 500;
  line-height: 1.03;
  letter-spacing: -0.04em;
}

.candidate-card__people {
  display: grid;
  grid-template-columns: 1fr 1fr;

  gap: 16px;

  margin-top: 28px;
}

.candidate-card__people div {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.candidate-card__people span {
  color: var(--ink-faint);

  font-size: 8px;
  font-weight: 700;
  letter-spacing: 0.09em;
  text-transform: uppercase;
}

.candidate-card__people strong {
  color: var(--ink-muted);

  font-size: 11px;
  font-weight: 600;
  line-height: 1.4;
}

.candidate-card__bottom {
  min-height: 52px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 20px;

  border-top: 1px solid var(--line);

  color: var(--ink-muted);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.07em;
  text-transform: uppercase;
}

.candidate-card__arrow {
  color: var(--ink);

  font-size: 17px;

  transition: transform 180ms ease;
}

.candidate-card:hover .candidate-card__arrow {
  transform: translate(2px, -2px);
}


/* =========================================================
   DETAIL
========================================================= */

.detail {
  padding-bottom: 10px;
}

.detail__back {
  display: inline-flex;
  align-items: center;
  gap: 8px;

  margin-bottom: 42px;
  padding: 0;

  border: 0;
  border-bottom: 1px solid var(--line);

  padding-bottom: 4px;

  background: transparent;
  color: var(--ink-muted);

  cursor: pointer;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 11px;
  font-weight: 600;

  transition:
    color 180ms ease,
    border-color 180ms ease;
}

.detail__back:hover {
  color: var(--ink);
  border-color: var(--ink);
}

.detail__back span {
  font-size: 15px;
}


/* =========================================================
   DETAIL HEADER
========================================================= */

.detail__header {
  display: grid;
  grid-template-columns: 170px minmax(0, 1fr);

  gap: 35px;

  padding-bottom: 32px;

  border-bottom: 1px solid var(--ink);
}

.detail__number {
  display: flex;
  flex-direction: column;

  padding-top: 6px;
}

.detail__number span {
  margin-bottom: 8px;

  color: var(--ink-faint);

  font-size: 8px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.detail__number strong {
  color: var(--accent);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 86px;
  font-weight: 500;
  line-height: 0.78;

  letter-spacing: -0.07em;
}

.detail__heading p {
  margin: 0 0 13px;

  color: var(--ink-muted);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.detail__heading h1 {
  max-width: 550px;

  margin: 0;

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: clamp(42px, 6vw, 62px);
  font-weight: 600;
  line-height: 0.98;
  letter-spacing: -0.045em;
}


/* =========================================================
   PEOPLE
========================================================= */

.detail__people {
  display: grid;
  grid-template-columns: 1fr 1fr;

  margin-bottom: 42px;

  border-bottom: 1px solid var(--line);
}

.person {
  display: flex;
  flex-direction: column;
  gap: 8px;

  min-height: 100px;

  padding: 23px 22px;
}

.person + .person {
  border-left: 1px solid var(--line);
}

.person__role {
  color: var(--ink-faint);

  font-size: 8px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.person__name {
  color: var(--ink);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 22px;
  font-weight: 500;
  line-height: 1.15;
  letter-spacing: -0.025em;
}


/* =========================================================
   DETAIL SECTIONS
========================================================= */

.detail__section {
  display: grid;
  grid-template-columns: 170px minmax(0, 1fr);

  gap: 35px;

  padding: 28px 0;

  border-bottom: 1px solid var(--line);
}

.detail__section-label {
  color: var(--ink-muted);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.detail__section-label span {
  display: inline-block;

  width: 28px;

  color: var(--ink-faint);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 13px;
}

.detail__section-content p {
  margin: 0;

  color: var(--ink-muted);

  font-size: 13px;
  line-height: 1.85;
}


/* =========================================================
   PROGRAM
========================================================= */

.program-list {
  margin: 0;
  padding: 0;

  list-style: none;
}

.program-list li {
  display: grid;
  grid-template-columns: 34px minmax(0, 1fr);

  gap: 14px;

  padding: 12px 0;

  border-bottom: 1px solid rgba(217, 212, 203, 0.7);
}

.program-list li:first-child {
  padding-top: 0;
}

.program-list li:last-child {
  padding-bottom: 0;
  border-bottom: 0;
}

.program-list li > span {
  color: var(--ink-faint);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 13px;
}

.program-list p {
  color: var(--ink) !important;

  font-size: 12px !important;
  line-height: 1.6 !important;
}


/* =========================================================
   DETAIL ACTION
========================================================= */

.detail__action {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr);

  gap: 12px;

  margin-top: 30px;
}

.detail__secondary,
.detail__primary {
  min-height: 52px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 19px;

  cursor: pointer;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 11px;
  font-weight: 600;

  transition:
    background 180ms ease,
    border-color 180ms ease,
    color 180ms ease,
    transform 180ms ease;
}

.detail__secondary {
  border: 1px solid var(--line);

  background: transparent;
  color: var(--ink-muted);
}

.detail__secondary:hover {
  border-color: var(--ink);
  color: var(--ink);
}

.detail__primary {
  border: 0;

  background: var(--ink);
  color: var(--paper);
}

.detail__primary:hover {
  background: #302e2b;
  transform: translateY(-2px);
}

.detail__primary span:last-child {
  font-size: 18px;
}

.detail__note {
  margin: 13px 0 0;

  color: var(--ink-faint);

  font-size: 9px;
  line-height: 1.6;

  text-align: right;
}


/* =========================================================
   MODAL
========================================================= */

.modal {
  position: fixed;
  inset: 0;

  z-index: 200;

  display: flex;
  align-items: center;
  justify-content: center;

  padding: 24px;

  background: rgba(24, 23, 22, 0.48);

  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}

.modal__card {
  width: min(100%, 490px);

  background: var(--white);

  border: 1px solid var(--line);
  border-top: 2px solid var(--ink);

  box-shadow:
    0 30px 80px rgba(24, 23, 22, 0.22);
}

.modal__top {
  min-height: 55px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 19px;

  border-bottom: 1px solid var(--line);
}

.modal__eyebrow {
  color: var(--ink-muted);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.11em;
  text-transform: uppercase;
}

.modal__close {
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

.modal__close:hover {
  border-color: var(--ink);
  background: var(--ink);
  color: var(--paper);
}

.modal__close svg {
  width: 14px;
  height: 14px;
}

.modal__body {
  padding: 30px 30px 27px;
}

.modal__number {
  margin-bottom: 24px;

  color: var(--accent);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 52px;
  font-weight: 500;
  line-height: 0.8;

  letter-spacing: -0.06em;
}

.modal__question {
  margin: 0 0 7px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 600;
}

.modal__candidate {
  margin: 0;

  color: var(--ink);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 32px;
  font-weight: 600;
  line-height: 1.05;
  letter-spacing: -0.035em;
}

.modal__people {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 7px;

  margin-top: 10px;

  color: var(--ink-muted);

  font-size: 11px;
  font-weight: 600;
}

.modal__separator {
  color: var(--ink-faint);
}

.modal__warning {
  display: flex;
  align-items: flex-start;
  gap: 10px;

  margin-top: 25px;
  padding: 13px;

  border: 1px solid rgba(167, 74, 61, 0.16);

  background: rgba(167, 74, 61, 0.045);
}

.modal__warning svg {
  width: 17px;
  height: 17px;

  flex: 0 0 auto;

  color: #a74a3d;
}

.modal__warning p {
  margin: 0;

  color: var(--ink-muted);

  font-size: 10px;
  line-height: 1.6;
}

.modal__actions {
  display: grid;
  grid-template-columns: 1fr 1.35fr;

  gap: 10px;

  padding: 0 30px 30px;
}

.modal__cancel,
.modal__confirm {
  min-height: 50px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 17px;

  cursor: pointer;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 11px;
  font-weight: 600;
}

.modal__cancel {
  border: 1px solid var(--line);

  background: transparent;
  color: var(--ink-muted);
}

.modal__cancel:hover {
  border-color: var(--ink);
  color: var(--ink);
}

.modal__confirm {
  border: 0;

  background: var(--ink);
  color: var(--paper);

  transition:
    background 180ms ease,
    transform 180ms ease;
}

.modal__confirm:hover {
  background: #302e2b;
  transform: translateY(-2px);
}

.modal__confirm:disabled {
  opacity: 0.55;

  cursor: not-allowed;

  transform: none;
}

.modal__confirm span:last-child {
  font-size: 17px;
}


/* =========================================================
   MODAL TRANSITION
========================================================= */

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 180ms ease;
}

.modal-fade-enter-active .modal__card,
.modal-fade-leave-active .modal__card {
  transition:
    opacity 180ms ease,
    transform 180ms cubic-bezier(.22, 1, .36, 1);
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-from .modal__card,
.modal-fade-leave-to .modal__card {
  opacity: 0;
  transform: translateY(10px) scale(0.985);
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

@media (max-width: 760px) {
  .candidate-list {
    grid-template-columns: 1fr;
  }

  .candidate-card {
    min-height: 300px;
  }

  .detail__header,
  .detail__section {
    grid-template-columns: 120px minmax(0, 1fr);

    gap: 24px;
  }

  .detail__number strong {
    font-size: 68px;
  }
}


/* =========================================================
   MOBILE
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

  .vote {
    width: calc(100% - 32px);

    padding: 50px 0 65px;
  }

  .vote__meta {
    margin-bottom: 32px;
  }

  .vote__year {
    display: none;
  }

  .vote__title {
    font-size: clamp(43px, 13vw, 58px);
  }

  .vote__desc {
    font-size: 13px;
  }

  .vote__notice {
    padding: 15px;
  }

  .candidate-card {
    min-height: 290px;
  }

  .candidate-card__name {
    font-size: 27px;
  }

  .detail__back {
    margin-bottom: 32px;
  }

  .detail__header {
    grid-template-columns: 1fr;

    gap: 24px;
  }

  .detail__number {
    flex-direction: row;
    align-items: flex-end;

    gap: 12px;
  }

  .detail__number span {
    margin-bottom: 7px;
  }

  .detail__number strong {
    font-size: 60px;
  }

  .detail__heading h1 {
    font-size: 43px;
  }

  .detail__people {
    grid-template-columns: 1fr;
  }

  .person {
    min-height: 82px;
    padding: 18px 0;
  }

  .person + .person {
    border-left: 0;
    border-top: 1px solid var(--line);
  }

  .detail__section {
    grid-template-columns: 1fr;

    gap: 13px;

    padding: 23px 0;
  }

  .detail__action {
    grid-template-columns: 1fr;

    gap: 8px;
  }

  .detail__secondary {
    order: 2;
  }

  .detail__primary {
    order: 1;
  }

  .detail__note {
    text-align: center;
  }

  .modal {
    align-items: flex-end;

    padding: 12px;
  }

  .modal__card {
    width: 100%;
  }

  .modal__body {
    padding: 26px 22px 23px;
  }

  .modal__actions {
    padding: 0 22px 22px;
  }

  .modal__candidate {
    font-size: 28px;
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

  .vote {
    padding-top: 42px;
  }

  .vote__title {
    font-size: 41px;
  }

  .candidate-card__top {
    padding-left: 17px;
    padding-right: 17px;
  }

  .candidate-card__body {
    padding-left: 18px;
    padding-right: 18px;
  }

  .candidate-card__people {
    grid-template-columns: 1fr;
    gap: 13px;
  }

  .candidate-card__bottom {
    padding: 0 17px;
  }

  .detail__heading h1 {
    font-size: 39px;
  }

  .modal__actions {
    grid-template-columns: 1fr;
  }

  .modal__cancel {
    order: 2;
  }

  .modal__confirm {
    order: 1;
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
  .overlay-fade-leave-active,
  .modal-fade-enter-active,
  .modal-fade-leave-active {
    transition: none !important;
  }

  .candidate-card,
  .candidate-card__arrow,
  .nav__menu-button,
  .nav__menu-close,
  .nav__hamburger span,
  .nav__menu-link,
  .detail__primary,
  .modal__confirm {
    transition: none !important;
  }
}
</style>