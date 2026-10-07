<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <title>{{ config('app.name', 'Literra') }} - Masuk</title>
</head>
<body class="bg-[#f9f9ff] text-[#151c27] font-['Inter'] overflow-hidden">
<main class="flex h-screen w-full">
    {{-- Left Side: Editorial (Deep Navy) --}}
    <section class="hidden lg:flex lg:w-1/2 relative bg-[#0A192F] overflow-hidden p-20 flex-col justify-between">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0A192F] to-[#0f2040]"></div>
        <div class="relative z-10">
            <h1 class="font-['Playfair_Display'] text-5xl font-bold text-[#FED65B] tracking-tight">Literra</h1>
        </div>
        <div class="relative z-10 max-w-lg mb-20">
            <span class="text-[#FED65B] text-6xl opacity-20 block mb-6 font-serif">&ldquo;</span>
            <blockquote class="font-['Playfair_Display'] text-3xl text-white leading-snug mb-8 italic">
                "Buku adalah cermin: Anda hanya melihat di dalamnya apa yang sudah Anda miliki di dalam diri Anda."
            </blockquote>
            <cite class="text-sm text-[#FED65B] uppercase tracking-widest block not-italic font-medium">— Carlos Ruiz Zafón</cite>
        </div>
        <div class="relative z-10 flex items-center gap-4 opacity-50">
            <div class="h-px w-12 bg-[#FED65B]"></div>
            <p class="text-xs text-white/60">EST. 2024 • MODERN CURATED LIBRARY</p>
        </div>
    </section>

    {{-- Right Side: Login Form --}}
    <section class="w-full lg:w-1/2 flex items-center justify-center p-6 md:p-12">
        <div class="w-full max-w-md">
            <div class="lg:hidden mb-12 text-center">
                <h1 class="font-['Playfair_Display'] text-2xl font-bold text-[#0A192F]">Literra</h1>
            </div>

            <div class="mb-10">
                <h2 class="font-['Playfair_Display'] text-3xl font-bold text-[#0A192F] mb-2">Selamat Datang Kembali</h2>
                <p class="text-[#44474D]">Silakan masuk untuk melanjutkan petualangan literasi Anda.</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#0A192F] uppercase tracking-wide" for="email">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="contoh@email.com"
                           class="w-full px-4 py-3 bg-white border border-[#c5c6cd] rounded-lg text-[#151c27] placeholder:text-[#75777e] focus:outline-none focus:border-[#0A192F] transition-all @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#0A192F] uppercase tracking-wide" for="password">Kata Sandi</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                               class="w-full px-4 py-3 bg-white border border-[#c5c6cd] rounded-lg text-[#151c27] placeholder:text-[#75777e] focus:outline-none focus:border-[#0A192F] transition-all pr-10 @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#44474D] hover:text-[#0A192F] transition-colors">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-[#c5c6cd] text-[#0A192F] focus:ring-[#0A192F]">
                        <span class="text-sm text-[#44474D] group-hover:text-[#0A192F] transition-colors">Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#0A192F] hover:underline decoration-[#FED65B] decoration-2 underline-offset-4 font-medium" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-[#FED65B] hover:bg-[#e9c349] text-[#0A192F] font-semibold text-sm uppercase tracking-widest py-4 px-6 rounded-lg shadow-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2 mt-8">
                    Masuk
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </button>
            </form>

            <p class="text-center mt-12 text-[#44474D]">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-[#0A192F] font-semibold hover:underline decoration-[#FED65B] decoration-2 underline-offset-4 ml-1">Daftar di sini</a>
            </p>
        </div>
    </section>
</main>

<script>
function togglePassword(id, button) {
    const input = document.getElementById(id);
    const icon = button.querySelector('.material-symbols-outlined');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
</body>
</html>
