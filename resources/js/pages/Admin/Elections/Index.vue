<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({
    elections: Array,
});

const form = useForm({});

function openVoting(election) {
    if (confirm(`Buka voting untuk ${election.name}?`)) {
        form.patch(`/admin/elections/${election.id}/open`);
    }
}

function closeVoting(election) {
    if (
        confirm(
            `Tutup voting untuk ${election.name}? Tindakan ini menghentikan pemilih baru masuk.`,
        )
    ) {
        form.patch(`/admin/elections/${election.id}/close`);
    }
}

function publishResult(election) {
    if (
        confirm(
            `Publikasikan hasil ${election.name}? Hasil akan tampil di halaman publik.`,
        )
    ) {
        form.patch(`/admin/elections/${election.id}/publish`);
    }
}

const statusLabel = {
    draft: 'Draft',
    scheduled: 'Terjadwal',
    open: 'Berlangsung',
    closed: 'Ditutup',
    result_published: 'Hasil Diumumkan',
};

// Status modifier untuk CSS class (bukan Tailwind warna solid)
const statusModifier = {
    draft: 'draft',
    scheduled: 'scheduled',
    open: 'open',
    closed: 'closed',
    result_published: 'published',
};
</script>

<template>
    <div class="elections">
        <!-- =====================================================
         HEADER
    ====================================================== -->

        <header class="elections__header">
            <div class="elections__header-left">
                <p class="elections__eyebrow">Panel Admin</p>

                <h1 class="elections__title">Kontrol <em>Voting.</em></h1>

                <p class="elections__desc">
                    Kelola status setiap pemilihan — buka, tutup, dan
                    publikasikan hasil OSIS maupun MPK secara langsung.
                </p>
            </div>
        </header>

        <!-- =====================================================
         META ROW
    ====================================================== -->

        <div class="elections__meta">
            <span class="elections__meta-step">
                <span class="elections__meta-number">04</span>
                Kontrol Voting
            </span>
            <span class="elections__meta-count">
                {{ elections?.length ?? 0 }} pemilihan
            </span>
        </div>

        <!-- =====================================================
         FLASH & ERRORS
    ====================================================== -->

        <div v-if="$page.props.flash?.success" class="elections__flash">
            <span class="elections__flash-dot"></span>
            {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.errors?.status" class="elections__error">
            <span class="elections__error-icon">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M12 3l9 17H3L12 3z" />
                    <path d="M12 9v5" />
                    <path d="M12 17h.01" />
                </svg>
            </span>
            {{ $page.props.errors.status }}
        </div>

        <!-- =====================================================
         CARD LIST
    ====================================================== -->

        <div class="elections__list">
            <article
                v-for="election in elections"
                :key="election.id"
                class="election-card"
            >
                <!-- HEADER CARD -->
                <div class="election-card__head">
                    <div class="election-card__info">
                        <p class="election-card__label">Pemilihan</p>

                        <h2 class="election-card__name">
                            {{ election.name }}
                        </h2>
                    </div>

                    <span
                        class="status-pill"
                        :class="`status-pill--${statusModifier[election.status]}`"
                    >
                        <span class="status-pill__dot"></span>
                        {{ statusLabel[election.status] }}
                    </span>
                </div>

                <!-- META -->
                <div class="election-card__meta">
                    <div class="election-card__meta-item">
                        <span class="election-card__meta-label">Tipe</span>
                        <span class="election-card__meta-value">
                            {{ election.type === 'osis' ? 'OSIS' : 'MPK' }}
                        </span>
                    </div>

                    <div class="election-card__meta-item">
                        <span class="election-card__meta-label">Status</span>
                        <span class="election-card__meta-value">
                            {{ statusLabel[election.status] }}
                        </span>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="election-card__actions">
                    <button
                        v-if="election.status !== 'open'"
                        type="button"
                        class="action-btn action-btn--primary"
                        :disabled="form.processing"
                        @click="openVoting(election)"
                    >
                        <span class="action-btn__icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polygon points="6 3 20 12 6 21 6 3" />
                            </svg>
                        </span>
                        <span class="action-btn__label">Buka Voting</span>
                        <span class="action-btn__arrow">↗</span>
                    </button>

                    <button
                        v-if="election.status === 'open'"
                        type="button"
                        class="action-btn action-btn--danger"
                        :disabled="form.processing"
                        @click="closeVoting(election)"
                    >
                        <span class="action-btn__icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect x="6" y="4" width="4" height="16" />
                                <rect x="14" y="4" width="4" height="16" />
                            </svg>
                        </span>
                        <span class="action-btn__label">Tutup Voting</span>
                        <span class="action-btn__arrow">↗</span>
                    </button>

                    <button
                        v-if="election.status === 'closed'"
                        type="button"
                        class="action-btn action-btn--accent"
                        :disabled="form.processing"
                        @click="publishResult(election)"
                    >
                        <span class="action-btn__icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M3 11l19-9-9 19-2-8-8-2z" />
                            </svg>
                        </span>
                        <span class="action-btn__label"
                            >Publikasikan Hasil</span
                        >
                        <span class="action-btn__arrow">↗</span>
                    </button>

                    <!-- Info untuk status draft / scheduled / published -->
                    <span
                        v-if="
                            election.status === 'draft' ||
                            election.status === 'scheduled' ||
                            election.status === 'result_published'
                        "
                        class="election-card__hint"
                    >
                        <template v-if="election.status === 'draft'">
                            Pemilihan belum dijadwalkan. Buka voting untuk
                            memulai.
                        </template>
                        <template v-else-if="election.status === 'scheduled'">
                            Pemilihan akan dibuka sesuai jadwal.
                        </template>
                        <template v-else>
                            Hasil sudah dipublikasikan dan tampil di halaman
                            publik.
                        </template>
                    </span>
                </div>
            </article>
        </div>

        <!-- =====================================================
         EMPTY STATE
    ====================================================== -->

        <div v-if="!elections?.length" class="elections__empty">
            <span class="elections__empty-icon">🗳️</span>
            <h3 class="elections__empty-title">Belum ada pemilihan</h3>
            <p class="elections__empty-text">
                Tambahkan pemilihan terlebih dahulu untuk memulai proses voting.
            </p>
        </div>
    </div>
</template>

<style scoped>
/* =========================================================
   ROOT — TEMA EDITORIAL
========================================================= */

.elections {
    --ink: #181716;
    --ink-muted: #77736d;
    --ink-faint: #a09b93;
    --accent: #4b1f63;
    --accent-soft: #7c3a9e;
    --green: #2d8a52;
    --red: #a74a3d;
    --blue: #3b6ea5;
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

.elections *,
.elections *::before,
.elections *::after {
    box-sizing: border-box;
}

.elections button,
.elections a {
    -webkit-tap-highlight-color: transparent;
}

/* =========================================================
   HEADER
========================================================= */

.elections__header {
    display: grid;
    grid-template-columns: minmax(0, 1fr);

    gap: 32px;

    padding: 8px 0 32px;

    border-bottom: 1px solid var(--ink);
}

.elections__header-left {
    max-width: 640px;
}

.elections__eyebrow {
    margin: 0 0 14px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.elections__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(36px, 5vw, 52px);
    font-weight: 600;
    line-height: 1;
    letter-spacing: -0.04em;

    color: var(--ink);
}

.elections__title em {
    font-weight: 400;
}

.elections__desc {
    margin: 16px 0 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.75;

    max-width: 540px;
}

/* =========================================================
   META ROW
========================================================= */

.elections__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 20px 0 28px;

    border-bottom: 1px solid var(--line);
}

.elections__meta-step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.elections__meta-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 17px;
    font-weight: 500;
    letter-spacing: 0;
}

