<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    groups: Array,
    elections: Array,
});

const showForm = ref(false);
const editingGroup = ref(null);

const form = useForm({
    election_id: '',
    nomor_urut: '',
    nama_kelompok: '',
    model: 'pair',
    visi: '',
    misi: '',
    ketua_name: '',
    ketua_photo: null,
    wakil_name: '',
    wakil_photo: null,
    programs: [{ title: '', description: '' }],
});

function openCreate() {
    editingGroup.value = null;
    form.reset();
    form.programs = [{ title: '', description: '' }];
    showForm.value = true;
}

function openEdit(group) {
    editingGroup.value = group;
    const ketua = group.candidates.find((c) => c.role === 'ketua');
    const wakil = group.candidates.find((c) => c.role === 'wakil');

    form.election_id = group.election_id;
    form.nomor_urut = group.nomor_urut;
    form.nama_kelompok = group.nama_kelompok;
    form.model = group.model;
    form.visi = group.visi;
    form.misi = group.misi;
    form.ketua_name = ketua?.name ?? '';
    form.wakil_name = wakil?.name ?? '';
    form.ketua_photo = null;
    form.wakil_photo = null;
    form.programs = group.programs.length
        ? group.programs.map((p) => ({
              title: p.title,
              description: p.description,
          }))
        : [{ title: '', description: '' }];

    showForm.value = true;
}

function addProgram() {
    form.programs.push({ title: '', description: '' });
}

function removeProgram(i) {
    form.programs.splice(i, 1);
}

function submit() {
    if (editingGroup.value) {
        form.transform((data) => ({ ...data, _method: 'post' })).post(
            `/admin/candidates/${editingGroup.value.id}`,
            {
                forceFormData: true,
                onSuccess: () => (showForm.value = false),
            },
        );
    } else {
        form.post('/admin/candidates', {
            forceFormData: true,
            onSuccess: () => (showForm.value = false),
        });
    }
}

function destroy(group) {
    if (confirm(`Hapus kandidat "${group.nama_kelompok}"?`)) {
        form.delete(`/admin/candidates/${group.id}`);
    }
}
</script>

