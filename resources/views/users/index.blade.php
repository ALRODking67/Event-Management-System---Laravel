@extends('layouts.ems')

@section('title', 'Manajemen Pengguna | EMS')
@section('page-heading', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-2xl font-bold text-slate-800">
            Manajemen Pengguna
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Kelola anggota dan undangan untuk {{ $organization->name }}.
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-semibold">Terjadi kesalahan:</p>
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- UNDANG ANGGOTA --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h3 class="text-lg font-bold text-slate-800">
                Undang Anggota
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Undang Staf Operasional atau Vendor untuk bergabung ke organisasi.
            </p>
        </div>

        <form method="POST"
            action="{{ route('organizations.invitations.store', $organization->slug) }}">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
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

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Peran Anggota
                    </label>
                    <select
                        name="role"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <option value="Staf Operasional"
                            @selected(old('role') === 'Staf Operasional')>
                            Staf Operasional
                        </option>
                        <option value="Vendor"
                            @selected(old('role') === 'Vendor')>
                            Vendor
                        </option>
                    </select>
                </div>
            </div>

            <button type="submit"
                class="mt-5 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                Buat Undangan
            </button>
        </form>

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
                    Salin tautan dan kirim kepada penerima. Tautan berlaku selama 48 jam dan hanya dapat digunakan sekali.
                </p>
            </div>
        @endif
    </div>

    {{-- DAFTAR ANGGOTA --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h3 class="text-lg font-bold text-slate-800">
                Anggota Organisasi
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                {{ $members->count() }} anggota terdaftar.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">No.</th>
                        <th class="px-5 py-4">Nama</th>
                        <th class="px-5 py-4">Email</th>
                        <th class="px-5 py-4">Role</th>
                        <th class="px-5 py-4">Profil Vendor</th>
                        <th class="px-5 py-4">Status Akun</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($members as $member)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-slate-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800">
                                {{ $member->user->name }}
                                @if (auth()->id() === $member->user_id)
                                    <span class="block text-xs font-normal text-blue-600">
                                        Akun Anda
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $member->user->email }}
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $roleName = $member->role->role_name;
                                @endphp

                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $roleName === 'Admin'
                                        ? 'bg-purple-100 text-purple-700'
                                        : ($roleName === 'Vendor'
                                            ? 'bg-orange-100 text-orange-700'
                                            : 'bg-blue-100 text-blue-700') }}">
                                    {{ $roleName }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $member->vendor?->vendor_name ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($member->user->is_active)
                                    <span class="font-semibold text-green-600">Aktif</span>
                                @else
                                    <span class="font-semibold text-red-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($roleName !== 'Admin')
                                    <a
                                        href="{{ route('users.edit', [
                                            'organization' => $organization->slug,
                                            'member' => $member->id
                                        ]) }}"
                                        class="font-semibold text-blue-600 hover:text-blue-800">
                                        Edit Role
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">
                                        Admin utama
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                                Belum ada anggota organisasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DAFTAR UNDANGAN --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h3 class="text-lg font-bold text-slate-800">
                Riwayat Undangan
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Pantau undangan yang telah dikirim kepada calon anggota.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Email</th>
                        <th class="px-5 py-4">Role</th>
                        <th class="px-5 py-4">Pengundang</th>
                        <th class="px-5 py-4">Berlaku Sampai</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($invitations as $invitation)
                        @php
                            if ($invitation->accepted_at) {
                                $inviteStatus = 'Diterima';
                                $statusClass = 'bg-green-100 text-green-700';
                            } elseif ($invitation->revoked_at) {
                                $inviteStatus = 'Dibatalkan';
                                $statusClass = 'bg-red-100 text-red-700';
                            } elseif ($invitation->expires_at->isPast()) {
                                $inviteStatus = 'Kedaluwarsa';
                                $statusClass = 'bg-slate-100 text-slate-600';
                            } else {
                                $inviteStatus = 'Menunggu';
                                $statusClass = 'bg-yellow-100 text-yellow-700';
                            }
                        @endphp

                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-medium text-slate-800">
                                {{ $invitation->email }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $invitation->role->role_name }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $invitation->inviter->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $invitation->expires_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $inviteStatus }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if (!$invitation->accepted_at && !$invitation->revoked_at && !$invitation->expires_at->isPast())
                                    <form
                                        method="POST"
                                        action="{{ route('users.invitation.revoke', [
                                            'organization' => $organization->slug,
                                            'invitation' => $invitation->id
                                        ]) }}"
                                        onsubmit="return confirm('Batalkan undangan ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="font-semibold text-red-600 hover:text-red-800">
                                            Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                Belum ada undangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
