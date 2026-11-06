
@extends('layouts.ems')

@section('content')
<div class="container">
    <h1>Manajemen Pengguna</h1>
    <p>Kelola akun Admin, Staf Operasional, dan Vendor.</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p>
        <a href="{{ route('users.create') }}">+ Tambah Pengguna</a>
    </p>

    <div style="overflow-x:auto;">
        <table class="table table-bordered" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="padding:10px;">No.</th>
                    <th style="padding:10px;">Nama</th>
                    <th style="padding:10px;">Email</th>
                    <th style="padding:10px;">Role</th>
                    <th style="padding:10px;">Profil Vendor</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td style="padding:10px;">{{ $loop->iteration }}</td>
                        <td style="padding:10px;">
                            {{ $user->name }}
                            @if (auth()->id() === $user->id)
                                <small>(Akun Anda)</small>
                            @endif
                        </td>
                        <td style="padding:10px;">{{ $user->email }}</td>
                        <td style="padding:10px;">
                            {{ $user->role?->role_name ?? 'Tidak memiliki role' }}
                        </td>
                        <td style="padding:10px;">
                            {{ $user->vendor?->vendor_name ?? '-' }}
                        </td>
                        <td style="padding:10px;">
                            @if ($user->is_active)
                                <span style="color:green;">Aktif</span>
                            @else
                                <span style="color:red;">Nonaktif</span>
                            @endif
                        </td>
                        <td style="padding:10px;">
                            <a href="{{ route('users.edit', $user) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:10px; text-align:center;">
                            Belum ada pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