<template>
    <div class="candidates">
        <!-- =====================================================
         HEADER
    ====================================================== -->

        <header class="candidates__header">
            <div class="candidates__header-left">
                <p class="candidates__eyebrow">Panel Admin</p>

                <h1 class="candidates__title">Manajemen <em>Kandidat.</em></h1>

                <p class="candidates__desc">
                    Kelola pasangan calon OSIS dan MPK — termasuk visi, misi,
                    serta program unggulan masing-masing kandidat.
                </p>
            </div>

            <div class="candidates__header-right">
                <button
                    type="button"
                    class="candidates__create"
                    @click="openCreate"
                >
                    <span class="candidates__create-icon">+</span>
                    <span class="candidates__create-label"
                        >Tambah Kandidat</span
                    >
                </button>
            </div>
        </header>

        <!-- =====================================================
         META ROW
    ====================================================== -->

        <div class="candidates__meta">
            <span class="candidates__meta-step">
                <span class="candidates__meta-number">02</span>
                Daftar Kandidat
            </span>
            <span class="candidates__meta-count">
                {{ groups.length }} kandidat terdaftar
            </span>
        </div>

        <!-- =====================================================
         FLASH MESSAGE
    ====================================================== -->

        <div v-if="$page.props.flash?.success" class="candidates__flash">
            <span class="candidates__flash-dot"></span>
            {{ $page.props.flash.success }}
        </div>

        <!-- =====================================================
         EMPTY STATE
    ====================================================== -->

        <div v-if="groups.length === 0" class="candidates__empty">
            <span class="candidates__empty-icon">🗳️</span>
            <h3 class="candidates__empty-title">Belum ada kandidat</h3>
            <p class="candidates__empty-text">
                Mulai tambahkan pasangan calon untuk pemilihan OSIS dan MPK.
            </p>
            <button
                type="button"
                class="candidates__empty-cta"
                @click="openCreate"
            >
                + Tambah Kandidat Pertama
            </button>
        </div>

        <!-- =====================================================
         GRID KANDIDAT
    ====================================================== -->

        <div v-else class="candidates__grid">
            <article
                v-for="group in groups"
                :key="group.id"
                class="candidate-card"
            >
                <div class="candidate-card__top">
                    <span class="candidate-card__type">
                        {{ group.election?.type?.toUpperCase() ?? '—' }}
                    </span>

                    <span class="candidate-card__number">
                        {{ String(group.nomor_urut).padStart(2, '0') }}
                    </span>
                </div>

                <div class="candidate-card__body">
                    <p class="candidate-card__label">Pasangan Calon</p>
                    <div
                        class="candidate-card__photos"
                        v-if="group.candidates.some((c) => c.photo)"
                    >
                        <img
                            v-for="c in group.candidates"
                            :key="c.id"
                            :src="c.photo ? `/storage/${c.photo}` : ''"
                            :alt="c.name"
                            class="candidate-card__photo"
                        />
                    </div>

                    <h3 class="candidate-card__name">
                        {{ group.nama_kelompok }}
                    </h3>

                    <p class="candidate-card__people">
                        {{ group.candidates.map((c) => c.name).join(' & ') }}
                    </p>

                    <div class="candidate-card__badges">
                        <span class="candidate-card__badge">
                            {{
                                group.model === 'pair' ? 'Pasangan' : 'Individu'
                            }}
                        </span>

                        <span
                            v-if="group.programs?.length"
                            class="candidate-card__badge candidate-card__badge--soft"
                        >
                            {{ group.programs.length }} program
                        </span>
                    </div>
                </div>

                <div class="candidate-card__actions">
                    <button
                        type="button"
                        class="candidate-card__btn candidate-card__btn--edit"
                        @click="openEdit(group)"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="candidate-card__btn candidate-card__btn--delete"
                        @click="destroy(group)"
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
            <div v-if="showForm" class="modal" @click.self="showForm = false">
                <form class="modal__card" @submit.prevent="submit">
                    <!-- MODAL HEADER -->
                    <div class="modal__top">
                        <span class="modal__eyebrow">
                            {{
                                editingGroup
                                    ? 'Edit Kandidat'
                                    : 'Tambah Kandidat'
                            }}
                        </span>

                        <button
                            type="button"
                            class="modal__close"
                            aria-label="Tutup"
                            @click="showForm = false"
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
                        <!-- ELECTION (hanya saat create) -->
                        <div v-if="!editingGroup" class="field">
                            <label class="field__label">Pemilihan</label>
                            <select
                                v-model="form.election_id"
                                class="field__input"
                            >
                                <option value="">— Pilih Pemilihan —</option>
                                <option
                                    v-for="e in elections"
                                    :key="e.id"
                                    :value="e.id"
                                >
                                    {{ e.name }}
                                </option>
                            </select>
                        </div>

                        <!-- NOMOR URUT + MODEL -->
                        <div class="field-row">
                            <div class="field">
                                <label class="field__label">Nomor Urut</label>
                                <input
                                    v-model="form.nomor_urut"
                                    type="number"
                                    class="field__input"
                                    placeholder="01"
                                />
                            </div>

                            <div v-if="!editingGroup" class="field">
                                <label class="field__label">Model</label>
                                <select
                                    v-model="form.model"
                                    class="field__input"
                                >
                                    <option value="pair">Pasangan</option>
                                    <option value="individual">Individu</option>
                                </select>
                            </div>
                        </div>

                        <!-- NAMA KELOMPOK -->
                        <div class="field">
                            <label class="field__label">Nama Kelompok</label>
                            <input
                                v-model="form.nama_kelompok"
                                type="text"
                                class="field__input"
                                placeholder="Contoh: Ahmad & Budi"
                            />
                        </div>

                        <!-- KETUA -->
                        <div class="field">
                            <label class="field__label">Nama Ketua</label>
                            <input
                                v-model="form.ketua_name"
                                type="text"
                                class="field__input"
                                placeholder="Nama lengkap ketua"
                            />
                            <input
                                type="file"
                                class="field__file"
                                @change="
                                    (e) =>
                                        (form.ketua_photo = e.target.files[0])
                                "
                            />
                        </div>

                        <!-- WAKIL (kalau pair) -->
                        <div v-if="form.model === 'pair'" class="field">
                            <label class="field__label">Nama Wakil</label>
                            <input
                                v-model="form.wakil_name"
                                type="text"
                                class="field__input"
                                placeholder="Nama lengkap wakil"
                            />
                            <input
                                type="file"
                                class="field__file"
                                @change="
                                    (e) =>
                                        (form.wakil_photo = e.target.files[0])
                                "
                            />
                        </div>

                        <!-- VISI -->
                        <div class="field">
                            <label class="field__label">Visi</label>
                            <textarea
                                v-model="form.visi"
                                rows="3"
                                class="field__input field__input--textarea"
                                placeholder="Visi kandidat"
                            ></textarea>
                        </div>

                        <!-- MISI -->
                        <div class="field">
                            <label class="field__label">Misi</label>
                            <textarea
                                v-model="form.misi"
                                rows="4"
                                class="field__input field__input--textarea"
                                placeholder="Misi kandidat"
                            ></textarea>
                        </div>

                        <!-- PROGRAM UNGGULAN -->
                        <div class="field">
                            <div class="field__row">
                                <label class="field__label"
                                    >Program Unggulan</label
                                >

                                <button
                                    type="button"
                                    class="field__add"
                                    @click="addProgram"
                                >
                                    + Tambah
                                </button>
                            </div>

                            <div class="program-list">
                                <div
                                    v-for="(p, i) in form.programs"
                                    :key="i"
                                    class="program-item"
                                >
                                    <span class="program-item__number">
                                        {{ String(i + 1).padStart(2, '0') }}
                                    </span>

                                    <input
                                        v-model="p.title"
                                        placeholder="Judul program"
                                        class="program-item__input"
                                    />

                                    <button
                                        type="button"
                                        class="program-item__remove"
                                        aria-label="Hapus program"
                                        @click="removeProgram(i)"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL ACTIONS -->
                    <div class="modal__actions">
                        <button
                            type="button"
                            class="modal__cancel"
                            @click="showForm = false"
                        >
                            Batalkan
                        </button>

                        <button
                            type="submit"
                            class="modal__submit"
                            :disabled="form.processing"
                        >
                            <span>
                                {{
                                    form.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Kandidat'
                                }}
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

