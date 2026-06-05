@extends('layouts/basic')

{{-- Page content --}}
@section('content')

<style>
/* ── Reset layout shell ─────────────────────────────────────────── */
body.login-page {
    background: #f0f4ff !important;
    padding: 0 !important;
    margin: 0 !important;
    min-height: 100vh !important;
    overflow-x: hidden !important;
}
/* Hide logo + privacy footer injected by layouts/basic */
body > div.text-center { display: none !important; }

/* ── Full-page wrapper ──────────────────────────────────────────── */
.pn-wrap {
    display: flex;
    min-height: 100vh;
    width: 100%;
    overflow: hidden; /* clips decorative blobs only */
}

/* ── LEFT — Brand Panel ─────────────────────────────────────────── */
.pn-brand {
    flex: 1.25;
    background: linear-gradient(145deg, #0b0f2e 0%, #1a1256 35%, #2d1b69 65%, #4c1d95 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    padding: 60px 56px;
    position: relative;
    overflow: hidden;
    color: #fff;
}

/* decorative glow blobs */
.pn-brand::before {
    content: '';
    position: absolute;
    top: -140px; right: -140px;
    width: 480px; height: 480px;
    background: radial-gradient(circle, rgba(139,92,246,.22) 0%, transparent 65%);
    border-radius: 50%;
    pointer-events: none;
}
.pn-brand::after {
    content: '';
    position: absolute;
    bottom: -100px; left: -100px;
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(79,172,254,.15) 0%, transparent 65%);
    border-radius: 50%;
    pointer-events: none;
}

/* Logo mark */
.pn-logomark { width: 90px; height: auto; margin-bottom: 4px; }

