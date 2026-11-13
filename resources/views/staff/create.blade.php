@extends('layouts.ems')

@section('title', 'Edit Staf')
@section('page-heading', 'Edit Staf')

@section('content')
<div style="max-width: 800px;">

    @if ($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 14px; margin-bottom: 20px; border-radius: 8px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;">

        <form action="{{ route('staff.update', [
            'organization' => $currentOrganization->slug,
            'member' => $member->id
        ]) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Nama Staf
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $member->user->name) }}"
                    maxlength="150"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Email Login
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $member->user->email) }}"
                    maxlength="255"
                    required
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 600; margin-bottom: 7px;">
                    Role
                </label>
                <input
                    type="text"
                    value="Staf Operasional"
                    readonly
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; background: #f1f5f9;"
                >
            </div>

            <div style="margin-bottom: 24px; color: #64748b; font-size: 13px;">
                Password dapat diubah sendiri oleh staf melalui halaman Profil.
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                <button
                    type="submit"
                    style="background: #2f65f6; color: white; padding: 11px 20px; border: none; border-radius: 7px; cursor: pointer; font-weight: 600;"
                >
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('staff.index', ['organization' => $currentOrganization->slug]) }}"
                    style="color: #475569; text-decoration: none; padding: 11px 14px;"
                >
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection
