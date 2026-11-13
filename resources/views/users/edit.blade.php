@extends('layouts.ems')

@section('title', 'Edit Role Anggota | EMS')
@section('page-heading', 'Edit Role Anggota')

@section('content')
<div class="max-w-2xl space-y-6">

    <div>
        <h2 class="text-2xl font-bold text-slate-800">
            Edit Role Anggota
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Ubah hak akses anggota dalam {{ $organization->name }}.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-6 border-b border-slate-100 pb-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                Informasi Anggota
            </p>

            <h3 class="mt-2 text-lg font-bold text-slate-800">
                {{ $member->user->name }}
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                {{ $member->user->email }}
            </p>

            <p class="mt-3 text-sm text-slate-600">
                Role saat ini:
                <span class="font-semibold text-slate-800">
                    {{ $member->role->role_name }}
                </span>
            </p>
        </div>

        <form method="POST"
            action="{{ route('users.update', [
                'organization' => $organization->slug,
                'member' => $member->id
            ]) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Role Baru
                </label>

                <select
                    name="role_id"
                    required
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    @foreach ($roles as $role)
                        <option
                            value="{{ $role->id }}"
                            @selected(old('role_id', $member->role_id) == $role->id)>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>

                <p class="mt-2 text-xs text-slate-500">
                    Jika memilih Vendor, sistem akan mencari profil Vendor dalam organisasi ini dengan email yang sama dengan akun anggota.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Simpan Perubahan
                </button>

                <a href="{{ route('users.index', $organization->slug) }}"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Kembali
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
