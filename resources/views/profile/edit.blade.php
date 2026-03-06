<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Profile Settings — {{ config('app.name', 'PEO Document Monitoring') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|syne:700,800" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                --orange-50:  #fff7ed;
                --orange-100: #ffedd5;
                --orange-500: #f97316;
                --orange-600: #ea580c;
                --orange-700: #c2410c;
                --ink:        #1a0f00;
                --ink-muted:  #6b4f35;
                --surface:    #fffaf5;
                --card-bg:    #ffffff;
                --border:     rgba(249,115,22,0.15);
                --radius:     14px;
            }

            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            html { scroll-behavior: smooth; }

            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: var(--surface);
                color: var(--ink);
                line-height: 1.6;
                overflow-x: hidden;
            }

            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
                background-repeat: repeat;
                background-size: 200px;
                pointer-events: none;
                z-index: 0;
            }

            .bg-blob {
                position: fixed;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.25;
                pointer-events: none;
                z-index: 0;
            }
            .bg-blob-1 {
                width: 600px; height: 600px;
                background: var(--orange-500);
                top: -200px; right: -150px;
            }
            .bg-blob-2 {
                width: 400px; height: 400px;
                background: var(--orange-400);
                bottom: 100px; left: -100px;
            }

            nav {
                position: fixed;
                top: 0; width: 100%;
                z-index: 100;
                padding: 0 2.5rem;
                height: 68px;
                display: flex;
                align-items: center;
                background: rgba(255, 250, 245, 0.85);
                backdrop-filter: blur(14px);
                border-bottom: 1px solid var(--border);
            }

            .nav-inner {
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                font-family: 'Syne', sans-serif;
                font-weight: 800;
                font-size: 1.25rem;
                color: var(--ink);
                text-decoration: none;
                letter-spacing: -0.02em;
            }

            .logo-icon {
                width: 34px; height: 34px;
                background: var(--orange-500);
                border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1rem;
                box-shadow: 0 2px 10px rgba(249,115,22,0.35);
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 1.5rem;
            }

            .nav-text {
                color: var(--ink-muted);
                font-size: 0.9rem;
                font-weight: 500;
            }

            .btn-nav {
                padding: 8px 22px;
                background: var(--orange-500);
                color: #fff;
                border-radius: 8px;
                font-weight: 600;
                font-size: 0.875rem;
                text-decoration: none;
                box-shadow: 0 2px 12px rgba(249,115,22,0.4);
                transition: background 0.2s, transform 0.2s;
                border: none;
                cursor: pointer;
            }

            .btn-nav:hover {
                background: var(--orange-600);
                transform: translateY(-1px);
            }

            .action-btn {
                padding: 0 !important;
                background: none !important;
                border: none !important;
                color: var(--orange-500) !important;
                font-weight: 600;
                text-decoration: none;
                cursor: pointer;
                font-size: 0.875rem;
            }

            .action-btn:hover {
                color: var(--orange-700) !important;
            }

            .profile-container {
                position: relative;
                z-index: 1;
                min-height: 100vh;
                padding: 2.5rem;
                padding-top: 120px;
            }

            .profile-header {
                max-width: 1100px;
                margin: 0 auto 3rem;
                padding-bottom: 2rem;
                border-bottom: 1px solid var(--border);
            }

            .profile-header h1 {
                font-family: 'Syne', sans-serif;
                font-size: 2.25rem;
                font-weight: 800;
                color: var(--ink);
                margin-bottom: 0.5rem;
                letter-spacing: -0.02em;
            }

            .profile-header p {
                color: var(--ink-muted);
                font-size: 0.95rem;
            }

            .profile-grid {
                max-width: 1100px;
                margin: 0 auto;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
                gap: 2rem;
            }

            .profile-card {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 2rem;
                box-shadow: 0 16px 48px rgba(249,115,22,0.08);
                transition: all 0.3s;
            }

            .profile-card:hover {
                border-color: var(--orange-500);
                box-shadow: 0 24px 64px rgba(249,115,22,0.12);
                transform: translateY(-4px);
            }

            .profile-card h2 {
                font-family: 'Syne', sans-serif;
                font-size: 1.4rem;
                font-weight: 800;
                color: var(--ink);
                margin-bottom: 0.5rem;
                letter-spacing: -0.01em;
                padding-bottom: 1rem;
                border-bottom: 2px solid var(--border);
            }

            .profile-card p {
                color: var(--ink-muted);
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                font-weight: 600;
                font-size: 0.9rem;
                color: var(--ink);
                margin-bottom: 0.5rem;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                width: 100%;
                padding: 0.85rem 1rem;
                border: 1.5px solid rgba(26,15,0,0.12);
                border-radius: 10px;
                font-family: 'Instrument Sans', sans-serif;
                font-size: 0.95rem;
                color: var(--ink);
                background: #fafaf9;
                transition: border-color 0.2s, box-shadow 0.2s;
            }

            .form-group input:focus,
            .form-group select:focus,
            .form-group textarea:focus {
                outline: none;
                border-color: var(--orange-500);
                background: white;
                box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
            }

            .form-group input::placeholder {
                color: #a89968;
            }

            .error-message {
                color: #dc2626;
                font-size: 0.8rem;
                margin-top: 0.35rem;
            }

            .success-message {
                background: #dcfce7;
                border: 1px solid #86efac;
                color: #166534;
                padding: 0.75rem 1rem;
                border-radius: 8px;
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }

            .btn {
                padding: 10px 20px;
                border-radius: 10px;
                font-size: 0.95rem;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                transition: all 0.25s;
                border: none;
                cursor: pointer;
                font-family: 'Instrument Sans', sans-serif;
            }

            .btn-primary {
                background: var(--orange-500);
                color: #fff;
                box-shadow: 0 4px 18px rgba(249,115,22,0.4);
            }

            .btn-primary:hover {
                background: var(--orange-600);
                transform: translateY(-1px);
                box-shadow: 0 6px 24px rgba(249,115,22,0.5);
            }

            .btn-danger {
                background: #ef4444;
                color: #fff;
                box-shadow: 0 4px 18px rgba(239,68,68,0.3);
            }

            .btn-danger:hover {
                background: #dc2626;
                transform: translateY(-1px);
            }

            .modal-bg {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1000;
                animation: fadeIn 0.2s ease;
            }

            .modal-bg.show {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .modal-box {
                background: var(--card-bg);
                border-radius: var(--radius);
                padding: 2rem;
                max-width: 420px;
                box-shadow: 0 24px 64px rgba(0,0,0,0.15);
                animation: slideUp 0.3s ease both;
            }

            .modal-box h3 {
                font-family: 'Syne', sans-serif;
                font-size: 1.3rem;
                font-weight: 800;
                color: var(--ink);
                margin-bottom: 0.5rem;
            }

            .modal-box p {
                color: var(--ink-muted);
                font-size: 0.95rem;
                margin-bottom: 1.5rem;
            }

            .modal-actions {
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
            }

            .btn-secondary {
                background: transparent;
                color: var(--ink-muted);
                border: 1.5px solid rgba(26,15,0,0.15);
                padding: 10px 18px;
            }

            .btn-secondary:hover {
                border-color: var(--ink-muted);
                background: #f5f5f1;
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to   { opacity: 1; }
            }

            @keyframes slideUp {
                from { opacity: 0; transform: translateY(32px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            .profile-card {
                animation: fadeUp 0.5s ease both;
            }

            .profile-card:nth-child(2) { animation-delay: 0.1s; }
            .profile-card:nth-child(3) { animation-delay: 0.2s; }

            @media (max-width: 768px) {
                nav { padding: 0 1.25rem; }
                .profile-container { padding: 1.25rem; }
                .profile-header h1 { font-size: 1.75rem; }
                .profile-grid {
                    grid-template-columns: 1fr;
                }
            }

            /* Dark Mode Toggle Sidebar */
            .theme-sidebar {
                position:fixed; right:0; top:50%; transform:translateY(-50%);
                background:var(--card-bg); border:1px solid var(--border);
                border-right:none; border-radius:16px 0 0 16px;
                padding:1.2rem 0.8rem; z-index:999;
                display:flex; flex-direction:column; gap:0.8rem;
                box-shadow:-4px 0 16px rgba(0,0,0,0.1);
                transition:all 0.3s;
            }

            @media (prefers-color-scheme: dark) {
                .theme-sidebar { box-shadow:-4px 0 16px rgba(0,0,0,0.3); }
            }

            .theme-btn {
                width:48px; height:48px;
                border-radius:12px; border:1.5px solid var(--border);
                background:var(--card-bg); color:var(--text-primary);
                display:flex; align-items:center; justify-content:center;
                cursor:pointer; transition:all 0.2s; font-size:1.1rem;
            }

            .theme-btn:hover {
                border-color:var(--orange-500);
                background:rgba(249,115,22,0.1);
                color:var(--orange-600);
            }

            .theme-btn.active {
                background:var(--orange-500); color:white; border-color:var(--orange-500);
                box-shadow:0 4px 12px rgba(249,115,22,0.3);
            }

        </style>
    </head>
    <body>
        <div class="bg-blob bg-blob-1"></div>
        <div class="bg-blob bg-blob-2"></div>

        <nav>
            <div class="nav-inner">
                <a href="{{ route('dashboard') }}" class="logo">
                    <div class="logo-icon">📊</div>
                    PEO Monitor
                </a>
                <div class="nav-links">
                    <span class="nav-text">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-nav">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="profile-container">
            <div class="profile-header">
                <h1>Profile Settings</h1>
                <p>Manage your account information and preferences.</p>
            </div>

            <div class="profile-grid">
                <!-- Update Profile Information Card -->
                <div class="profile-card">
                    <h2>Personal Information</h2>
                    <p>Update your name and email address.</p>

                    @if (session('status') === 'profile-updated')
                        <div class="success-message">Profile updated successfully!</div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required autofocus autocomplete="name">
                            @if ($errors->has('name'))
                                <div class="error-message">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required autocomplete="username">
                            @if ($errors->has('email'))
                                <div class="error-message">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !Auth::user()->hasVerifiedEmail())
                            <div style="margin-bottom: 1rem; padding: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #7f1d1d; font-size: 0.9rem;">
                                Your email address is unverified.
                                <form action="{{ route('verification.send') }}" method="post" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="action-btn">Click here to re-send the verification email.</button>
                                </form>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary" style="width:100%;">
                            Save Changes
                        </button>
                    </form>
                </div>

                <!-- Update Password Card -->
                <div class="profile-card">
                    <h2>Change Password</h2>
                    <p>Update your password to keep your account secure.</p>

                    @if (session('status') === 'password-updated')
                        <div class="success-message">Password updated successfully!</div>
                    @endif

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input id="current_password" type="password" name="current_password" autocomplete="current-password">
                            @if ($errors->updatePassword->has('current_password'))
                                <div class="error-message">{{ $errors->updatePassword->first('current_password') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input id="password" type="password" name="password" autocomplete="new-password">
                            @if ($errors->updatePassword->has('password'))
                                <div class="error-message">{{ $errors->updatePassword->first('password') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                            @if ($errors->updatePassword->has('password_confirmation'))
                                <div class="error-message">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;">
                            Update Password
                        </button>
                    </form>
                </div>

                <!-- Delete Account Card -->
                <div class="profile-card">
                    <h2>Danger Zone</h2>
                    <p>Permanently delete your account and all associated data.</p>

                    <button onclick="document.getElementById('deleteModal').classList.add('show')" class="btn btn-danger" style="width:100%;">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Account Modal -->
        <div id="deleteModal" class="modal-bg">
            <div class="modal-box">
                <h3>Delete Account</h3>
                <p>Once your account is deleted, there is no going back. Please be certain.</p>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" placeholder="Enter your password to confirm" required autocomplete="current-password">
                        @if ($errors->userDeletion->has('password'))
                            <div class="error-message">{{ $errors->userDeletion->first('password') }}</div>
                        @endif
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('deleteModal').classList.remove('show')">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Close modal when clicking outside
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        </script>
    </body>
</html>
