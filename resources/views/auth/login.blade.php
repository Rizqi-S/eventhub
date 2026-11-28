<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Super Admin · EventHub</title>
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body class="auth-body">
        <main class="auth-card">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark">E</span>
                <span>EventHub</span>
            </a>
            <p class="eyebrow">PRIVATE MANAGEMENT</p>
            <h1>Selamat datang kembali</h1>
            <p class="muted">Masuk menggunakan akun Admin.</p>
            <form method="POST" action="{{ route('admin.login.store') }}" class="stack-form">
                @csrf
                <label>Email
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </label>
                @error('email')
                    <small class="field-error">{{ $message }}</small>
                @enderror
                <label>Password
                    <input type="password" name="password" required autocomplete="current-password">
                </label>
                <label class="check-line">
                    <input type="checkbox" name="remember" value="1"> Ingat saya
                </label>
                <button class="btn primary full" type="submit">Masuk ke Dashboard</button>
            </form>
            <div style="margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--border, #333);">
                <p class="muted" style="font-size: 12px; margin: 0 0 10px;">Isi otomatis akun Admin:</p>
                <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px;">
                    <button type="button" class="btn" data-fill-email="admin1@eventhub.test" data-fill-password="Admin1Pass!2026">Admin 1</button>
                    <button type="button" class="btn" data-fill-email="admin2@eventhub.test" data-fill-password="Admin2Pass!2026">Admin 2</button>
                    <button type="button" class="btn" data-fill-email="admin3@eventhub.test" data-fill-password="Admin3Pass!2026">Admin 3</button>
                </div>
            </div>
            <a class="muted back-link" href="{{ route('home') }}">← Kembali ke website</a>
        </main>
        <script>
            document.querySelectorAll('[data-fill-email]').forEach((button) => {
                button.addEventListener('click', () => {
                    const emailInput = document.querySelector('input[name="email"]');
                    const passwordInput = document.querySelector('input[name="password"]');
                    if (emailInput) emailInput.value = button.dataset.fillEmail;
                    if (passwordInput) passwordInput.value = button.dataset.fillPassword;
                });
            });
        </script>
    </body>
</html>
