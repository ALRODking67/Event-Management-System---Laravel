
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
        $userRole = $currentOrganizationRole ?? Auth::user()->role?->role_name;

        $isVendor = $userRole === 'Vendor';
        $isAdmin = $userRole === 'Admin';

        $hasCurrentOrganization = isset($currentOrganization);
    @endphp

    <aside class="ems-sidebar">

        <div class="ems-logo">EMS</div>
        <div class="ems-subtitle">EVENT MANAGEMENT SYSTEM</div>

        {{-- MENU UTAMA --}}
        <div class="ems-menu-title">MENU UTAMA</div>

        <a href="{{ route('dashboard') }}"
           class="ems-menu {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            ▦ &nbsp; Dashboard
        </a>

        @if (!$isVendor)
            <a href="{{ route('events.index') }}"
               class="ems-menu {{ request()->routeIs('events.*') ? 'active' : '' }}">
                ◈ &nbsp; Kelola Acara
            </a>

            <a href="{{ route('event-concepts.index') }}"
               class="ems-menu {{ request()->routeIs('event-concepts.*') ? 'active' : '' }}">
                ◇ &nbsp; Konsep Acara
            </a>

            <a href="{{ route('rundowns.index') }}"
               class="ems-menu {{ request()->routeIs('rundowns.*') ? 'active' : '' }}">
                ◷ &nbsp; Rundown Acara
            </a>

            {{-- SUMBER DAYA --}}
            <div class="ems-menu-title">SUMBER DAYA</div>

            @if ($hasCurrentOrganization)

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

            @endif

            <a href="{{ route('event-equipment.index') }}"
               class="ems-menu {{ request()->routeIs('event-equipment.*') ? 'active' : '' }}">
                ▧ &nbsp; Penugasan Peralatan
            </a>

            <a href="{{ route('vendors.index') }}"
               class="ems-menu {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                ▣ &nbsp; Kelola Vendor
            </a>

            <a href="{{ route('event-vendors.index') }}"
               class="ems-menu {{ request()->routeIs('event-vendors.*') ? 'active' : '' }}">
                ▣ &nbsp; Penugasan Vendor
            </a>
        @endif

        {{-- KONFIRMASI VENDOR --}}
        <a href="{{ route('vendor-confirmations.index') }}"
           class="ems-menu {{ request()->routeIs('vendor-confirmations.*') ? 'active' : '' }}">
            ✓ &nbsp; Konfirmasi Vendor
        </a>

        {{-- EVALUASI --}}
        @if (!$isVendor)
            <div class="ems-menu-title">EVALUASI</div>

            <a href="{{ route('evaluations.index') }}"
               class="ems-menu {{ request()->routeIs('evaluations.*') ? 'active' : '' }}">
                ▥ &nbsp; Evaluasi & ROI
            </a>
        @endif

        {{-- LAPORAN --}}
        @if ($isAdmin)
            <a href="{{ route('reports.index') }}"
               class="ems-menu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                ▤ &nbsp; Laporan Evaluasi
            </a>
        @endif

        {{-- AKUN --}}
        <div class="ems-menu-title">AKUN</div>

        {{-- MANAJEMEN PENGGUNA KHUSUS ADMIN --}}
        @if ($isAdmin)
            <a href="{{ route('users.index') }}"
               class="ems-menu {{ request()->routeIs('users.*') ? 'active' : '' }}">
                ♙ &nbsp; Manajemen Pengguna
            </a>
        @endif

        {{-- INFORMASI PENGGUNA --}}
        <div class="ems-menu">
            <strong>{{ Auth::user()->name }}</strong>

            <div style="font-size:11px;color:#94A3B8;margin-top:5px;">
                {{ $userRole ?? 'Tidak memiliki role' }}
            </div>
        </div>

        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="ems-menu"
                style="background:none;border:0;width:100%;text-align:left;cursor:pointer;">
                ↪ &nbsp; Keluar
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

            <div class="ems-user">
                {{ Auth::user()->name }}
            </div>
        </div>

        @yield('content')

    </main>

</body>
</html>
