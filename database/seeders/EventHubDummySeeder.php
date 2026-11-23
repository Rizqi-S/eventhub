<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventHubDummySeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Kampus IPB Dramaga, Bogor', 'lat' => -6.5599, 'lng' => 106.7267],
            ['name' => 'Botani Square, Bogor', 'lat' => -6.5971, 'lng' => 106.8060],
            ['name' => 'Stadion Pakansari, Cibinong', 'lat' => -6.4919, 'lng' => 106.8342],
            ['name' => 'Bekasi Cyber Park, Bekasi', 'lat' => -6.2383, 'lng' => 106.9756],
            ['name' => 'JCC Senayan, Jakarta', 'lat' => -6.2133, 'lng' => 106.8076],
            ['name' => 'ICE BSD City, Tangerang', 'lat' => -6.3016, 'lng' => 106.6386],
        ];
        $titles = [
            'Cybersecurity Awareness Summit', 'Cloud Computing Workshop', 'Artificial Intelligence Forum',
            'UI/UX Design Sprint', 'Startup Innovation Meetup', 'Data Science Bootcamp',
            'Internet of Things Lab', 'Digital Product Exhibition', 'Web Engineering Conference',
            'Technology Career Day', 'Network Automation Workshop', 'Ethical Hacking Intensive',
            'Mobile App Development Class', 'Creative Technology Festival', 'Open Source Community Day',
            'Digital Business Conference', 'Robotics and Embedded Systems Lab', 'Big Data Analytics Forum',
            'Game Development Workshop', 'Future Technology Symposium',
        ];
        $institutions = ['IPB University', 'Universitas Indonesia', 'Telkom University', 'BINUS University', 'SMKN 1 Bogor', 'Komunitas Tech Bogor', 'Digital Talent Hub'];
        $firstNames = ['Aditya', 'Nabila', 'Fajar', 'Alya', 'Rizky', 'Citra', 'Dimas', 'Salsa', 'Farhan', 'Zahra', 'Rafi', 'Intan', 'Bagas', 'Nadia', 'Rangga', 'Putri', 'Arif', 'Tiara', 'Ilham', 'Dewi', 'Reza', 'Anisa', 'Galih', 'Mutiara', 'Bima', 'Syifa', 'Kevin', 'Aurel', 'Yoga', 'Vina'];
        $lastNames = ['Pratama', 'Putri', 'Ramadhan', 'Safitri', 'Maulana', 'Maharani', 'Saputra', 'Aprilia', 'Akbar', 'Amelia', 'Kurniawan', 'Permatasari', 'Firmansyah', 'Khairunnisa', 'Aditya', 'Azzahra', 'Setiawan', 'Anindya', 'Fauzi', 'Anggraini'];
        $fees = [0, 25000, 50000, 75000, 100000, 150000, 200000];

        DB::transaction(function () use ($locations, $titles, $institutions, $firstNames, $lastNames, $fees) {
            // Clean only records produced by the previous dummy seeder, identified by its exact description pattern.
            $legacyEventIds = Event::query()
                ->where('deskripsi', 'like', 'Sesi % ke-% yang diselenggarakan di %')
                ->pluck('id');

            if ($legacyEventIds->isNotEmpty()) {
                $legacyRegistrations = Pendaftaran::query()
                    ->whereIn('event_id', $legacyEventIds)
                    ->get(['id', 'peserta_id']);
                $legacyRegistrationIds = $legacyRegistrations->pluck('id');
                $legacyParticipantIds = $legacyRegistrations->pluck('peserta_id')->unique();

                foreach ($legacyRegistrationIds->chunk(500) as $idChunk) {
                    Presensi::query()->whereIn('pendaftaran_id', $idChunk)->delete();
                }
                Pendaftaran::query()->whereIn('event_id', $legacyEventIds)->delete();
                Event::query()->whereIn('id', $legacyEventIds)->delete();
                foreach ($legacyParticipantIds->chunk(500) as $idChunk) {
                    Peserta::query()->whereIn('id', $idChunk)
                        ->whereDoesntHave('pendaftarans')->delete();
                }

                $this->command?->info('Data dummy lama dibersihkan: ' . $legacyEventIds->count() . ' event beserta pendaftaran terkait.');
            }

            foreach ($titles as $index => $title) {
                if (Event::query()->where('nama_event', $title)->exists()) {
                    continue;
                }

                $location = $locations[$index % count($locations)];
                $start = match (true) {
                    $index < 5 => now()->subDays(random_int(8, 35))->setTime(random_int(8, 14), 0),
                    $index < 8 => now()->subMinutes(random_int(5, 90)),
                    $index === 8 => now()->addHours(random_int(1, 5)),
                    default => now()->addDays(random_int(3, 60))->setTime(random_int(8, 14), 0),
                };
                $end = $start->copy()->addHours(random_int(2, 7));
                $eventStatus = $end->isPast() ? 'selesai' : ($start->isPast() ? 'berlangsung' : 'aktif');
                $capacity = random_int(0, 1) === 0 ? 100 : 200;

                $event = Event::create([
                    'nama_event' => $title,
                    'deskripsi' => "Kegiatan {$title} untuk memperluas wawasan, keterampilan, dan jejaring peserta di bidang teknologi.",
                    'tanggal_mulai' => $start,
                    'tanggal_selesai' => $end,
                    'lokasi' => $location['name'],
                    'latitude' => round($location['lat'] + random_int(-20, 20) / 10000, 6),
                    'longitude' => round($location['lng'] + random_int(-20, 20) / 10000, 6),
                    'kuota' => $capacity,
                    'biaya' => $fees[array_rand($fees)],
                    'status_event' => $eventStatus,
                ]);

                $registrantCount = random_int(18, min(45, $capacity));
                for ($personIndex = 0; $personIndex < $registrantCount; $personIndex++) {
                    $name = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
                    $email = 'dummy.' . Str::lower(Str::random(12)) . '@example.test';
                    $participant = Peserta::create([
                        'nama' => $name,
                        'email' => $email,
                        'no_hp' => '08' . random_int(1000000000, 9999999999),
                        'instansi' => $institutions[array_rand($institutions)],
                    ]);

                    $isFree = (float) $event->biaya <= 0;
                    $paymentRoll = random_int(1, 100);
                    if ($isFree) {
                        $paymentStatus = 'gratis';
                    } elseif ($eventStatus === 'selesai') {
                        $paymentStatus = $paymentRoll <= 12 ? 'kedaluwarsa' : 'lunas';
                    } else {
                        $paymentStatus = match (true) {
                            $paymentRoll <= 10 => 'kedaluwarsa',
                            $paymentRoll <= 30 => 'belum_bayar',
                            default => 'lunas',
                        };
                    }
                    $registeredAt = now()->subDays(random_int(0, 20))->subMinutes(random_int(0, 600));
                    if ($paymentStatus === 'belum_bayar') {
                        $registeredAt = now()->subMinutes(random_int(1, 14));
                    }
                    $expiry = $paymentStatus === 'belum_bayar' ? $registeredAt->copy()->addMinutes(15) : null;
                    $registration = Pendaftaran::create([
                        'public_token' => (string) Str::uuid(),
                        'attendance_code' => Str::upper(Str::random(8)),
                        'tanggal_daftar' => $registeredAt->toDateString(),
                        'peserta_id' => $participant->id,
                        'event_id' => $event->id,
                        'status_pembayaran' => $paymentStatus,
                        'expires_at' => $expiry,
                    ]);

                    // Each registration gets exactly one attendance record; only paid/free entries can attend.
                    $canAttend = in_array($paymentStatus, ['lunas', 'gratis'], true) && $start->isPast();
                    $attended = $canAttend && random_int(1, 100) <= 65;
                    Presensi::create([
                        'pendaftaran_id' => $registration->id,
                        'waktu_hadir' => $attended ? $start->copy()->addMinutes(random_int(0, 180)) : null,
                        'status_kehadiran' => $attended ? 'hadir' : 'belum_hadir',
                    ]);
                }
            }
        });

        $this->command?->info('EventHub dummy data dibuat: 20 event, kuota 100/200, harga bervariasi, pembayaran dan presensi acak.');
    }
}
