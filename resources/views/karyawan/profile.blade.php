@extends('layouts.app')

@section('title', 'Pengaturan Profile Karyawan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Pengaturan Profile</h1>
        <p class="text-sm text-slate-500 mt-1">Perbarui informasi akun dan kata sandi Anda.</p>
    </div>

    @if($errors->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
            {{ $errors->first('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('karyawan.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Photo Section -->
            <div class="flex flex-col items-center pb-6 border-b border-slate-100">
                <div class="relative group">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-slate-100 border-4 border-white shadow-md">
                        @if($user->profile_photo_url)
                            <img id="profile-preview" src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                        @else
                            <div id="profile-initial" class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-500 text-white text-3xl font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <img id="profile-preview" src="#" class="w-full h-full object-cover hidden">
                        @endif
                    </div>
                    <label for="photo-upload" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer shadow-lg hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                        </svg>
                        <input type="file" name="photo" id="photo-upload" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <p class="text-xs text-slate-500 mt-3 text-center">Klik ikon kamera untuk ganti foto<br>(Maks. 2MB)</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr class="border-slate-200">

            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-800">Ubah Kata Sandi</h3>
                <p class="text-xs text-slate-500">Kosongkan jika tidak ingin mengubah kata sandi.</p>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors pr-10 @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('password', 'eyeIconPassword')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <svg id="eyeIconPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors pr-10">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eyeIconConfirm')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                            <svg id="eyeIconConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors shadow-sm shadow-blue-600/20">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
        }
    }

    function previewImage(input) {
        const preview = document.getElementById('profile-preview');
        const initial = document.getElementById('profile-initial');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (initial) initial.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
