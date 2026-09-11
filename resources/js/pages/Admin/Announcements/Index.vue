<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  announcements: Array,
})

const showForm = ref(false)
const editing = ref(null)

const form = useForm({
  title: '',
  content: '',
  image: null,
  status: 'draft',
})

function openCreate() {
  editing.value = null
  form.reset()
  showForm.value = true
}

function openEdit(a) {
  editing.value = a
  form.title = a.title
  form.content = a.content
  form.status = a.status
  form.image = null
  showForm.value = true
}

function submit() {
  if (editing.value) {
    form.transform((d) => ({ ...d, _method: 'post' })).post(`/admin/announcements/${editing.value.id}`, {
      forceFormData: true,
      onSuccess: () => (showForm.value = false),
    })
  } else {
    form.post('/admin/announcements', {
      forceFormData: true,
      onSuccess: () => (showForm.value = false),
    })
  }
}

function destroy(a) {
  if (confirm(`Hapus pengumuman "${a.title}"?`)) {
    form.delete(`/admin/announcements/${a.id}`)
  }
}

function closeForm() {
  showForm.value = false
}

// Format tanggal
function formatDate(dateString) {
  if (!dateString) return '—'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}
</script>

<template>
  <div class="announcements">

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <header class="announcements__header">
      <div class="announcements__header-left">
        <p class="announcements__eyebrow">Panel Admin</p>

        <h1 class="announcements__title">
          Manajemen <em>Pengumuman.</em>
        </h1>

        <p class="announcements__desc">
          Publikasikan informasi resmi terkait pemilihan OSIS dan MPK
          — hasil, jadwal, dan pengumuman penting lainnya.
        </p>
      </div>

      <div class="announcements__header-right">
        <button
          type="button"
          class="announcements__create"
          @click="openCreate"
        >
          <span class="announcements__create-icon">+</span>
          <span class="announcements__create-label">Tambah Pengumuman</span>
        </button>
      </div>
    </header>

    <!-- =====================================================
         META ROW
    ====================================================== -->

    <div class="announcements__meta">
      <span class="announcements__meta-step">
        <span class="announcements__meta-number">05</span>
        Daftar Pengumuman
      </span>
      <span class="announcements__meta-count">
        {{ announcements?.length ?? 0 }} pengumuman
      </span>
    </div>

    <!-- =====================================================
         FLASH
    ====================================================== -->

    <div
      v-if="$page.props.flash?.success"
      class="announcements__flash"
    >
      <span class="announcements__flash-dot"></span>
      {{ $page.props.flash.success }}
    </div>

    <!-- =====================================================
         EMPTY STATE
    ====================================================== -->

    <div
      v-if="!announcements?.length"
      class="announcements__empty"
    >
      <span class="announcements__empty-icon">📢</span>
      <h3 class="announcements__empty-title">Belum ada pengumuman</h3>
      <p class="announcements__empty-text">
        Tambahkan pengumuman pertama untuk dipublikasikan kepada
        seluruh pemilih.
      </p>
      <button
        type="button"
        class="announcements__empty-cta"
        @click="openCreate"
      >
        + Tambah Pengumuman Pertama
      </button>
    </div>

    <!-- =====================================================
         LIST PENGUMUMAN
    ====================================================== -->

    <div v-else class="announcements__list">

      <article
        v-for="a in announcements"
        :key="a.id"
        class="announcement-card"
      >

        <!-- THUMB -->
        <div class="announcement-card__thumb">
          <img
            v-if="a.image"
            :src="`/storage/${a.image}`"
            :alt="a.title"
            class="announcement-card__img"
          />
          <div v-else class="announcement-card__placeholder">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.4"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
              <circle cx="12" cy="13" r="3" />
            </svg>
          </div>

          <span
            class="announcement-card__status"
            :class="a.status === 'published'
              ? 'announcement-card__status--published'
              : 'announcement-card__status--draft'"
          >
            <span class="announcement-card__status-dot"></span>
            {{ a.status === 'published' ? 'Published' : 'Draft' }}
          </span>
        </div>

        <!-- BODY -->
        <div class="announcement-card__body">

          <div class="announcement-card__top">
            <span class="announcement-card__date">
              {{ formatDate(a.created_at) }}
            </span>

            <span class="announcement-card__id">
              #{{ String(a.id).padStart(3, '0') }}
            </span>
          </div>

          <h3 class="announcement-card__title">
            {{ a.title }}
          </h3>

          <p class="announcement-card__excerpt">
            {{ a.content }}
          </p>

        </div>

        <!-- ACTIONS -->
        <div class="announcement-card__actions">

          <button
            type="button"
            class="announcement-card__btn announcement-card__btn--edit"
            @click="openEdit(a)"
          >
            Edit
          </button>

          <button
            type="button"
            class="announcement-card__btn announcement-card__btn--delete"
            @click="destroy(a)"
          >
            Hapus
          </button>

        </div>

      </article>

    </div>

    <!-- =====================================================
         MODAL FORM
    ====================================================== -->

    <Transition name="modal-fade">
      <div
        v-if="showForm"
        class="modal"
        @click.self="closeForm"
      >
        <form
          class="modal__card"
          @submit.prevent="submit"
        >

          <!-- MODAL TOP -->
          <div class="modal__top">
            <span class="modal__eyebrow">
              {{ editing ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}
            </span>

            <button
              type="button"
              class="modal__close"
              aria-label="Tutup"
              @click="closeForm"
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

          <!-- MODAL BODY -->
          <div class="modal__body">

            <!-- JUDUL -->
            <div class="field">
              <label class="field__label">Judul</label>
              <input
                v-model="form.title"
                type="text"
                class="field__input"
                placeholder="Contoh: Hasil Pemilihan Ketua OSIS 2026/2027"
              />
            </div>

            <!-- ISI -->
            <div class="field">
              <label class="field__label">Isi Pengumuman</label>
              <textarea
                v-model="form.content"
                rows="6"
                class="field__input field__input--textarea"
                placeholder="Tuliskan isi pengumuman secara lengkap..."
              ></textarea>
            </div>

            <!-- GAMBAR -->
            <div class="field">
              <label class="field__label">
                Gambar
                <span class="field__label-optional">(opsional)</span>
              </label>

              <label class="field__file">
                <input
                  type="file"
                  class="field__file-input"
                  @change="(e) => (form.image = e.target.files[0])"
                />

                <span class="field__file-icon">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <path d="M17 8l-5-5-5 5" />
                    <path d="M12 3v12" />
                  </svg>
                </span>

                <span class="field__file-label">
                  {{ form.image ? form.image.name : 'Pilih gambar untuk diunggah' }}
                </span>
              </label>
            </div>

            <!-- STATUS -->
            <div class="field">
              <label class="field__label">Status</label>

              <div class="field__options">
                <label
                  class="field__option"
                  :class="{ 'field__option--active': form.status === 'draft' }"
                >
                  <input
                    v-model="form.status"
                    type="radio"
                    value="draft"
                    class="field__option-input"
                  />
                  <span class="field__option-dot field__option-dot--draft"></span>
                  <span class="field__option-text">Draft</span>
                  <span class="field__option-desc">Simpan sebagai draft</span>
                </label>

                <label
                  class="field__option"
                  :class="{ 'field__option--active': form.status === 'published' }"
                >
                  <input
                    v-model="form.status"
                    type="radio"
                    value="published"
                    class="field__option-input"
                  />
                  <span class="field__option-dot field__option-dot--published"></span>
                  <span class="field__option-text">Published</span>
                  <span class="field__option-desc">Tampil di halaman publik</span>
                </label>
              </div>
            </div>

          </div>

          <!-- MODAL ACTIONS -->
          <div class="modal__actions">
            <button
              type="button"
              class="modal__cancel"
              @click="closeForm"
            >
              Batalkan
            </button>

            <button
              type="submit"
              class="modal__submit"
              :disabled="form.processing"
            >
              <span>
                {{ form.processing ? 'Menyimpan...' : 'Simpan Pengumuman' }}
              </span>
              <span class="modal__submit-icon">↗</span>
            </button>
          </div>

        </form>
      </div>
    </Transition>

  </div>
</template>

<style scoped>
/* =========================================================
   ROOT — TEMA EDITORIAL
========================================================= */

.announcements {
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

  margin-left: auto;
  margin-right: auto;

  padding: 32px 40px 64px;

  color: var(--ink);

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  -webkit-font-smoothing: antialiased;
}

.announcements *,
.announcements *::before,
.announcements *::after {
  box-sizing: border-box;
}

.announcements button,
.announcements a {
  -webkit-tap-highlight-color: transparent;
}


/* =========================================================
   HEADER
========================================================= */

.announcements__header {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;

  gap: 32px;

  padding: 8px 0 32px;

  border-bottom: 1px solid var(--ink);
}

.announcements__header-left {
  max-width: 640px;
}

.announcements__eyebrow {
  margin: 0 0 14px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.13em;
  text-transform: uppercase;
}

.announcements__title {
  margin: 0;

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: clamp(36px, 5vw, 52px);
  font-weight: 600;
  line-height: 1.0;
  letter-spacing: -0.04em;

  color: var(--ink);
}

.announcements__title em {
  font-weight: 400;
}

.announcements__desc {
  margin: 16px 0 0;

  color: var(--ink-muted);

  font-size: 13px;
  line-height: 1.75;

  max-width: 540px;
}

.announcements__header-right {
  display: flex;
  align-items: flex-end;
}

/* ----- CREATE BUTTON ----- */

.announcements__create {
  display: inline-flex;
  align-items: center;
  gap: 10px;

  min-height: 48px;

  padding: 0 22px;

  background: var(--ink);
  color: var(--paper);

  border: 0;

  cursor: pointer;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

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

.announcements__create:hover {
  background: #302e2b;
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(24, 23, 22, 0.15);
}

.announcements__create-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;

  font-size: 18px;
  line-height: 1;
  font-weight: 500;
}

.announcements__create-label {
  white-space: nowrap;
}


/* =========================================================
   META ROW
========================================================= */

.announcements__meta {
  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 20px;

  padding: 20px 0 28px;

  border-bottom: 1px solid var(--line);
}

.announcements__meta-step {
  display: inline-flex;
  align-items: center;
  gap: 10px;

  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.announcements__meta-number {
  color: var(--ink-faint);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 17px;
  font-weight: 500;
  letter-spacing: 0;
}

.announcements__meta-count {
  color: var(--ink-faint);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}


/* =========================================================
   FLASH
========================================================= */

.announcements__flash {
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

.announcements__flash-dot {
  width: 7px;
  height: 7px;

  flex: 0 0 auto;

  border-radius: 50%;

  background: var(--green);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.announcements__empty {
  display: flex;
  flex-direction: column;
  align-items: center;

  margin-top: 40px;
  padding: 60px 24px;

  background: var(--white);

  border: 1px solid var(--line);

  text-align: center;
}

.announcements__empty-icon {
  font-size: 3rem;

  margin-bottom: 16px;
}

.announcements__empty-title {
  margin: 0 0 8px;

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 22px;
  font-weight: 600;
  letter-spacing: -0.02em;
}

.announcements__empty-text {
  margin: 0 0 20px;

  color: var(--ink-muted);

  font-size: 13px;
  line-height: 1.6;

  max-width: 360px;
}

.announcements__empty-cta {
  min-height: 44px;

  padding: 0 22px;

  background: var(--ink);
  color: var(--paper);

  border: 0;

  cursor: pointer;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;

  transition: background 180ms ease, transform 180ms ease;
}

.announcements__empty-cta:hover {
  background: #302e2b;
  transform: translateY(-2px);
}


/* =========================================================
   LIST
========================================================= */

.announcements__list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));

  gap: 20px;

  margin-top: 36px;
}

.announcement-card {
  display: flex;
  flex-direction: column;

  background: var(--white);

  border: 1px solid var(--line);
  border-top: 2px solid var(--ink);

  overflow: hidden;

  transition:
    transform 180ms ease,
    box-shadow 180ms ease;
}

.announcement-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 36px rgba(24, 23, 22, 0.06);
}


