<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $events = collect();
        $pesertas = collect();
        $pendaftarans = collect();

        if ($q !== '') {
            $keyword = "%{$q}%";

            $events = Event::query()
                ->where('nama_event', 'like', $keyword)
                ->orWhere('lokasi', 'like', $keyword)
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            $pesertas = Peserta::query()
                ->where('nama', 'like', $keyword)
                ->orWhere('email', 'like', $keyword)
                ->orWhere('no_hp', 'like', $keyword)
                ->orWhere('instansi', 'like', $keyword)
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            $pendaftarans = Pendaftaran::query()
                ->with(['peserta', 'event'])
                ->where(function ($query) use ($keyword) {
                    $query->where('attendance_code', 'like', $keyword)
                        ->orWhere('midtrans_order_id', 'like', $keyword)
                        ->orWhereHas('peserta', function ($peserta) use ($keyword) {
                            $peserta->where('nama', 'like', $keyword)
                                ->orWhere('email', 'like', $keyword);
                        })
                        ->orWhereHas('event', function ($event) use ($keyword) {
                            $event->where('nama_event', 'like', $keyword);
                        });
                })
                ->orderByDesc('id')
                ->limit(10)
                ->get();
        }

        return view('search.index', compact(
            'q',
            'events',
            'pesertas',
            'pendaftarans'
        ));
    }
}