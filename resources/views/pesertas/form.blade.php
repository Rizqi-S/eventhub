@push('styles')
<style>
.form-panel .stack-form input:not([type="checkbox"]),
.form-panel .stack-form select,
.form-panel .stack-form textarea {
    width: 100%;
    min-height: 44px;
    padding: 12px 14px;
    background: #252525;
    color: #eee;
    border: 1px solid #414141;
    border-radius: 9px;
}
.form-panel .stack-form input:not([type="checkbox"]):focus,
.form-panel .stack-form select:focus,
.form-panel .stack-form textarea:focus {
    outline: none;
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(85, 214, 194, .1);
}
</style>
@endpush
<label>Nama lengkap
    <input name="nama" value="{{ old('nama',$peserta->nama ?? '') }}" required>
    @error('nama')
    <small class="field-error">{{ $message }}</small>
    @enderror
</label>
<label>Email
    <input type="email" name="email" value="{{ old('email',$peserta->email ?? '') }}" required>
    @error('email')
    <small class="field-error">{{ $message }}</small>
    @enderror
</label>
<label>Nomor HP
    <input type="tel" name="no_hp" value="{{ old('no_hp',$peserta->no_hp ?? '') }}" required minlength="8" maxlength="16" pattern="\+?[0-9]{8,15}" inputmode="tel" placeholder="081234567890">
    <small class="muted">8–15 digit, boleh diawali +62.</small>
    @error('no_hp')
    <small class="field-error">{{ $message }}</small>
    @enderror
</label>
<label>Instansi
    <input name="instansi" value="{{ old('instansi',$peserta->instansi ?? '') }}" required>
    @error('instansi')
    <small class="field-error">{{ $message }}</small>
    @enderror
</label>
