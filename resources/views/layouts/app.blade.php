
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trim($__env->yieldContent('title', 'EMS Dashboard')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

@php
    $user = auth()->user();
    $role = $currentOrganizationRole ?? $user?->role?->role_name ?? 'Pengguna';

    $menus = [
        ['label' => 'Dashboard', 'icon' => '▦', 'route' => 'dashboard', 'roles' => ['Admin', 'Staf Operasional', 'Vendor']],
        ['label' => 'Event', 'icon' => '▣', 'route' => 'events.index', 'roles' => ['Admin', 'Staf Operasional']],
        ['label' => 'Rundown', 'icon' => '☷', 'route' => 'rundown-items.index', 'roles' => ['Admin', 'Staf Operasional', 'Vendor']],
        ['label' => 'Venue', 'icon' => '⌖', 'route' => 'venues.index', 'roles' => ['Admin', 'Staf Operasional']],
        ['label' => 'Equipment', 'icon' => '▤', 'route' => 'equipment.index', 'roles' => ['Admin', 'Staf Operasional']],
        ['label' => 'Kategori Equipment', 'icon' => '▧', 'route' => 'equipment-categories.index', 'roles' => ['Admin', 'Staf Operasional']],
        ['label' => 'Vendor', 'icon' => '♧', 'route' => 'vendors.index', 'roles' => ['Admin', 'Staf Operasional']],
        ['label' => 'Keuangan', 'icon' => '＄', 'route' => 'financial-transactions.index', 'roles' => ['Admin', 'Staf Operasional']],
        ['label' => 'Evaluasi', 'icon' => '✧', 'route' => 'evaluations.index', 'roles' => ['Admin', 'Staf Operasional']],
        ['label' => 'Laporan', 'icon' => '▥', 'route' => 'reports.index', 'roles' => ['Admin']],
        ['label' => 'Anggota', 'icon' => '♙', 'route' => 'users.index', 'roles' => ['Admin']],
        ['label' => 'Organisasi', 'icon' => '▣', 'route' => 'organizations.index', 'roles' => ['Admin', 'Staf Operasional', 'Vendor']],
    ];
@endphp

<div class="min-h-screen lg:flex">

    <aside class="hidden lg:flex lg:w-64 lg:flex-col bg-slate-950 text-slate-300 fixed inset-y-0 left-0 z-40">
        <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-xl">E</div>
            <div>
                <div class="text-white font-bold text-lg tracking-wide">EMS</div>
                <div class="text-xs text-slate-400">Event Management System</div>
            </div>
        </div>

        <div class="px-5 py-5 border-b border-slate-800">
            <div class="text-xs uppercase tracking-wider text-slate-500 mb-2">Workspace</div>
            <div class="text-sm font-semibold text-white truncate">
                {{ $currentOrganization->name ?? 'EMS Workspace' }}
            </div>
            <div class="text-xs text-slate-400 mt-1">{{ $role }}</div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-1">
            <div class="px-3 mb-3 text-xs uppercase tracking-wider text-slate-500">Menu Utama</div>

            @foreach ($menus as $menu)
                @if (in_array($role, $menu['roles']) && \Illuminate\Support\Facades\Route::has($menu['route']))
                    <a href="{{ route($menu['route']) }}"
                       class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm transition
                       {{ request()->routeIs($menu['route']) ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <span class="w-6 text-center text-lg">{{ $menu['icon'] }}</span>
                        <span>{{ $menu['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="p-4 border-t border-slate-800">
            <a href="{{ route('profile.edit') }}"
               class="block px-3 py-3 rounded-xl hover:bg-slate-800 text-sm">
                ⚙ Pengaturan Profil
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button class="w-full text-left px-3 py-3 rounded-xl hover:bg-red-950/50 text-sm text-red-300">
                    ↪ Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 min-w-0 lg:ml-64">
        <header class="sticky top-0 z-30 h-20 bg-white/90 backdrop-blur border-b border-slate-200 flex items-center justify-between px-5 lg:px-9">
            <div>
                <div class="text-xs text-slate-400">Event Management System</div>
                <h1 class="font-bold text-lg text-slate-900">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right">
                    <div class="text-sm font-semibold text-slate-800">{{ $user?->name }}</div>
                    <div class="text-xs text-slate-500">{{ $role }}</div>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                </div>
            </div>
        </header>

        <section class="p-5 lg:p-9 max-w-[1600px] mx-auto">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </section>

        <footer class="px-5 lg:px-9 py-5 text-xs text-slate-400 border-t border-slate-200">
            EMS — Event Management System
        </footer>
    </main>
</div>

</body>
</html>
