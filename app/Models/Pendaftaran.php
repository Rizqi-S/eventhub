<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $fillable = [
        'public_token', 'attendance_code', 'tanggal_daftar', 'peserta_id', 'event_id',
        'status_pembayaran', 'expires_at',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'expires_at' => 'datetime',
    ];

    public function peserta() { return $this->belongsTo(Peserta::class); }
    public function event() { return $this->belongsTo(Event::class); }
    public function presensi() { return $this->hasOne(Presensi::class); }
    public function presensis() { return $this->hasOne(Presensi::class); }

    public function isPaymentActive(): bool
    {
        return in_array($this->status_pembayaran, ['lunas', 'gratis'], true)
            || ($this->status_pembayaran === 'belum_bayar' && $this->expires_at?->isFuture());
    }
}