.candidates {
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

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    -webkit-font-smoothing: antialiased;
}

.candidates *,
.candidates *::before,
.candidates *::after {
    box-sizing: border-box;
}

.candidates button,
.candidates a {
    -webkit-tap-highlight-color: transparent;
}

/* =========================================================
   HEADER
========================================================= */

.candidates__header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;

    gap: 32px;

    padding: 8px 0 32px;

    border-bottom: 1px solid var(--ink);
}

.candidates__header-left {
    max-width: 640px;
}

.candidates__eyebrow {
    margin: 0 0 14px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.candidates__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(36px, 5vw, 52px);
    font-weight: 600;
    line-height: 1;
    letter-spacing: -0.04em;

    color: var(--ink);
}

.candidates__title em {
    font-weight: 400;
}

.candidates__desc {
    margin: 16px 0 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.75;

    max-width: 540px;
}

.candidates__header-right {
    display: flex;
    align-items: flex-end;
}

/* ----- CREATE BUTTON ----- */

.candidates__create {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    min-height: 48px;

    padding: 0 22px;

    background: var(--ink);
    color: var(--paper);

    border: 0;

    cursor: pointer;

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

.candidates__create:hover {
    background: #302e2b;
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(24, 23, 22, 0.15);
}

.candidates__create-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    font-size: 18px;
    line-height: 1;
    font-weight: 500;
}

