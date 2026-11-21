<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EventHubDummySeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Kampus IPB Dramaga, Bogor', 'lat' => -6.5599, 'lng' => 106.7267],
            ['name' => 'Botani Square & Sekitarnya, Bogor', 'lat' => -6.5971, 'lng' => 106.8060],
            ['name' => 'Stadion Pakansari, Cibinong', 'lat' => -6.4919, 'lng' => 106.8342],
            ['name' => 'Bekasi Cyber Park & Pusat Kota Bekasi', 'lat' => -6.2383, 'lng' => 106.9756],
            ['name' => 'JCC Senayan, Jakarta Pusat', 'lat' => -6.2133, 'lng' => 106.8076],
            ['name' => 'ICE BSD City, Tangerang', 'lat' => -6.3016, 'lng' => 106.6386],
        ];

        $topics = [
            'Tech & AI Summit',
            'Cloud & DevOps Workshop',
            'Cybersecurity Bootcamp',
            'UI/UX Design Sprint',
            'Startup Pitch & Networking',
            'Data Science Clinic',
            'IoT & Embedded Systems Lab',
            'Digital Product Expo',
            'Web Engineering Meetup',
            'Career & Portfolio Review',
        ];

        $institutions = [
            'IPB University',
            'Universitas Indonesia',
            'Telkom University',
            'BINUS University',
            'SMKN 1 Bogor',
            'Komunitas Tech Bogor',
            'Digital Talent Hub',
        ];

        $now = now();

        DB::transaction(function () use ($locations, $topics, $institutions, $now) {
            for ($i = 1; $i <= 200; $i++) {
                $loc = $locations[$i % count($locations)];
                $lat = round($loc['lat'] + ((($i % 10) - 5) * 0.0018), 6);
                $lng = round($loc['lng'] + ((($i % 7) - 3) * 0.0018), 6);

                // 1..60: Selesai | 61..100: Ongoing | 101..200: Akan Datang
                if ($i <= 60) {
                    $status = 'completed';
                    $statusId = 'selesai';
                    $startAt = $now->copy()->subDays(65 - $i);
                    $endAt = $startAt->copy()->addHours(4);
                } elseif ($i <= 100) {
                    $status = 'ongoing';
                    $statusId = 'ongoing';
                    $startAt = $now->copy()->subHours(2);
                    $endAt = $now->copy()->addHours(4);
                } else {
                    $status = 'upcoming';
                    $statusId = 'akan_datang';
                    $startAt = $now->copy()->addDays($i - 100);
                    $endAt = $startAt->copy()->addHours(3);
                }

                $topic = $topics[$i % count($topics)];
                $title = "{$topic} #{$i} - " . Str::before($loc['name'], ',');
                $desc = "Sesi {$topic} ke-{$i} yang diselenggarakan di {$loc['name']}.";
                $slug = Str::slug($title) . '-' . $i;

                $rawEvent = [
                    'nama_event'      => $title,
                    'title'           => $title,
                    'name'            => $title,
                    'slug'            => $slug,
                    'deskripsi'       => $desc,
                    'description'     => $desc,
                    'lokasi'          => $loc['name'],
                    'location'        => $loc['name'],
                    'alamat'          => $loc['name'],
                    'address'         => $loc['name'],
                    'status'          => $status,
                    'status_event'    => $statusId,
                    'tanggal_mulai'   => $startAt,
                    'tanggal_selesai' => $endAt,
                    'waktu_mulai'     => $startAt,
                    'waktu_selesai'   => $endAt,
                    'start_at'        => $startAt,
                    'end_at'          => $endAt,
                    'kuota'           => 100,
                    'quota'           => 100,
                    'kapasitas'       => 100,
                    'capacity'        => 100,
                    'coordinates'     => json_encode(['lat' => $lat, 'lng' => $lng]),
                    'latitude'        => $lat,
                    'longitude'       => $lng,
                    'lat'             => $lat,
                    'lng'             => $lng,
                    'is_published'    => true,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];

                $eventData = [];
                foreach ($rawEvent as $col => $val) {
                    if ($val !== null && Schema::hasColumn('events', $col)) {
                        $eventData[$col] = $val;
                    }
                }

                $eventId = DB::table('events')->insertGetId($eventData);

                $registrantCount = random_int(15, 85);

                for ($p = 1; $p <= $registrantCount; $p++) {
                    $pesertaName = "Peserta Event {$i} - {$p}";
                    $pesertaEmail = "peserta.e{$i}.p{$p}.{$i}@example.test";
                    $pesertaPhone = '0859' . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                    $instansi = $institutions[($i + $p) % count($institutions)];
                    $registeredAt = $now->copy()->subDays(random_int(0, 14));

                    $pesertaId = null;
                    if (Schema::hasTable('pesertas')) {
                        $pesertaData = [
                            'nama'       => $pesertaName,
                            'email'      => $pesertaEmail,
                            'no_hp'      => $pesertaPhone,
                            'instansi'   => $instansi,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        foreach (['name' => $pesertaName, 'phone' => $pesertaPhone, 'telepon' => $pesertaPhone] as $col => $val) {
                            if (Schema::hasColumn('pesertas', $col)) {
                                $pesertaData[$col] = $val;
                            }
                        }

                        $pesertaId = DB::table('pesertas')->insertGetId($pesertaData);
                    }

                    if (Schema::hasTable('pendaftarans')) {
                        $rawPendaftaran = [
                            'event_id'         => $eventId,
                            'peserta_id'       => $pesertaId,
                            'nama'             => $pesertaName,
                            'name'             => $pesertaName,
                            'email'            => $pesertaEmail,
                            'no_hp'            => $pesertaPhone,
                            'phone'            => $pesertaPhone,
                            'instansi'         => $instansi,
                            'tanggal_daftar'   => $registeredAt,
                            'waktu_daftar'     => $registeredAt,
                            'registered_at'    => $registeredAt,
                            'status'           => $status === 'completed' ? 'attended' : 'confirmed',
                            'status_kehadiran' => $status === 'completed' ? 'hadir' : 'terdaftar',
                            'public_token'     => Str::random(32),
                            'attendance_code'  => strtoupper(Str::random(8)),
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ];

                        $pendaftaranData = [];
                        foreach ($rawPendaftaran as $col => $val) {
                            if ($val !== null && Schema::hasColumn('pendaftarans', $col)) {
                                $pendaftaranData[$col] = $val;
                            }
                        }

                        if (! empty($pendaftaranData)) {
                            DB::table('pendaftarans')->insert($pendaftaranData);
                        }
                    }
                }
            }
        });
    }
}