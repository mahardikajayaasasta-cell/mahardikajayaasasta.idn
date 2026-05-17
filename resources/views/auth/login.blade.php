<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MJA Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('logo-mja.jpg') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .login-title {
            color: #2d3748;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .input-group svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }
        .input-field {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            outline: none;
            transition: all 0.2s;
            color: #4a5568;
        }
        .input-field:focus {
            border-color: #3b5cbd;
            box-shadow: 0 0 0 3px rgba(59, 92, 189, 0.1);
        }
        .btn-login {
            background-color: #4c66c1;
            color: white;
            padding: 0.875rem;
            border-radius: 0.375rem;
            font-weight: 600;
            width: 100%;
            max-width: 320px;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .btn-login:hover {
            background-color: #3b5cbd;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .right-panel {
            background-color: #3b5cbd;
        }
    </style>
</head>
<body class="bg-white min-h-screen flex overflow-hidden">
    <!-- Left Section: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 md:p-16 lg:p-24 bg-white relative z-10">
        <div class="w-full max-w-md">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center overflow-hidden border border-slate-100 shadow-md p-1">
                    <img src="{{ asset('logo-mja.jpg') }}" alt="MJA Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-wide leading-none">Mahardika Jaya Asasta</h2>
                    <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wider font-semibold">Sistem Absensi Digital Karyawan</p>
                </div>
            </div>
            <h1 class="login-title">Log in<span class="text-[#3b5cbd]">.</span></h1>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Username/Email Field -->
                <div class="input-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input-field"
                           placeholder="Username">
                </div>
                @error('email')
                    <p class="mt-[-1rem] mb-4 text-sm text-red-500">{{ $message }}</p>
                @enderror

                <!-- Password Field -->
                <div class="input-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <input id="password" type="password" name="password" required
                           class="input-field"
                           placeholder="Password">
                    
                    <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-[-1rem] mb-4 text-sm text-red-500">{{ $message }}</p>
                @enderror

                <div class="pt-4">
                    <button type="submit" class="btn-login">
                        Log in
                    </button>
                </div>

                <!-- Remember Me (Hidden to match UI strictly, but kept for functionality if needed) -->
                <div class="hidden flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-[#3b5cbd] border-gray-300 rounded focus:ring-[#3b5cbd]">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-600">Remember me</label>
                </div>
            </form>

            <div class="mt-12 text-xs text-gray-400 border-t border-gray-100 pt-6">
                <p>Default Login:</p>
                <p>Admin: <strong>admin@absensi.app</strong> / <strong>password</strong></p>
                <p>Karyawan: <strong>budi@absensi.app</strong> / <strong>password</strong></p>
            </div>
        </div>
    </div>

    <!-- Right Section: Blue Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 items-center justify-center relative overflow-hidden">
        <div class="absolute -right-24 -bottom-24 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-24 -top-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col items-center text-center px-12 max-w-lg">
            <div class="w-36 h-36 bg-white rounded-full p-4 shadow-2xl flex items-center justify-center overflow-hidden mb-8 border border-amber-400/50 backdrop-blur-sm animate-[pulse_3s_infinite]">
                <img src="{{ asset('logo-mja.jpg') }}" alt="MJA Logo Large" class="w-full h-full object-contain">
            </div>
            <h3 class="text-2xl font-bold text-white tracking-wider mb-3">MAHARDIKA JAYA ASASTA</h3>
            <div class="w-24 h-1 bg-gradient-to-r from-amber-400 to-amber-600 rounded-full mb-6"></div>
            <p class="text-slate-300 text-sm leading-relaxed font-light">
                Platform absensi digital berkinerja tinggi, mengintegrasikan verifikasi lokasi GPS presisi tinggi dan pencatatan berbasis foto kamera untuk efisiensi operasional perusahaan.
            </p>
        </div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]"></div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Change icon based on state
                if (type === 'text') {
                    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                } else {
                    eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                }
            });
        }
    </script>
</body>
</html>
