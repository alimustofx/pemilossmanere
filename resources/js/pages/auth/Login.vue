<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { request } from '@/routes/password';
import { store } from '@/routes/login';

defineOptions({
    layout: {
        title: 'Masuk ke akun Anda',
        description: 'Masukkan email dan password untuk melanjutkan',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Masuk — Pemilos" />

    <div class="auth">
        <!-- =====================================================
         HEADER MINIMAL
    ====================================================== -->

        <header class="auth__header">
            <a href="/" class="auth__logo">Pemilos</a>

            <a href="/" class="auth__back">
                <span>←</span>
                Kembali ke Beranda
            </a>
        </header>

        <!-- =====================================================
         MAIN
    ====================================================== -->

        <main class="auth__main">
            <div class="auth__card">
                <!-- META -->
                <div class="auth__meta">
                    <span class="auth__step">
                        <span class="auth__step-number">01</span>
                        Akses Panitia
                    </span>
                    <span class="auth__year"> Pemilos 2026/2027 </span>
                </div>

                <!-- HEADING -->
                <div class="auth__heading">
                    <p class="auth__eyebrow">Login Admin</p>

                    <h1 class="auth__title">Masuk ke <em>dashboard.</em></h1>

                    <p class="auth__desc">
                        Halaman ini hanya untuk panitia dan administrator
                        pemilihan. Siswa tidak perlu login di sini.
                    </p>
                </div>

                <!-- STATUS -->
                <div v-if="status" class="auth__status">
                    <span class="auth__status-dot"></span>
                    {{ status }}
                </div>

                <!-- FORM -->
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="auth__form"
                >
                    <!-- EMAIL -->
                    <div class="field">
                        <label for="email" class="field__label">
                            Alamat Email
                        </label>

                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="email@sekolah.sch.id"
                            class="field__input"
                        />

                        <InputError
                            :message="errors.email"
                            class="field__error"
                        />
                    </div>

                    <!-- PASSWORD -->
                    <div class="field">
                        <div class="field__row">
                            <label for="password" class="field__label">
                                Password
                            </label>

                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="field__link"
                                :tabindex="5"
                            >
                                Lupa password?
                            </TextLink>
                        </div>

                        <PasswordInput
                            id="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Password"
                            class="field__input"
                        />

                        <InputError
                            :message="errors.password"
                            class="field__error"
                        />
                    </div>

                    <!-- REMEMBER -->
                    <div class="field field--inline">
                        <label for="remember" class="remember">
                            <Checkbox
                                id="remember"
                                name="remember"
                                :tabindex="3"
                                class="remember__checkbox"
                            />
                            <span class="remember__label">
                                Ingat saya di perangkat ini
                            </span>
                        </label>
                    </div>

                    <!-- SUBMIT -->
                    <button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        class="auth__submit"
                        data-test="login-button"
                    >
                        <span class="auth__submit-text">
                            {{ processing ? 'Memproses...' : 'Masuk' }}
                        </span>

                        <span class="auth__submit-icon">
                            <Spinner v-if="processing" class="auth__spinner" />
                            <template v-else>↗</template>
                        </span>
                    </button>
                </Form>

                <!-- NOTE -->
                <p class="auth__note">
                    Dengan masuk, Anda menyetujui tata tertib dan prosedur yang
                    berlaku dalam penyelenggaraan Pemilos.
                </p>
            </div>
        </main>

        <!-- =====================================================
         FOOTER
    ====================================================== -->

        <footer class="auth__footer">
            <span>Panitia Pemilihan OSIS &amp; MPK</span>
            <span>{{ new Date().getFullYear() }}</span>
        </footer>
    </div>
</template>

<style scoped>
/* =========================================================
   ROOT — SAMA PERSIS DENGAN HALAMAN LAIN
========================================================= */

.auth {
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

    display: flex;
    flex-direction: column;
}

.auth *,
.auth *::before,
.auth *::after {
    box-sizing: border-box;
}

.auth button,
.auth a {
    -webkit-tap-highlight-color: transparent;
}

/* =========================================================
   HEADER
========================================================= */

.auth__header {
    width: min(1180px, calc(100% - 48px));

    min-height: 76px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

.auth__logo {
    color: var(--ink);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
    font-weight: 600;
    letter-spacing: -0.025em;

    text-decoration: none;
}

.auth__back {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 8px 14px;

    border: 1px solid var(--line);
    border-radius: 999px;

    color: var(--ink-muted);

    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.04em;

    text-decoration: none;

    transition:
        border-color 180ms ease,
        color 180ms ease,
        background 180ms ease;
}

.auth__back:hover {
    border-color: var(--ink);
    color: var(--ink);
    background: var(--white);
}

.auth__back span {
    font-size: 15px;
}

/* =========================================================
   MAIN
========================================================= */

.auth__main {
    flex: 1;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 40px 24px 60px;
}

.auth__card {
    width: min(100%, 480px);

    background: var(--white);

    border: 1px solid var(--line);
    border-top: 2px solid var(--ink);

    padding: 40px 40px 36px;
}

/* =========================================================
   META
========================================================= */

.auth__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 32px;
    padding-bottom: 14px;

    border-bottom: 1px solid var(--line);
}

.auth__step {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.auth__step-number {
    color: var(--ink-faint);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 16px;
    font-weight: 500;
    letter-spacing: 0;
}

.auth__year {
    color: var(--ink-faint);

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* =========================================================
   HEADING
========================================================= */

.auth__heading {
    margin-bottom: 32px;
}

.auth__eyebrow {
    margin: 0 0 14px;

    color: var(--ink-muted);

    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.auth__title {
    margin: 0;

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(36px, 6vw, 48px);
    font-weight: 600;
    line-height: 1.02;
    letter-spacing: -0.04em;

    color: var(--ink);
}

.auth__title em {
    font-weight: 400;
}

.auth__desc {
    margin: 16px 0 0;

    color: var(--ink-muted);

    font-size: 13px;
    line-height: 1.7;
}

/* =========================================================
   STATUS BANNER
========================================================= */

.auth__status {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 24px;
    padding: 12px 16px;

    border: 1px solid rgba(45, 138, 82, 0.2);

    background: rgba(45, 138, 82, 0.05);

    color: #2d8a52;

    font-size: 12px;
    font-weight: 600;

    line-height: 1.5;
}

.auth__status-dot {
    width: 7px;
    height: 7px;

    flex: 0 0 auto;

    border-radius: 50%;

    background: #2d8a52;

    animation: statusPulse 1.6s ease-in-out infinite;
}

@keyframes statusPulse {
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

/* =========================================================
   FORM
========================================================= */

.auth__form {
    display: flex;
    flex-direction: column;
    gap: 22px;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field--inline {
    gap: 0;
}

.field__row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.field__label {
    color: var(--ink);

    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.04em;

    text-transform: uppercase;
}

.field__link {
    color: var(--ink-muted);

    font-size: 11px;
    font-weight: 600;

    text-decoration: none;

    border-bottom: 1px solid var(--line);

    padding-bottom: 2px;

    transition:
        color 180ms ease,
        border-color 180ms ease;
}

.field__link:hover {
    color: var(--ink);
    border-color: var(--ink);
}

.field__error {
    color: #a74a3d;

    font-size: 11px;
    font-weight: 500;

    line-height: 1.5;
}

/* ----- INPUT OVERRIDES (mengalahkan shadcn default) ----- */

.field__input,
:deep(.field__input) {
    width: 100%;
    height: auto;

    padding: 14px 16px;

    border: 1px solid var(--line);
    border-radius: 0;

    background: var(--white);
    color: var(--ink);

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 14px;
    font-weight: 500;

    transition:
        border-color 180ms ease,
        background 180ms ease,
        box-shadow 180ms ease;
}

.field__input::placeholder,
:deep(.field__input::placeholder) {
    color: var(--ink-faint);
    opacity: 0.6;
}

.field__input:focus,
:deep(.field__input:focus) {
    outline: none;

    border-color: var(--accent);

    background: var(--white);

    box-shadow: 0 0 0 3px rgba(75, 31, 99, 0.08);
}

/* =========================================================
   REMEMBER
========================================================= */

.remember {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    cursor: pointer;

    user-select: none;
}

.remember__label {
    color: var(--ink-muted);

    font-size: 12px;
    font-weight: 500;

    line-height: 1.4;
}

/* =========================================================
   SUBMIT
========================================================= */

.auth__submit {
    min-height: 54px;

    margin-top: 6px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 22px;

    border: 0;

    background: var(--ink);
    color: var(--paper);

    cursor: pointer;

    font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;

    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.04em;

    transition:
        background 180ms ease,
        transform 180ms ease;
}

.auth__submit:hover:not(:disabled) {
    background: #302e2b;
    transform: translateY(-2px);
}

.auth__submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.auth__submit-text {
    font-size: 12px;
}

.auth__submit-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 20px;
    height: 20px;

    font-size: 17px;
    line-height: 1;
}

.auth__spinner {
    width: 16px;
    height: 16px;
}

/* =========================================================
   NOTE
========================================================= */

.auth__note {
    margin: 24px 0 0;

    color: var(--ink-faint);

    font-size: 10px;
    line-height: 1.7;

    text-align: center;
}

/* =========================================================
   FOOTER
========================================================= */

.auth__footer {
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
    .auth__header {
        width: calc(100% - 32px);
        min-height: 68px;
    }

    .auth__back {
        font-size: 10px;
        padding: 7px 12px;
    }

    .auth__main {
        padding: 20px 16px 40px;
    }

    .auth__card {
        padding: 30px 24px 28px;
    }

    .auth__meta {
        margin-bottom: 28px;
    }

    .auth__year {
        display: none;
    }

    .auth__title {
        font-size: clamp(32px, 10vw, 42px);
    }

    .auth__desc {
        font-size: 12px;
    }

    .field__input,
    :deep(.field__input) {
        padding: 13px 14px;
        font-size: 13px;
    }

    .auth__submit {
        min-height: 50px;
        padding: 0 18px;
    }

    .auth__footer {
        width: calc(100% - 32px);
        padding-bottom: 28px;
        gap: 12px;
        font-size: 9px;
    }
}

@media (max-width: 400px) {
    .auth__card {
        padding: 26px 18px 24px;
    }

    .auth__title {
        font-size: 32px;
    }
}

/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {
    .auth__status-dot {
        animation: none !important;
    }

    .auth__back,
    .auth__submit,
    .field__input,
    .field__link {
        transition: none !important;
    }
}
</style>
