@extends('layouts.app')
@section('title', $action === 'create' ? 'Tambah Karyawan' : 'Edit Karyawan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.karyawan.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-blue-600 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
        <h2 class="text-2xl font-bold text-slate-800">{{ $action === 'create' ? 'Tambah Karyawan Baru' : 'Edit Data Karyawan' }}</h2>
        <p class="text-slate-500 text-sm mt-1">Lengkapi informasi biodata dan akun akses karyawan</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ $action === 'create' ? route('admin.karyawan.store') : route('admin.karyawan.update', $user) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @if($action === 'edit')
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="Contoh: Budi Santoso">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="budi@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Employee ID -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">NIP / ID Karyawan</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="Contoh: EMP-12345">
                    @error('employee_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Departemen</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="Contoh: IT, Marketing">
                    @error('department') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jabatan</label>
                    <input type="text" name="position" value="{{ old('position', $user->position) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="Contoh: Staff, Manager">
                    @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="0812...">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Status (in Edit only) -->
                @if($action === 'edit')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status Akun</label>
                    <select name="is_active" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all">
                        <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $user->is_active) ? '' : 'selected' }}>Non-Aktif</option>
                    </select>
                </div>
                @endif

                <div class="md:col-span-2 mt-4 pt-4 border-t border-slate-100">
                    <p class="text-sm font-bold text-slate-800 mb-4">Pengaturan Password</p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password {{ $action === 'edit' ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" name="password" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="********">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all"
                        placeholder="********">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.karyawan.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-200">
                    {{ $action === 'create' ? 'Simpan Karyawan' : 'Simpan Perubahan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
