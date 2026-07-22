<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">


<!-- Mirrored from edudash-php.theme.picode.in/index-4.php by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 03 May 2026 04:26:08 GMT -->
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
  <title>Edudash - School, College & LMS Admin Dashboard Template | PHP </title>
  <link rel="icon" type="image/png" href="{{asset('assets/images/logo.png')}}" sizes="16x16">
  <!-- remix icon font css  -->
  <!-- Remix Icon -->
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">

    <!-- BootStrap css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">

    <!-- Apex Chart css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}">

    <!-- Data Table css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}">

    <!-- Date picker css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}">

    <!-- Full Calendar css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}">

    <!-- Calendar css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/calendar.css') }}">

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h6 class="text-sm dark:text-white">Theme Settings</h6>
            <p class="text-xs mb-0 text-neutral-500 dark:text-neutral-200">Customize and preview instantly</p>
        </div>
        <button data-slot="button"
            class="theme-customization-sidebar__close text-neutral-900 bg-transparent text-hover-primary-600 d-flex text-xl">
            <i class="ri-close-fill"></i>
        </button>
    </div>

    <div class="d-flex flex-column gap-48 p-24 overflow-y-auto flex-grow-1">

        <div class="theme-setting-item">
            <h6 class="fw-medium text-primary-light text-md mb-3">Theme Mode</h6>
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
            <h6 class="fw-medium text-primary-light text-md mb-3">Page Direction</h6>
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
            <h6 class="fw-medium text-primary-light text-md mb-3">Color Schema</h6>
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
                    <span class="fw-medium mt-1" style="color: #dc2626;">Red</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="blue" aria-label="Blue">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #2563eb;"></span>
                    <span class="fw-medium mt-1" style="color: #2563eb;">Blue</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="yellow" aria-label="Yellow">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #ff9f29;"></span>
                    <span class="fw-medium mt-1" style="color: #ff9f29;">Yellow</span>
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
  <div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300">
  </div><aside class="sidebar">
  <button type="button" class="sidebar-close-btn">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div class="">
    <div class="sidebar-logo d-flex align-items-center justify-content-between">
      <a href="index-2.html" class="">
        <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="dark-logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="logo-icon">
      </a>
      <button type="button" class="text-xxl d-xl-flex d-none line-height-1 sidebar-toggle text-neutral-500"
        aria-label="Collapse Sidebar">
        <i class="ri-contract-left-line"></i>
      </button>
    </div>
  </div>
  <!-- User Info start -->
  <div class="mx-16 py-12">
    <div class="dropdown profile-dropdown">
      <button type="button"
        class="profile-dropdown__button d-flex align-items-center justify-content-between p-10 w-100 overflow-hidden bg-neutral-50 radius-12 "
        data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
        <span class="d-flex align-items-start gap-10">
          <img src="{{asset('assets/images/thumbs/leave-request-img2.png')}}" alt="Thumbnail"
            class="w-40-px h-40-px rounded-circle object-fit-cover flex-shrink-0">
          <span class="profile-dropdown__contents">
            <span class="h6 mb-0 text-md d-block text-primary-light">Jone Copper</span>
            <span class="text-secondary-light text-sm mb-0 d-block">Admin</span>
          </span>
        </span>
        <span class="profile-dropdown__icon pe-8 text-xl d-flex line-height-1">
          <i class="ri-arrow-right-s-line"></i>
        </span>
      </button>
      <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
        <li>
          <a href="student-details.html"
            class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
            <i class="ri-user-3-line"></i>
            My Profile
          </a>
        </li>
        <li>
          <a href="general.html"
            class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
            <i class="ri-settings-3-line"></i>
            Setting
          </a>
        </li>
        <li>
          <a href="login.html"
            class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
            <i class="ri-shut-down-line"></i>
            Log Out
          </a>
        </li>
      </ul>
    </div>
  </div>
  <!-- User Info end -->
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-home-4-line"></i>
          <span>Dashboard </span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="index-2.html">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              School
            </a>
          </li>
          <li>
            <a href="index-3.html">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Student
            </a>
          </li>
          <li>
            <a href="index-4.html">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Teacher
            </a>
          </li>
          <li>
            <a href="index-5.html">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Parent
            </a>
          </li>
          <li>
            <a href="index-6.html">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              LMS
            </a>
          </li>
        </ul>
      </li>


    <li>
        <a href="{{route('index.annee.scolaire')}}">
            <i class="ri-calendar-2-line"></i>
            <span>Année scolaire</span>
        </a>
    </li>

    <li>
        <a href="{{route('index.etablissement')}}">
            <i class="ri-building-2-line"></i>
            <span>Etablissement</span>
        </a>
    </li>

    <li>
        <a href="{{route('index.ecole')}}">
            <i class="ri-school-line"></i>
            <span>Ecoles</span>
        </a>
    </li>

    <li>
        <a href="{{route('index.contrat')}}">
            <i class="ri-file-text-line"></i>
            <span>Contrat</span>
        </a>
    </li>

    <li>
        <a href=" {{route('index.menus')}} ">
            <i class="ri-menu-add-line"></i>
            <span>Création de menu</span>
        </a>
    </li>

    <li>
        <a href="{{route('index.users')}}">
            <i class="ri-user-settings-line"></i>
            <span>Utilisateurs</span>
        </a>
    </li>



    </ul>
  </div>
