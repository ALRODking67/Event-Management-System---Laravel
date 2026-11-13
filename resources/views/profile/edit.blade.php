@extends('layouts.ems')

@section('title', 'Profil Saya')
@section('page-heading', 'Profil Saya')

@section('content')
<div style="max-width: 850px; display: flex; flex-direction: column; gap: 20px;">

    {{-- Informasi Profil --}}
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
        <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
            Informasi Profil
        </h2>

        <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
            Perbarui nama dan alamat email akun EMS kamu.
        </p>

        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Ubah Password --}}
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
        <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
            Ubah Password
        </h2>

        <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
            Gunakan password yang kuat untuk melindungi akun kamu.
        </p>

        @include('profile.partials.update-password-form')
    </div>

</div>
@endsection
