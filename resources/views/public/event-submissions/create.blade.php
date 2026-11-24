@extends('public.layout')

@section('title', 'Ajukan Event')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

    <style>
        .submission-wrap {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .submission-card {
            background: var(--surface, #20242b);
            border: 1px solid var(--border, #39414c);
            border-radius: 18px;
            padding: 26px;
        }

        .submission-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .submission-grid .full {
            grid-column: 1 / -1;
        }

        .submission-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .submission-field label {
            font-weight: 600;
        }

        .submission-field input,
        .submission-field textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border, #555);
            border-radius: 9px;
            background: var(--surface-2, #171b22);
            color: inherit;
        }

        .submission-error {
            color: #e66;
        }

        .submission-editor {
            background: white;
            color: #222;
            border-radius: 8px;
        }

        .submission-editor #editor,
        .submission-editor .ql-editor {
            min-height: 190px;
        }

        .submission-check {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 18px;
        }

        .submission-actions {
            margin-top: 22px;
        }

        @media(max-width:650px) {
            .submission-grid {
                grid-template-columns: 1fr;
            }

            .submission-grid .full {
                grid-column: auto;
            }

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
            Bagikan informasi kegiatanmu.
            Tim EventHub akan meninjau pengajuan sebelum ditampilkan ke publik.
        </p>

        <form class="submission-card" method="POST" action="{{ route('public.event-submissions.store') }}"
            enctype="multipart/form-data">
            @csrf

            <h2>Informasi Event</h2>

            <div class="submission-grid">

                <div class="submission-field full">
                    <label for="nama_event">Nama event</label>

                    <input id="nama_event" name="nama_event" value="{{ old('nama_event') }}" maxlength="255" required>

                    @error('nama_event')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field full">
                    <label>Deskripsi event</label>

                    <div class="submission-editor">
                        <div id="editor">{!! old('deskripsi') !!}</div>
                    </div>

                    <input type="hidden" name="deskripsi" id="deskripsi">

                    @error('deskripsi')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field full">
                    <label for="poster">
                        Poster (JPG/PNG/WebP, maks. 5 MB)
                    </label>

                    <input id="poster" type="file" name="poster"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                    @error('poster')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="tanggal_mulai">Tanggal mulai</label>

                    <input id="tanggal_mulai" type="datetime-local" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                        required>

                    @error('tanggal_mulai')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="tanggal_selesai">Tanggal selesai</label>

                    <input id="tanggal_selesai" type="datetime-local" name="tanggal_selesai"
                        value="{{ old('tanggal_selesai') }}">

                    @error('tanggal_selesai')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="lokasi">Lokasi</label>

                    <input id="lokasi" name="lokasi" value="{{ old('lokasi') }}" maxlength="255" required>

                    @error('lokasi')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="kuota">Kuota peserta</label>

                    <input id="kuota" type="number" min="1" name="kuota" value="{{ old('kuota', 1) }}" required>

                    @error('kuota')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="biaya">Biaya (Rp)</label>

                    <input id="biaya" type="number" min="0" step="0.01" name="biaya" value="{{ old('biaya', 0) }}" required>

                    @error('biaya')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="source_url">Link sumber resmi</label>

                    <input id="source_url" type="url" name="source_url" value="{{ old('source_url') }}"
                        placeholder="https://" required>

                    @error('source_url')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <h2 style="margin-top:28px">Kontak Penyelenggara</h2>

            <div class="submission-grid">

                <div class="submission-field">
                    <label for="contact_name">Nama narahubung</label>

                    <input id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required>

                    @error('contact_name')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="contact_phone">Nomor telepon</label>

                    <input id="contact_phone" type="tel" name="contact_phone" value="{{ old('contact_phone') }}" required>

                    @error('contact_phone')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field full">
                    <label for="contact_email">Email kontak</label>

                    <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email') }}" required>

                    @error('contact_email')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <h2 style="margin-top:28px">Data Pengaju</h2>

            <div class="submission-grid">

                <div class="submission-field">
                    <label for="submitted_by_name">Nama kamu</label>

                    <input id="submitted_by_name" name="submitted_by_name" value="{{ old('submitted_by_name') }}" required>

                    @error('submitted_by_name')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field">
                    <label for="submitted_by_phone">Nomor HP kamu</label>

                    <input id="submitted_by_phone" type="tel" name="submitted_by_phone"
                        value="{{ old('submitted_by_phone') }}" required>

                    @error('submitted_by_phone')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="submission-field full">
                    <label for="submitted_by_email">Email kamu</label>

                    <input id="submitted_by_email" type="email" name="submitted_by_email"
                        value="{{ old('submitted_by_email') }}" required>

                    @error('submitted_by_email')
                        <small class="submission-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <label class="submission-check">
                <input type="checkbox" name="agreement" value="1" @checked(old('agreement')) required>

                <span>
                    Saya menyatakan informasi yang dikirim benar dan saya
                    berhak membagikan informasi serta materi event ini
                    untuk ditinjau EventHub.
                </span>
            </label>

            @error('agreement')
                <small class="submission-error">{{ $message }}</small>
            @enderror

            <div class="submission-actions">
                <button class="btn primary" type="submit">
                    Kirim Pengajuan
                </button>
            </div>

        </form>
    </section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editor = document.getElementById('editor');
            const form = editor?.closest('form');

            if (!editor || !form || typeof Quill === 'undefined') {
                return;
            }

            const quill = new Quill(editor, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ header: [2, 3, false] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            form.addEventListener('submit', function () {
                document.getElementById('deskripsi').value =
                    quill.root.innerHTML;
            });
        });
    </script>
@endpush