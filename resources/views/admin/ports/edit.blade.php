<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Panel</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @include('admin.partials.tabs')

                <h5 class="fw-bold mb-4">Edit Pelabuhan</h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.ports.update', $port) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">UN/LOCODE (opsional)</label>
                            <input type="text" name="unlocode" class="form-control" value="{{ old('unlocode', $port->unlocode) }}" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Pelabuhan</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $port->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Negara</label>
                            <select name="country_id" class="form-select" required>
                                <option value="">-- Pilih Negara --</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $port->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipe Pelabuhan (opsional)</label>
                            <input type="text" name="port_type" class="form-control" value="{{ old('port_type', $port->port_type) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $port->latitude) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $port->longitude) }}" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-add">Simpan Perubahan</button>
                        <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>