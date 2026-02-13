<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMADOK LAKNIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-500 to-indigo-700 min-h-screen flex items-center justify-center p-4">
    <div class="glass p-8 rounded-3xl shadow-2xl w-full max-w-md text-center">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">SIMADOK</h1>
            <p class="text-gray-600">Sistem Pemakaian Alat</p>
        </div>

        <div class="space-y-6">
            <p class="text-xs text-gray-400 uppercase tracking-widest text-center">Masuk ke SIMADOK</p>
            
            @if(session('error'))
            <div class="bg-red-50 text-red-600 p-3 rounded-xl text-xs font-medium border border-red-100 italic">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase ml-2">Email</label>
                    <input type="email" name="email" required class="w-full px-5 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="nama@email.com">
                </div>
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase ml-2">Kata Sandi</label>
                    <input type="password" name="password" required class="w-full px-5 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all">
                    Masuk Sekarang
                </button>
            </form>

            <div class="relative py-2">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-100"></div>
                </div>
                <div class="relative flex justify-center text-[10px] uppercase font-bold">
                    <span class="bg-white px-4 text-gray-300">Atau</span>
                </div>
            </div>
            
            <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-3 w-full py-3.5 px-6 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-2xl transition-all duration-300 shadow-sm border border-gray-100 active:scale-95">
                <svg class="w-5 h-5" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.13-.45-4.63H24v9.03h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.58z"></path>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24s.92 7.54 2.56 10.78l7.97-6.19z"></path>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                </svg>
                <span class="text-sm">Masuk dengan Google</span>
            </a>
        </div>

        <div class="mt-12 text-xs text-gray-500">
            &copy; 2026 SIMADOK LAKNIS. Modern & Secure.
        </div>
    </div>
</body>
</html>
