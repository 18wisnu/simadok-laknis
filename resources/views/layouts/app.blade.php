<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIMADOK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .nav-active { color: #4f46e5; border-bottom: 2px solid #4f46e5; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(8px); }
    </style>
</head>
<body class="pb-24">
    <!-- Top Nav -->
    <nav class="sticky top-0 z-50 glass border-b border-gray-200 px-4 py-3 flex items-center justify-between">
        <h1 class="text-xl font-bold text-indigo-600">SIMADOK</h1>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-600">{{ auth()->user()->name }}</span>
            <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full border border-indigo-200" alt="Avatar">
        </div>
    </nav>

    <main class="p-4 max-w-4xl mx-auto">
        @yield('content')
    </main>

    <!-- Bottom Mobile Nav -->
    <nav class="fixed bottom-0 left-0 right-0 glass border-t border-gray-200 px-6 py-3 flex justify-between items-center z-50">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="fas fa-home text-xl"></i>
            <span class="text-[10px]">Beranda</span>
        </a>
        <a href="{{ route('equipments.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('equipments.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="fas fa-box text-xl"></i>
            <span class="text-[10px]">Alat</span>
        </a>
        <a href="{{ route('schedules.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('schedules.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="fas fa-calendar-alt text-xl"></i>
            <span class="text-[10px]">Jadwal</span>
        </a>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('users.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="fas fa-users-cog text-xl"></i>
            <span class="text-[10px]">User</span>
        </a>
        @endif
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex flex-col items-center gap-1 text-gray-400">
                <i class="fas fa-sign-out-alt text-xl"></i>
                <span class="text-[10px]">Keluar</span>
            </button>
        </form>
    </nav>

    @stack('scripts')
</body>
</html>