.candidates__create-label {
    white-space: nowrap;
}

/* =========================================================
   META ROW
========================================================= */

.candidates__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 20px 0 28px;

    border-bottom: 1px solid var(--line);
}

.candidates__meta-step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.candidates__meta-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.candidates__meta-count {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   FLASH
========================================================= */

.candidates__flash {
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

.candidates__flash-dot {
    width: 7px;
    height: 7px;

    flex: 0 0 auto;

    border-radius: 50%;

    background: var(--green);
}

/* =========================================================
   EMPTY STATE
========================================================= */

.candidates__empty {
    display: flex;
    flex-direction: column;
    align-items: center;

    margin-top: 40px;
    padding: 60px 24px;

    background: var(--white);

    border: 1px solid var(--line);

    text-align: center;
}

.candidates__empty-icon {
    font-size: 3rem;

    margin-bottom: 16px;
}

.candidates__empty-title {
    margin: 0 0 8px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 22px;
    font-weight: 600;
    letter-spacing: -0.02em;
}

.candidates__empty-text {
    margin: 0 0 20px;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.6;

    max-width: 320px;
}

.candidates__empty-cta {
    min-height: 44px;

    padding: 0 22px;

    background: var(--ink);
    color: var(--paper);

    border: 0;

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;

    transition:
        background 180ms ease,
        transform 180ms ease;
}

.candidates__empty-cta:hover {
    background: #302e2b;
    transform: translateY(-2px);
}

/* =========================================================
   GRID KANDIDAT
========================================================= */

.candidates__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));

    gap: 20px;

    margin-top: 36px;
}

.candidate-card {
    display: flex;
    flex-direction: column;

    background: var(--white);

    border: 1px solid var(--line);
    border-top: 2px solid var(--ink);

    transition:
        border-color 180ms ease,
        transform 180ms ease,
        box-shadow 180ms ease;
}

.candidate-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 36px rgba(24, 23, 22, 0.06);
}

.candidate-card__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    padding: 16px 20px;

    border-bottom: 1px solid var(--line);
}