/* ----- THUMB ----- */

.announcement-card__thumb {
  position: relative;

  width: 100%;
  height: 160px;

  overflow: hidden;

  background: var(--paper-dark);

  border-bottom: 1px solid var(--line);
}

.announcement-card__img {
  width: 100%;
  height: 100%;

  object-fit: cover;

  transition: transform 400ms ease;
}

.announcement-card:hover .announcement-card__img {
  transform: scale(1.03);
}

.announcement-card__placeholder {
  width: 100%;
  height: 100%;

  display: flex;
  align-items: center;
  justify-content: center;

  color: var(--ink-faint);
  opacity: 0.4;
}

.announcement-card__placeholder svg {
  width: 36px;
  height: 36px;
}

.announcement-card__status {
  position: absolute;

  bottom: 12px;
  left: 12px;

  display: inline-flex;
  align-items: center;
  gap: 6px;

  padding: 4px 10px;

  background: var(--white);

  border: 1px solid var(--line);

  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;

  white-space: nowrap;
}

.announcement-card__status-dot {
  width: 6px;
  height: 6px;

  border-radius: 50%;
}

.announcement-card__status--published {
  color: var(--green);
}

.announcement-card__status--published .announcement-card__status-dot {
  background: var(--green);
}

.announcement-card__status--draft {
  color: var(--ink-muted);
}

