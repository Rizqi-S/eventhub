@extends('layouts.admin')

@section('title', 'Moderasi Pengajuan Event')

@section('content')
    <div class="legacy-module-page">

        <div class="header">
            <div>
                <h1>Pengajuan Event</h1>
                <p>Tinjau event yang dikirim oleh pengunjung.</p>
            </div>
        </div>

        <div class="table-card">
            <h3>Antrean Moderasi</h3>

            <table>
                <thead>
                    <tr>
                        <th>Nama Event</th>
                        <th>Mulai</th>
                        <th>Pengaju</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($submissions as $event)
                        <tr>
                            <td>{{ $event->nama_event }}</td>

                            <td>
                                {{ $event->tanggal_mulai?->format('d-m-Y H:i') }}
                            </td>

                            <td>
                                {{ $event->submitted_by_name }}
                                <br>
                                <small>{{ $event->submitted_by_email }}</small>
                            </td>

                            <td>
                                <span class="status">
                                    {{ ucfirst($event->submission_status) }}
                                </span>
                            </td>

                            <td>
                                <div class="actions">
                                    <a class="btn-detail" href="{{ route('admin.event-submissions.show', $event) }}">
                                        Tinjau
                                    </a>

                                    @if($event->submission_status === 'pending')
                                        <a class="btn-edit" href="{{ route('events.edit', $event) }}">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                Belum ada pengajuan event.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination">
                {{ $submissions->links() }}
            </div>
        </div>
    </div>
@endsection