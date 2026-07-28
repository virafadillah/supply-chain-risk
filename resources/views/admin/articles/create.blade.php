<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Panel</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @include('admin.partials.tabs')

                <h5 class="fw-bold mb-4">Tulis Artikel Baru</h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.articles.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori (opsional)</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="Logistics, Trade, Economy, dll">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Artikel</label>
                        <textarea name="content" class="form-control" rows="8" required>{{ old('content') }}</textarea>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublished" {{ old('is_published') ? 'checked' : '' }}>
                        <label class="form-check-label" for="isPublished">Publikasikan sekarang</label>
                    </div>

                    <button type="submit" class="btn btn-add">Simpan Artikel</button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Batal</a>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>