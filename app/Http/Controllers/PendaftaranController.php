<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Event;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:terbaru,terlama'],
        ]);
        $sort = $filters['sort'] ?? 'terbaru';

        $query = Pendaftaran::with(['peserta', 'event'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('peserta', fn ($peserta) => $peserta->where('nama', 'like', "%{$search}%"))
                        ->orWhereHas('event', fn ($event) => $event->where('nama_event', 'like', "%{$search}%"));
                });
            });

        $totalPendaftaran = (clone $query)->count();
        $menunggu = (clone $query)->where('status_pembayaran', 'belum_bayar')->where('expires_at', '>', now())->count();
        $pendaftarans = $query->orderBy('tanggal_daftar', $sort === 'terlama' ? 'asc' : 'desc')
            ->paginate(10)->withQueryString();

        return view('pendaftarans.index', compact('pendaftarans', 'sort', 'totalPendaftaran', 'menunggu'));
    }

    public function create()
    {
        $pesertas = Peserta::all();
        $events = Event::all();
        return view('pendaftarans.create', compact('pesertas', 'events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_daftar' => ['required', 'date'],
            'peserta_id' => ['required', 'integer', 'exists:pesertas,id'],
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'status_pembayaran' => ['required', 'in:belum_bayar,lunas,kedaluwarsa,gratis'],
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $validated['expires_at'] = $validated['status_pembayaran'] === 'belum_bayar' ? now()->addMinutes(15) : null;
        if ((float) $event->biaya <= 0) {
            $validated['status_pembayaran'] = 'gratis';
            $validated['expires_at'] = null;
        }
        Pendaftaran::create($validated);

        return redirect()->route('pendaftarans.index')->with('success', 'Pendaftaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        $pesertas = Peserta::all();
        $events = Event::all();
        return view('pendaftarans.edit', compact('pendaftaran', 'pesertas', 'events'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_daftar' => ['required', 'date'],
            'peserta_id' => ['required', 'integer', 'exists:pesertas,id'],
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'status_pembayaran' => ['required', 'in:belum_bayar,lunas,kedaluwarsa,gratis'],
        ]);

        $event = Event::findOrFail($validated['event_id']);
        if ((float) $event->biaya <= 0) {
            $validated['status_pembayaran'] = 'gratis';
        }
        $validated['expires_at'] = $validated['status_pembayaran'] === 'belum_bayar' ? now()->addMinutes(15) : null;

        Pendaftaran::findOrFail($id)->update($validated);
        return redirect()->route('pendaftarans.index')->with('success', 'Pendaftaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Pendaftaran::findOrFail($id)->delete();
        return redirect()->route('pendaftarans.index')->with('success', 'Pendaftaran berhasil dihapus!');
    }
}
