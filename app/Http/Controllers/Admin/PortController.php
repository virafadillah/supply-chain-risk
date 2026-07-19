<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortController extends Controller
{
    public function index(): View
    {
        $ports = Port::with('country')->orderBy('name')->get();

        return view('admin.ports.index', compact('ports'));
    }

    public function create(): View
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.ports.create', compact('countries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'unlocode' => 'nullable|string|max:10|unique:ports,unlocode',
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'port_type' => 'nullable|string|max:50',
        ]);

        Port::create($validated);

        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil ditambahkan.');
    }

    public function edit(Port $port): View
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function update(Request $request, Port $port): RedirectResponse
    {
        $validated = $request->validate([
            'unlocode' => 'nullable|string|max:10|unique:ports,unlocode,' . $port->id,
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'port_type' => 'nullable|string|max:50',
        ]);

        $port->update($validated);

        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil diperbarui.');
    }

    public function destroy(Port $port): RedirectResponse
    {
        $port->delete();

        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil dihapus.');
    }
}