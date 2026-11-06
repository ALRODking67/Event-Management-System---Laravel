
@extends('layouts.ems')

@section('title', $organization->name . ' | EMS')
@section('page-heading', 'Workspace Organisasi')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h2 class="text-2xl font-bold text-slate-800">
            {{ $organization->name }}
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Kelola anggota dan pantau aktivitas organisasi Anda.
        </p>
    </div>

    {{-- INFORMASI ORGANISASI --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Jumlah Anggota
            </p>

            <p class="mt-3 text-3xl font-bold text-slate-800">
                {{ $organization->members_count }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Anggota terdaftar dalam organisasi
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Jumlah Acara
            </p>

            <p class="mt-3 text-3xl font-bold text-slate-800">
                {{ $organization->events_count }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Acara yang terdaftar dalam organisasi
            </p>
        </div>

    </div>

    {{-- INFORMASI WORKSPACE --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-slate-800">
                    Informasi Workspace
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Identitas dan akses organisasi.
                </p>
            </div>

            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ $membership->role->role_name }}
            </span>
        </div>

        <div class="mt-5">
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                Link Organisasi
            </label>

            <div class="flex flex-col gap-2 sm:flex-row">
                <input
                    type="text"
                    readonly
                    value="{{ url('/o/' . $organization->slug) }}"
                    onclick="this.select()"
                    class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600"
                >

                <button
                    type="button"
                    onclick="navigator.clipboard.writeText('{{ url('/o/' . $organization->slug) }}').then(() => { this.textContent = 'Tersalin!'; }).catch(() => alert('Gagal menyalin tautan. Silakan salin secara manual.'))"
                    class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-700">
                    Salin Link
                </button>
            </div>
        </div>
    </div>

    {{-- UNDANG ANGGOTA --}}
    @if ($membership->role->role_name === 'Admin')
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="mb-5">
                <h3 class="text-lg font-bold text-slate-800">
                    Undang Anggota
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Tambahkan Staf Operasional atau Vendor ke organisasi ini.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="mb-2 font-semibold">Ada kesalahan:</p>

                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST"
                action="{{ route('organizations.invitations.store', $organization->slug) }}">

                @csrf

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Email Anggota
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="nama@email.com"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div class="mb-5">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Peran Anggota
                    </label>

                    <select
                        name="role"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                        <option value="Staf Operasional"
                            {{ old('role') === 'Staf Operasional' ? 'selected' : '' }}>
                            Staf Operasional
                        </option>

                        <option value="Vendor"
                            {{ old('role') === 'Vendor' ? 'selected' : '' }}>
                            Vendor
                        </option>
                    </select>
                </div>

                <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Buat Undangan
                </button>
            </form>

            {{-- LINK UNDANGAN --}}
            @if (session('inviteUrl'))
                <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="mb-2 text-sm font-semibold text-blue-800">
                        Undangan berhasil dibuat
                    </p>

                    <input
                        type="text"
                        readonly
                        value="{{ session('inviteUrl') }}"
                        onclick="this.select()"
                        class="w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm text-slate-700"
                    >

                    <p class="mt-2 text-xs text-blue-700">
                        Salin tautan ini dan kirim kepada penerima. Tautan berlaku selama 48 jam dan hanya dapat digunakan sekali.
                    </p>
                </div>
            @endif

        </div>
    @endif

</div>
@endsection
