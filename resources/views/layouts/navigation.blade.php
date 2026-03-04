{{-- resources/views/components/navigation.blade.php --}}

<style>
    :root {
        --orange-500: #f97316;
        --orange-600: #ea580c;
        --ink:        #1a0f00;
        --ink-muted:  #6b4f35;
    }

    /* ── SIDEBAR SHELL ── */
    #sidebar {
        position: fixed;
        left: 0; top: 0;
        height: 100vh;
        width: 260px;
        background: #1a0f00;
        display: flex;
        flex-direction: column;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    #sidebar.open,
    @media (min-width: 768px) {
        #sidebar { transform: translateX(0); }
    }

    @media (min-width: 768px) {
        #sidebar { transform: translateX(0); position: static; height: 100vh; }
    }

    /* Subtle noise texture */
    #sidebar::before {
        content: '';
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
        background-size: 180px;
        pointer-events: none;
        z-index: 0;
    }

    /* Ambient orange glow top-right */
    #sidebar::after {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(249,115,22,0.18) 0%, transparent 70%);
        pointer-events: none;
        z-index: 0;
    }

    /* All direct children above pseudo-elements */
    #sidebar > * { position: relative; z-index: 1; }

    /* ── LOGO ── */
    .sb-logo {
        padding: 1.5rem 1.25rem 1.25rem;
        border-bottom: 1px solid rgba(249,115,22,0.12);
    }

    .sb-logo a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
    }

    .sb-logo-icon {
        width: 38px; height: 38px;
        background: var(--orange-500);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        box-shadow: 0 2px 12px rgba(249,115,22,0.4);
        flex-shrink: 0;
    }

    .sb-logo-text {
        display: flex;
        flex-direction: column;
    }

    .sb-logo-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 1.05rem;
        color: white;
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .sb-logo-sub {
        font-size: 0.65rem;
        color: rgba(255,255,255,0.4);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-top: 2px;
    }

    /* ── SCROLL BODY ── */
    .sb-body {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 0.875rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(249,115,22,0.2) transparent;
    }

    .sb-body::-webkit-scrollbar { width: 3px; }
    .sb-body::-webkit-scrollbar-track { background: transparent; }
    .sb-body::-webkit-scrollbar-thumb { background: rgba(249,115,22,0.2); border-radius: 99px; }

    /* ── SECTION LABEL ── */
    .sb-section-label {
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: rgba(255,255,255,0.28);
        padding: 0.85rem 0.6rem 0.4rem;
    }

    /* ── NAV LINK ── */
    .sb-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 0.85rem;
        border-radius: 10px;
        text-decoration: none;
        color: rgba(255,255,255,0.55);
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        border: 1px solid transparent;
    }

    .sb-link:hover {
        color: white;
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.06);
    }

    .sb-link.active {
        color: white;
        background: rgba(249,115,22,0.15);
        border-color: rgba(249,115,22,0.25);
        font-weight: 600;
    }

    .sb-link.active::before {
        content: '';
        position: absolute;
        left: 0; top: 20%; bottom: 20%;
        width: 3px;
        background: var(--orange-500);
        border-radius: 0 3px 3px 0;
    }

    .sb-link-icon {
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem;
        background: rgba(255,255,255,0.05);
        flex-shrink: 0;
        transition: background 0.2s;
    }

    .sb-link:hover .sb-link-icon {
        background: rgba(255,255,255,0.1);
    }

    .sb-link.active .sb-link-icon {
        background: rgba(249,115,22,0.25);
        color: var(--orange-500);
    }

    .sb-link-label { flex: 1; }

    /* ── DIVIDER ── */
    .sb-divider {
        height: 1px;
        background: rgba(255,255,255,0.06);
        margin: 0.5rem 0;
    }

    /* ── USER FOOTER ── */
    .sb-footer {
        border-top: 1px solid rgba(249,115,22,0.12);
        padding: 1rem 0.875rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .sb-user {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0.75rem;
        margin-bottom: 0.25rem;
    }

    .sb-avatar {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: 0.9rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(249,115,22,0.35);
    }

    .sb-user-info { flex: 1; min-width: 0; }

    .sb-user-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .sb-user-role {
        font-size: 0.68rem;
        color: rgba(255,255,255,0.38);
        text-transform: capitalize;
        letter-spacing: 0.04em;
        margin-top: 1px;
    }

    .sb-footer-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.55rem 0.85rem;
        border-radius: 9px;
        text-decoration: none;
        color: rgba(255,255,255,0.45);
        font-size: 0.825rem;
        font-weight: 500;
        transition: all 0.2s;
        border: 1px solid transparent;
        background: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        font-family: 'Instrument Sans', sans-serif;
    }

    .sb-footer-link:hover {
        color: white;
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.06);
    }

    .sb-footer-link.logout:hover {
        color: #fca5a5;
        background: rgba(239,68,68,0.1);
        border-color: rgba(239,68,68,0.15);
    }

    .sb-footer-link-icon {
        width: 26px; height: 26px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem;
        background: rgba(255,255,255,0.05);
        flex-shrink: 0;
    }

    /* ── MOBILE OVERLAY ── */
    #sidebar-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        z-index: 40;
        display: none;
    }

    #sidebar-overlay.visible { display: block; }

    @media (min-width: 768px) {
        #sidebar { transform: translateX(0) !important; }
        #sidebar-overlay { display: none !important; }
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
        </a>
    </div>

    {{-- Nav links --}}
    <div class="sb-body">
        @auth

            @if(Auth::user()->role === 'admin')

                {{-- Overview --}}
                <span class="sb-section-label">Overview</span>

                <a href="{{ route('admin.dashboard') }}"
                   class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="sb-link-icon"><i class="fas fa-chart-line"></i></span>
                    <span class="sb-link-label">Dashboard</span>
                </a>

                {{-- Management --}}
                <span class="sb-section-label">Management</span>

                <a href="{{ route('admin.projects.index') }}"
                   class="sb-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                    <span class="sb-link-icon"><i class="fas fa-folder"></i></span>
                    <span class="sb-link-label">Projects</span>
                </a>

            @else

                {{-- User nav --}}
                <span class="sb-section-label">Overview</span>

                <a href="{{ route('user.dashboard') }}"
                   class="sb-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <span class="sb-link-icon"><i class="fas fa-home"></i></span>
                    <span class="sb-link-label">Dashboard</span>
                </a>

            @endif

        @endauth
    </div>

    {{-- Footer --}}
    <div class="sb-footer">

        {{-- User info --}}
        <div class="sb-user">
            <div class="sb-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
            <div class="sb-user-info">
                <p class="sb-user-name">{{ Auth::user()->name }}</p>
                <p class="sb-user-role">{{ Auth::user()->role }}</p>
            </div>
        </div>

        <div class="sb-divider"></div>

        <a href="{{ route('profile.edit') }}" class="sb-footer-link">
            <span class="sb-footer-link-icon"><i class="fas fa-user"></i></span>
            Profile Settings
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-footer-link logout">
                <span class="sb-footer-link-icon"><i class="fas fa-sign-out-alt"></i></span>
                Sign Out
            </button>
        </form>

    </div>
</nav>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<script>
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');

    function openSidebar() {
        sidebar.style.transform = 'translateX(0)';
        overlay.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.style.transform = 'translateX(-100%)';
        overlay.classList.remove('visible');
        document.body.style.overflow = '';
    }

    // Keep existing toggleSidebar API working for any hamburger buttons
    window.toggleSidebar = function() {
        const isOpen = sidebar.style.transform === 'translateX(0px)' || sidebar.style.transform === 'translateX(0)';
        isOpen ? closeSidebar() : openSidebar();
    };
</script>