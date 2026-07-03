
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Connexion | {{ $ecole->v_nomecole }}
    </title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1100px;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        }

        .left-side {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .left-side::before {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            top: -120px;
            right: -120px;
        }

        .left-side::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -80px;
            left: -80px;
        }

        .school-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            background: white;
            padding: 6px;
            margin-bottom: 24px;
            border: 4px solid rgba(255,255,255,0.2);
        }

        .school-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .school-text {
            font-size: 15px;
            opacity: 0.9;
            line-height: 1.8;
        }

        .right-side {
            padding: 60px;
            background: #fff;
        }

        .login-title {
            font-size: 30px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #64748b;
            margin-bottom: 35px;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-control {
            height: 55px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            padding-left: 18px;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,0.15);
        }

        .input-group-text {
            border-radius: 14px;
            background: white;
            border: 1px solid #cbd5e1;
        }

        .btn-login {
            height: 55px;
            border-radius: 14px;
            background: #2563eb;
            border: none;
            font-weight: 600;
            font-size: 16px;
            transition: 0.3s ease;
        }

        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .bottom-text {
            color: #94a3b8;
            font-size: 14px;
            margin-top: 24px;
        }

        @media(max-width: 992px) {

            .left-side,
            .right-side {
                padding: 40px;
            }

        }
        @media(max-width: 768px) {

            .left-side {
                display: none;
            }

            .right-side {
                padding: 35px 25px;
            }

            .login-title {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="row g-0">

            <!-- LEFT -->
            <div class="col-lg-6 left-side d-flex flex-column justify-content-center">

                @if($ecole->logo)

                    <img
                        src="{{ asset($ecole->logo) }}"
                        alt="Logo école"
                        class="school-logo">

                @else

                    <img
                        src="{{ asset('assets/images/default-school.png') }}"
                        alt="Logo école"
                        class="school-logo">

                @endif

                <h1 class="school-title">
                    {{ $ecole->v_nomecole }}
                </h1>

                <p class="school-text">
                    Bienvenue sur votre plateforme scolaire numérique.
                    Accédez à votre espace sécurisé pour gérer les élèves,
                    les enseignants, les notes, les paiements et toutes les
                    activités administratives de votre établissement.
                </p>

            </div>

            <!-- RIGHT -->
            <div class="col-lg-6 right-side d-flex flex-column justify-content-center">

                <h2 class="login-title">
                    Connexion
                </h2>

                <p class="login-subtitle">
                    Connectez-vous à votre espace scolaire.
                </p>

                @if(session('success'))
                    <div class="alert alert-success radius-12">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('ecole.do.login', $ecole->v_slugecole) }}">
                    @csrf

                    <!-- EMAIL -->
                    <div class="mb-20">

                        <label class="form-label">
                            Adresse email
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="ri-mail-line"></i>
                            </span>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="email@exemple.com"
                                required>
                                @error('email')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                                @enderror
                        </div>

                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-20">

                        <label class="form-label">
                            Mot de passe
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="ri-lock-password-line"></i>
                            </span>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="********"
                                required>

                        </div>

                    </div>

                    <!-- REMEMBER -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="remember">

                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>

                        </div>

                        <a href="#" class="text-decoration-none fw-semibold">
                            Mot de passe oublié ?
                        </a>

                    </div>

                    <!-- BUTTON -->
                    <button type="submit" class="btn btn-primary btn-login w-100">

                        <i class="ri-login-circle-line"></i>

                        Se connecter

                    </button>

                </form>

                <div class="bottom-text text-center">
                    © {{ date('Y') }} {{ $ecole->v_nomecole }} - Tous droits réservés.
                </div>

            </div>

        </div>

    </div>

</body>
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


