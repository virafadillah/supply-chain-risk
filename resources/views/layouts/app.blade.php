<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Supply Chain Risk') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-app: #f7f8fa;
            --bg-sidebar: #2f4bc0;
            --bg-card: #ffffff;
            --bg-input: #ffffff;
            --border-soft: #e4e7eb;
            --text-primary: #1a1d26;
            --text-muted: #6b7280;
            --accent: #3b5bdb;
            --accent-strong: #2f4bc0;
            --risk-low: #1e7a44;
            --risk-medium: #8a5a05;
            --risk-high: #b32020;
        }
        body {
            font-family: 'Figtree', sans-serif;
            background: var(--bg-app);
            color: var(--text-primary);
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { color: var(--accent-strong); }

        .app-shell { display: flex; min-height: 100vh; }

        .app-sidebar {
            width: 250px;
            flex-shrink: 0;
            background: var(--bg-sidebar);
            padding: 1.25rem 1rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-brand { display: flex; align-items: center; gap: .6rem; margin-bottom: 1.75rem; padding: 0 .25rem; }
        .sidebar-brand .icon { width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-weight:700; color:#fff; font-size: 1.1rem; }
        .sidebar-brand .title { font-weight: 700; font-size: .95rem; line-height:1.1; color: #fff; }
        .sidebar-brand .subtitle { font-size: .7rem; color: rgba(255,255,255,0.6); }

        .sidebar-section-title { font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; color: rgba(255,255,255,0.5); margin: 1.1rem .5rem .4rem; font-weight: 600; }
        .sidebar-link { display: flex; align-items: center; gap: .6rem; padding: .55rem .6rem; border-radius: 8px; color: rgba(255,255,255,0.75); font-size: .88rem; font-weight: 500; margin-bottom: .15rem; }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar-link.active { background: rgba(255,255,255,0.18); color: #fff; }

        .app-main { flex: 1; min-width: 0; }
        .app-topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-soft);
            padding: .75rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            position: sticky; top: 0; z-index: 20;
        }
        .app-content { padding: 1.5rem; }

        .card { background: var(--bg-card); border: 1px solid var(--border-soft); color: var(--text-primary); box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
        .table { color: var(--text-primary); }
        .table > :not(caption) > * > * { background: transparent; color: var(--text-primary); border-bottom-color: var(--border-soft); }
        .table-bordered > :not(caption) > * > * { border-color: var(--border-soft); }
        .table-light, .table-light > td, .table-light > th { background: #fafbfc !important; color: var(--text-primary) !important; }
        .table-warning, .table-warning > td, .table-warning > th { background: #fdf3d8 !important; color: var(--text-primary) !important; }
        .table thead { color: var(--text-muted); }
        .form-control, .form-select { background: var(--bg-input); border-color: var(--border-soft); color: var(--text-primary); }
        .form-control:focus, .form-select:focus { background: var(--bg-input); border-color: var(--accent); color: var(--text-primary); box-shadow: 0 0 0 .2rem rgba(59,91,219,.12); }
        .form-control::placeholder { color: var(--text-muted); }
        .text-muted { color: var(--text-muted) !important; }
        hr { border-color: var(--border-soft); }
        .btn-outline-secondary { color: var(--text-muted); border-color: var(--border-soft); }
        .btn-outline-secondary:hover { background: #f1f3f9; color: var(--text-primary); }
        .btn-outline-primary { color: var(--accent); border-color: var(--accent); }
        .btn-outline-primary:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-outline-danger { color: var(--accent-strong); border-color: var(--accent); }
        .btn-outline-danger:hover { background: var(--accent); color: #fff; border-color: var(--accent); }

        .risk-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem; border-radius:999px; font-size:.78rem; font-weight:600; }
        .risk-badge.low { background: #e2f5e8; color: var(--risk-low); }
        .risk-badge.medium { background: #fdf3d8; color: var(--risk-medium); }
        .risk-badge.high { background: #fbe4e4; color: var(--risk-high); }

        .stat-card { background: var(--bg-card); border: 1px solid var(--border-soft); border-radius: 12px; padding: 1.1rem 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
        .stat-card .stat-label { font-size: .75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing:.04em; display:flex; align-items:center; gap:.4rem; }
        .stat-card .stat-value { font-size: 1.6rem; font-weight: 700; margin: .35rem 0 .15rem; color: var(--text-primary); }
        .stat-card .stat-sub { font-size: .75rem; color: var(--text-muted); }

        .progress-dark { height: 6px; background: var(--border-soft); border-radius: 999px; overflow: hidden; }
        .progress-dark .fill { height: 100%; border-radius: 999px; background: var(--accent); }

        .btn-warning { background: #f0b429; border-color: #f0b429; color: #3d2c00; font-weight: 600; }
        .btn-warning:hover { background: #d99f1f; border-color: #d99f1f; color: #3d2c00; }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <div class="icon">🌐</div>
                <div>
                    <div class="title">Supply Chain Risk</div>
                    <div class="subtitle">Intelligence Platform</div>
                </div>
            </div>

            <div class="sidebar-section-title">Main</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('map') }}" class="sidebar-link {{ request()->routeIs('map') ? 'active' : '' }}">🗺️ Peta Global</a>
            <a href="{{ route('compare') }}" class="sidebar-link {{ request()->routeIs('compare') ? 'active' : '' }}">📈 Perbandingan</a>

            <div class="sidebar-section-title">Analysis</div>
            <a href="{{ route('currency') }}" class="sidebar-link {{ request()->routeIs('currency') ? 'active' : '' }}">💱 Kurs Mata Uang</a>
            <a href="{{ route('news') }}" class="sidebar-link {{ request()->routeIs('news') ? 'active' : '' }}">📰 Berita & Sentimen</a>
            <a href="{{ route('ports') }}" class="sidebar-link {{ request()->routeIs('ports') ? 'active' : '' }}">⚓ Pelabuhan</a>

            <div class="sidebar-section-title">Personal</div>
            <a href="{{ route('watchlist') }}" class="sidebar-link {{ request()->routeIs('watchlist') ? 'active' : '' }}">🔖 Watchlist</a>

            @if (auth()->user()->is_admin)
                <div class="sidebar-section-title">System</div>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">⚙️ Admin Panel</a>
            @endif
        </aside>

        <div class="app-main">
            <div class="app-topbar">
                <form action="{{ route('search.country') }}" method="GET" class="flex-grow-1" style="max-width: 320px;">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="🔍 Cari negara..." list="countryDatalist">
                    <datalist id="countryDatalist">
                        @foreach (\App\Models\Country::all() as $c)
                            <option value="{{ $c->name }}">
                        @endforeach
                    </datalist>
                </form>

                <div class="d-flex align-items-center gap-2">
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-danger">⚙ Admin</a>
                    @endif
                    <a href="{{ route('watchlist') }}" class="btn btn-sm btn-outline-secondary">🔖</a>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @isset($header)
                <div class="app-content pb-0">
                    {{ $header }}
                </div>
            @endisset

            <main class="app-content">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>