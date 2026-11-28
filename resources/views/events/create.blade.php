@extends('layouts.admin')

@section('title', 'Events')

@section('content')
    <div class="legacy-module-page">
        <div class="header">
            <h1>Tambah Event</h1>
            <p>Tambahkan informasi event baru ke EventHub.</p>
        </div>

        <div class="form-card">

            @if($errors->any())
                <div class="error-box">
                    <strong>Terjadi kesalahan:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @include('events.form')

                <div class="form-actions">
                    <a href="{{ route('events.index') }}" class="btn outline">Kembali</a>
                    <button type="submit" class="btn primary">Simpan Event</button>
                </div>
            </form>
        </div>
    </div>
@endsection