.elections__meta-count {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   FLASH & ERROR
========================================================= */

.elections__flash {
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

.elections__flash-dot {
    width: 7px;
    height: 7px;

    flex: 0 0 auto;

    border-radius: 50%;

    background: var(--green);
}

.elections__error {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-top: 24px;
    padding: 14px 18px;

    border: 1px solid rgba(167, 74, 61, 0.22);

    background: rgba(167, 74, 61, 0.045);

    color: var(--red);

    font-size: 12px;
    font-weight: 600;

    line-height: 1.5;
}

.elections__error-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 18px;
    height: 18px;

    flex: 0 0 auto;
}

.elections__error-icon svg {
    width: 100%;
    height: 100%;
}

/* =========================================================
   CARD LIST
========================================================= */

.elections__list {
    display: flex;
    flex-direction: column;
    gap: 20px;

    margin-top: 32px;
}

.election-card {
    background: var(--white);

    border: 1px solid var(--line);
    border-top: 2px solid var(--ink);

    overflow: hidden;

    transition:
        transform 180ms ease,
        box-shadow 180ms ease;
}

.election-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px rgba(24, 23, 22, 0.05);
}

/* ----- HEAD CARD ----- */

.election-card__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 20px;

    padding: 24px 28px 20px;

    border-bottom: 1px solid var(--line);
}

