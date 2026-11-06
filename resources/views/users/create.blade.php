
@extends('layouts.ems')

@section('content')
<div class="container">
    <h1>Tambah Pengguna</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div style="margin-bottom:15px;">
            <label>Nama</label><br>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div style="margin-bottom:15px;">
            <label>Email</label><br>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div style="margin-bottom:15px;">
            <label>Role</label><br>
            <select name="role_id" required>
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}"
                        @selected(old('role_id') == $role->id)>
                        {{ $role->role_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label>Profil Vendor</label><br>
            <select name="vendor_id">
                <option value="">-- Tidak dihubungkan --</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}"
                        @selected(old('vendor_id') == $vendor->id)>
                        {{ $vendor->vendor_name }}
                    </option>
                @endforeach
            </select>
            <small>Wajib dipilih jika role yang digunakan adalah Vendor.</small>
        </div>

        <div style="margin-bottom:15px;">
            <label>Password</label><br>
            <input type="password" name="password" required>
        </div>

        <div style="margin-bottom:15px;">
            <label>Konfirmasi Password</label><br>
            <input type="password" name="password_confirmation" required>
        </div>

        <div style="margin-bottom:15px;">
            <label>Status Akun</label><br>
            <select name="is_active" required>
                <option value="1" @selected(old('is_active', '1') == '1')>Aktif</option>
                <option value="0" @selected(old('is_active') == '0')>Nonaktif</option>
            </select>
        </div>

        <button type="submit">Simpan Pengguna</button>
        <a href="{{ route('users.index') }}">Batal</a>
    </form>
</div>
@endsection
