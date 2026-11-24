<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'nama_event',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'latitude',
        'longitude',
        'kuota',
        'biaya',
        'status_event',

        // Stage 2
        'poster_url',
        'poster_public_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'source_url',

        // Stage 3
        'submission_status',
        'submitted_by_name',
        'submitted_by_email',
        'submitted_by_phone',
        'review_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'biaya' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'reviewed_at' => 'datetime',
    ];

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}