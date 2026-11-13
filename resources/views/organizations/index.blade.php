@extends('layouts.ems')

@section('title', 'Organisasi Saya | EMS')
@section('page-heading', 'Organisasi Saya')

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Organisasi Saya
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pilih organisasi untuk membuka dashboard dan mengelola kegiatan.
            </p>
        </div>

        <a href="{{ route('organizations.create') }}"
           class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
            + Buat Organisasi
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

        @forelse ($organizations as $membership)

            <a href="{{ route('organizations.dashboard', [
                    'organization' => $membership->organization->slug
                ]) }}"
               class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md">

                <div class="flex items-start justify-between gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-lg font-bold text-blue-600">
                        {{ strtoupper(substr($membership->organization->name, 0, 1)) }}
                    </div>

                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                        {{ $membership->role->role_name }}
                    </span>

                </div>

                <h3 class="mt-5 font-bold text-slate-800">
                    {{ $membership->organization->name }}
                </h3>

                <p class="mt-2 break-all text-xs text-slate-400">
                    {{ url('/o/' . $membership->organization->slug) }}
                </p>

                <div class="mt-5 border-t border-slate-100 pt-4 text-sm font-semibold text-blue-600">
                    Buka Dashboard Organisasi →
                </div>

            </a>

        @empty

            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center md:col-span-2 xl:col-span-3">

                <div class="text-4xl text-slate-300">▦</div>

                <h3 class="mt-3 font-semibold text-slate-700">
                    Belum Ada Organisasi
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Buat organisasi baru untuk mulai mengelola acara,
                    atau bergabung melalui undangan organisasi.
                </p>

                <a href="{{ route('organizations.create') }}"
                   class="mt-5 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Buat Organisasi
                </a>

            </div>

        @endforelse

    </div>

</div>
@endsection
