
@extends('layouts.ems')

@section('title', 'Undangan Organisasi | EMS')
@section('page-heading', 'Undangan Organisasi')

@section('content')
<div class="mx-auto max-w-2xl">

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-2xl text-blue-600">
            ✉
        </div>

        <h2 class="text-xl font-bold text-slate-800">
            Anda Mendapat Undangan
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            Seseorang mengundang Anda untuk bergabung ke organisasi berikut.
        </p>

        <div class="my-6 rounded-lg border border-slate-100 bg-slate-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                Nama Organisasi
            </p>

            <p class="mt-1 text-lg font-bold text-slate-800">
                {{ $invitation->organization->name }}
            </p>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs text-slate-400">Email</p>
                    <p class="mt-1 break-all text-sm font-medium text-slate-700">
                        {{ $invitation->email }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-400">Peran</p>
                    <p class="mt-1 text-sm font-medium text-slate-700">
                        {{ $invitation->role->role_name }}
                    </p>
                </div>
            </div>

            <div class="mt-4 border-t border-slate-200 pt-4">
                <p class="text-xs text-slate-400">
                    Berlaku sampai
                </p>

                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ $invitation->expires_at->format('d-m-Y H:i') }}
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST"
            action="{{ route('invitations.accept.submit', $token) }}">
            @csrf

            <button type="submit"
                class="w-full rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                Terima Undangan
            </button>
        </form>

    </div>
</div>
@endsection