</aside>




<main class="dashboard-main">
    <div class="navbar-header shadow-1">
  <div class="row align-items-center justify-content-between">
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-4">
        <button type="button" class="sidebar-mobile-toggle" aria-label="Sidebar Mobile Toggler Button">
          <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
        </button>
        <form class="navbar-search">
          <input type="text" class="bg-transparent" name="search" placeholder="Search">
          <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
        </form>
      </div>
    </div>
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-3">
        <button type="button" data-theme-toggle
          class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" aria-label="Dark & Light Mode Button"></button>
        <div class="dropdown d-inline-block">
          <button
            class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
            type="button" data-bs-toggle="dropdown" aria-label="Language Change Button">
            <img src="{{asset('assets/images/flags/flag1.png')}}" alt="image" class="w-24 h-24 object-fit-cover rounded-circle">
          </button>
          <div class="dropdown-menu to-top dropdown-menu-sm">
            <div
              class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
              <div>
                <h6 class="text-lg text-primary-light fw-semibold mb-0">Choose Your Language</h6>
              </div>
            </div>

            <div class="max-h-400-px overflow-y-auto scroll-sm pe-8">
              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="english">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="{{asset('assets/images/flags/flag1.png')}}" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">English</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="english">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="japan">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="assets/images/flags/flag2.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Japan</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="japan">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="france">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="assets/images/flags/flag3.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">France</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="france">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="germany">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="assets/images/flags/flag4.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Germany</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="germany">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="korea">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="assets/images/flags/flag5.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">South Korea</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="korea">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="bangladesh">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="assets/images/flags/flag6.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Bangladesh</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="bangladesh">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="india">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="assets/images/flags/flag7.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">India</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="india">
              </div>
              <div class="form-check style-check d-flex align-items-center justify-content-between">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="canada">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="assets/images/flags/flag8.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Canada</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="canada">
              </div>
            </div>
          </div>
        </div><!-- Language dropdown end -->

        <div class="dropdown">
          <button
            class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center position-relative"
            type="button" data-bs-toggle="dropdown" aria-label="Notification Button">
            <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
            <span class="w-8-px h-8-px bg-danger-600 position-absolute end-0 top-0 rounded-circle mt-2 me-2"></span>
          </button>
          <div class="dropdown-menu to-top dropdown-menu-lg p-0">
            <div
              class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
              <div>
                <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
              </div>
              <span
                class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
            </div>

            <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
              <a href="javascript:void(0)"
                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                  <span
                    class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                    <iconify-icon icon="bitcoin-icons:verify-outline" class="icon text-xxl"></iconify-icon>
                  </span>
                  <div>
                    <h6 class="text-md fw-semibold mb-4">Congratulations</h6>
                    <p class="mb-0 text-sm text-secondary-light text-w-200-px">Your profile has been Verified. Your
                      profile has been Verified</p>
                  </div>
                </div>
                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
              </a>

              <a href="javascript:void(0)"
                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                  <span
                    class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                    <img src="assets/images/notification/profile-1.png" alt="Image">
                  </span>
                  <div>
                    <h6 class="text-md fw-semibold mb-4">Ronald Richards</h6>
                    <p class="mb-0 text-sm text-secondary-light text-w-200-px">You can stitch between artboards</p>
                  </div>
                </div>
                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
              </a>

              <a href="javascript:void(0)"
                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                  <span
                    class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                    AM
                  </span>
                  <div>
                    <h6 class="text-md fw-semibold mb-4">Arlene McCoy</h6>
                    <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to prototyping</p>
                  </div>
                </div>
                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
              </a>

              <a href="javascript:void(0)"
                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between bg-neutral-50">
                <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                  <span
                    class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                    <img src="assets/images/notification/profile-2.png" alt="Image">
                  </span>
                  <div>
                    <h6 class="text-md fw-semibold mb-4">Robiul Hasan</h6>
                    <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to prototyping</p>
                  </div>
                </div>
                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
              </a>

              <a href="javascript:void(0)"
                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                  <span
                    class="w-44-px h-44-px bg-info-subtle text-info-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                    DR
                  </span>
                  <div>
                    <h6 class="text-md fw-semibold mb-4">Darlene Robertson</h6>
                    <p class="mb-0 text-sm text-secondary-light text-w-200-px">Invite you to prototyping</p>
                  </div>
                </div>
                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
              </a>
            </div>

            <div class="text-center py-12 px-16">
              <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md hover-underline">See All Notification</a>
            </div>

          </div>
        </div><!-- Notification dropdown end -->

      </div>
    </div>
  </div>
