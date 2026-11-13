
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'EMS | Event Management System')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            background: #F8FAFC;
            font-family: Arial, sans-serif;
            color: #172033;
        }

        .ems-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 250px;
            background: #111C2E;
            color: white;
            padding: 28px 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .ems-logo {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .ems-subtitle {
            color: #94A3B8;
            font-size: 10px;
            letter-spacing: 2px;
            margin-top: 3px;
        }

        .ems-menu-title {
            color: #64748B;
            font-size: 10px;
            font-weight: bold;
            margin: 30px 0 12px;
            letter-spacing: 1.5px;
        }

        .ems-menu {
            display: block;
            padding: 12px 14px;
            margin-bottom: 6px;
            border-radius: 9px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 13px;
        }

        .ems-menu:hover {
            background: #1E293B;
            color: white;
        }

        .ems-menu.active {
            background: #2F65F6;
            color: white;
            font-weight: bold;
        }

        .ems-main {
            margin-left: 250px;
            padding: 32px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .ems-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .ems-user {
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 12px 18px;
        }

        @media (max-width: 650px) {
            .ems-sidebar {
                position: static;
                width: 100%;
                max-height: 400px;
            }

            .ems-main {
                margin-left: 0;
                padding: 20px;
            }

            .ems-topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    @php
        $user = Auth::user();

        $hasCurrentOrganization = isset($currentOrganization)
            && $currentOrganization instanceof \App\Models\Organization;

        $userRole = $currentOrganizationRole
            ?? $user->role?->role_name;

        $isVendor = $userRole === 'Vendor';
        $isAdmin = $userRole === 'Admin';

        $canManageOrganization = $hasCurrentOrganization
            && in_array($userRole, ['Admin', 'Staf Operasional']);
    @endphp

    <aside class="ems-sidebar">

        <div class="ems-logo">EMS</div>
        <div class="ems-subtitle">EVENT MANAGEMENT SYSTEM</div>

        {{-- MENU UTAMA --}}
        <div class="ems-menu-title">MENU UTAMA</div>

        <a href="{{ $hasCurrentOrganization
                ? route('organizations.dashboard', ['organization' => $currentOrganization->slug])
                : route('dashboard') }}"
           class="ems-menu {{ request()->routeIs('dashboard', 'organizations.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        {{-- MENU VENDOR --}}
        @if ($hasCurrentOrganization && $isVendor)

            <div class="ems-menu-title">LAYANAN VENDOR</div>

            <a href="{{ route('vendor-confirmations.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('vendor-negotiations.*') ? 'active' : '' }}">
                Konfirmasi Vendor
            </a>

        @endif

        {{-- FITUR PENGELOLAAN ORGANISASI --}}
        @if ($canManageOrganization)

            <div class="ems-menu-title">PENGELOLAAN ACARA</div>

            <a href="{{ route('events.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('events.*') ? 'active' : '' }}">
                Kelola Acara
            </a>

            <a href="{{ route('event-concepts.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('event-concepts.*') ? 'active' : '' }}">
                Konsep Acara
            </a>

            <a href="{{ route('rundowns.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('rundowns.*') ? 'active' : '' }}">
                Rundown Acara
            </a>

            {{-- SUMBER DAYA --}}
            <div class="ems-menu-title">SUMBER DAYA</div>

            <a href="{{ route('venues.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('venues.*') ? 'active' : '' }}">
                Venue
            </a>

            <a href="{{ route('equipment.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
                Peralatan Teknis
            </a>

            <a href="{{ route('equipment-categories.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('equipment-categories.*') ? 'active' : '' }}">
                Kategori Peralatan
            </a>

            <a href="{{ route('event-equipment.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('event-equipment.*') ? 'active' : '' }}">
                Penugasan Peralatan
            </a>

            <a href="{{ route('vendors.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                Kelola Vendor
            </a>

            @if ($userRole === 'Admin')
                <a href="{{ route('staff.index', ['organization' => $currentOrganization->slug]) }}"
                class="ems-menu {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                    Kelola Staf
                </a>
            @endif

           <a href="{{ route('vendor-confirmations.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('event-vendors.*', 'vendor-confirmations.*', 'vendor-negotiations.*') ? 'active' : '' }}">
                Penugasan Vendor
            </a>

            {{-- KEUANGAN --}}
            <div class="ems-menu-title">KEUANGAN</div>

            <a href="{{ route('financial-transactions.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('financial-transactions.*') ? 'active' : '' }}">
                Manajemen Keuangan
            </a>

            {{-- EVALUASI --}}
            <div class="ems-menu-title">EVALUASI</div>

            <a href="{{ route('evaluations.index', ['organization' => $currentOrganization->slug]) }}"
               class="ems-menu {{ request()->routeIs('evaluations.*') ? 'active' : '' }}">
                Evaluasi &amp; ROI
            </a>

            {{-- LAPORAN --}}
            @if ($isAdmin)

                <div class="ems-menu-title">LAPORAN</div>

                <a href="{{ route('reports.index', ['organization' => $currentOrganization->slug]) }}"
                   class="ems-menu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    Laporan Evaluasi
                </a>

            @endif

            {{-- MANAJEMEN AKUN --}}
            @if ($isAdmin)

                <div class="ems-menu-title">MANAJEMEN ORGANISASI</div>

                <a href="{{ route('users.index', ['organization' => $currentOrganization->slug]) }}"
                   class="ems-menu {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    Manajemen Pengguna
                </a>

            @endif

        @endif

        {{-- INFORMASI AKUN --}}
        <div class="ems-menu-title">AKUN</div>

        <div class="ems-menu">
            <strong>{{ $user->name }}</strong>

            <div style="font-size:11px;color:#94A3B8;margin-top:5px;">
                {{ $userRole ?? 'Tidak memiliki role' }}
            </div>

            @if ($hasCurrentOrganization)
                <div style="font-size:11px;color:#94A3B8;margin-top:5px;">
                    {{ $currentOrganization->name }}
                </div>
            @endif
        </div>

        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                    class="ems-menu"
                    style="background:none;border:0;width:100%;text-align:left;cursor:pointer;">
                Keluar
            </button>
        </form>

    </aside>

    {{-- KONTEN UTAMA --}}
    <main class="ems-main">

        <div class="ems-topbar">

            <div>
                <div style="color:#94A3B8;font-size:13px;">
                    Event Management System
                </div>

                <h1 style="font-size:24px;font-weight:bold;margin-top:6px;">
                    @yield('page-heading', 'EMS')
                </h1>
            </div>

            <div style="display:flex; align-items:center; gap:15px;">

                @if ($hasCurrentOrganization)
                    @php
                        $recentNotifications = $user->notifications()
                            ->latest()
                            ->take(5)
                            ->get();

                        $unreadNotificationCount = $user->unreadNotifications()->count();
                    @endphp

                    <details style="position:relative;">
                        <summary style="list-style:none; cursor:pointer; position:relative; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; color:#334155; display:flex; align-items:center; gap:8px;">
                            <span>🔔 Notifikasi</span>

                            @if ($unreadNotificationCount > 0)
                                <span style="
                                    background:#dc2626;
                                    color:white;
                                    border-radius:50%;
                                    min-width:20px;
                                    height:20px;
                                    padding:0 5px;
                                    display:inline-flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:11px;
                                    font-weight:bold;
                                ">
                                    {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                                </span>
                            @endif
                        </summary>

                        <div style="position:absolute; right:0; top:45px; width:320px; max-width:80vw; background:white; border:1px solid #e2e8f0; border-radius:9px; box-shadow:0 8px 25px rgba(0,0,0,.10); z-index:1000; padding:10px;">

                            @forelse ($recentNotifications as $notification)
                                <a href="{{ route('notifications.open', [
                                    'organization' => $currentOrganization->slug,
                                    'notification' => $notification->id,
                                ]) }}"
                                style="display:block; padding:11px; border-radius:6px; text-decoration:none; color:#334155; background:{{ $notification->read_at ? '#ffffff' : '#eff6ff' }}; margin-bottom:5px;">

                                    <strong style="font-size:13px;">
                                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                                    </strong>

                                    <p style="font-size:12px; margin:5px 0; color:#64748b;">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>

                                    <span style="font-size:11px; color:#94a3b8;">
                                        {{ $notification->created_at->format('d-m-Y H:i') }}
                                    </span>
                                </a>
                            @empty
                                <p style="font-size:13px; color:#64748b; padding:10px;">
                                    Belum ada notifikasi.
                                </p>
                            @endforelse
                        </div>
                    </details>
                @endif

                <div class="ems-user">
                    {{ $user->name }}
                </div>
            </div>

        </div>

        @yield('content')

    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.ems-sidebar');

        if (!sidebar) return;

        const storageKey = 'ems_sidebar_scroll';

        // Pulihkan posisi scroll sebelumnya.
        const savedPosition = sessionStorage.getItem(storageKey);

        if (savedPosition !== null) {
            sidebar.scrollTop = parseInt(savedPosition, 10);
        }

        // Simpan posisi ketika sidebar di-scroll.
        sidebar.addEventListener('scroll', function () {
            sessionStorage.setItem(storageKey, sidebar.scrollTop);
        });

        // Simpan posisi sebelum berpindah halaman melalui menu.
        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                sessionStorage.setItem(storageKey, sidebar.scrollTop);
            });
        });
    });
    </script>

</body>
</html>
