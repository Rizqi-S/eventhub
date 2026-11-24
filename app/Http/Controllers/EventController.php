<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\CloudinaryService;
use App\Services\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'terbaru');
        $direction = $sort === 'terlama' ? 'asc' : 'desc';
        $events = Event::orderBy('tanggal_mulai', $direction)->paginate(10)->withQueryString();
        return view('events.index', compact('events', 'sort'));
    }

    public function create() { return view('events.create'); }

    public function store(Request $request, CloudinaryService $cloudinary, RichTextSanitizer $sanitizer)
    {
        $validated = $this->validateEvent($request, true);
        $poster = null;
        if ($request->hasFile('poster')) {
            try { $poster = $cloudinary->upload($request->file('poster')); }
            catch (Throwable $e) {
                Log::error('Cloudinary event poster upload failed', ['exception' => $e]);
                throw ValidationException::withMessages(['poster' => 'Poster gagal diunggah. Periksa konfigurasi Cloudinary dan coba lagi.']);
            }
        }

        $validated['deskripsi'] = $sanitizer->clean($validated['deskripsi'] ?? null);
        if ($poster) {
            $validated['poster_url'] = $poster['url'];
            $validated['poster_public_id'] = $poster['public_id'];
        }
        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Event berhasil ditambahkan!');
    }

    public function show(Event $event) { return view('events.show', compact('event')); }
    public function edit(Event $event) { return view('events.edit', compact('event')); }

    public function update(Request $request, Event $event, CloudinaryService $cloudinary, RichTextSanitizer $sanitizer)
    {
        $validated = $this->validateEvent($request, false);
        $oldPublicId = $event->poster_public_id;
        $newPoster = null;

        if ($request->hasFile('poster')) {
            try { $newPoster = $cloudinary->upload($request->file('poster')); }
            catch (Throwable $e) {
                Log::error('Cloudinary event poster replacement failed', ['exception' => $e]);
                throw ValidationException::withMessages(['poster' => 'Poster baru gagal diunggah. Poster lama tetap dipertahankan.']);
            }
        }

        $validated['deskripsi'] = $sanitizer->clean($validated['deskripsi'] ?? null);
        if ($newPoster) {
            $validated['poster_url'] = $newPoster['url'];
            $validated['poster_public_id'] = $newPoster['public_id'];
        }
        $event->update($validated);

        if ($newPoster && $oldPublicId) {
            try { $cloudinary->delete($oldPublicId); }
            catch (Throwable $e) { Log::warning('Poster lama tidak berhasil dihapus dari Cloudinary', ['public_id' => $oldPublicId, 'exception' => $e]); }
        }

        return redirect()->route('events.index')->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy(Event $event, CloudinaryService $cloudinary)
    {
        $publicId = $event->poster_public_id;
        try {
            $event->delete();
            if ($publicId) {
                try { $cloudinary->delete($publicId); }
                catch (Throwable $e) { Log::warning('Poster event terhapus dari database tetapi gagal dihapus dari Cloudinary', ['public_id' => $publicId, 'exception' => $e]); }
            }
            return redirect()->route('events.index')->with('success', 'Event berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->route('events.index')->with('error', 'Event tidak dapat dihapus karena sudah memiliki data pendaftaran.');
        }
    }

    private function validateEvent(Request $request, bool $creating): array
    {
        return $request->validate([
            'nama_event' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:20000',
            'poster' => [$creating ? 'nullable' : 'sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => ['nullable', 'string', 'regex:/^\\+?[0-9]{8,15}$/'],
            'contact_email' => 'nullable|email|max:255',
            'source_url' => 'nullable|url:http,https|max:2048',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'required|string|max:255',
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'kuota' => 'required|integer|min:1',
            'biaya' => 'required|numeric|min:0',
            'status_event' => 'required|in:draft,aktif,berlangsung,selesai',
        ], [
            'poster.image' => 'Poster harus berupa file gambar.',
            'poster.mimes' => 'Format poster yang didukung: JPG, PNG, dan WebP.',
            'poster.max' => 'Ukuran poster maksimal 5 MB.',
            'contact_phone.regex' => 'Nomor kontak harus berisi 8–15 digit dan boleh diawali tanda +.',
            'source_url.url' => 'Source link harus berupa URL HTTP atau HTTPS yang valid.',
        ]);
    }
}
