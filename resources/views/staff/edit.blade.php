@extends('layouts.ems')

@section('title', 'Edit Staf | EMS')
@section('page-heading', 'Edit Staf')

@section('content')
<div class="max-w-2xl space-y-6">

    <div>
        <h2 class="text-2xl font-bold text-slate-800">Edit Data Staf</h2>
        <p class="mt-1 text-sm text-slate-500">
            Perbarui informasi staf organisasi {{ $organization->name }}.
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
        <form method="POST"
              action="{{ route('staff.update', [
                  'organization' => $currentOrganization->slug,
                  'member' => $member->id
              ]) }}"
              class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Nama Staf *
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $member->user->name) }}"
                       required maxlength="150"
                       class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Email Login *
                </label>
                <input type="email" name="email"
                       value="{{ old('email', $member->user->email) }}"
                       required
                       class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                Password tidak diubah dari halaman ini. Staf dapat mengganti password sendiri melalui halaman Profil.
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Simpan Perubahan
                </button>

                <a href="{{ route('staff.index', ['organization' => $currentOrganization->slug]) }}"
                   class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
