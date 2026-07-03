<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">


<!-- Mirrored from edudash-php.theme.picode.in/login.php by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 03 May 2026 04:25:52 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description"
    content="Modern Education Admin Dashboard for schools, colleges, universities, and eLearning platforms. Includes student and course management, attendance, exams, payments, analytics, and a fully responsive clean UI—ideal for LMS, coaching centers, and academic admin systems.">
  <meta name="keywords"
    content="Education Admin Dashboard, School Admin Panel, College Dashboard, University Dashboard, LMS Dashboard, eLearning Admin Template, Student Management System, Course Management, Education Template, Study Dashboard, Online Learning Dashboard, Academic Admin Panel, Bootstrap Dashboard, React Education Dashboard, Next.js Education Template">
  <meta name="robots" content="INDEX,FOLLOW">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Title -->
  <title>Spint-shool | connexion </title>
  <link rel="icon" type="image/png" href="assets/images/favicon.png" sizes="16x16">
  <!-- remix icon font css  -->
  <link rel="stylesheet" href="assets/css/remixicon.css">
  <!-- BootStrap css -->
  <link rel="stylesheet" href="assets/css/lib/bootstrap.min.css">
  <!-- Apex Chart css -->
  <link rel="stylesheet" href="assets/css/lib/apexcharts.css">
  <!-- Data Table css -->
  <link rel="stylesheet" href="assets/css/lib/dataTables.min.css">
  <!-- Date picker css -->
  <link rel="stylesheet" href="assets/css/lib/flatpickr.min.css">
  <!-- Calendar css -->
  <link rel="stylesheet" href="assets/css/lib/full-calendar.css">
  <!-- calendar -->
  <link rel="stylesheet" href="assets/css/lib/calendar.css">
  <!-- main css -->
  <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

  <!-- Theme Customization Structure Start -->
<div class="body-overlay"></div>

<button type="button"
    class="theme-customization__button w-48-px h-48-px bg-primary-600 text-white rounded-circle d-flex justify-content-center align-items-center position-fixed end-0 bottom-0 mb-40 me-40 text-2xxl bg-hover-primary-700" aria-label="Theme Customization Button">
    <i class="ri-settings-3-line animate-spin"></i>
</button>
<div class="theme-customization-sidebar w-100 bg-base h-100vh overflow-y-auto position-fixed end-0 top-0">
    <div class="d-flex align-items-center gap-3 py-16 px-24 justify-content-between border-bottom">
        <div>
            <h6 class="text-sm dark:text-white">Paramètres du thème</h6>
            <p class="text-xs mb-0 text-neutral-500 dark:text-neutral-200">Customize and preview instantly</p>
        </div>
        <button data-slot="button"
            class="theme-customization-sidebar__close text-neutral-900 bg-transparent text-hover-primary-600 d-flex text-xl">
            <i class="ri-close-fill"></i>
        </button>
    </div>

    <div class="d-flex flex-column gap-48 p-24 overflow-y-auto flex-grow-1">

        <div class="theme-setting-item">
            <h6 class="fw-medium text-primary-light text-md mb-3">Mode Thème</h6>
            <div class="d-grid grid-cols-3 gap-3 dark-light-mode">
                <button type="button"
                    class="theme-btn theme-setting-item__btn d-flex align-items-center justify-content-center h-64-px rounded-3 text-xl active"
                    data-theme="light" aria-label="light">
                    <i class="ri-sun-line"></i>
                </button>
                <button type="button"
                    class="theme-btn theme-setting-item__btn d-flex align-items-center justify-content-center h-64-px rounded-3 text-xl"
                    data-theme="dark" aria-label="dark">
                    <i class="ri-moon-line"></i>
                </button>
                <button type="button"
                    class="theme-btn theme-setting-item__btn d-flex align-items-center justify-content-center h-64-px rounded-3 text-xl"
                    data-theme="system" aria-label="system">
                    <i class="ri-computer-line"></i>
                </button>
            </div>
        </div>

        <div class="theme-setting-item">
            <h6 class="fw-medium text-primary-light text-md mb-3">Orientation des pages</h6>
            <div class="d-grid grid-cols-2 gap-3">
                <button type="button"
                    class="theme-setting-item__btn ltr-mode-btn d-flex align-items-center justify-content-center gap-2 h-56-px rounded-3 text-xl" aria-label="LTR">
                    <span><i class="ri-align-item-left-line"></i></span>
                    <span class="h6 text-sm font-medium mb-0">LTR</span>
                </button>

                <button type="button"
                    class="theme-setting-item__btn rtl-mode-btn d-flex align-items-center justify-content-center gap-2 h-56-px rounded-3 text-xl" aria-label="RTL">
                    <span class="h6 text-sm font-medium mb-0">RTL</span>
                    <span><i class="ri-align-item-right-line"></i></span>
                </button>
            </div>
        </div>

        <div class="theme-setting-item">
            <h6 class="fw-medium text-primary-light text-md mb-3">Schéma de couleurs</h6>
            <div class="d-grid grid-cols-3 gap-3">
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="base" aria-label="Base">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #25A194;"></span>
                    <span class="fw-medium mt-1" style="color: #25A194;">Base</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="red" aria-label="Red">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #dc2626;"></span>
                    <span class="fw-medium mt-1" style="color: #dc2626;">Rouge</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="blue" aria-label="Blue">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #2563eb;"></span>
                    <span class="fw-medium mt-1" style="color: #2563eb;">Bleue</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="yellow" aria-label="Yellow">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #ff9f29;"></span>
                    <span class="fw-medium mt-1" style="color: #ff9f29;">Jaune</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="cyan" aria-label="Cyan">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #00b8f2;"></span>
                    <span class="fw-medium mt-1" style="color: #00b8f2;">Cyan</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="violet" aria-label="Violet">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #7c3aed;"></span>
                    <span class="fw-medium mt-1" style="color: #7c3aed;">Violet</span>
                </button>
            </div>
        </div>

    </div>
