<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Panel</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @include('admin.partials.tabs')

                <div class="admin-card-header">
                    <h5 class="mb-0 fw-bold">Dataset Pelabuhan</h5>
                    <a href="{{ route('admin.ports.create') }}" class="btn btn-add btn-sm">+ Tambah Pelabuhan</a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>UN/LOCODE</th>
                                <th>Nama Pelabuhan</th>
                                <th>Negara</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Tipe</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ports as $port)
                                <tr>
                                    <td>{{ $port->unlocode ?? '-' }}</td>
                                    <td>{{ $port->name }}</td>
                                    <td>{{ $port->country->name ?? '-' }}</td>
                                    <td>{{ $port->latitude }}</td>
                                    <td>{{ $port->longitude }}</td>
                                    <td>{{ $port->port_type ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.ports.edit', $port) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.ports.destroy', $port) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pelabuhan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pelabuhan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>