.announcement-card__status--draft .announcement-card__status-dot {
  background: var(--ink-faint);
}


/* ----- BODY ----- */

.announcement-card__body {
  flex: 1;

  padding: 20px 22px;
}

.announcement-card__top {
  display: flex;
  align-items: center;
  justify-content: space-between;

  margin-bottom: 10px;
}

.announcement-card__date {
  color: var(--ink-faint);

  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.04em;
}

.announcement-card__id {
  color: var(--ink-faint);

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 13px;
  font-weight: 500;
}

.announcement-card__title {
  margin: 0 0 8px;

  font-family:
    'Fraunces',
    Georgia,
    serif;

  font-size: 18px;
  font-weight: 600;
  line-height: 1.25;
  letter-spacing: -0.025em;

  color: var(--ink);

  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.announcement-card__excerpt {
  margin: 0;

  color: var(--ink-muted);

  font-size: 12px;
  line-height: 1.65;

  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}


/* ----- ACTIONS ----- */

.announcement-card__actions {
  display: grid;
  grid-template-columns: 1fr 1fr;

  border-top: 1px solid var(--line);
}

.announcement-card__btn {
  min-height: 46px;

  padding: 0 16px;

  border: 0;

  background: transparent;

  cursor: pointer;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.07em;
  text-transform: uppercase;

  transition:
    background 180ms ease,
    color 180ms ease;
}

.announcement-card__btn--edit {
  color: var(--ink-muted);
  border-right: 1px solid var(--line);
}

.announcement-card__btn--edit:hover {
  background: var(--paper);
  color: var(--ink);
}

.announcement-card__btn--delete {
  color: var(--red);
}

.announcement-card__btn--delete:hover {
  background: rgba(167, 74, 61, 0.06);
  color: var(--red);
}


/* =========================================================
   MODAL
========================================================= */

.modal {
  position: fixed;
  inset: 0;

  z-index: 200;

  display: flex;
  align-items: flex-start;
  justify-content: center;

  padding: 40px 24px;

  background: rgba(24, 23, 22, 0.48);

  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);

  overflow-y: auto;
}

