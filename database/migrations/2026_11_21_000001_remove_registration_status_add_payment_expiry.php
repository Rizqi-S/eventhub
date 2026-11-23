<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('status_pembayaran');
        });

        // Normalize the legacy payment label and give existing unpaid records a 15-minute deadline.
        DB::table('pendaftarans')->where('status_pembayaran', 'sudah_bayar')->update(['status_pembayaran' => 'lunas']);
        DB::table('pendaftarans')->where('status_pembayaran', 'belum_bayar')->whereNull('expires_at')
            ->orderBy('id')->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $deadline = $row->created_at
                        ? \Illuminate\Support\Carbon::parse($row->created_at)->addMinutes(15)
                        : now();
                    DB::table('pendaftarans')->where('id', $row->id)->update(['expires_at' => $deadline]);
                }
            });

        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn('status_pendaftaran');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->string('status_pendaftaran')->default('menunggu');
        });
        DB::table('pendaftarans')->where('status_pembayaran', 'lunas')->update(['status_pembayaran' => 'sudah_bayar']);
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
