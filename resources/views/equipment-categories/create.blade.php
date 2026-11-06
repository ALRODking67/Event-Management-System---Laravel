@extends('layouts.ems')

@section('title', 'Tambah Kategori Peralatan')

@section('page-heading', 'Tambah Kategori Peralatan')

@section('content')
<div style="max-width: 760px;">

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">

        <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
            Tambah Kategori Peralatan
        </h2>

        <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">
            Daftarkan kategori baru untuk mengelompokkan inventaris peralatan acara.
        </p>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #fecaca;">
                <strong>Periksa kembali data berikut:</strong>

                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('equipment-categories.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 24px;">
                <label for="category_name" style="display: block; font-weight: 600; margin-bottom: 7px; color: #334155;">
                    Nama Kategori
                </label>

                <input
                    id="category_name"
                    type="text"
                    name="category_name"
                    value="{{ old('category_name') }}"
                    maxlength="100"
                    required
                    placeholder="Contoh: Audio"
                    style="display: block; width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; box-sizing: border-box;"
                >

                <p style="font-size: 12px; color: #64748b; margin-top: 6px;">
                    Masukkan nama kategori yang belum terdaftar.
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">

                <button
                    type="submit"
                    style="background: #2f65f6; color: white; border: none; padding: 11px 20px; border-radius: 7px; font-weight: 600; cursor: pointer;"
                >
                    Simpan Kategori
                </button>

                <a
                    href="{{ route('equipment-categories.index') }}"
                    style="display: inline-block; color: #475569; text-decoration: none; padding: 11px 16px; border: 1px solid #cbd5e1; border-radius: 7px;"
                >
                    Batal
                </a>

            </div>
        </form>

    </div>

</div>
@endsection