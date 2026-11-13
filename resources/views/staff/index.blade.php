@extends('layouts.ems')

@section('title', 'Kelola Staf')
@section('page-heading', 'Kelola Staf')

@section('content')
<div>

    @if (session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 14px; margin-bottom: 20px; border-radius: 8px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('staff.create', ['organization' => $currentOrganization->slug]) }}"
            style="display: inline-block; background: #2f65f6; color: white; padding: 11px 20px; border-radius: 7px; text-decoration: none; font-weight: 600;">
            + Tambah Staf
        </a>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; overflow-x: auto;">

        @if ($staffMembers->isEmpty())
            <p style="color: #64748b; margin: 0;">
                Belum ada data staf.
            </p>
        @else
            <table style="width: 100%; border-collapse: collapse; min-width: 750px;">
                <thead>
                    <tr style="background: #f1f5f9;">
                        <th style="padding: 12px; text-align: left;">No.</th>
                        <th style="padding: 12px; text-align: left;">Nama Staf</th>
                        <th style="padding: 12px; text-align: left;">Email</th>
                        <th style="padding: 12px; text-align: left;">Role</th>
                        <th style="padding: 12px; text-align: left;">Status Akun</th>
                        <th style="padding: 12px; text-align: left;">Tanggal Bergabung</th>
                        <th style="padding: 12px; text-align: left;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($staffMembers as $index => $member)
                        <tr style="border-top: 1px solid #e2e8f0;">
                            <td style="padding: 12px;">{{ $index + 1 }}</td>

                            <td style="padding: 12px;">
                                {{ $member->user->name }}
                            </td>

                            <td style="padding: 12px;">
                                {{ $member->user->email }}
                            </td>

                            <td style="padding: 12px;">
                                {{ $member->role->role_name }}
                            </td>

                            <td style="padding: 12px;">
                                @if ($member->user->is_active)
                                    <span style="color: #16a34a; font-weight: 600;">Aktif</span>
                                @else
                                    <span style="color: #dc2626; font-weight: 600;">Nonaktif</span>
                                @endif
                            </td>

                            <td style="padding: 12px;">
                                {{ $member->created_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td style="padding: 12px;">
                                <a href="{{ route('staff.edit', [
                                    'organization' => $currentOrganization->slug,
                                    'member' => $member->id
                                ]) }}"
                                    style="color: #2563eb; text-decoration: underline;">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
</div>
@endsection
