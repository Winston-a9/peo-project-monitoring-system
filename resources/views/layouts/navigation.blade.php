{{-- resources/views/components/navigation.blade.php --}}

<style>
    :root {
        --orange-500: #f97316;
        --orange-600: #ea580c;
        --ink:        #1a0f00;
        --ink-muted:  #6b4f35;
        --sb-width-collapsed: 68px;
        --sb-width-expanded:  248px;
        --sb-bg: #1a0f00;
        --sb-transition: 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ── SIDEBAR SHELL ── */
    #sidebar {
        position: fixed;
        left: 0; top: 0;
        height: 100vh;
        width: var(--sb-width-collapsed);
        background: var(--sb-bg);
        display: flex;
        flex-direction: column;
        z-index: 50;
        transition: width var(--sb-transition);
        overflow: hidden;
        will-change: width;
    }

    /* Expand on hover (desktop) */
    @media (min-width: 768px) {
        #sidebar:hover,
        #sidebar.pinned {
            width: var(--sb-width-expanded);
        }
    }

    /* Mobile: full slide-in */
    @media (max-width: 767px) {
        #sidebar {
            width: var(--sb-width-expanded);
            transform: translateX(-100%);
            transition: transform var(--sb-transition);
        }
        #sidebar.open { transform: translateX(0); }
    }

    /* Noise texture */
    #sidebar::before {
        content: '';
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
        background-size: 180px;
        pointer-events: none; z-index: 0;
    }

    /* Ambient glow */
    #sidebar::after {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(249,115,22,0.16) 0%, transparent 70%);
        pointer-events: none; z-index: 0;
    }

    #sidebar > * { position: relative; z-index: 1; }

    /* ── LOGO ── */
    .sb-logo {
        padding: 1.1rem 0;
        border-bottom: 1px solid rgba(249,115,22,0.1);
        flex-shrink: 0;
        overflow: hidden;
    }

    .sb-logo a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        padding: 0 1.1rem;
        white-space: nowrap;
    }

    .sb-logo-icon {
        width: 36px; height: 36px;
        background: var(--orange-500);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem;
        box-shadow: 0 2px 12px rgba(249,115,22,0.4);
        flex-shrink: 0;
        transition: box-shadow 0.2s;
    }

    #sidebar:hover .sb-logo-icon,
    #sidebar.pinned .sb-logo-icon {
        box-shadow: 0 4px 18px rgba(249,115,22,0.5);
    }

    .sb-logo-text {
        display: flex;
        flex-direction: column;
        opacity: 0;
        transform: translateX(-6px);
        transition: opacity var(--sb-transition), transform var(--sb-transition);
        pointer-events: none;
    }

    #sidebar:hover .sb-logo-text,
    #sidebar.pinned .sb-logo-text {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
    }

    .sb-logo-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 1rem;
        color: white;
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .sb-logo-sub {
        font-size: 0.6rem;
        color: rgba(255,255,255,0.35);
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-top: 2px;
    }

    /* ── SCROLL BODY ── */
    .sb-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0.875rem 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(249,115,22,0.15) transparent;
    }

    .sb-body::-webkit-scrollbar { width: 3px; }
    .sb-body::-webkit-scrollbar-track { background: transparent; }
    .sb-body::-webkit-scrollbar-thumb { background: rgba(249,115,22,0.15); border-radius: 99px; }

    /* ── SECTION LABEL ── */
    .sb-section-label {
        font-size: 0.58rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: rgba(255,255,255,0.22);
        padding: 0.9rem 0.5rem 0.35rem;
        white-space: nowrap;
        overflow: hidden;
        opacity: 0;
        transition: opacity var(--sb-transition);
        height: 2rem;
    }

    #sidebar:hover .sb-section-label,
    #sidebar.pinned .sb-section-label {
        opacity: 1;
    }

    /* ── NAV LINK ── */
    .sb-link {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.6rem 0.6rem;
        border-radius: 10px;
        text-decoration: none;
        color: rgba(255,255,255,0.5);
        font-size: 0.855rem;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        border: 1px solid transparent;
        white-space: nowrap;
        overflow: hidden;
    }

    .sb-link:hover {
        color: white;
        background: rgba(255,255,255,0.07);
        border-color: rgba(255,255,255,0.06);
    }

    .sb-link.active {
        color: white;
        background: rgba(249,115,22,0.16);
        border-color: rgba(249,115,22,0.28);
        font-weight: 600;
    }

    /* Active indicator bar */
    .sb-link.active::before {
        content: '';
        position: absolute;
        left: 0; top: 22%; bottom: 22%;
        width: 3px;
        background: var(--orange-500);
        border-radius: 0 3px 3px 0;
        box-shadow: 0 0 8px rgba(249,115,22,0.6);
    }

    .sb-link-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem;
        background: rgba(255,255,255,0.05);
        flex-shrink: 0;
        transition: background 0.2s, transform 0.2s;
    }

    .sb-link:hover .sb-link-icon {
        background: rgba(255,255,255,0.1);
        transform: scale(1.05);
    }

    .sb-link.active .sb-link-icon {
        background: rgba(249,115,22,0.22);
        color: var(--orange-500);
    }

    .sb-link-label {
        flex: 1;
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity var(--sb-transition), transform var(--sb-transition);
        pointer-events: none;
    }

    #sidebar:hover .sb-link-label,
    #sidebar.pinned .sb-link-label {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
    }

    /* ── TOOLTIP (shows on collapsed hover) ── */
    .sb-link[data-tooltip]::after {
        content: attr(data-tooltip);
        position: absolute;
        left: calc(var(--sb-width-collapsed) - 4px);
        top: 50%;
        transform: translateY(-50%);
        background: #2d1a00;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s 0.05s;
        border: 1px solid rgba(249,115,22,0.2);
        box-shadow: 0 4px 16px rgba(0,0,0,0.35);
        z-index: 100;
    }

    /* Only show tooltip when sidebar is COLLAPSED */
    @media (min-width: 768px) {
        #sidebar:not(:hover):not(.pinned) .sb-link:hover::after {
            opacity: 1;
        }
    }

    /* ── DIVIDER ── */
    .sb-divider {
        height: 1px;
        background: rgba(255,255,255,0.06);
        margin: 0.4rem 0;
    }

    /* ── PIN BUTTON ── */
    .sb-pin-btn {
        display: none;
        align-items: center;
        justify-content: center;
        width: 26px; height: 26px;
        border-radius: 7px;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.04);
        color: rgba(255,255,255,0.35);
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
        font-size: 0.65rem;
        margin-left: auto;
        opacity: 0;
        transition: opacity var(--sb-transition), background 0.2s, color 0.2s;
    }

    #sidebar:hover .sb-pin-btn,
    #sidebar.pinned .sb-pin-btn {
        opacity: 1;
        display: flex;
    }

    .sb-pin-btn:hover {
        background: rgba(249,115,22,0.15);
        color: var(--orange-500);
        border-color: rgba(249,115,22,0.25);
    }

    #sidebar.pinned .sb-pin-btn {
        background: rgba(249,115,22,0.18);
        color: var(--orange-500);
        border-color: rgba(249,115,22,0.3);
    }

    /* ── USER FOOTER ── */
    .sb-footer {
        border-top: 1px solid rgba(249,115,22,0.1);
        padding: 0.875rem 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        flex-shrink: 0;
        overflow: hidden;
    }

    .sb-user {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.5rem 0.6rem;
        margin-bottom: 0.2rem;
        overflow: hidden;
    }

    .sb-avatar {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 0.875rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(249,115,22,0.3);
    }

    .sb-user-info {
        flex: 1; min-width: 0;
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity var(--sb-transition), transform var(--sb-transition);
        white-space: nowrap;
        overflow: hidden;
    }

    #sidebar:hover .sb-user-info,
    #sidebar.pinned .sb-user-info {
        opacity: 1;
        transform: translateX(0);
    }

    .sb-user-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: white;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .sb-user-role {
        font-size: 0.64rem;
        color: rgba(255,255,255,0.32);
        text-transform: capitalize;
        letter-spacing: 0.04em;
        margin-top: 1px;
    }

    .sb-footer-link {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.5rem 0.6rem;
        border-radius: 9px;
        text-decoration: none;
        color: rgba(255,255,255,0.42);
        font-size: 0.815rem;
        font-weight: 500;
        transition: all 0.2s;
        border: 1px solid transparent;
        background: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        font-family: 'Instrument Sans', sans-serif;
        white-space: nowrap;
        overflow: hidden;
    }

    .sb-footer-link:hover {
        color: white;
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.06);
    }

    .sb-footer-link.logout:hover {
        color: #fca5a5;
        background: rgba(239,68,68,0.1);
        border-color: rgba(239,68,68,0.14);
    }

    .sb-footer-link-icon {
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem;
        background: rgba(255,255,255,0.05);
        flex-shrink: 0;
        transition: background 0.2s;
    }

    .sb-footer-link:hover .sb-footer-link-icon {
        background: rgba(255,255,255,0.1);
    }

    .sb-footer-link-label {
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity var(--sb-transition), transform var(--sb-transition);
    }

    #sidebar:hover .sb-footer-link-label,
    #sidebar.pinned .sb-footer-link-label {
        opacity: 1;
        transform: translateX(0);
    }

    /* Tooltip for footer links */
    .sb-footer-link[data-tooltip]::after {
        content: attr(data-tooltip);
        position: absolute;
        left: calc(var(--sb-width-collapsed) - 4px);
        top: 50%;
        transform: translateY(-50%);
        background: #2d1a00;a
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s 0.05s;
        border: 1px solid rgba(249,115,22,0.2);
        box-shadow: 0 4px 16px rgba(0,0,0,0.35);
        z-index: 100;
    }

    .sb-footer-link { position: relative; }

    @media (min-width: 768px) {
        #sidebar:not(:hover):not(.pinned) .sb-footer-link:hover::after {
            opacity: 1;
        }
    }

    /* ── CONTENT OFFSET ── */
    .sb-content-offset {
        margin-left: var(--sb-width-collapsed);
        transition: margin-left var(--sb-transition);
    }

    @media (max-width: 767px) {
        .sb-content-offset { margin-left: 0; }
    }

    /* ── MOBILE OVERLAY ── */
    #sidebar-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(3px);
        z-index: 40;
        display: none;
        opacity: 0;
        transition: opacity 0.25s;
    }

    #sidebar-overlay.visible {
        display: block;
        opacity: 1;
    }

    @media (min-width: 768px) {
        #sidebar-overlay { display: none !important; }
    }

    /* ── COLLAPSED INDICATOR DOTS ── */
    .sb-link.active .sb-active-dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: var(--orange-500);
        position: absolute;
        right: 6px; top: 50%;
        transform: translateY(-50%);
        box-shadow: 0 0 6px rgba(249,115,22,0.7);
        opacity: 1;
        transition: opacity var(--sb-transition);
        flex-shrink: 0;
    }

    #sidebar:hover .sb-link.active .sb-active-dot,
    #sidebar.pinned .sb-link.active .sb-active-dot {
        opacity: 0;
    }
