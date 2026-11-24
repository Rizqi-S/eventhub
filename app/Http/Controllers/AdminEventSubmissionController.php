<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class AdminEventSubmissionController extends Controller
{
    public function index()
    {
        $submissions = Event::query()
            ->whereIn('submission_status', ['pending', 'rejected'])
            ->orderByRaw("
                CASE
                    WHEN submission_status = 'pending' THEN 0
                    ELSE 1
                END
            ")
            ->latest()
            ->paginate(15);

        return view(
            'admin.event-submissions.index',
            compact('submissions')
        );
    }

    public function show(Event $event)
    {
        abort_unless(
            in_array($event->submission_status, ['pending', 'rejected'], true),
            404
        );

        return view(
            'admin.event-submissions.show',
            compact('event')
        );
    }

    public function approve(Event $event)
    {
        abort_unless(
            $event->submission_status === 'pending',
            404
        );

        $event->update([
            'submission_status' => 'approved',
            'status_event' => 'aktif',
            'review_notes' => null,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.event-submissions.index')
            ->with('success', 'Pengajuan disetujui dan event diterbitkan.');
    }

    public function reject(Request $request, Event $event)
    {
        abort_unless(
            $event->submission_status === 'pending',
            404
        );

        $validated = $request->validate([
            'review_notes' => 'required|string|max:2000',
        ]);

        $event->update([
            'submission_status' => 'rejected',
            'status_event' => 'draft',
            'review_notes' => $validated['review_notes'],
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.event-submissions.index')
            ->with('success', 'Pengajuan berhasil ditolak.');
    }
}