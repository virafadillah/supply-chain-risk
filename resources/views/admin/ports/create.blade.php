<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin — Tambah Pelabuhan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.ports.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">UNLOCODE</label>
                        <input type="text" name="unlocode" class="form-control" value="{{ old('unlocode') }}" maxlength="10">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Pelabuhan</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Negara</label>
                        <select name="country_id" class="form-select" required>
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Pelabuhan</label>
                        <select name="port_type" class="form-select">
                            <option value="seaport" {{ old('port_type') == 'seaport' ? 'selected' : '' }}>Seaport</option>
                            <option value="airport" {{ old('port_type') == 'airport' ? 'selected' : '' }}>Airport</option>
                            <option value="dry port" {{ old('port_type') == 'dry port' ? 'selected' : '' }}>Dry Port</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary">Batal</a>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>