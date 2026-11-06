
@extends('layouts.ems')

@section('content')
<div class="container">
    <h1>Edit Pengguna</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $isSelf = auth()->id() === $user->id;
    @endphp

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom:15px;">
            <label>Nama</label><br>
            <input type="text" name="name"
                value="{{ old('name', $user->name) }}" required>
        </div>

        <div style="margin-bottom:15px;">
            <label>Email</label><br>
            <input type="email" name="email"
                value="{{ old('email', $user->email) }}" required>
        </div>

        <div style="margin-bottom:15px;">
            <label>Role</label><br>
            <select name="role_id" {{ $isSelf ? 'disabled' : '' }} required>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}"
                        @selected(old('role_id', $user->role_id) == $role->id)>
                        {{ $role->role_name }}
                    </option>
                @endforeach
            </select>

            @if ($isSelf)
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                <small>Role akun sendiri tidak dapat diubah di sini.</small>
            @endif
        </div>

        <div style="margin-bottom:15px;">
            <label>Profil Vendor</label><br>
            <select name="vendor_id" {{ $isSelf ? 'disabled' : '' }}>
                <option value="">-- Tidak dihubungkan --</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}"
                        @selected(old('vendor_id', $user->vendor_id) == $vendor->id)>
                        {{ $vendor->vendor_name }}
                    </option>
                @endforeach
            </select>

            @if ($isSelf)
                <input type="hidden" name="vendor_id" value="{{ $user->vendor_id }}">
            @endif
        </div>

        <div style="margin-bottom:15px;">
            <label>Status Akun</label><br>
            <select name="is_active" {{ $isSelf ? 'disabled' : '' }} required>
                <option value="1" @selected(old('is_active', $user->is_active) == '1')>Aktif</option>
                <option value="0" @selected(old('is_active', $user->is_active) == '0')>Nonaktif</option>
            </select>

            @if ($isSelf)
                <input type="hidden" name="is_active" value="1">
                <small>Akun sendiri tidak dapat dinonaktifkan.</small>
            @endif
        </div>

        <hr>

        <h3>Ganti Password</h3>
        <p>Kosongkan jika tidak ingin mengganti password.</p>

        <div style="margin-bottom:15px;">
            <label>Password Baru</label><br>
            <input type="password" name="password">
        </div>

        <div style="margin-bottom:15px;">
            <label>Konfirmasi Password Baru</label><br>
            <input type="password" name="password_confirmation">
        </div>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('users.index') }}">Kembali</a>
    </form>
</div>
@endsection
