<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminLocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')->paginate(10);
        return view('admin.lokasi.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.lokasi.form', ['location' => new Location(), 'action' => 'create']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'nullable|string|max:500',
            'latitude'   => 'required|numeric|between:-90,90',
            'longitude'  => 'required|numeric|between:-180,180',
            'radius'     => 'required|integer|min:10|max:5000',
            'work_start' => 'required|date_format:H:i',
            'work_end'   => 'required|date_format:H:i',
            'late_after' => 'required|date_format:H:i',
        ]);

        Location::create($request->validated() + [
            'work_start' => $request->work_start . ':00',
            'work_end'   => $request->work_end . ':00',
            'late_after' => $request->late_after . ':00',
            'is_active'  => true,
        ]);

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi kerja berhasil ditambahkan.');
    }

    public function edit(Location $lokasi)
    {
        return view('admin.lokasi.form', ['location' => $lokasi, 'action' => 'edit']);
    }

    public function update(Request $request, Location $lokasi)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'nullable|string|max:500',
            'latitude'   => 'required|numeric|between:-90,90',
            'longitude'  => 'required|numeric|between:-180,180',
            'radius'     => 'required|integer|min:10|max:5000',
            'work_start' => 'required|date_format:H:i',
            'work_end'   => 'required|date_format:H:i',
            'late_after' => 'required|date_format:H:i',
        ]);

        $lokasi->update([
            'name'       => $request->name,
            'address'    => $request->address,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'radius'     => $request->radius,
            'work_start' => $request->work_start . ':00',
            'work_end'   => $request->work_end . ':00',
            'late_after' => $request->late_after . ':00',
            'is_active'  => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi kerja berhasil diperbarui.');
    }

    public function destroy(Location $lokasi)
    {
        $lokasi->update(['is_active' => false]);
        return back()->with('success', 'Lokasi berhasil dinonaktifkan.');
    }
}