</div>
<!-- Theme Customization Structure End -->
<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300"></div>
<div class="d-lg-flex bg-white">
    <div class="w-50 d-lg-flex d-none overflow-hidden">
        <img src="assets/images/thumbs/login-img.png" alt="Login Image" class="w-100 h-100 object-fit-cover">
    </div>
    <div class="lg-w-50 px-24 py-32 d-flex justify-content-center align-items-center">
        <div class="mx-auto" style="max-width: 800px; width: 80%;">
            <a href="{{route('login')}}" class="d-block mb-12">
                <img src="assets/images/logo.png" alt="Logo">
            </a>
            <div class="mb-30">
                <h1 class="h6 fw-bold text-primary-light mb-8 text-center">
                    Content de te revoir 👋
                </h1>
                <p class="text-sm text-secondary-light mb-0 text-center">
                    Connectez-vous à votre compte pour continuer
                </p>
            </div>


            <form method="POST" autocomplete="off" action="{{ route('auth.login') }}" class="d-flex flex-column gap-32 submit-form">
                @csrf
                <div class="d-flex flex-column gap-16">
                    <div>
                        <label for="email" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                            Adresse email
                            <span class="text-danger-600">*</span>
                        </label>
                        <input  name="email" value="{{ old('email') }}" type="email" id="email" class="email-field form-control @error('email') is-invalid @enderror" placeholder="Entrez votre email">
                        @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                            Mot de passe
                            <span class="text-danger-600">*</span>
                        </label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" class="password-field form-control @error('password') is-invalid @enderror" placeholder="Entrez votre mot de passe">
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                            <button type="button"
                                class="toggle-password btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light cursor-pointer ri-eye-line"
                                data-toggle="#password" aria-label="Toggle password visibility">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between gap-2">
                    <div class="form-check style-check d-flex align-items-center">
                        <input class="form-check-input border border-neutral-400" type="checkbox" value="" id="remeber">
                        <label class="form-check-label text-small" for="remeber">Souviens-toi de moi </label>
                    </div>
                    <a href="javascript:void(0)" class="text-primary-600 fw-medium text-decoration-underline">Mot de passe oublié ?</a>
                </div>
                <div class="">
                    <button type="submit" class="loginBtn btn btn-primary-600 text-sm btn-sm px-12 py-16 w-100 radius-8"> Se connecter </button>
                </div>
            </form>
        </div>
    </div>
</div>

  <!-- jQuery library js -->
  <script src="assets/js/lib/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap js -->
  <script src="assets/js/lib/bootstrap.bundle.min.js"></script>
  <!-- Apex Chart js -->
  <script src="assets/js/lib/apexcharts.min.js"></script>
  <!-- Iconify Font js -->
  <script src="assets/js/lib/iconify-icon.min.js"></script>
  <!-- Data Table js -->
  <script src="assets/js/lib/dataTables.min.js"></script>

  <!-- jQuery UI js -->
  <script src="assets/js/lib/jquery-ui.min.js"></script>

  <!-- main js -->
  <script src="assets/js/app.js"></script>
</body>


<!-- Mirrored from edudash-php.theme.picode.in/login.php by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 03 May 2026 04:25:59 GMT -->
</html>
