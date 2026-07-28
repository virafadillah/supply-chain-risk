@once
<style>
    .admin-tabs { flex-wrap: wrap; }
    .admin-tab {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 18px; border-radius: 10px;
        background: #f1f4fb; color: #3b5bdb; font-weight: 600; font-size: 0.92rem;
        text-decoration: none; border: 1.5px solid transparent; transition: all .15s ease;
    }
    .admin-tab:hover { background: #e4e9fa; color: #2f4bc0; text-decoration: none; }
    .admin-tab.active { background: #3b5bdb; color: #fff; box-shadow: 0 4px 10px rgba(59,91,219,0.25); }
    .admin-card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
    .btn-add { background:#3b5bdb; border-color:#3b5bdb; color:#fff; font-weight:600; }
    .btn-add:hover { background:#2f4bc0; border-color:#2f4bc0; color:#fff; }
    .badge-published { background:#198754; }
    .badge-draft { background:#6c757d; }
</style>
@endonce

<div class="d-flex gap-2 mb-4 admin-tabs">
    <a href="{{ route('admin.users.index') }}" class="admin-tab {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">👤 Kelola User</a>
    <a href="{{ route('admin.ports.index') }}" class="admin-tab {{ request()->routeIs('admin.ports.*') ? 'active' : '' }}">⚓ Dataset Pelabuhan</a>
    <a href="{{ route('admin.articles.index') }}" class="admin-tab {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">📰 Artikel Analisis</a>
</div>