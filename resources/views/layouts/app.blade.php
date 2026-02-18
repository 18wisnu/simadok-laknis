<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Dashboard') - SIMADOK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .glass { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .nav-pill { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-pill.active { color: #4f46e5; }
        .nav-pill.active i { transform: translateY(-4px); transition: transform 0.3s ease; }
        .safe-area-inset-bottom { padding-bottom: env(safe-area-inset-bottom); }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        /* Premium Toasts */
        @keyframes slideInUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .toast { animation: slideInUp 0.4s ease-out; }
    </style>
</head>
<body class="pb-28 antialiased">
    <!-- Top Nav -->
    <nav class="sticky top-0 z-50 glass border-b border-gray-100/50 px-5 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <i class="fas fa-camera-retro text-sm"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-gray-800">SIMADOK</h1>
        </div>
        <a href="{{ route('profile') }}" class="flex items-center gap-2 p-1 pl-3 bg-white border border-gray-100 rounded-2xl hover:shadow-md transition-all">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ explode(' ', auth()->user()->name)[0] }}</span>
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4f46e5&color=fff' }}" 
                 class="w-7 h-7 rounded-lg object-cover" alt="Avatar">
        </a>
    </nav>

    <main class="p-4 max-w-4xl mx-auto space-y-6">
        @yield('content')
    </main>

    <!-- Bottom Mobile Nav -->
    <nav class="fixed bottom-4 left-4 right-4 glass border border-white/20 px-2 py-3 flex justify-between items-center z-[100] rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 safe-area-inset-bottom">
        <a href="{{ route('dashboard') }}" class="nav-pill flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('dashboard') ? 'active' : 'text-gray-400' }}">
            <i class="fas fa-house-chimney text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Home</span>
        </a>
        <a href="{{ route('equipments.index') }}" class="nav-pill flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('equipments.*') ? 'active' : 'text-gray-400' }}">
            <i class="fas fa-boxes-stacked text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Alat</span>
        </a>
        <a href="{{ route('schedules.index') }}" class="nav-pill flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('schedules.*') ? 'active' : 'text-gray-400' }}">
            <i class="fas fa-calendar-check text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Jadwal</span>
        </a>
        <a href="{{ route('repairs.index') }}" class="nav-pill flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('repairs.*') ? 'active' : 'text-gray-400' }}">
            <i class="fas fa-screwdriver-wrench text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Servis</span>
        </a>
        <a href="{{ route('profile') }}" class="nav-pill flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('profile') ? 'active' : 'text-gray-400' }}">
            <i class="fas fa-circle-user text-lg"></i>
            <span class="text-[9px] font-bold uppercase tracking-widest">Profil</span>
        </a>
    </nav>

    <!-- Toast Notifications -->
    <div class="fixed bottom-24 left-6 right-6 z-[120] pointer-events-none space-y-3">
        @if(session('success'))
        <div class="toast pointer-events-auto bg-emerald-500 text-white p-4 rounded-2xl shadow-xl flex items-center gap-3 border border-emerald-400/50">
            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-xs font-bold">{{ session('success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="toast pointer-events-auto bg-red-500 text-white p-4 rounded-2xl shadow-xl flex items-center gap-3 border border-red-400/50">
            <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-circle-exclamation"></i>
            </div>
            <p class="text-xs font-bold">{{ session('error') }}</p>
        </div>
        @endif
    </div>

    @stack('scripts')
    <script>
        // Auto-hide toasts
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                toast.style.transition = 'all 0.5s ease-in-out';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        });
    </script>
</body>
</html>