</style>

{{-- ── SIDEBAR ── --}}
<nav id="sidebar">

    {{-- Logo --}}
    <div class="sb-logo">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}">
            <div class="sb-logo-icon">
                <i class="fas fa-project-diagram" style="color:white;"></i>
            </div>
            <div class="sb-logo-text">
                <span class="sb-logo-title">PEO Monitor</span>
                <span class="sb-logo-sub">Project Control</span>
            </div>
            {{-- Pin button --}}
            <button class="sb-pin-btn" id="sb-pin" type="button" onclick="togglePin(event)" title="Pin sidebar">
                <i class="fas fa-thumbtack" id="sb-pin-icon"></i>
            </button>
        </a>
    </div>

    {{-- Nav links --}}
    <div class="sb-body">
        @auth
            @if(Auth::user()->role === 'admin')

                <span class="sb-section-label">Overview</span>

                <a href="{{ route('admin.dashboard') }}"
                   class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   data-tooltip="Dashboard">
                    <span class="sb-link-icon"><i class="fas fa-chart-line"></i></span>
                    <span class="sb-link-label">Dashboard</span>
                    <span class="sb-active-dot"></span>
                </a>

                <span class="sb-section-label">Management</span>

                <a href="{{ route('admin.projects.index') }}"
                   class="sb-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}"
                   data-tooltip="Projects">
                    <span class="sb-link-icon"><i class="fas fa-folder-open"></i></span>
                    <span class="sb-link-label">Projects</span>
                    <span class="sb-active-dot"></span>
                </a>

           {{-- User nav section only — replace the @else block in navigation.blade.php --}}

            @else

                <span class="sb-section-label">Overview</span>

                <a href="{{ route('user.dashboard') }}"
                   class="sb-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"
                   data-tooltip="Dashboard">
                    <span class="sb-link-icon"><i class="fas fa-home"></i></span>
                    <span class="sb-link-label">Dashboard</span>
                    <span class="sb-active-dot"></span>
                </a>

                <span class="sb-section-label">Browse</span>

                <a href="{{ route('user.projects.index') }}"
                   class="sb-link {{ request()->routeIs('user.projects.*') ? 'active' : '' }}"
                   data-tooltip="Projects">
                    <span class="sb-link-icon"><i class="fas fa-folder-open"></i></span>
                    <span class="sb-link-label">Projects</span>
                    <span class="sb-active-dot"></span>
                </a>

            @endif
        @endauth
    </div>

    {{-- Footer --}}
    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
            <div class="sb-user-info">
                <p class="sb-user-name">{{ Auth::user()->name }}</p>
                <p class="sb-user-role">{{ Auth::user()->role }}</p>
            </div>
        </div>

        <div class="sb-divider"></div>

        <a href="{{ route('profile.edit') }}" class="sb-footer-link" data-tooltip="Profile">
            <span class="sb-footer-link-icon"><i class="fas fa-user-cog"></i></span>
            <span class="sb-footer-link-label">Profile Settings</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="sb-footer-link logout" data-tooltip="Sign Out">
                <span class="sb-footer-link-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="sb-footer-link-label">Sign Out</span>
            </button>
        </form>
    </div>
</nav>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    let isPinned  = localStorage.getItem('sb-pinned') === 'true';

    // Apply pinned state on load
    if (isPinned) sidebar.classList.add('pinned');
    updatePinIcon();

    function togglePin(e) {
        e.preventDefault();
        e.stopPropagation();
        isPinned = !isPinned;
        localStorage.setItem('sb-pinned', isPinned);
        sidebar.classList.toggle('pinned', isPinned);
        updatePinIcon();
    }

    function updatePinIcon() {
        const icon = document.getElementById('sb-pin-icon');
        const btn  = document.getElementById('sb-pin');
        if (!icon || !btn) return;
        if (isPinned) {
            icon.style.transform = 'rotate(45deg)';
            btn.title = 'Unpin sidebar';
        } else {
            icon.style.transform = 'rotate(0deg)';
            btn.title = 'Pin sidebar open';
        }
    }

    /* Mobile */
    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('visible');
        document.body.style.overflow = '';
    }

    window.toggleSidebar = function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    };


</script>