@extends('public.layout')

@section('title', 'Ajukan Event')

@push('styles')
    <style>
        .submission-wrap {
            max-width: 980px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .submission-card {
            background: var(--surface, #20242b);
            border: 1px solid var(--border, #39414c);
            border-radius: 18px;
            padding: 26px;
            margin-top: 20px;
        }

        .submission-error-box {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 10px;
            background: rgba(239, 119, 119, 0.12);
            border: 1px solid rgba(239, 119, 119, 0.35);
            color: #ffb3b3;
            font-size: 13px;
        }

        .submission-error-box ul {
            margin: 8px 0 0 18px;
            padding: 0;
        }

        .submission-submitter-section {
            margin-top: 18px;
        }

        .submission-check {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 18px;
            font-size: 13px;
            color: var(--text, #eee);
        }

        .submission-check input[type="checkbox"] {
            margin-top: 3px;
            width: auto;
            min-height: auto;
        }

        .submission-actions {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
        }

        @media (max-width: 650px) {
            .submission-card {
                padding: 18px;
            }
        }
    </style>
@endpush

@section('content')
    <section class="submission-wrap">
        <p class="eyebrow">EVENTHUB COMMUNITY</p>
        <h1>Ajukan Event</h1>
        <p class="muted">
            Bagikan informasi kegiatanmu. Tim EventHub akan meninjau pengajuan sebelum ditampilkan ke publik.
        </p>

        <form class="submission-card" method="POST" action="{{ route('public.event-submissions.store') }}"
            enctype="multipart/form-data">
            @csrf

            @if($errors->any())
                <div class="submission-error-box">
                    <strong>Terjadi kesalahan:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('events.form', ['hideStatus' => true])

            <div class="field full-width event-contact-fields submission-submitter-section">
                <strong>Data Pengaju</strong>
                <div class="event-form-grid">
                    <label class="field">
                        <span>Nama Kamu</span>
                        <input type="text" name="submitted_by_name" value="{{ old('submitted_by_name') }}" maxlength="255" placeholder="Nama lengkap pengaju" required>
                        @error('submitted_by_name')<small class="field-error">{{ $message }}</small>@enderror
                    </label>

                    <label class="field">
                        <span>Nomor HP Kamu</span>
                        <input type="tel" name="submitted_by_phone" value="{{ old('submitted_by_phone') }}" inputmode="tel" placeholder="08xxxxxxxxxx" required>
                        @error('submitted_by_phone')<small class="field-error">{{ $message }}</small>@enderror
                    </label>

                    <label class="field full-width">
                        <span>Email Kamu</span>
                        <input type="email" name="submitted_by_email" value="{{ old('submitted_by_email') }}" maxlength="255" placeholder="email@example.com" required>
                        @error('submitted_by_email')<small class="field-error">{{ $message }}</small>@enderror
                    </label>
                </div>
            </div>

            <label class="submission-check">
                <input type="checkbox" name="agreement" value="1" @checked(old('agreement')) required>
                <span>
                    Saya menyatakan informasi yang dikirim benar dan saya berhak membagikan informasi serta materi event ini untuk ditinjau EventHub.
                </span>
            </label>
            @error('agreement')<small class="field-error">{{ $message }}</small>@enderror

            <div class="submission-actions">
                <button class="btn primary" type="submit">Kirim Pengajuan</button>
            </div>
        </form>
    </section>
@endsection
