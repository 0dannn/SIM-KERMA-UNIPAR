<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - SIMKERMA UNIPAR</title>
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('partial/images/favicon.ico') }}">

    <!-- Google Fonts: IBM Plex Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS (Minia) -->
    <link href="{{ asset('partial/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Icons CSS (Boxicons, Feather, FontAwesome) -->
    <link href="{{ asset('partial/css/icons.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --brand-primary: #5156be;
            --brand-primary-hover: #4549a2;
            --brand-primary-light: rgba(81, 86, 190, 0.08);
            --brand-navy: #2a3042;
            --bg-surface: #f4f5f8;
            --border-color: #e9e9ef;
        }
        body {
            font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-surface);
            color: #495057;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            max-width: 480px;
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }
        .login-header {
            background-color: var(--brand-navy);
            color: white;
            padding: 30px 24px;
            text-align: center;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 10px;
            background-color: var(--brand-primary);
            color: white;
            font-size: 1.6rem;
            margin-bottom: 14px;
            box-shadow: 0 4px 12px rgba(81, 86, 190, 0.4);
        }
        .btn-brand {
            background-color: var(--brand-primary);
            color: white;
            font-weight: 600;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .btn-brand:hover {
            background-color: var(--brand-primary-hover);
            color: white;
        }
        .quick-role-badge {
            cursor: pointer;
            border: 1px solid #e9e9ef;
            border-radius: 6px;
            padding: 9px 12px;
            background: #ffffff;
            transition: all 0.15s;
            text-align: left;
        }
        .quick-role-badge:hover {
            background: var(--brand-primary-light);
            border-color: #5156be;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="brand-badge">
            <i class="bi bi-shield-check"></i>
        </div>
        <h5 class="fw-bold mb-1 tracking-tight text-white">SIMKERMA UNIPAR</h5>
        <div class="small text-white-50">Sistem Informasi Dokumen Kerja Sama Berbasis FSM</div>
        <div class="badge bg-dark border border-secondary mt-2 px-2 py-1" style="font-size: 0.72rem;">
            Universitas Parna Raya Manado
        </div>
    </div>

    <div class="p-4 p-sm-4">
        @if (session('flash'))
            <div class="alert alert-{{ session('flash')['type'] ?? 'info' }} alert-dismissible fade show text-sm py-2 px-3 mb-3" role="alert">
                <i class="bi bi-info-circle me-1"></i> {{ session('flash')['msg'] ?? '' }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 mb-3 text-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label small fw-bold text-muted">Alamat Email Institusi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', 'admin@unipar.ac.id') }}" required autofocus placeholder="nama@unipar.ac.id">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-bold text-muted">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" id="password" name="password" value="admin123" required placeholder="Masukkan kata sandi">
                    <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat sesi saya
                    </label>
                </div>
                <span class="small text-muted fst-italic">Port 3306 MySQL</span>
            </div>

            <button type="submit" class="btn btn-brand w-100 shadow-sm mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Portal SIMKERMA
            </button>
        </form>

        <hr class="my-3 text-muted">

        <!-- PRESET AKUN PENGUJIAN & DEMO SIDANG -->
        <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-muted"><i class="bi bi-key-fill text-warning me-1"></i>Pilih Cepat Akun Demo:</span>
                <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Klik untuk isi</span>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <div class="quick-role-badge" onclick="fillCredential('admin@unipar.ac.id', 'admin123')">
                        <div class="fw-bold small text-danger"><i class="bi bi-star-fill me-1"></i>Admin / Master</div>
                        <div class="text-muted" style="font-size: 0.7rem;">admin@unipar.ac.id</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="quick-role-badge" onclick="fillCredential('unit@unipar.ac.id', 'unit123')">
                        <div class="fw-bold small text-primary"><i class="bi bi-person-fill me-1"></i>Unit Pengusul</div>
                        <div class="text-muted" style="font-size: 0.7rem;">unit@unipar.ac.id</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="quick-role-badge" onclick="fillCredential('legal@unipar.ac.id', 'legal123')">
                        <div class="fw-bold small text-warning"><i class="bi bi-journal-check me-1"></i>Tim Legal</div>
                        <div class="text-muted" style="font-size: 0.7rem;">legal@unipar.ac.id</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="quick-role-badge" onclick="fillCredential('rektor@unipar.ac.id', 'rektor123')">
                        <div class="fw-bold small text-success"><i class="bi bi-award-fill me-1"></i>Pimpinan / Rektor</div>
                        <div class="text-muted" style="font-size: 0.7rem;">rektor@unipar.ac.id</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('simkerma.index') }}" class="small text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>Lanjut Masuk Langsung (Mode Eksplorasi Cepat)
            </a>
        </div>
    </div>
</div>

<script>
    function fillCredential(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }

    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    toggleBtn.addEventListener('click', function () {
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
</script>

</body>
</html>
