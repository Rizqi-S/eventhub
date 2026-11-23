<?php

namespace App\Console\Commands;

use App\Models\Pendaftaran;
use Illuminate\Console\Command;

class ExpirePendingPayments extends Command
{
    protected $signature = 'payments:expire-pending';
    protected $description = 'Menandai pembayaran yang melewati batas 15 menit sebagai kedaluwarsa.';

    public function handle(): int
    {
        $count = Pendaftaran::query()->where('status_pembayaran', 'belum_bayar')
            ->whereNotNull('expires_at')->where('expires_at', '<=', now())
            ->update(['status_pembayaran' => 'kedaluwarsa', 'updated_at' => now()]);

        $this->info("{$count} pembayaran ditandai kedaluwarsa.");
        return self::SUCCESS;
    }
}