.candidate-card__type {
    color: var(--ink-muted);

    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.candidate-card__number {
    color: var(--accent);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 28px;
    font-weight: 500;
    line-height: 0.85;
    letter-spacing: -0.04em;
}

.candidate-card__body {
    flex: 1;

    padding: 22px 20px 20px;
}

.candidate-card__label {
    margin: 0 0 8px;

    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.candidate-card__photos {
    display: flex;
    gap: 8px;

    margin-bottom: 14px;
}

.candidate-card__photo {
    width: 52px;
    height: 52px;

    object-fit: cover;

    border: 1px solid var(--line);
}

.candidate-card__name {
    margin: 0 0 12px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 22px;
    font-weight: 600;
    line-height: 1.15;
    letter-spacing: -0.03em;

    color: var(--ink);
}

.candidate-card__people {
    margin: 0;

    color: var(--ink-muted);

    font-size: 12px;
    line-height: 1.55;
}

.candidate-card__badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;

    margin-top: 16px;
}

.candidate-card__badge {
    display: inline-flex;
    align-items: center;

    padding: 4px 10px;

    background: var(--accent);
    color: var(--paper);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.candidate-card__badge--soft {
    background: var(--paper);
    color: var(--ink-muted);

    border: 1px solid var(--line);
}

.candidate-card__actions {
    display: grid;
    grid-template-columns: 1fr 1fr;

    border-top: 1px solid var(--line);
}

.candidate-card__btn {
    min-height: 46px;

    padding: 0 16px;

    border: 0;

    background: transparent;

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;

    transition:
        background 180ms ease,
        color 180ms ease;
}

.candidate-card__btn--edit {
    color: var(--ink-muted);
    border-right: 1px solid var(--line);
}

.candidate-card__btn--edit:hover {
    background: var(--paper);
    color: var(--ink);
}

.candidate-card__btn--delete {
    color: var(--red);
}

.candidate-card__btn--delete:hover {
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

    box-shadow: 0 30px 80px rgba(24, 23, 22, 0.22);

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

    scrollbar-width: thin;
}

/* ----- FORM FIELDS ----- */

.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.field__row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.field__label {
    color: var(--ink);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.field__input {
    width: 100%;
    height: auto;

    padding: 12px 14px;

    border: 1px solid var(--line);
    border-radius: 0;

    background: var(--white);
    color: var(--ink);

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

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
    min-height: 72px;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;
}

.field__file {
    margin-top: 4px;

    font-size: 11px;
    color: var(--ink-muted);
}

.field__file::file-selector-button {
    margin-right: 12px;
    padding: 6px 12px;

    border: 1px solid var(--line);
    background: var(--paper);

    color: var(--ink);

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;

    transition: background 180ms ease;
}

.field__file::file-selector-button:hover {
    background: var(--paper-dark);
}

.field__add {
    padding: 4px 10px;

    border: 1px solid var(--line);

    background: transparent;
    color: var(--accent);

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;

    transition: background 180ms ease;
}

.field__add:hover {
    background: var(--paper);
}

/* =========================================================
   PROGRAM LIST (di dalam modal)
========================================================= */

.program-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.program-item {
    display: grid;
    grid-template-columns: 34px minmax(0, 1fr) 32px;

    gap: 10px;
    align-items: center;

    padding: 8px 10px;

    border: 1px solid var(--line);

    background: var(--paper);
}

.program-item__number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 13px;
}

.program-item__input {
    width: 100%;

    padding: 6px 0;

    border: 0;
    border-bottom: 1px solid transparent;

    background: transparent;
    color: var(--ink);

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 12px;

    transition: border-color 180ms ease;
}

.program-item__input:focus {
    outline: none;
    border-bottom-color: var(--accent);
}

.program-item__input::placeholder {
    color: var(--ink-faint);
    opacity: 0.6;
}

.program-item__remove {
    width: 28px;
    height: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 0;

    background: transparent;
    color: var(--ink-faint);

    cursor: pointer;

    font-size: 12px;

    transition: color 180ms ease;
}

.program-item__remove:hover {
    color: var(--red);
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

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

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
        transform 200ms cubic-bezier(0.22, 1, 0.36, 1);
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
    .candidates {
        padding: 28px 32px 56px;
    }

    .candidates__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 760px) {
    .candidates {
        padding: 24px 24px 48px;
    }

    .candidates__header {
        grid-template-columns: 1fr;

        gap: 20px;

        padding: 4px 0 24px;
    }

    .candidates__header-right {
        align-items: stretch;
    }

    .candidates__create {
        width: 100%;
        justify-content: center;
    }

    .candidates__title {
        font-size: clamp(30px, 9vw, 44px);
    }

    .candidates__meta {
        padding: 14px 0 20px;
    }

    .candidates__meta-count {
        display: none;
    }

    .candidates__grid {
        grid-template-columns: 1fr;
        gap: 14px;
        margin-top: 28px;
    }

    .candidate-card__name {
        font-size: 20px;
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

    .field-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .candidates {
        padding: 20px 18px 40px;
    }

    .candidates__title {
        font-size: 30px;
    }

    .candidates__desc {
        font-size: 12px;
    }

    .candidates__empty {
        padding: 40px 20px;
    }

    .candidate-card__name {
        font-size: 18px;
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
    .candidates__create,
    .candidate-card,
    .candidate-card__btn,
    .modal__submit,
    .modal__cancel,
    .candidates__empty-cta,
    .field__input {
        transition: none !important;
    }

    .candidates__create:hover,
    .candidate-card:hover,
    .modal__submit:hover:not(:disabled) {
        transform: none;
    }
}
</style>