</div>
@yield('container')
<footer class="d-footer">
  <div class="">
    <p class="mb-0 text-center"> &copy;   2025 Made With ❤️ by sprit-tech.</p>
  </div>
</footer></main>

 <!-- jQuery library js -->
<script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>

<!-- Bootstrap js -->
<script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>

<!-- Apex Chart js -->
<script src="{{ asset('assets/js/lib/apexcharts.min.js') }}"></script>

<!-- Iconify Font js -->
<script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>

<!-- Data Table js -->
<script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>

<!-- jQuery UI js -->
<script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>

<!-- Main js -->
<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
    // ===================== Average Enrollment Rate Start ===============================
    function createChartTwo(chartId, color1, color2) {
        var options = {
            series: [{
                name: 'Free Course',
                data: [48, 35, 55, 32, 48, 30, 55, 50, 57]
            }, {
                name: 'Paid Course',
                data: [12, 20, 15, 26, 22, 60, 40, 48, 25]
            }],
            legend: {
                show: false
            },
            chart: {
                type: 'area',
                width: '100%',
                height: 210,
                toolbar: {
                    show: false
                },
                padding: {
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [color1, color2], // Use two colors for the lines
                lineCap: 'round'
            },
            grid: {
                show: true,
                borderColor: '#D1D5DB',
                strokeDashArray: 1,
                position: 'back',
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                row: {
                    colors: undefined,
                    opacity: 0.5
                },
                column: {
                    colors: undefined,
                    opacity: 0.5
                },
                padding: {
                    top: -20,
                    right: 0,
                    bottom: -10,
                    left: 0
                },
            },
            colors: [color1, color2],
            markers: {
                colors: [color1, color2], // Use two colors for the markers
                strokeWidth: 3,
                size: 0,
                hover: {
                    size: 10
                }
            },
            xaxis: {
                labels: {
                    show: false
                },
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                tooltip: {
                    enabled: false
                },
                labels: {
                    formatter: function(value) {
                        return value;
                    },
                    style: {
                        fontSize: "14px"
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: "14px"
                    }
                },
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
        chart.render();
    }

    createChartTwo('courseActivityChart', '#487FFF', '#FF9F29');
    // ===================== Average Enrollment Rate End ===============================

    //============================= ✅ Data Table start =============================
    let table = new DataTable('#dataTable');
    $('.data-table').each(function() {
        const $table = $(this);
        const tableInstance = new DataTable(this);

        // Handle search input (inside same wrapper)
        $table.closest('.dataTable-wrapper').find('.dt-search .dt-input').on('keyup', function() {
            tableInstance.search(this.value).draw();
        });

        // Handle page length change (inside same wrapper)
        $table.closest('.dataTable-wrapper').find('.dt-length .dt-input').on('change', function() {
            const value = $(this).val();
            tableInstance.page.len(value).draw();
        });
    });
    //============================= ✅ Data Table end =============================
</script>

</body>


<!-- Mirrored from edudash-php.theme.picode.in/index-4.php by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 03 May 2026 04:26:09 GMT -->
</html>
