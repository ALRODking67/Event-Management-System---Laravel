@extends('layouts.ems')

@section('title', 'Kategori Peralatan')

@section('page-heading', 'Kategori Peralatan')

@section('content')
<div>

    @if (session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 14px 18px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #bbf7d0;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;">

        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 5px;">
                Daftar Kategori Peralatan
            </h2>

            <p style="font-size: 14px; color: #64748b;">
                Kelola kategori untuk mengelompokkan inventaris peralatan acara.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">

            <a href="{{ route('equipment.index') }}"
               style="display: inline-block; padding: 11px 16px; border: 1px solid #cbd5e1; border-radius: 7px; color: #475569; text-decoration: none; font-size: 14px;">
                Kembali ke Inventaris
            </a>

            <a href="{{ route('equipment-categories.create') }}"
               style="display: inline-block; background: #2f65f6; color: white; padding: 11px 18px; border-radius: 7px; text-decoration: none; font-size: 14px; font-weight: 600;">
                + Tambah Kategori
            </a>

        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">

        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">
                Data Kategori
            </h3>
        </div>

        @if ($categories->isEmpty())

            <div style="padding: 40px 24px; text-align: center;">
                <p style="font-size: 15px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                    Belum ada kategori peralatan
                </p>

                <p style="font-size: 14px; color: #64748b; margin-bottom: 18px;">
                    Tambahkan kategori agar inventaris peralatan dapat dikelompokkan dengan baik.
                </p>

                <a href="{{ route('equipment-categories.create') }}"
                   style="display: inline-block; background: #2f65f6; color: white; padding: 10px 18px; border-radius: 7px; text-decoration: none; font-size: 14px;">
                    + Tambah Kategori
                </a>
            </div>

        @else

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">

                    <thead>
                        <tr style="background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase;">
                            <th style="padding: 15px 24px; font-weight: 700;">No.</th>
                            <th style="padding: 15px 24px; font-weight: 700;">Nama Kategori</th>
                            <th style="padding: 15px 24px; font-weight: 700;">Jumlah Peralatan</th>
                            <th style="padding: 15px 24px; font-weight: 700;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categories as $index => $category)
                            <tr style="border-top: 1px solid #e2e8f0; color: #334155;">

                                <td style="padding: 16px 24px; font-size: 14px;">
                                    {{ $index + 1 }}
                                </td>

                                <td style="padding: 16px 24px; font-size: 14px; font-weight: 600;">
                                    {{ $category->category_name }}
                                </td>

                                <td style="padding: 16px 24px; font-size: 14px;">
                                    <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; padding: 5px 10px; border-radius: 6px; font-weight: 600;">
                                        {{ $category->equipment_count }} peralatan
                                    </span>
                                </td>

                                <td style="padding: 16px 24px; font-size: 14px;">
                                    <a href="{{ route('equipment-categories.edit', $category->id) }}"
                                       style="display: inline-block; color: #2f65f6; font-weight: 600; text-decoration: none;">
                                        Edit
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @endif

    </div>

</div>
@endsection