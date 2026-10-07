<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <title>{{ config('app.name', 'Literra') }} - Daftar</title>
</head>
<body class="min-h-screen bg-[#f9f9ff] text-[#151c27] font-['Inter']">
<main class="flex min-h-screen w-full flex-col md:flex-row">
    {{-- Left Side: Editorial Content --}}
    <section class="relative w-full md:w-1/2 lg:w-5/12 flex flex-col justify-between p-12 lg:p-20 overflow-hidden bg-[#0A192F]">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0A192F] to-[#0f2040]"></div>
        <div class="relative z-10">
            <a href="/">
                <h1 class="font-['Playfair_Display'] text-2xl font-bold text-[#FED65B] tracking-tight">Literra</h1>
            </a>
        </div>
        <div class="relative z-10 max-w-md">
            <span class="text-[#FED65B] text-4xl mb-6 opacity-50 block font-serif">&ldquo;</span>
            <blockquote class="mb-8">
                <p class="font-['Playfair_Display'] text-3xl text-white leading-tight italic">
                    "Membaca adalah alat yang paling dasar untuk menjadi orang yang merdeka."
                </p>
                <footer class="mt-4 text-sm text-[#FED65B] uppercase tracking-widest font-medium">— Frederick Douglass</footer>
            </blockquote>
        </div>
        <div class="relative z-10">
            <div class="w-16 h-1 bg-[#FED65B] mb-4"></div>
            <p class="text-xs text-white/60">Est. 2024 • Kurasi Sastra Modern</p>
        </div>
    </section>

    {{-- Right Side: Registration Form --}}
    <section class="w-full md:w-1/2 lg:w-7/12 flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-[480px]">
            <header class="mb-12">
                <h2 class="font-['Playfair_Display'] text-3xl font-bold text-[#0A192F] mb-2">Bergabung dengan Literra</h2>
                <p class="text-[#44474D]">Mulailah perjalanan Anda di antara ribuan judul pilihan.</p>
            </header>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-[#44474D] mb-2" for="name">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap Anda"
                           class="w-full bg-transparent border-b border-[#c5c6cd] py-3 focus:outline-none focus:border-[#0A192F] transition-all @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#44474D] mb-2" for="email">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="contoh@email.com"
                           class="w-full bg-transparent border-b border-[#c5c6cd] py-3 focus:outline-none focus:border-[#0A192F] transition-all @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-[#44474D] mb-2" for="password">Kata Sandi</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••"
                                   class="w-full bg-transparent border-b border-[#c5c6cd] py-3 pr-10 focus:outline-none focus:border-[#0A192F] transition-all @error('password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-0 top-1/2 -translate-y-1/2 text-[#44474D] hover:text-[#0A192F] transition-colors">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#44474D] mb-2" for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••"
                                   class="w-full bg-transparent border-b border-[#c5c6cd] py-3 pr-10 focus:outline-none focus:border-[#0A192F] transition-all">
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-0 top-1/2 -translate-y-1/2 text-[#44474D] hover:text-[#0A192F] transition-colors">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input id="terms" type="checkbox" class="w-5 h-5 rounded border-[#c5c6cd] text-[#FED65B] focus:ring-[#FED65B]">
                    <label class="text-sm text-[#44474D]" for="terms">
                        Saya setuju dengan <a href="#" class="text-[#0A192F] font-semibold underline underline-offset-4 decoration-[#FED65B]/50 hover:decoration-[#FED65B] transition-all">Syarat & Ketentuan</a> Literra.
                    </label>
                </div>

                <button type="submit" class="w-full py-4 bg-[#FED65B] hover:bg-[#e9c349] text-[#0A192F] font-semibold text-sm uppercase tracking-widest rounded-lg shadow-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span>Daftar Sekarang</span>
                    <span class="material-symbols-outlined">arrow_right_alt</span>
                </button>
            </form>

            <div class="text-center pt-8">
                <p class="text-[#44474D]">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-[#0A192F] font-bold ml-1 hover:text-[#FED65B] transition-colors">Masuk di sini</a>
                </p>
            </div>
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
