<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk | {{ $appBranding['name'] }} — Proyek</title>
    <link href="{{ $appBranding['favicon'] }}" rel="icon">
    <style>
        :root { color-scheme: light; --blue: #1079a8; --ink: #203746; --muted: #5c6d79; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #e5edf2; color: var(--ink); font: 16px/1.6 system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        body::before { content: ""; position: fixed; inset: 0; z-index: -1; background: linear-gradient(120deg, rgba(226, 237, 244, .65), rgba(226, 237, 244, .35)), url("{{ asset('assets/img/login-perumahan-subsidi.jpg') }}") center / cover no-repeat; }
        body { isolation: isolate; }
        .login-page { min-height: 100vh; min-height: 100svh; display: flex; align-items: center; justify-content: center; padding: 36px 20px; }
        .login-shell { width: 100%; max-width: 440px; }
        .brand { text-align: center; margin-bottom: 26px; }
        .brand img { display: block; width: 112px; height: 112px; object-fit: contain; margin: 0 auto 16px; }
        .brand-name { margin: 0; font-size: 24px; line-height: 1.35; font-weight: 800; letter-spacing: .02em; }
        .login-card { padding: 32px; background: #fff; border: 1px solid #dfe7ec; border-top: 3px solid var(--blue); border-radius: 12px; box-shadow: 0 8px 28px #20374608; }
        .eyebrow { display: flex; align-items: center; gap: 8px; color: var(--blue); font-size: 14px; font-weight: 600; margin: 0 0 12px; }
        .eyebrow::before { content: ""; width: 16px; height: 3px; background: #ffbd40; border-radius: 2px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 7px; font-size: 14px; font-weight: 600; }
        input { width: 100%; min-height: 48px; border: 1px solid #cbd6de; border-radius: 7px; background: #fff; color: var(--ink); padding: 11px 13px; font: inherit; }
        input::placeholder { color: #768590; font-size: 14px; }
        input:focus { border-color: var(--blue); outline: 3px solid #1079a820; }
        input.is-invalid { border-color: #bb3434; }
        .invalid-feedback { display: block; color: #a62b2b; font-size: 14px; margin-top: 5px; }
        .invalid-feedback:empty { display: none; }
        .form-alert { margin: 0 0 18px; padding: 10px 12px; border: 1px solid #f0caca; border-radius: 6px; background: #fff5f5; color: #a62b2b; font-size: 14px; }
        [hidden] { display: none !important; }
        button { width: 100%; min-height: 48px; display: flex; justify-content: center; align-items: center; gap: 9px; padding: 12px; border: 0; border-radius: 7px; background: var(--blue); color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        button:hover { background: #09658e; }
        button:focus-visible { outline: 3px solid #1079a84d; outline-offset: 3px; }
        button:disabled { opacity: .7; cursor: wait; }
        .spinner { width: 16px; height: 16px; border: 2px solid #ffffff60; border-top-color: #fff; border-radius: 50%; animation: spin .8s linear infinite; }
        .features { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 18px; margin: 22px 0 0; padding-top: 18px; border-top: 1px solid #edf1f4; color: var(--muted); font-size: 14px; }
        footer { text-align: center; margin-top: 22px; color: #203746; font-size: 13px; background: rgba(255, 255, 255, .9); border-radius: 6px; padding: 8px 12px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 480px) { .login-page { padding: 24px 16px; } .login-card { padding: 26px 22px; } .brand { margin-bottom: 22px; } }
        @media (prefers-reduced-motion: reduce) { .spinner { animation: none; } }
    </style>
</head>
<body>
    <main class="login-page">
        <div class="login-shell">
            <section class="login-card" aria-labelledby="login-title">
            <header class="brand">
                <img src="{{ $appBranding['logo'] }}" alt="Logo {{ $appBranding['name'] }}" width="112" height="112">
                <p class="brand-name">{{ $appBranding['name'] }}</p>
            </header>
                <p class="eyebrow" id="login-title">Sistem Pengelolaan Proyek</p>
                <form method="POST" action="{{ route('admin.loginPost') }}" id="formLogin">
                    @csrf
                    <div class="form-alert" id="login-alert" role="alert" hidden></div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Masukkan username" autocomplete="username" autocapitalize="none" spellcheck="false" value="{{ old('username') }}" aria-describedby="username-error" required>
                        <span class="invalid-feedback" role="alert" id="username-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Masukkan password" autocomplete="current-password" aria-describedby="password-error" required>
                        <span class="invalid-feedback" role="alert" id="password-error"></span>
                    </div>
                    <button type="submit" id="submitBtn">
                        <span class="spinner" aria-hidden="true" hidden></span>
                        <span class="button-text" aria-live="polite">Masuk</span>
                    </button>
                </form>
                <div class="features" aria-label="Fungsi aplikasi">
                    <span>Persediaan material</span>
                    <span>Progres pembangunan</span>
                </div>
            </section>
            <footer>&copy; {{ date('Y') }} {{ $appBranding['name'] }}</footer>
        </div>
    </main>
    <script src="{{ asset('templates/plugins/jquery/jquery.min.js') }}"></script>
    <script>
        $(function () {
            const form = $('#formLogin');
            const submitBtn = $('#submitBtn');
            const alertBox = $('#login-alert');

            function setLoading(loading) {
                submitBtn.prop('disabled', loading);
                form.attr('aria-busy', loading ? 'true' : 'false');
                submitBtn.find('.spinner').prop('hidden', !loading);
                submitBtn.find('.button-text').text(loading ? 'Memproses...' : 'Masuk');
            }

            form.on('submit', function (event) {
                event.preventDefault();
                if (submitBtn.prop('disabled')) return;

                form.find('input').removeClass('is-invalid').removeAttr('aria-invalid');
                form.find('.invalid-feedback').empty();
                alertBox.prop('hidden', true).text('');
                setLoading(true);

                $.getJSON('{{ route('refresh.csrf') }}')
                    .done(function (data) {
                        $('meta[name="csrf-token"]').attr('content', data.token);
                        form.find('input[name="_token"]').val(data.token);

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: new FormData(form[0]),
                            headers: { 'X-CSRF-TOKEN': data.token, 'Accept': 'application/json' },
                            contentType: false,
                            processData: false
                        }).done(function () {
                            window.location.href = '{{ route('dashboard') }}';
                        }).fail(function (xhr) {
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function (key, messages) {
                                    if (key === 'username' || key === 'password') {
                                        $('#' + key).addClass('is-invalid').attr('aria-invalid', 'true');
                                        $('#' + key + '-error').text(messages[0]);
                                    }
                                });
                                form.find('.is-invalid').first().trigger('focus');
                            } else {
                                alertBox.text('Tidak dapat masuk. Periksa koneksi Anda dan coba lagi.').prop('hidden', false);
                            }
                        }).always(function () {
                            setLoading(false);
                        });
                    }).fail(function () {
                        alertBox.text('Tidak dapat menyiapkan sesi login. Silakan coba lagi.').prop('hidden', false);
                        setLoading(false);
                    });
            });
        });
    </script>
</body>
</html>
