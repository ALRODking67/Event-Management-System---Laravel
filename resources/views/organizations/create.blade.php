
@extends('layouts.ems')

@section('title', 'Buat Organisasi | EMS')
@section('page-heading', 'Buat Organisasi')

@section('content')
<div class="max-w-3xl">

    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">
            Buat Workspace Baru
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Buat organisasi untuk mulai mengelola acara dan anggota tim.
        </p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">Ada kesalahan:</p>
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('organizations.store') }}">
            @csrf

            <div class="mb-6">
                <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">
                    Nama Organisasi
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    placeholder="Contoh: Event Organizer Nusantara"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >

                <p class="mt-2 text-xs text-slate-400">
                    Gunakan nama yang mudah dikenali oleh anggota organisasi.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-5">
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Buat Organisasi
                </button>

                <a href="{{ route('organizations.index') }}"
                   class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>

    </div>
</div>
@endsection
