<x-app-layout>
    <x-slot name="header"><h4>📰 Berita & Sentimen</h4></x-slot>

    @php
        $categories = [
            'logistics' => 'Logistics',
            'trade' => 'Trade',
            'shipping' => 'Shipping',
            'economy' => 'Economy',
        ];
        $activeCategory = request('category');
    @endphp

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="{{ route('news') }}" class="btn btn-sm {{ !$activeCategory ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
        @foreach ($categories as $key => $label)
            <a href="{{ route('news', ['category' => $key]) }}" class="btn btn-sm {{ $activeCategory === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="row g-3">
        @forelse ($news as $item)
            @php
                $sc = match($item->sentiment) { 'positive' => 'low', 'negative' => 'high', default => 'medium' };
            @endphp
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="d-flex gap-2">
                            <span class="risk-badge {{ $sc }}">{{ ucfirst($item->sentiment ?? 'N/A') }}</span>
                            @if($item->category)
                                <span class="badge bg-secondary">{{ $categories[$item->category] ?? ucfirst($item->category) }}</span>
                            @endif
                        </div>
                        <span class="text-muted small">{{ $item->country->name ?? '-' }}</span>
                    </div>
                    <h6>{{ $item->title }}</h6>
                    <p class="text-muted small mb-0">{{ Str::limit($item->description, 120) }}</p>
                </div>
            </div>
        @empty
            <p class="text-muted">Belum ada berita untuk kategori ini. Jalankan <code>php artisan risk:calculate</code>.</p>
        @endforelse
    </div>

    <div class="mt-3">{{ $news->links() }}</div>
</x-app-layout>