.modal__card {
  width: min(100%, 620px);

  background: var(--white);

  border: 1px solid var(--line);
  border-top: 2px solid var(--ink);

  box-shadow:
    0 30px 80px rgba(24, 23, 22, 0.22);

  display: flex;
  flex-direction: column;

  max-height: calc(100vh - 80px);
}

.modal__top {
  min-height: 58px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 24px;

  border-bottom: 1px solid var(--line);

  flex-shrink: 0;
}

.modal__eyebrow {
  color: var(--ink-muted);

  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.11em;
  text-transform: uppercase;
}

.modal__close {
  width: 32px;
  height: 32px;

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
  display: flex;
  flex-direction: column;
  gap: 20px;

  padding: 28px 24px 24px;

  overflow-y: auto;

  flex: 1;
}


/* =========================================================
   FORM FIELDS
========================================================= */

.field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.field__label {
  color: var(--ink);

  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.field__label-optional {
  color: var(--ink-faint);
  font-weight: 500;
  letter-spacing: 0.02em;
  text-transform: none;
  font-style: italic;
}

.field__input {
  width: 100%;
  height: auto;

  padding: 12px 14px;

  border: 1px solid var(--line);
  border-radius: 0;

  background: var(--white);
  color: var(--ink);

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;

  font-size: 13px;
  font-weight: 500;

  line-height: 1.5;

  transition:
    border-color 180ms ease,
    box-shadow 180ms ease;
}

.field__input::placeholder {
  color: var(--ink-faint);
  opacity: 0.6;
}

.field__input:focus {
  outline: none;

  border-color: var(--accent);

  box-shadow: 0 0 0 3px rgba(75, 31, 99, 0.08);
}

.field__input--textarea {
  resize: vertical;
  min-height: 120px;

  font-family:
    'Inter',
    'Plus Jakarta Sans',
    system-ui,
    sans-serif;
}

/* ----- FILE UPLOAD ----- */

.field__file {
  display: flex;
  align-items: center;
  gap: 12px;

  min-height: 56px;

  padding: 0 16px;

  border: 1px dashed var(--line);

  background: var(--white);

  cursor: pointer;

  transition:
    border-color 180ms ease,
    background 180ms ease;
}

.field__file:hover {
  border-color: var(--accent);
  background: rgba(75, 31, 99, 0.02);
}

.field__file-input {
  position: absolute;

  width: 1px;
  height: 1px;

  opacity: 0;

  pointer-events: none;
}

.field__file-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;

  width: 22px;
  height: 22px;

  flex: 0 0 auto;

  color: var(--accent);
}

.field__file-icon svg {
  width: 100%;
  height: 100%;
}

.field__file-label {
  color: var(--ink-muted);

  font-size: 12px;
  font-weight: 500;

  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}


/* ----- STATUS OPTIONS ----- */

