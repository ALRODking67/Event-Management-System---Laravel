@extends('layouts.ems')

@section('title', 'Susun Rundown Acara | EMS')
@section('page-heading', 'Susun Rundown Acara')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    @if (session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #bbf7d0;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <a href="{{ route('rundowns.create', ['organization' => $currentOrganization->slug]) }}"
           style="display: inline-block; background: #2563eb; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
            + Tambah Rundown
        </a>
    </div>

    @forelse ($events as $event)
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 20px;">

            <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 16px;">
                {{ $event->event_name }}
            </h2>

            @if ($event->rundownItems->isEmpty())
                <p style="color: #64748b; font-size: 14px; margin: 0;">
                    Belum ada item rundown untuk acara ini.
                </p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f1f5f9; color: #334155;">
                                <th style="padding: 12px; text-align: left;">Aktivitas</th>
                                <th style="padding: 12px; text-align: left;">Mulai</th>
                                <th style="padding: 12px; text-align: left;">Selesai</th>
                                <th style="padding: 12px; text-align: left;">Lokasi</th>
                                <th style="padding: 12px; text-align: left;">Penanggung Jawab</th>
                                <th style="padding: 12px; text-align: left;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($event->rundownItems as $item)
                                <tr style="border-top: 1px solid #e2e8f0; vertical-align: top;">

                                    <td style="padding: 12px; color: #1e293b;">
                                        {{ $item->activity_name }}
                                    </td>

                                    <td style="padding: 12px; color: #475569; white-space: nowrap;">
                                        {{ $item->start_time->format('d-m-Y H:i') }}
                                    </td>

                                    <td style="padding: 12px; color: #475569; white-space: nowrap;">
                                        {{ $item->end_time->format('d-m-Y H:i') }}
                                    </td>

                                    <td style="padding: 12px; color: #475569;">
                                        {{ $item->location_detail ?? '-' }}
                                    </td>

                                    <td style="padding: 12px; color: #475569;">
                                        {{ $item->responsibleUser->name ?? '-' }}
                                    </td>

                                    <td style="padding: 12px; min-width: 150px;">

                                        <a href="{{ route('rundowns.edit', ['organization' => $currentOrganization->slug, 'rundown' => $item->id]) }}"
                                           style="display: inline-block; color: #2563eb; text-decoration: none; font-weight: 600; margin-bottom: 10px;">
                                            Edit
                                        </a>

                                        <div>
                                            @if ($item->is_published)
                                                <span style="display: inline-block; color: #166534; background: #dcfce7; padding: 4px 8px; border-radius: 5px; font-size: 12px; margin-bottom: 8px;">
                                                    Dipublikasikan
                                                </span>
                                            @else
                                                <span style="display: inline-block; color: #92400e; background: #fef3c7; padding: 4px 8px; border-radius: 5px; font-size: 12px; margin-bottom: 8px;">
                                                    Draft
                                                </span>
                                            @endif

                                            <form action="{{ route('rundowns.publish', ['organization' => $currentOrganization->slug, 'rundown' => $item->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin mengubah status publikasi rundown ini?');">

                                                @csrf
                                                @method('PATCH')

                                                @if ($item->is_published)
                                                    <button type="submit"
                                                            style="display: inline-block; background: #dc2626; color: white; padding: 7px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">
                                                        Batalkan Publikasi
                                                    </button>
                                                @else
                                                    <button type="submit"
                                                            style="display: inline-block; background: #059669; color: white; padding: 7px 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">
                                                        Publikasikan
                                                    </button>
                                                @endif

                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    @empty
        <div style="background: white; border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px; color: #64748b;">
            Belum ada data acara. Buat acara terlebih dahulu sebelum menyusun rundown.
        </div>
    @endforelse

</div>
@endsection