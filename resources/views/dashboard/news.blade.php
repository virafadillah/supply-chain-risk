<x-app-layout>
    <x-slot name="header"><h4>📰 Berita & Sentimen</h4></x-slot>

    <div class="row g-3">
        @forelse ($news as $item)
            @php
                $sc = match($item->sentiment) { 'positive' => 'low', 'negative' => 'high', default => 'medium' };
            @endphp
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="risk-badge {{ $sc }}">{{ ucfirst($item->sentiment ?? 'N/A') }}</span>
                        <span class="text-muted small">{{ $item->country->name ?? '-' }}</span>
                    </div>
                    <h6>{{ $item->title }}</h6>
                    <p class="text-muted small mb-0">{{ Str::limit($item->description, 120) }}</p>
                </div>
            </div>
        @empty
            <p class="text-muted">Belum ada berita ter-cache. Jalankan <code>php artisan risk:calculate</code>.</p>
        @endforelse
    </div>

    <div class="mt-3">{{ $news->links() }}</div>
</x-app-layout>