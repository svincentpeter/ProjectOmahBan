<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/login.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        /* Animasi Background yang lebih halus */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 10s infinite;
        }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        /* Custom Focus Ring untuk Input */
        .input-focus-effect:focus {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); /* Primary color shadow */
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 relative overflow-hidden selection:bg-blue-500 selection:text-white">

    <div class="fixed inset-0 -z-10 h-full w-full bg-slate-50 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] [background-size:24px_24px]"></div>
    
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="w-full max-w-[420px] relative">
        
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/60 p-8 sm:p-10 relative z-10">
            
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/Logo.png') }}" alt="Omah Ban Logo" class="h-16 w-auto object-contain drop-shadow-sm hover:scale-105 transition-transform duration-300">
                </div>
                
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang Kembali</h1>
                <p class="text-slate-500 text-sm mt-2 font-medium">Masuk untuk mengelola Omah Ban</p>
            </div>

            @if(Session::has('account_deactivated'))
                <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-200 rounded-xl bg-red-50/80 backdrop-blur-sm" role="alert">
                    <svg class="flex-shrink-0 inline w-5 h-5 me-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                    <span class="font-medium">{{ Session::get('account_deactivated') }}</span>
                </div>
            @endif

            @if($errors->any() && !Session::has('account_deactivated'))
                 <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-200 rounded-xl bg-red-50/80 backdrop-blur-sm">
                    <svg class="flex-shrink-0 inline w-5 h-5 me-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <div>
                        <span class="font-bold">Gagal Masuk!</span> Periksa kembali kredensial Anda.
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        </div>
                        <input type="email" name="email" id="email" 
                            class="input-focus-effect block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl transition-all duration-200 placeholder-slate-400 focus:bg-white @error('email') border-red-500 bg-red-50 text-red-900 placeholder-red-300 @enderror" 
                            placeholder="nama@perusahaan.com" required value="{{ old('email') }}" autofocus>
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 font-medium flex items-center mt-1">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Kata Sandi</label>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="input-focus-effect block w-full pl-11 pr-11 py-3 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl transition-all duration-200 placeholder-slate-400 focus:bg-white @error('password') border-red-500 bg-red-50 @enderror" 
                            placeholder="••••••••" required>
                        
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer focus:outline-none" onclick="togglePassword()">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="w-4 h-4 border-slate-300 rounded text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <label for="remember" class="ml-2 text-sm font-medium text-slate-600 cursor-pointer select-none">Ingat Saya</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-all">Lupa kata sandi?</a>
                    @endif
                </div>

                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center shadow-lg shadow-blue-500/30 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 flex justify-center items-center gap-2">
                    Masuk ke Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400 font-medium">
                    &copy; {{ date('Y') }} Omah Ban System. <br class="sm:hidden">
                    Dibuat oleh <a href="#" class="text-slate-500 hover:text-blue-600 transition-colors">Vincent Peter</a>
                </p>
            </div>
        </div>
        
        <div class="absolute -top-4 -right-4 w-24 h-24 bg-dots-pattern opacity-20 hidden sm:block"></div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>