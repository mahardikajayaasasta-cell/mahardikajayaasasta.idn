<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController
{
    public function index()
    {
        $users = User::karyawan()->orderBy('name')->paginate(15);
        return view('admin.karyawan.index', compact('users'));
    }

    public function create()
    {
        return view('admin.karyawan.form', ['user' => new User(), 'action' => 'create']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'employee_id' => 'nullable|string|unique:users',
            'department'  => 'nullable|string|max:100',
            'position'    => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'employee_id' => $request->employee_id,
            'department'  => $request->department,
            'position'    => $request->position,
            'phone'       => $request->phone,
            'role'        => 'karyawan',
            'password'    => Hash::make($request->password),
            'is_active'   => true,
        ]);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $karyawan)
    {
        return view('admin.karyawan.form', ['user' => $karyawan, 'action' => 'edit']);
    }

    public function update(Request $request, User $karyawan)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'email', Rule::unique('users')->ignore($karyawan->id)],
            'employee_id' => ['nullable', 'string', Rule::unique('users')->ignore($karyawan->id)],
            'department'  => 'nullable|string|max:100',
            'position'    => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'employee_id', 'department', 'position', 'phone']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $karyawan->update($data);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $karyawan)
    {
        $karyawan->update(['is_active' => false]);
        return back()->with('success', 'Karyawan berhasil dinonaktifkan.');
    }
}
