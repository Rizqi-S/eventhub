<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventJejepanganSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            // NOVEMBER 2026
            [
                'nama_event' => 'MOSHI MOSHI "3 DAYS OF JAPAN FESTIVAL" IS BACK',
                'tanggal_mulai' => '2026-11-27 00:00:00',
                'tanggal_selesai' => '2026-11-29 23:59:59',
                'lokasi' => 'Summarecon Mall Bekasi',
                'deskripsi' => 'Event jejepangan 3 Days of Japan Festival. Informasi: https://www.instagram.com/p/DfbuDQokjzU/',
            ],
            [
                'nama_event' => 'Hajatan Weeaboo',
                'tanggal_mulai' => '2026-11-28 00:00:00',
                'tanggal_selesai' => '2026-11-28 23:59:59',
                'lokasi' => 'Plaza Multifungsi, Jakarta International Velodrome',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/Dflz1Ffk9x7/',
            ],
            [
                'nama_event' => 'Magic Hour Show Anime Culture',
                'tanggal_mulai' => '2026-11-28 00:00:00',
                'tanggal_selesai' => '2026-11-28 23:59:59',
                'lokasi' => 'Mal Mangga Dua',
                'deskripsi' => 'Informasi: https://www.instagram.com/reel/Df6r-QthpQT/',
            ],
            [
                'nama_event' => 'Wibu Kumpul Main',
                'tanggal_mulai' => '2026-11-28 00:00:00',
                'tanggal_selesai' => '2026-11-28 23:59:59',
                'lokasi' => 'Bagi Kopi, Lenteng Agung',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/DfzcfZQzHG6/',
            ],
            [
                'nama_event' => 'Mirai Matsuri',
                'tanggal_mulai' => '2026-11-29 00:00:00',
                'tanggal_selesai' => '2026-11-29 23:59:59',
                'lokasi' => 'Revo Mall',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/Df9TAm7Ogkv/',
            ],
            [
                'nama_event' => 'Dreamer Festival 2026',
                'tanggal_mulai' => '2026-11-29 00:00:00',
                'tanggal_selesai' => '2026-11-29 23:59:59',
                'lokasi' => 'Piazza, Gandaria City',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/DftoIGLTjv2/',
            ],

            // DESEMBER 2026
            [
                'nama_event' => 'Yume No Mitai Festival',
                'tanggal_mulai' => '2026-12-05 00:00:00',
                'tanggal_selesai' => '2026-12-06 23:59:59',
                'lokasi' => 'Museum Bank Mandiri',
                'deskripsi' => 'Informasi tiket: Available on August. Informasi: https://www.instagram.com/p/Dao1j4nPBaX/',
            ],
            [
                'nama_event' => 'JAPAN MUSICOLOGY 2',
                'tanggal_mulai' => '2026-12-05 00:00:00',
                'tanggal_selesai' => '2026-12-05 23:59:59',
                'lokasi' => 'Rossi Music Fatmawati',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/Dfuco3IlBDd/',
            ],
            [
                'nama_event' => 'IAM JAKARTA 2026',
                'tanggal_mulai' => '2026-12-12 00:00:00',
                'tanggal_selesai' => '2026-12-13 23:59:59',
                'lokasi' => 'Ecovention Ancol, Jakarta',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/DfUnsAwmWmC/',
            ],
            [
                'nama_event' => 'Winter Fest Gakure',
                'tanggal_mulai' => '2026-12-12 00:00:00',
                'tanggal_selesai' => '2026-12-13 23:59:59',
                'lokasi' => 'Mall Ciputra Cibubur',
                'deskripsi' => 'Informasi penyelenggara: @kazokujapanfestival',
            ],
            [
                'nama_event' => 'Isekai Run',
                'tanggal_mulai' => '2026-12-20 00:00:00',
                'tanggal_selesai' => '2026-12-20 23:59:59',
                'lokasi' => 'Pantai Lagoon, Ancol',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/Dd70UDxE4TS/',
            ],
            [
                'nama_event' => 'Alam Sutera Matsuri',
                'tanggal_mulai' => '2026-12-25 00:00:00',
                'tanggal_selesai' => '2026-12-27 23:59:59',
                'lokasi' => 'Mall Alam Sutera, Tangerang',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/DfXQWNJPAii/',
            ],
            [
                'nama_event' => 'M. Group Expo',
                'tanggal_mulai' => '2026-12-27 00:00:00',
                'tanggal_selesai' => '2026-12-27 23:59:59',
                'lokasi' => 'Citra Xperience, Kebayoran',
                'deskripsi' => 'Harga tiket: Rp20.000. Informasi WhatsApp: 0851-8196-2334. Instagram: @agungmozagroup',
            ],

            // JANUARI 2027
            [
                'nama_event' => 'The Jakarta 22nd Toys & Comics Fair 2026',
                'tanggal_mulai' => '2027-01-30 00:00:00',
                'tanggal_selesai' => '2027-01-31 23:59:59',
                'lokasi' => 'Kartika Expo Center, Balai Kota Kartini',
                'deskripsi' => 'Event Toys & Comics Fair. Nama event dipertahankan sesuai sumber.',
            ],

            // MEI 2027
            [
                'nama_event' => 'Comic Frontier-24',
                'tanggal_mulai' => '2027-05-15 00:00:00',
                'tanggal_selesai' => '2027-05-16 23:59:59',
                'lokasi' => 'ICE BSD',
                'deskripsi' => 'Informasi: https://www.instagram.com/p/DfMhcwQmK0T/',
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['nama_event' => $event['nama_event']],
                array_merge($event, [
                    'kuota' => 0,
                    'biaya' => $event['nama_event'] === 'M. Group Expo'
                        ? 20000
                        : 0,
                ])
            );
        }
    }
}