<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion | {{ $ecole->v_nomecole }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <!-- Google Font (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-school: #2563eb; /* Couleur bleue institutionnelle harmonisée */
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        /* LE LOGO EST ICI EN BACKGROUND DIRECT DU BODY */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;

            /* Superposition d'un voile blanc doux + le logo de l'école centré */
            background: linear-gradient(rgba(248, 250, 252, 0.96), rgba(248, 250, 252, 0.96)),
                        url("{{ $ecole->logo ? asset($ecole->logo) : asset('assets/images/default-school.png') }}");
            background-size: auto 65vh; /* Taille ajustée pour occuper élégamment le centre */
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 35px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .header-box {
            text-align: center;
            margin-bottom: 32px;
        }

        .school-logo {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            object-fit: cover;
            background: #ffffff;
            padding: 4px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .school-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-group-text {
            background: transparent;
            border-right: none;
            border-color: #cbd5e1;
            color: var(--text-muted);
            padding-left: 16px;
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }

        .form-control {
            height: 50px;
            border-radius: 12px;
            border-color: #cbd5e1;
            font-size: 14.5px;
            color: var(--text-dark);
        }

        .form-control:focus {
            border-color: var(--primary-school);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .form-check-input:checked {
            background-color: var(--primary-school);
            border-color: var(--primary-school);
        }

        .form-check-label, .forgot-link {
            font-size: 13.5px;
        }

        .forgot-link {
            color: var(--text-muted);
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--primary-school);
        }

        .btn-login {
            height: 50px;
            border-radius: 12px;
            background-color: var(--primary-school);
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .bottom-text {
            color: var(--text-muted);
            font-size: 12.5px;
            margin-top: 30px;
        }

        @media(max-width: 480px) {
            .login-card {
                padding: 30px 20px;
                border: none;
                box-shadow: none;
                background: transparent;
            }
            body {
                background-color: #ffffff;
                background-size: auto 45vh;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">

        <div class="header-box">
            <!-- LOGO AU-DESSUS DU FORMULAIRE -->
            <img src="{{ $ecole->logo ? asset($ecole->logo) : asset('assets/images/default-school.png') }}"
                 alt="Logo"
                 class="school-logo">

            <h1 class="school-title">{{ $ecole->v_nomecole }}</h1>
            <p class="login-subtitle">Connectez-vous à votre espace scolaire.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-10 px-16 radius-12 small mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('ecole.do.login', $ecole->v_slugecole) }}">
            @csrf

            <!-- EMAIL -->
            <div class="mb-3">
                <label class="form-label">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ri-mail-line"></i>
                    </span>
                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="email@exemple.com"
                           required
                           value="{{ old('email') }}">
                </div>
                @error('email')
                    <div class="text-danger small mt-1">
                        <i class="ri-error-warning-line"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- PASSWORD -->
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ri-lock-password-line"></i>
                    </span>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="••••••••"
                           required>
                </div>
            </div>

            <!-- REMEMBER & FORGOT -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-secondary" for="remember">
                        Se souvenir de moi
                    </label>
                </div>
                <a href="#" class="text-decoration-none forgot-link">
                    Mot de passe oublié ?
                </a>
            </div>

            <!-- BUTTON -->
            <button type="submit" class="btn btn-login w-100">
                <i class="ri-login-circle-line"></i>
                Se connecter
            </button>

        </form>

        <div class="bottom-text text-center">
            © {{ date('Y') }} {{ $ecole->v_nomecole }} - Tous droits réservés.
        </div>

    </div>

</body>

<!-- SWEETALERT ERRORS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if($errors->any())
<script>
document.addEventListener("DOMContentLoaded", function () {
    @foreach($errors->all() as $error)
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: "{{ $error }}",
            showConfirmButton: false,
            timer: 4000
        });
    @endforeach
});
</script>
@endif

</html>
