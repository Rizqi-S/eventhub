<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PublicEventController extends Controller
{
    private function expirePendingPayments(): void
    {
        Pendaftaran::query()->where('status_pembayaran', 'belum_bayar')
            ->whereNotNull('expires_at')->where('expires_at', '<=', now())
            ->update(['status_pembayaran' => 'kedaluwarsa', 'updated_at' => now()]);
    }

    private function activeRegistrationQuery(int $eventId)
    {
        return Pendaftaran::query()->where('event_id', $eventId)
            ->where(function ($query) {
                $query->whereIn('status_pembayaran', ['lunas', 'gratis'])
                    ->orWhere(function ($pending) {
                        $pending->where('status_pembayaran', 'belum_bayar')->where('expires_at', '>', now());
                    });
            });
    }

    public function home()
    {
        $this->expirePendingPayments();
        $events = Event::query()->whereIn('status_event', ['aktif', 'berlangsung', 'ongoing', 'akan_datang'])
            ->where('tanggal_mulai', '>=', now()->startOfDay())->withCount('pendaftarans')
            ->orderBy('tanggal_mulai')->take(6)->get();
        return view('public.home', compact('events'));
    }

    public function index(Request $request)
    {
        $this->expirePendingPayments();
        $events = Event::query()->whereIn('status_event', ['aktif', 'berlangsung', 'ongoing', 'akan_datang'])
            ->where('tanggal_mulai', '>=', now()->startOfDay())
            ->when($request->filled('q'), fn ($q) => $q->where(function ($sub) use ($request) {
                $term = trim($request->string('q')->toString());
                $sub->where('nama_event', 'like', "%{$term}%")->orWhere('lokasi', 'like', "%{$term}%");
            }))
            ->orderBy('tanggal_mulai')->paginate(9)->withQueryString();
        return view('public.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $this->expirePendingPayments();
        abort_unless(in_array($event->status_event, ['aktif', 'berlangsung', 'ongoing', 'akan_datang'], true), 404);
        abort_if($event->tanggal_mulai->lt(now()->startOfDay()), 404);
        $registered = $this->activeRegistrationQuery($event->id)->count();
        $remaining = max(0, $event->kuota - $registered);
        return view('public.events.show', compact('event', 'remaining'));
    }

    public function register(Event $event)
    {
        $this->expirePendingPayments();
        abort_unless(in_array($event->status_event, ['aktif', 'berlangsung', 'ongoing', 'akan_datang', 'selesai'], true), 404);
        abort_if($this->activeRegistrationQuery($event->id)->count() >= $event->kuota, 410, 'Kuota event sudah penuh.');
        return view('public.events.register', compact('event'));
    }

    public function storeRegistration(Request $request, Event $event)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_hp' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
            'instansi' => ['required', 'string', 'max:255'],
            'agreement' => ['accepted'],
        ], [
            'no_hp.regex' => 'Nomor HP harus berisi 8–15 digit dan boleh diawali tanda + (contoh: 081234567890 atau +6281234567890).',
            'agreement.accepted' => 'Kamu harus menyetujui ketentuan pendaftaran.',
        ]);

        $pendaftaran = DB::transaction(function () use ($validated, $event) {
            $this->expirePendingPayments();
            $lockedEvent = Event::query()->lockForUpdate()->findOrFail($event->id);
            if (! in_array($lockedEvent->status_event, ['aktif', 'berlangsung', 'ongoing', 'akan_datang'], true)
                || $lockedEvent->tanggal_mulai->lt(now()->startOfDay())) {
                throw ValidationException::withMessages(['event' => 'Pendaftaran event ini sudah ditutup.']);
            }

            if ($this->activeRegistrationQuery($lockedEvent->id)->count() >= $lockedEvent->kuota) {
                throw ValidationException::withMessages(['event' => 'Maaf, kuota event sudah penuh.']);
            }

            $peserta = Peserta::firstOrCreate(['email' => strtolower($validated['email'])], [
                'nama' => $validated['nama'], 'no_hp' => $validated['no_hp'], 'instansi' => $validated['instansi'],
            ]);

            $alreadyRegistered = $this->activeRegistrationQuery($lockedEvent->id)
                ->where('peserta_id', $peserta->id)->exists();
            if ($alreadyRegistered) {
                throw ValidationException::withMessages(['email' => 'Email ini sudah terdaftar pada event tersebut.']);
            }

            $free = (float) $lockedEvent->biaya <= 0;
            return Pendaftaran::create([
                'public_token' => (string) Str::uuid(),
                'attendance_code' => Str::upper(Str::random(8)),
                'tanggal_daftar' => now()->toDateString(),
                'peserta_id' => $peserta->id,
                'event_id' => $lockedEvent->id,
                'status_pembayaran' => $free ? 'gratis' : 'belum_bayar',
                'expires_at' => $free ? null : now()->addMinutes(15),
            ]);
        }, 3);

        return redirect()->route('public.registration.confirmation', $pendaftaran->public_token)
            ->with('registration_email', $validated['email']);
    }

    public function confirmation(string $token)
    {
        $this->expirePendingPayments();
        $pendaftaran = Pendaftaran::where('public_token', $token)->firstOrFail();
        $pendaftaran->load(['peserta', 'event']);
        return view('public.confirmation', compact('pendaftaran'));
    }

    public function attendanceForm() { return view('public.attendance'); }

    public function submitAttendance(Request $request)
    {
        $validated = $request->validate(['code' => ['required', 'string', 'size:8']], [
            'code.required' => 'Masukkan kode presensi.', 'code.size' => 'Kode presensi harus terdiri dari 8 karakter.',
        ]);

        $pendaftaran = Pendaftaran::with(['peserta', 'event'])
            ->where('attendance_code', Str::upper(trim($validated['code'])))->first();

        if (! $pendaftaran || ! in_array($pendaftaran->status_pembayaran, ['lunas', 'gratis'], true)) {
            throw ValidationException::withMessages(['code' => 'Kode tidak valid atau pembayaran belum diselesaikan.']);
        }

        $presensi = $pendaftaran->presensis()->first();
        if ($presensi && $presensi->status_kehadiran === 'hadir') {
            throw ValidationException::withMessages(['code' => 'Presensi untuk kode ini sudah tercatat.']);
        }
        if ($presensi) {
            $presensi->update(['status_kehadiran' => 'hadir', 'waktu_hadir' => now()]);
        } else {
            $pendaftaran->presensis()->create(['status_kehadiran' => 'hadir', 'waktu_hadir' => now()]);
        }
        return view('public.attendance-success', compact('pendaftaran'));
    }
}