.field__options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.field__option {
  position: relative;

  display: flex;
  flex-direction: column;
  gap: 4px;

  padding: 14px 16px;

  border: 1px solid var(--line);

  background: var(--white);

  cursor: pointer;

  transition:
    border-color 180ms ease,
    background 180ms ease;
}

.field__option:hover {
  border-color: var(--ink-faint);
}

.field__option--active {
  border-color: var(--accent);
  background: rgba(75, 31, 99, 0.03);
}

.field__option-input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.field__option-dot {
  width: 10px;
  height: 10px;

  border-radius: 50%;

  margin-bottom: 6px;
}

.field__option-dot--draft {
  background: var(--ink-faint);
}

.field__option-dot--published {
  background: var(--green);
}

.field__option-text {
  color: var(--ink);

  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.field__option-desc {
  color: var(--ink-faint);

  font-size: 10px;
  font-weight: 500;
  line-height: 1.4;
}


/* =========================================================
   MODAL ACTIONS
========================================================= */

.modal__actions {
  display: grid;
  grid-template-columns: 1fr 1.35fr;

  gap: 10px;

  padding: 20px 24px 24px;

  border-top: 1px solid var(--line);

  flex-shrink: 0;
}

.modal__cancel,
.modal__submit {
  min-height: 50px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 18px;

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

  justify-content: center;
}

.modal__cancel:hover {
  border-color: var(--ink);
  color: var(--ink);
}

.modal__submit {
  border: 0;

  background: var(--ink);
  color: var(--paper);

  transition:
    background 180ms ease,
    transform 180ms ease;
}

.modal__submit:hover:not(:disabled) {
  background: #302e2b;
  transform: translateY(-2px);
}

.modal__submit:disabled {
  opacity: 0.55;
  cursor: not-allowed;
  transform: none;
}

.modal__submit-icon {
  font-size: 17px;
}


/* =========================================================
   MODAL TRANSITION
========================================================= */

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 200ms ease;
}

.modal-fade-enter-active .modal__card,
.modal-fade-leave-active .modal__card {
  transition:
    opacity 200ms ease,
    transform 200ms cubic-bezier(.22, 1, .36, 1);
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-from .modal__card,
.modal-fade-leave-to .modal__card {
  opacity: 0;
  transform: translateY(12px) scale(0.985);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1024px) {
  .announcements {
    padding: 28px 32px 56px;
  }

  .announcements__list {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 760px) {
  .announcements {
    padding: 24px 24px 48px;
  }

  .announcements__header {
    grid-template-columns: 1fr;

    gap: 20px;

    padding: 4px 0 24px;
  }

  .announcements__header-right {
    align-items: stretch;
  }

  .announcements__create {
    width: 100%;
    justify-content: center;
  }

  .announcements__title {
    font-size: clamp(30px, 9vw, 44px);
  }

  .announcements__desc {
    font-size: 12px;
  }

  .announcements__meta {
    padding: 14px 0 20px;
  }

  .announcements__meta-count {
    display: none;
  }

  .announcements__list {
    margin-top: 28px;
    gap: 16px;
  }

  .announcement-card__thumb {
    height: 140px;
  }

  .announcement-card__body {
    padding: 18px 18px;
  }

  .announcement-card__title {
    font-size: 17px;
  }

  .modal {
    padding: 20px 16px;
  }

  .modal__card {
    max-height: calc(100vh - 40px);
  }

  .modal__body {
    padding: 22px 18px 20px;
  }

  .modal__actions {
    padding: 16px 18px 20px;
  }

  .field__options {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .announcements {
    padding: 20px 18px 40px;
  }

  .announcements__title {
    font-size: 30px;
  }

  .announcements__empty {
    padding: 40px 20px;
  }

  .announcement-card__thumb {
    height: 120px;
  }

  .announcement-card__body {
    padding: 16px 16px;
  }

  .announcement-card__title {
    font-size: 16px;
  }

  .announcement-card__excerpt {
    font-size: 11px;
  }

  .modal__actions {
    grid-template-columns: 1fr;
  }

  .modal__cancel {
    order: 2;
  }

  .modal__submit {
    order: 1;
  }
}


/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
  .announcements__create,
  .announcement-card,
  .announcement-card__img,
  .announcement-card__btn,
  .modal__submit,
  .modal__cancel,
  .announcements__empty-cta,
  .field__input,
  .field__file,
  .field__option {
    transition: none !important;
  }

  .announcements__create:hover,
  .announcement-card:hover,
  .announcement-card:hover .announcement-card__img,
  .modal__submit:hover:not(:disabled) {
    transform: none;
  }
}
</style>