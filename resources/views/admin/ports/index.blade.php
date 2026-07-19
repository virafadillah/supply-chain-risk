<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin — Manage Ports</h2>
            <a href="{{ route('admin.ports.create') }}" class="btn btn-sm btn-primary">+ Tambah Pelabuhan</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>UNLOCODE</th>
                            <th>Nama</th>
                            <th>Negara</th>
                            <th>Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ports as $port)
                            <tr>
                                <td>{{ $port->unlocode }}</td>
                                <td>{{ $port->name }}</td>
                                <td>{{ $port->country->name }}</td>
                                <td>{{ ucfirst($port->port_type) }}</td>
                                <td>
                                    <a href="{{ route('admin.ports.edit', $port) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.ports.destroy', $port) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pelabuhan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>