.election-card__info {
    min-width: 0;
}

.election-card__label {
    margin: 0 0 8px;

    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.election-card__name {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 22px;
    font-weight: 600;
    line-height: 1.15;
    letter-spacing: -0.03em;

    color: var(--ink);
}

/* ----- STATUS PILL ----- */

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 5px 12px;

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;

    white-space: nowrap;

    flex: 0 0 auto;
}

.status-pill__dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;
}

.status-pill--draft {
    background: var(--paper-dark);
    color: var(--ink-muted);
}

.status-pill--draft .status-pill__dot {
    background: var(--ink-faint);
}

.status-pill--scheduled {
    background: rgba(59, 110, 165, 0.08);
    color: var(--blue);
}

.status-pill--scheduled .status-pill__dot {
    background: var(--blue);
}

.status-pill--open {
    background: rgba(45, 138, 82, 0.08);
    color: var(--green);
}

.status-pill--open .status-pill__dot {
    background: var(--green);
    animation: pulse-dot 1.6s ease-in-out infinite;
}

.status-pill--closed {
    background: rgba(167, 74, 61, 0.08);
    color: var(--red);
}

.status-pill--closed .status-pill__dot {
    background: var(--red);
}

.status-pill--published {
    background: rgba(75, 31, 99, 0.08);
    color: var(--accent);
}

.status-pill--published .status-pill__dot {
    background: var(--accent);
}

@keyframes pulse-dot {
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

/* ----- META ----- */

.election-card__meta {
    display: flex;
    gap: 40px;

    padding: 16px 28px;

    border-bottom: 1px solid var(--line);
}

.election-card__meta-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.election-card__meta-label {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.election-card__meta-value {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 14px;
    font-weight: 600;
    letter-spacing: -0.01em;
}

/* ----- ACTIONS ----- */

.election-card__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;

    gap: 12px;

    padding: 20px 28px 24px;
}

.election-card__hint {
    color: var(--ink-faint);

    font-size: 11px;
    font-style: italic;
    line-height: 1.6;

    max-width: 380px;
}

/* =========================================================
   ACTION BUTTONS
========================================================= */

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    min-height: 44px;

    padding: 0 18px;

    border: 0;

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;

    white-space: nowrap;

    transition:
        background 180ms ease,
        transform 180ms ease,
        box-shadow 180ms ease;
}

.action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.action-btn__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 16px;
    height: 16px;
}

.action-btn__icon svg {
    width: 100%;
    height: 100%;
}

.action-btn__label {
    flex: 1;
}

.action-btn__arrow {
    font-size: 15px;
    font-weight: 500;

    transition: transform 180ms ease;
}

.action-btn:hover:not(:disabled) .action-btn__arrow {
    transform: translate(2px, -2px);
}

