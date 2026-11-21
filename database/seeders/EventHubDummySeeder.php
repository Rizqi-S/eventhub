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

        // Cek kolom tabel SEKALI saja di awal (bukan di dalam loop)
        $eventCols = array_flip(Schema::getColumnListing('events'));
        $hasPesertas = Schema::hasTable('pesertas');
        $pesertaCols = $hasPesertas ? array_flip(Schema::getColumnListing('pesertas')) : [];
        $hasPendaftarans = Schema::hasTable('pendaftarans');
        $pendaftaranCols = $hasPendaftarans ? array_flip(Schema::getColumnListing('pendaftarans')) : [];

        $now = now();
        $runTag = $now->format('YmdHis') . '_' . Str::lower(Str::random(4));

        $eventsBatch = [];
        $pesertasBatch = [];
        $pendaftaransBatch = [];
        $globalCounter = 0;

        for ($i = 1; $i <= 200; $i++) {
            $loc = $locations[$i % count($locations)];
            $lat = round($loc['lat'] + ((($i % 10) - 5) * 0.0018), 6);
            $lng = round($loc['lng'] + ((($i % 7) - 3) * 0.0018), 6);

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
            $slug = Str::slug($title) . '-' . $i . '-' . $runTag;

            $rawEvent = [
                'id'              => $i,
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

            $eventsBatch[] = array_intersect_key($rawEvent, $eventCols);

            $registrantCount = random_int(15, 45);
            for ($p = 1; $p <= $registrantCount; $p++) {
                $globalCounter++;
                $pesertaName = "Peserta Event {$i} - {$p}";
                $pesertaEmail = "peserta.{$runTag}.{$globalCounter}@example.test";
                $pesertaPhone = '0859' . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $instansi = $institutions[($i + $p) % count($institutions)];
                $registeredAt = $now->copy()->subDays(random_int(0, 14));

                if ($hasPesertas) {
                    $rawPeserta = [
                        'id'         => $globalCounter,
                        'nama'       => $pesertaName,
                        'name'       => $pesertaName,
                        'email'      => $pesertaEmail,
                        'no_hp'      => $pesertaPhone,
                        'phone'      => $pesertaPhone,
                        'telepon'    => $pesertaPhone,
                        'instansi'   => $instansi,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $pesertasBatch[] = array_intersect_key($rawPeserta, $pesertaCols);
                }

                if ($hasPendaftarans) {
                    $rawPendaftaran = [
                        'event_id'           => $i,
                        'peserta_id'         => $hasPesertas ? $globalCounter : null,
                        'nama'               => $pesertaName,
                        'name'               => $pesertaName,
                        'email'              => $pesertaEmail,
                        'no_hp'              => $pesertaPhone,
                        'phone'              => $pesertaPhone,
                        'instansi'           => $instansi,
                        'tanggal_daftar'     => $registeredAt,
                        'waktu_daftar'       => $registeredAt,
                        'registered_at'      => $registeredAt,
                        'status'             => $status === 'completed' ? 'attended' : 'confirmed',
                        'status_pendaftaran' => $status === 'completed' ? 'hadir' : 'terdaftar',
                        'status_kehadiran'   => $status === 'completed' ? 'hadir' : 'terdaftar',
                        'public_token'       => Str::random(32),
                        'attendance_code'    => strtoupper(Str::random(8)),
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                    $pendaftaransBatch[] = array_intersect_key($rawPendaftaran, $pendaftaranCols);
                }
            }
        }

        DB::transaction(function () use ($eventsBatch, $pesertasBatch, $pendaftaransBatch, $hasPesertas, $hasPendaftarans) {
            foreach (array_chunk($eventsBatch, 200) as $chunk) {
                DB::table('events')->insert($chunk);
            }
            if ($hasPesertas) {
                foreach (array_chunk($pesertasBatch, 500) as $chunk) {
                    DB::table('pesertas')->insert($chunk);
                }
            }
            if ($hasPendaftarans) {
                foreach (array_chunk($pendaftaransBatch, 500) as $chunk) {
                    DB::table('pendaftarans')->insert($chunk);
                }
            }
        });
    }
}