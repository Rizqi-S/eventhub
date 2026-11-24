<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\CloudinaryService;
use App\Services\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class GuestEventSubmissionController extends Controller
{
    public function create()
    {
        return view('public.event-submissions.create');
    }

    public function store(
        Request $request,
        CloudinaryService $cloudinary,
        RichTextSanitizer $sanitizer
    ) {
        $validated = $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:20000',

            'poster' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'tanggal_mulai' => 'required|date|after:now',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',

            'lokasi' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
            'biaya' => 'required|numeric|min:0',

            'contact_name' => 'required|string|max:255',
            'contact_phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{8,15}$/',
            ],
            'contact_email' => 'required|email|max:255',

            'source_url' => 'required|url:http,https|max:2048',

            'submitted_by_name' => 'required|string|max:255',
            'submitted_by_email' => 'required|email|max:255',
            'submitted_by_phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{8,15}$/',
            ],

            'agreement' => ['accepted'],
        ], [
            'source_url.required' => 'Link sumber resmi event wajib diisi.',
            'agreement.accepted' => 'Kamu harus menyetujui pernyataan pengajuan.',
            'contact_phone.regex' => 'Nomor kontak harus berisi 8–15 digit.',
            'submitted_by_phone.regex' => 'Nomor pengaju harus berisi 8–15 digit.',
        ]);

        $description = $sanitizer->clean($validated['deskripsi']);

        if (trim(strip_tags($description)) === '') {
            throw ValidationException::withMessages([
                'deskripsi' => 'Deskripsi event tidak boleh kosong.',
            ]);
        }

        $poster = null;

        if ($request->hasFile('poster')) {
            try {
                $poster = $cloudinary->upload($request->file('poster'));
            } catch (Throwable $e) {
                Log::error('Guest event poster upload failed', [
                    'exception' => $e,
                ]);

                throw ValidationException::withMessages([
                    'poster' => 'Poster gagal diunggah. Silakan coba lagi.',
                ]);
            }
        }

        try {
            DB::transaction(function () use (
                $validated,
                $description,
                $poster
            ) {
                $eventData = [
                    'nama_event' => $validated['nama_event'],
                    'deskripsi' => $description,

                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,

                    'lokasi' => $validated['lokasi'],
                    'kuota' => $validated['kuota'],
                    'biaya' => $validated['biaya'],

                    // Belum boleh dipublikasikan.
                    'status_event' => 'draft',
                    'submission_status' => 'pending',

                    'contact_name' => $validated['contact_name'],
                    'contact_phone' => $validated['contact_phone'],
                    'contact_email' => strtolower($validated['contact_email']),

                    'source_url' => $validated['source_url'],

                    'submitted_by_name' => $validated['submitted_by_name'],
                    'submitted_by_email' => strtolower($validated['submitted_by_email']),
                    'submitted_by_phone' => $validated['submitted_by_phone'],
                ];

                if ($poster) {
                    $eventData['poster_url'] = $poster['url'];
                    $eventData['poster_public_id'] = $poster['public_id'];
                }

                Event::create($eventData);
            });
        } catch (Throwable $e) {
            // Jangan biarkan poster yatim di Cloudinary.
            if ($poster) {
                try {
                    $cloudinary->delete($poster['public_id']);
                } catch (Throwable $cleanupError) {
                    Log::warning('Failed to clean up guest poster', [
                        'exception' => $cleanupError,
                    ]);
                }
            }

            throw $e;
        }

        return redirect()
            ->route('public.event-submissions.success');
    }

    public function success()
    {
        return view('public.event-submissions.success');
    }
}