/* ----- VARIANTS ----- */

.action-btn--primary {
    background: var(--green);
    color: var(--white);

    box-shadow: 0 4px 12px rgba(45, 138, 82, 0.18);
}

.action-btn--primary:hover:not(:disabled) {
    background: #266f43;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(45, 138, 82, 0.28);
}

.action-btn--danger {
    background: var(--red);
    color: var(--white);

    box-shadow: 0 4px 12px rgba(167, 74, 61, 0.18);
}

.action-btn--danger:hover:not(:disabled) {
    background: #8d3e32;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(167, 74, 61, 0.28);
}

.action-btn--accent {
    background: var(--ink);
    color: var(--paper);

    box-shadow: 0 4px 12px rgba(24, 23, 22, 0.12);
}

.action-btn--accent:hover:not(:disabled) {
    background: #302e2b;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(24, 23, 22, 0.22);
}

/* =========================================================
   EMPTY STATE
========================================================= */

.elections__empty {
    display: flex;
    flex-direction: column;
    align-items: center;

    margin-top: 32px;
    padding: 60px 24px;

    background: var(--white);

    border: 1px solid var(--line);

    text-align: center;
}

.elections__empty-icon {
    font-size: 3rem;

    margin-bottom: 16px;
}

.elections__empty-title {
    margin: 0 0 8px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 22px;
    font-weight: 600;
    letter-spacing: -0.02em;
}

.elections__empty-text {
    margin: 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.6;

    max-width: 340px;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1024px) {
    .elections {
        padding: 28px 32px 56px;
    }
}

@media (max-width: 760px) {
    .elections {
        padding: 24px 24px 48px;
    }

    .elections__title {
        font-size: clamp(30px, 9vw, 44px);
    }

    .elections__desc {
        font-size: 12px;
    }

    .elections__meta {
        padding: 14px 0 20px;
    }

    .elections__meta-count {
        display: none;
    }

    .elections__list {
        margin-top: 24px;
        gap: 16px;
    }

    .election-card__head {
        flex-direction: column;
        align-items: flex-start;

        gap: 14px;

        padding: 20px 20px 16px;
    }

    .election-card__name {
        font-size: 20px;
    }

    .election-card__meta {
        padding: 14px 20px;
        gap: 28px;
    }

    .election-card__actions {
        padding: 16px 20px 20px;
        flex-direction: column;
        align-items: stretch;
    }

    .action-btn {
        width: 100%;
        justify-content: space-between;
    }

    .election-card__hint {
        text-align: center;
        max-width: 100%;
    }

    .elections__empty {
        padding: 44px 20px;
    }
}

@media (max-width: 480px) {
    .elections {
        padding: 20px 18px 40px;
    }

    .elections__title {
        font-size: 30px;
    }

    .elections__eyebrow {
        font-size: 9px;
    }

    .election-card__head {
        padding: 18px 16px 14px;
    }

    .election-card__name {
        font-size: 18px;
    }

    .election-card__meta {
        padding: 12px 16px;
        gap: 22px;
    }

    .election-card__meta-value {
        font-size: 13px;
    }

    .election-card__actions {
        padding: 14px 16px 18px;
    }

    .action-btn {
        font-size: 10px;
        min-height: 42px;
        padding: 0 14px;
    }

    .status-pill {
        padding: 4px 10px;
        font-size: 9px;
    }

    .elections__empty {
        padding: 36px 16px;
    }

    .elections__empty-title {
        font-size: 18px;
    }

    .elections__empty-text {
        font-size: 12px;
    }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .election-card,
    .action-btn,
    .action-btn__arrow {
        transition: none !important;
    }

    .election-card:hover {
        transform: none;
    }

    .action-btn:hover:not(:disabled) {
        transform: none;
    }

    .action-btn:hover:not(:disabled) .action-btn__arrow {
        transform: none;
    }

    .status-pill--open .status-pill__dot {
        animation: none;
    }
}
</style>