/* Brand text */
.pn-brandname {
    font-size: 44px;
    font-weight: 900;
    letter-spacing: -1.5px;
    line-height: 1;
    margin-bottom: 10px;
    font-family: 'Arial Black', 'Helvetica Neue', Arial, sans-serif;
    color: #fff;
}
.pn-brandname span {
    background: linear-gradient(90deg, #93c5fd 0%, #c4b5fd 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Tagline */
.pn-tagline {
    display: flex;
    align-items: center;
    gap: 14px;
    color: rgba(255,255,255,.6);
    font-size: 13px;
    letter-spacing: .6px;
    margin-bottom: 52px;
}
.pn-tagline-line {
    height: 1px;
    width: 52px;
    background: rgba(255,255,255,.25);
}

/* Feature list */
.pn-features { display: flex; flex-direction: column; gap: 14px; width: 100%; max-width: 400px; }

.pn-feat {
    display: flex;
    align-items: center;
    gap: 16px;
    background: rgba(255,255,255,.055);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px;
    padding: 13px 18px;
    transition: background .18s;
}
.pn-feat:hover { background: rgba(255,255,255,.1); }

.pn-feat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(79,172,254,.28), rgba(139,92,246,.28));
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
    color: #a5b4fc;
    flex-shrink: 0;
}
.pn-feat-text strong { display: block; color: #fff; font-size: 13px; font-weight: 700; }
.pn-feat-text small  { color: rgba(255,255,255,.45); font-size: 11.5px; }

/* ── RIGHT — Form Panel ─────────────────────────────────────────── */
.pn-form-panel {
    flex: 0.75;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px 52px;
    position: relative;
    overflow-y: auto;
    min-height: 100vh;
}

/* small logo repeated on form side */
.pn-form-logomark { width: 54px; height: auto; display: block; margin-bottom: 8px; }
.pn-form-brandname {
    font-size: 22px;
    font-weight: 900;
    color: #1e1b4b;
    font-family: 'Arial Black', 'Helvetica Neue', Arial, sans-serif;
    letter-spacing: -.5px;
    line-height: 1;
}
.pn-form-brandname span { color: #7c3aed; }
.pn-form-sub {
    font-size: 11px;
    color: #9ca3af;
    letter-spacing: .7px;
    text-transform: uppercase;
    margin-top: 4px;
    margin-bottom: 36px;
}

.pn-heading { font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 6px; }
.pn-desc    { font-size: 14px; color: #6b7280; margin-bottom: 28px; }

/* SSO button */
.pn-sso-btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 12px !important;
    background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 15px 24px !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #fff !important;
    letter-spacing: .3px;
    box-shadow: 0 4px 18px rgba(79,70,229,.38) !important;
    transition: transform .15s, box-shadow .15s !important;
    height: auto !important;
    line-height: 1.4 !important;
    text-decoration: none !important;
}
.pn-sso-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(79,70,229,.48) !important;
    color: #fff !important;
}
.pn-sso-btn:active { transform: translateY(0); }

/* Divider */
.pn-divider {
    display: flex; align-items: center;
    gap: 12px; margin: 22px 0;
    color: #9ca3af; font-size: 12px; letter-spacing: .4px;
}
.pn-divider::before, .pn-divider::after {
    content: ''; flex: 1; height: 1px; background: #e5e7eb;
}

/* Footer note */
.pn-footer {
    margin-top: 36px;
    padding-top: 18px;
    border-top: 1px solid #f3f4f6;
    text-align: center;
    color: #9ca3af;
    font-size: 11.5px;
}
.pn-footer a { color: #a5b4fc; }

/* ── Tablet (≤ 1024px): tighten the split ───────────────────────── */
@media (max-width: 1024px) {
    .pn-brand        { flex: 1;    padding: 48px 36px; }
    .pn-form-panel   { flex: 1;    padding: 48px 36px; }
    .pn-brandname    { font-size: 36px; }
    .pn-feat         { padding: 10px 14px; }
    .pn-features     { gap: 10px; }
}

/* ── Mobile (≤ 767px): stack vertically ─────────────────────────── */
@media (max-width: 767px) {
    body.login-page  { overflow-y: auto !important; }

    .pn-wrap {
        flex-direction: column;
        overflow: visible;
        min-height: 100vh;
    }

    /* Brand panel → compact top banner */
    .pn-brand {
        flex: none;
        padding: 28px 24px 24px;
        align-items: center;
        text-align: center;
    }
    .pn-logomark     { width: 64px; }
    .pn-brandname    { font-size: 28px; letter-spacing: -1px; }
    .pn-tagline      { margin-bottom: 0; font-size: 12px; justify-content: center; }
    .pn-tagline-line { display: none; }
    /* hide features on mobile to keep banner compact */
    .pn-features     { display: none; }

    /* Form panel fills the rest */
    .pn-form-panel {
        flex: 1;
        min-height: auto;
        padding: 32px 24px 40px;
        justify-content: flex-start;
    }
    .pn-form-logomark { display: none; } /* logo already in top banner */
    .pn-form-brandname, .pn-form-sub { display: none; }
    .pn-heading  { font-size: 22px; }
    .pn-form-sub { margin-bottom: 20px; }
}

/* ── Very small phones (≤ 380px) ────────────────────────────────── */
@media (max-width: 380px) {
    .pn-brand        { padding: 22px 16px 18px; }
    .pn-form-panel   { padding: 24px 16px 32px; }
    .pn-brandname    { font-size: 24px; }
    .pn-sso-btn      { font-size: 14px !important; padding: 13px 16px !important; }
}
</style>

<div class="pn-wrap">

    {{-- ═══════════════════════════════════════════
         LEFT — Brand Identity Panel
    ═══════════════════════════════════════════ --}}
    <div class="pn-brand">

        <img src="{{ asset('img/procurenova-logo.png') }}"
             alt="ProcureNova"
             class="pn-logomark">

        <div class="pn-brandname">Procure<span>Nova</span></div>

        <div class="pn-tagline">
            <div class="pn-tagline-line"></div>
            Smarter Procurement. Stronger Business.
            <div class="pn-tagline-line"></div>
        </div>

        <div class="pn-features">

            <div class="pn-feat">
                <div class="pn-feat-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="pn-feat-text">
                    <strong>Automated Procurement</strong>
                    <small>End-to-end purchase lifecycle automation</small>
                </div>
            </div>

            <div class="pn-feat">
                <div class="pn-feat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="pn-feat-text">
                    <strong>Data-Driven Decisions</strong>
                    <small>AI-powered supplier scoring &amp; insights</small>
                </div>
            </div>

            <div class="pn-feat">
                <div class="pn-feat-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="pn-feat-text">
                    <strong>Secure &amp; Compliant</strong>
                    <small>Role-based access with full audit trail</small>
                </div>
            </div>

            <div class="pn-feat">
                <div class="pn-feat-icon"><i class="fas fa-network-wired"></i></div>
                <div class="pn-feat-text">
                    <strong>Connected Ecosystem</strong>
                    <small>OrangeHRM, assets &amp; maintenance synced</small>
                </div>
            </div>

            <div class="pn-feat">
                <div class="pn-feat-icon"><i class="fas fa-rocket"></i></div>
                <div class="pn-feat-text">
                    <strong>Faster Processes</strong>
                    <small>Streamlined approvals. Better outcomes.</small>
                </div>
            </div>

        </div>

    </div>

    {{-- ═══════════════════════════════════════════
         RIGHT — Sign-In Form Panel
    ═══════════════════════════════════════════ --}}
    <div class="pn-form-panel">

        <img src="{{ asset('img/procurenova-logo.png') }}"
             alt="ProcureNova"
             class="pn-form-logomark">

        <div class="pn-form-brandname">Procure<span>Nova</span></div>
        <div class="pn-form-sub">Smarter Procurement. Stronger Business.</div>

        <h2 class="pn-heading">Welcome back</h2>
        <p class="pn-desc">Sign in to your workspace to continue.</p>

        {{-- Alerts --}}
        @if ($snipeSettings->login_note)
            <div class="alert alert-info" style="border-radius:8px; margin-bottom:20px;">
                {!! Helper::parseEscapedMarkedown($snipeSettings->login_note) !!}
            </div>
        @endif

        @include('notifications')

        @if ($errors->any())
            <div class="alert alert-danger" style="border-radius:8px; margin-bottom:20px;">
                <i class="fas fa-times-circle" aria-hidden="true"></i>
                {!! $errors->first('username') !!}
            </div>
        @endif

        {{-- OrangeHRM SSO button --}}
        <a href="{{ route('orangehrm.redirect') }}" class="btn btn-block pn-sso-btn">
            <svg width="20" height="20" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="flex-shrink:0;">
                <circle cx="20" cy="20" r="18" fill="rgba(255,255,255,.15)" stroke="rgba(255,255,255,.35)" stroke-width="1.5"/>
                <text x="20" y="27" text-anchor="middle" font-size="17" font-weight="900" fill="#fff"
                      font-family="Arial Black, sans-serif">O</text>
            </svg>
            {{ trans('auth/general.orangehrm_login') }}
        </a>

        <div class="pn-divider">Secure Single Sign-On</div>

        <div class="pn-footer">
            <i class="fas fa-lock" style="color:#d1d5db; margin-right:5px;" aria-hidden="true"></i>
            Your session is encrypted and protected.
            @if ($snipeSettings->privacy_policy_link)
                &nbsp;&middot;&nbsp;
                <a href="{{ $snipeSettings->privacy_policy_link }}" target="_blank" rel="noopener">
                    {{ trans('admin/settings/general.privacy_policy') }}
                </a>
            @endif
        </div>

    </div>

</div>

@stop
