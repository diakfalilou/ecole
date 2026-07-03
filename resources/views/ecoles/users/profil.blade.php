@extends('ecoles.layout.app')
@section('containte')
@php
    $user = Auth::user();
@endphp
<div class="dashboard-main-body">

        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div class="">
                <h1 class="fw-semibold mb-4 h6 text-primary-light">votre profil</h1>
                <div class="">
                    <a href="{{route('ecole.dashboard', session('slug_ecole'))}}" class="text-secondary-light hover-text-primary hover-underline">Acceuille </a>

                    <span class="text-secondary-light">/ Mon profil</span>
                </div>
            </div>
            <button type="button"
                class="my-sidebar-btn btn btn-primary-600 d-flex align-items-center gap-6 bg-base text-primary-light bg-hover-primary-600">
                <span class="d-flex text-md">
                    <i class="ri-lock-2-line"></i>
                </span>
                Login Details
            </button>
        </div>

        <div class="mt-24">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex gap-32 flex-md-row flex-column">
                        <div class="max-w-300-px w-100 text-center">
                            <figure class="mb-24 w-120-px h-120-px mx-auto rounded-circle overflow-hidden">
                                <img src="{{ asset($user->logo ?? 'assets/images/thumbs/leave-request-img2.png') }}" alt="Student Image" class="w-100 h-100 object-fit-cover">
                            </figure>
                            <h2 class="h6 text-primary-light mb-16 fw-semibold"> {{ $user->name }}</h2>
                            <p class="mb-0">E-mail : <span class="text-primary-600 fw-semibold">{{ $user->email  }}</span>
                            </p>
                            <p class="mb-0">Téléphone : <span class="text-primary-light fw-semibold">{{ $user->telephone }}</span> </p>
                            <div class="mt-32 d-flex gap-16 w-100">
                                <button disabled type="button"
                                    class="btn border fw-medium border-danger-600 bg-hover-danger-200 text-danger-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8"
                                    data-bs-toggle="modal" data-bs-target="#exampleModalDelete">
                                    <span class="d-flex text-lg">
                                        <i class="ri-delete-bin-2-line"></i>
                                    </span>
                                    suspedre
                                </button>
                                <button href="#!" disabled
                                    class="btn btn-primary-600 border fw-medium border-primary-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8">
                                    <span class="d-flex text-lg">
                                        <i class="ri-edit-line"></i>
                                    </span>
                                    Modifie
                                </button>
                            </div>
                        </div>
                        <div class="">
                            <span class="h-100 w-1-px bg-neutral-200"></span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="pb-16 border-bottom d-flex align-items-center justify-content-between gap-20">
                                <h3 class="h6 text-primary-light text-lg mb-0 fw-semibold">Informations personnelles</h3>
                                <span
                                    class="bg-success-100 text-success-600 px-16 py-4 radius-4 fw-medium text-sm">Active</span>
                            </div>

                            <div class="mt-16 d-flex flex-column gap-8">
                                <div class="d-flex gap-4">
                                    <span class="fw-semibold text-sm text-primary-light w-110-px">Nom & prénom</span>
                                    <span class="fw-normal text-sm text-secondary-light">: <input value="{{$user->name }}" > </span>
                                </div>
                                <div class="d-flex gap-4">
                                    <span class="fw-semibold text-sm text-primary-light w-110-px">E-mail</span>
                                    <span class="fw-normal text-sm text-secondary-light">: {{$user->email}} </span>
                                </div>
                                <div class="d-flex gap-4">
                                    <span class="fw-semibold text-sm text-primary-light w-110-px">Geare</span>
                                    <span class="fw-normal text-sm text-secondary-light">:
                                    <select  name="geare" id="geare">
                                        <option value="{{ $user->geare ?: '-' }}">
                                            {{ $user->geare ?: '-' }}
                                        </option>
                                        <option value="Homme">Homme</option>
                                        <option value="Femme">Femme</option>
                                        <option value="Autres">Autres</option>
                                    </select>
                                    </span>
                                </div>

                                <div class="d-flex gap-4">
                                    <span class="fw-semibold text-sm text-primary-light w-110-px">Dt. naissance</span>
                                    <span class="fw-normal text-sm text-secondary-light">: {{$user->datenaissance}} </span>
                                </div>
                                <div class="d-flex gap-4">
                                    <span class="fw-semibold text-sm text-primary-light w-110-px">Poste/Fonction</span>
                                    <span class="fw-normal text-sm text-secondary-light">: {{$user->poste}}</span>
                                </div>
                                <div class="d-flex gap-4">
                                    <span class="fw-semibold text-sm text-primary-light w-110-px">Dt. embauche</span>
                                    <span class="fw-normal text-sm text-secondary-light">: {{ $user->dateembauche }} </span>
                                </div>
                                <div class="d-flex gap-4">
                                    <span class="fw-semibold text-sm text-primary-light w-110-px">N° téléphone</span>
                                    <span class="fw-normal text-sm text-primary-600">: {{$user->telephone}} </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-16">
                <ul class="nav nav-pills bordered-tab mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm text-hover-primary-600 text-capitalize bg-transparent px-20 py-12  active"
                            id="pills-studentDetails-tab" data-bs-toggle="pill" data-bs-target="#pills-studentDetails"
                            type="button" role="tab" aria-controls="pills-studentDetails" aria-selected="true">
                            <span class="d-flex tab-icon line-height-1 text-md">
                                <i class="ri-group-line"></i>
                            </span>
                            Modifié vos informations
                        </button>
                    </li>
                    {{-- <li class="nav-item" role="presentation">
                        <button
                            class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm text-hover-primary-600 text-capitalize bg-transparent px-20 py-12 "
                            id="pills-attendance-tab" data-bs-toggle="pill" data-bs-target="#pills-attendance"
                            type="button" role="tab" aria-controls="pills-attendance" aria-selected="false">
                            <span class="d-flex tab-icon line-height-1 text-md">
                                   <i class="ri-calendar-check-line"></i>
                            </span>
                            Attendance
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm text-hover-primary-600 text-capitalize bg-transparent px-20 py-12 "
                            id="pills-leave-tab" data-bs-toggle="pill" data-bs-target="#pills-leave" type="button"
                            role="tab" aria-controls="pills-leave" aria-selected="false">
                            <span class="d-flex tab-icon line-height-1 text-md">
                                    <i class="ri-login-box-line"></i>
                            </span>
                            Leave
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm text-hover-primary-600 text-capitalize bg-transparent px-20 py-12 "
                            id="pills-fees-tab" data-bs-toggle="pill" data-bs-target="#pills-fees" type="button"
                            role="tab" aria-controls="pills-fees" aria-selected="false">
                            <span class="d-flex tab-icon line-height-1 text-md">
                                 <i class="ri-money-dollar-box-line"></i>
                            </span>
                            Fees
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm text-hover-primary-600 text-capitalize bg-transparent px-20 py-12 "
                            id="pills-exam-tab" data-bs-toggle="pill" data-bs-target="#pills-exam" type="button"
                            role="tab" aria-controls="pills-exam" aria-selected="false">
                            <span class="d-flex tab-icon line-height-1 text-md">
                                 <i class="ri-file-edit-line"></i>
                            </span>
                            exam
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm text-hover-primary-600 text-capitalize bg-transparent px-20 py-12 "
                            id="pills-library-tab" data-bs-toggle="pill" data-bs-target="#pills-library" type="button"
                            role="tab" aria-controls="pills-library" aria-selected="false">
                            <span class="d-flex tab-icon line-height-1 text-md">
                                <i class="ri-book-line"></i>
                            </span>
                            library
                        </button>
                    </li> --}}
                </ul>


                <div class="tab-content" id="pills-tabContent">

                    <!-- Student Details tab start -->
                    <div class="tab-pane fade show active" id="pills-studentDetails" role="tabpanel"
                        aria-labelledby="pills-studentDetails-tab" tabindex="0">
                        <div class="row gy-4">
                            <div class="col-12">
                                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                                    <div
                                        class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                                        <h6 class="text-lg fw-semibold mb-0">Photo & mot de passe</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <form id="monFormulaire"
                                            action="{{ route('user.profil.update', ['slug' => $slug]) }}"
                                            method="POST"
                                            enctype="multipart/form-data">

                                            @csrf
                                            <div class="bg-hover-neutral-50 p-20">
                                                <div class="row g-4">

                                                    <!-- Photo -->
                                                    <div class="col-md-3">
                                                        <label class="form-label">Charger une photo</label>
                                                        <input
                                                            name="photo"
                                                            id="photo"
                                                            class="form-control"
                                                            type="file"
                                                            accept="image/*"
                                                        >

                                                        <!-- Aperçu de l'image -->
                                                        <div class="mt-3">
                                                            <img
                                                                id="previewPhoto"
                                                                src=""
                                                                alt="Aperçu"
                                                                style="display:none; max-width:200px; max-height:200px;"
                                                                class="img-thumbnail"
                                                            >
                                                        </div>
                                                    </div>

                                                   <!-- Ancien mot de passe -->
                                                    <div class="col-md-3">
                                                        <label class="form-label">Ancien mot de passe</label>

                                                        <div class="input-group">
                                                            <input name="old_password" id="old_password"
                                                                class="form-control" type="password"
                                                                placeholder="Entrez votre ancien mot de passe">

                                                            <button class="btn btn-outline-secondary" type="button"
                                                                    onclick="togglePassword('old_password')">
                                                                👁
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Nouveau mot de passe -->
                                                    <div class="col-md-3">
                                                        <label class="form-label">Nouveau mot de passe</label>

                                                        <div class="input-group">
                                                            <input name="new_password" id="new_password"
                                                                class="form-control" type="password"
                                                                placeholder="Entrez votre nouveau mot de passe">

                                                            <button class="btn btn-outline-secondary" type="button"
                                                                    onclick="togglePassword('new_password')">
                                                                👁
                                                            </button>
                                                        </div>

                                                        <!-- FORCE PASSWORD -->
                                                        <small id="passwordStrengthText" class="fw-bold"></small>
                                                    </div>

                                                    <!-- Confirmation -->
                                                    <div class="col-md-3">
                                                        <label class="form-label">Confirmer le mot de passe</label>

                                                        <div class="input-group">
                                                            <input name="confirm_password" id="confirm_password"
                                                                class="form-control" type="password"
                                                                placeholder="Confirmez votre nouveau mot de passe">

                                                            <button class="btn btn-outline-secondary" type="button"
                                                                    onclick="togglePassword('confirm_password')">
                                                                👁
                                                            </button>
                                                        </div>

                                                        <!-- MATCH STATUS -->
                                                        <small id="matchText" class="fw-bold"></small>
                                                    </div>

                                                </div>

                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-end gap-2">

                                                            <button type="button" class="btn btn-secondary" onclick="confirmerAnnulation()">
                                                                Annuler
                                                            </button>

                                                            <button type="submit" id="btnSubmit" class="btn btn-primary">
                                                                <span id="btnText">Enregistrer</span>

                                                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                            </button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <script>
                                        document.getElementById('photo').addEventListener('change', function(e) {
                                            const file = e.target.files[0];

                                            if (file) {
                                                const reader = new FileReader();

                                                reader.onload = function(event) {
                                                    const preview = document.getElementById('previewPhoto');
                                                    preview.src = event.target.result;
                                                    preview.style.display = 'block';
                                                };

                                                reader.readAsDataURL(file);
                                            }
                                        });
                                        </script>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                    <!-- Student Details tab end -->

                    <!-- Attendance tab start -->
                    <div class="tab-pane fade" id="pills-attendance" role="tabpanel"
                        aria-labelledby="pills-attendance-tab" tabindex="0">
                        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                            <div
                                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                                <h6 class="text-lg fw-semibold mb-0">Attendance</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="px-20 pt-20">
                                    <div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 g-3">
                                        <div class="col">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-7">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">227</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Present</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-success-600 text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/attendence-icon1.png"
                                                                alt="Present Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-8">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">70</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Absent</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-danger-600 text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/attendence-icon2.png"
                                                                alt="Absent Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-9">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">27</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Half
                                                                Day</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-purple-600 text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/attendence-icon3.png"
                                                                alt="Calendar Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-10">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">28</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Late</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-info-600 text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/attendence-icon4.png"
                                                                alt="Clock Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-11">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">12</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Holiday</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-orange text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/attendence-icon5.png"
                                                                alt="Holiday Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-24 mb-16 mx-20">
                                    <div
                                        class="d-flex flex-wrap align-items-center gap-24 justify-content-between flex-wrap">
                                        <div class="d-flex flex-wrap align-items-center gap-16 ">
                                            <div class="">
                                                <select class="form-control form-select">
                                                    <option value="Jun 2025/2026">Jun 2025/2026</option>
                                                    <option value="Jun 2026/2027">Jun 2026/2027</option>
                                                    <option value="Jun 2027/2028">Jun 2027/2028</option>
                                                    <option value="Jun 2028/2029">Jun 2028/2029</option>
                                                </select>
                                            </div>
                                            <div class="dropdown">
                                                <button type="button"
                                                    class="px-12 py-8 border border-neutral-300 radius-8 d-flex align-items-center gap-20"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span
                                                        class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                                                        <i class="ri-file-upload-line text-md line-height-1"></i>
                                                        Export
                                                    </span>
                                                    <span class="">
                                                        <i class="ri-arrow-down-s-line"></i>
                                                    </span>
                                                </button>
                                                <ul class="dropdown-menu p-12 border bg-base shadow">
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                                            data-bs-toggle="modal" data-bs-target="#exampleModalView">
                                                            <i class="ri-file-3-line"></i>
                                                            PDF
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                                            data-bs-toggle="modal" data-bs-target="#exampleModalEdit">
                                                            <i class="ri-file-excel-line"></i>
                                                            Excel
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap gap-8">
                                            <p class="text-primary-light text-sm fw-medium mb-0">
                                                Present:
                                                <span class="fw-semibold text-success-600">P </span>
                                            </p>
                                            <p class="text-primary-light text-sm fw-medium mb-0">
                                                Absent:
                                                <span class="fw-semibold text-danger-600">A </span>
                                            </p>
                                            <p class="text-primary-light text-sm fw-medium mb-0">
                                                Holiday:
                                                <span class="fw-semibold text-warning-600">H </span>
                                            </p>
                                            <p class="text-primary-light text-sm fw-medium mb-0">
                                                Late:
                                                <span class="fw-semibold text-info-600">L </span>
                                            </p>
                                            <p class="text-primary-light text-sm fw-medium mb-0">
                                                Half Day:
                                                <span class="fw-semibold text-purple-600">F </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive overflow-x-auto">
                                    <table class="table mb-0 table-heading-dark-mode">
                                        <thead>
                                            <tr>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">Month
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">1</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">2</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">3</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">4</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">5</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">6</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">7</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">8</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">9</th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">10
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">11
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">12
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">13
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">14
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">15
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">15
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">16
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">17
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">18
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">19
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">20
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">21
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">22
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">23
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">24
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">25
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">26
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">27
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">28
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">29
                                                </th>
                                                <th class="bg-neutral-100 text-sm text-primary-light px-10 py-16">30
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Jan</td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">H</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">A</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">F</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">L</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">H</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">A</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">L</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">h</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">F</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">H</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">P</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">A</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">H</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">p</span>
                                                </td>
                                                <td class="px-10 py-14 text-sm text-uppercase">
                                                    <span class="attendance">p</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Feb</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Mar</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Apr</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">May</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">May</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">F</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">L</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">H</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">P</span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance">A</span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Jun</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Ju</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Aug</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Sep</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Oct</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Nov</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="px-10 py-16 text-sm">Dec</td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                                <td class="px-10 py-16 text-sm"><span class="attendance"></span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Attendance tab end -->

                    <!-- leaves tab start -->
                    <div class="tab-pane fade" id="pills-leave" role="tabpanel" aria-labelledby="pills-leave-tab"
                        tabindex="0">
                        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                            <div
                                class="card-header border-bottom bg-base py-10 px-20 d-flex align-items-center justify-content-between">
                                <h6 class="text-lg fw-semibold mb-0">Leave </h6>
                                <button type="button"
                                    class="apply-leave-btn btn btn-primary-600 d-flex align-items-center gap-6 py-8 text-sm">
                                    <span class="d-flex text-sm">
                                        <i class="ri-calendar-close-line"></i>
                                    </span>
                                    Apply Leave
                                </button>
                            </div>
                            <div class="card-body p-0 dataTable-wrapper">
                                <div
                                    class="d-flex flex-wrap align-items-center gap-24 justify-content-between px-20 py-12">
                                    <div class="d-flex flex-wrap align-items-center gap-16">
                                        <form class="navbar-search dt-search m-0">
                                            <input type="text" class="dt-input bg-transparent radius-4"
                                                aria-controls="dataTable" name="search" placeholder="Search...">
                                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                                        </form>
                                        <div class="">
                                            <select class="form-control form-select">
                                                <option value="Year 2025/2026">Year 2025/2026</option>
                                                <option value="Year 2026/2027">Year 2026/2027</option>
                                                <option value="Year 2027/2028">Year 2027/2028</option>
                                                <option value="Year 2028/2029">Year 2028/2029</option>
                                            </select>
                                        </div>
                                        <div class="dropdown">
                                            <button type="button"
                                                class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20 "
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span
                                                    class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                                                    <i class="ri-file-upload-line text-md line-height-1"></i>
                                                    Export
                                                </span>
                                                <span class="">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu p-12 border bg-base shadow">
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                                        data-bs-toggle="modal" data-bs-target="#exampleModalView">
                                                        <i class="ri-file-3-line"></i>
                                                        PDF
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                                        data-bs-toggle="modal" data-bs-target="#exampleModalEdit">
                                                        <i class="ri-file-excel-line"></i>
                                                        Excel
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-8 text-secondary-light">
                                        <span class="">
                                            Rows per page:
                                        </span>
                                        <div class="dt-length">
                                            <select name="dataTable_length" aria-controls="dataTable"
                                                class="dt-input form-control form-select">
                                                <option value="5">5</option>
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <table class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                    id="dataTable" data-page-length='10'>
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        S.L
                                                    </label>
                                                </div>
                                            </th>
                                            <th scope="col">Leave Type</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Duration</th>
                                            <th scope="col">Apply Date</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        01
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>1</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Approved</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        02
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Special Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>3</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-warning-100 text-warning-600 px-20 py-4 radius-4 fw-medium text-sm">Pending</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        03
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>5</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Approved</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        04
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Casual Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>6</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-warning-100 text-warning-600 px-20 py-4 radius-4 fw-medium text-sm">Pending</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        05
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>1</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Approved</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        06
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Special Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>2</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-danger-100 text-danger-600 px-20 py-4 radius-4 fw-medium text-sm">Rejected</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        07
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>5</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Approved</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        08
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Casual Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>6</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-danger-100 text-danger-600 px-20 py-4 radius-4 fw-medium text-sm">Rejected</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        09
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Medical Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>1</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Approved</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        10
                                                    </label>
                                                </div>
                                            </td>
                                            <td>Special Leave</td>
                                            <td>07 May 2025 - 08 may 2025</td>
                                            <td>2</td>
                                            <td>07 May 2025 </td>
                                            <td>
                                                <span
                                                    class="bg-danger-100 text-danger-600 px-20 py-4 radius-4 fw-medium text-sm">Rejected</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- leaves tab start -->

                    <!-- Fees tab start -->
                    <div class="tab-pane fade" id="pills-fees" role="tabpanel" aria-labelledby="pills-fees-tab"
                        tabindex="0">
                        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                            <div
                                class="card-header border-bottom bg-base py-10 px-20 d-flex align-items-center justify-content-between">
                                <h6 class="text-lg fw-semibold mb-0">Fees </h6>
                                <button type="button"
                                    class="collect-fees-btn btn btn-primary-600 d-flex align-items-center gap-6 py-8 text-sm">
                                    <span class="d-flex text-sm">
                                        <i class="ri-calendar-close-line"></i>
                                    </span>
                                    Collect Fees
                                </button>
                            </div>
                            <div class="card-body p-0 dataTable-wrapper">
                                <div class="p-20">
                                    <div class="row g-3">
                                        <div class="col-xl-3 col-sm-6">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-10">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">$10,500</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Amount</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-info-600 text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/fees-icon1.png"
                                                                alt="Clock Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-8">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">$200</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Fine</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-danger-600 text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/fees-icon2.png"
                                                                alt="Absent Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-7">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">$7,500</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Paid </span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-success-600 text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/fees-icon3.png"
                                                                alt="Present Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6">
                                            <div
                                                class="card px-20 py-28 shadow-2 radius-8 h-100 border border-neutral-200 shadow-none gradient-bg-end-11">
                                                <div class="card-body p-0">
                                                    <div
                                                        class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="fw-semibold mb-2">$3,000</h6>
                                                            <span class="fw-medium text-secondary-light text-sm">Total
                                                                Due</span>
                                                        </div>
                                                        <span
                                                            class="mb-0 w-48-px h-48-px bg-orange text-white flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                                            <img src="assets/images/icons/fees-icon4.png"
                                                                alt="Holiday Icon">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="d-flex flex-wrap align-items-center gap-24 justify-content-between px-20 pb-16">
                                    <div class="d-flex flex-wrap align-items-center gap-16">
                                        <form class="navbar-search dt-search m-0">
                                            <input type="text" class="dt-input bg-transparent radius-4"
                                                aria-controls="dataTable" name="search" placeholder="Search...">
                                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                                        </form>
                                        <div class="">
                                            <select class="form-control form-select">
                                                <option value="Year 2025/2026">Year 2025/2026</option>
                                                <option value="Year 2026/2027">Year 2026/2027</option>
                                                <option value="Year 2027/2028">Year 2027/2028</option>
                                                <option value="Year 2028/2029">Year 2028/2029</option>
                                            </select>
                                        </div>
                                        <div class="dropdown">
                                            <button type="button"
                                                class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20 "
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <span
                                                    class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                                                    <i class="ri-file-upload-line text-md line-height-1"></i>
                                                    Export
                                                </span>
                                                <span class="">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu p-12 border bg-base shadow">
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                                        data-bs-toggle="modal" data-bs-target="#exampleModalView">
                                                        <i class="ri-file-3-line"></i>
                                                        PDF
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                                        data-bs-toggle="modal" data-bs-target="#exampleModalEdit">
                                                        <i class="ri-file-excel-line"></i>
                                                        Excel
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-8 text-secondary-light">
                                        <span class="">
                                            Rows per page:
                                        </span>
                                        <div class="dt-length">
                                            <select name="dataTable_length" aria-controls="dataTable"
                                                class="dt-input form-control form-select">
                                                <option value="5">5</option>
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <table class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                    id="dataTableTwo" data-page-length='10'>
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">
                                                        S.L
                                                    </label>
                                                </div>
                                            </th>
                                            <th scope="col">Fees Type</th>
                                            <th scope="col">Due Date</th>
                                            <th scope="col">Payment Type</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Discount</th>
                                            <th scope="col">Fine</th>
                                            <th scope="col">Paid</th>
                                            <th scope="col">Due</th>
                                            <th scope="col">Paid Date</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">01</label>
                                                </div>
                                            </td>
                                            <td>May Month Fees</td>
                                            <td>05 May 2025</td>
                                            <td>Bank</td>
                                            <td>$700.50</td>
                                            <td>10%</td>
                                            <td>$50</td>
                                            <td>$700.50</td>
                                            <td>$0</td>
                                            <td>12 May 2025</td>
                                            <td>
                                                <span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">
                                                    Paid
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">02</label>
                                                </div>
                                            </td>
                                            <td>June Month Fees</td>
                                            <td>05 Jun 2025</td>
                                            <td>Cash</td>
                                            <td>$680.00</td>
                                            <td>5%</td>
                                            <td>$30</td>
                                            <td>$350.00</td>
                                            <td>$330.00</td>
                                            <td>09 Jun 2025</td>
                                            <td>
                                                <span
                                                    class="bg-warning-100 text-warning-600 px-20 py-4 radius-4 fw-medium text-sm">
                                                    Partial
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">03</label>
                                                </div>
                                            </td>
                                            <td>July Month Fees</td>
                                            <td>05 Jul 2025</td>
                                            <td>Bank</td>
                                            <td>$700.00</td>
                                            <td>10%</td>
                                            <td>$0</td>
                                            <td>$0.00</td>
                                            <td>$700.00</td>
                                            <td>-</td>
                                            <td>
                                                <span
                                                    class="bg-danger-100 text-danger-600 px-20 py-4 radius-4 fw-medium text-sm">
                                                    Unpaid
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">04</label>
                                                </div>
                                            </td>
                                            <td>August Month Fees</td>
                                            <td>05 Aug 2025</td>
                                            <td>Online</td>
                                            <td>$750.00</td>
                                            <td>15%</td>
                                            <td>$40</td>
                                            <td>$750.00</td>
                                            <td>$0</td>
                                            <td>07 Aug 2025</td>
                                            <td>
                                                <span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">
                                                    Paid
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">05</label>
                                                </div>
                                            </td>
                                            <td>September Month Fees</td>
                                            <td>05 Sep 2025</td>
                                            <td>Bank</td>
                                            <td>$720.00</td>
                                            <td>10%</td>
                                            <td>$25</td>
                                            <td>$360.00</td>
                                            <td>$360.00</td>
                                            <td>10 Sep 2025</td>
                                            <td>
                                                <span
                                                    class="bg-warning-100 text-warning-600 px-20 py-4 radius-4 fw-medium text-sm">
                                                    Partial
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">06</label>
                                                </div>
                                            </td>
                                            <td>June month fees</td>
                                            <td>05 Jun 2025</td>
                                            <td>Cash</td>
                                            <td>$700.50</td>
                                            <td>5%</td>
                                            <td>$0</td>
                                            <td>$665.00</td>
                                            <td>$35.50</td>
                                            <td>07 Jun 2025</td>
                                            <td><span
                                                    class="bg-warning-100 text-warning-600 px-20 py-4 radius-4 fw-medium text-sm">Partial</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">07</label>
                                                </div>
                                            </td>
                                            <td>July month fees</td>
                                            <td>05 Jul 2025</td>
                                            <td>Bank</td>
                                            <td>$700.50</td>
                                            <td>10%</td>
                                            <td>$25</td>
                                            <td>$700.50</td>
                                            <td>$0</td>
                                            <td>06 Jul 2025</td>
                                            <td><span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Paid</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">08</label>
                                                </div>
                                            </td>
                                            <td>August month fees</td>
                                            <td>05 Aug 2025</td>
                                            <td>Card</td>
                                            <td>$700.50</td>
                                            <td>0%</td>
                                            <td>$0</td>
                                            <td>$0</td>
                                            <td>$700.50</td>
                                            <td>-</td>
                                            <td><span
                                                    class="bg-danger-100 text-danger-600 px-20 py-4 radius-4 fw-medium text-sm">Unpaid</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">09</label>
                                                </div>
                                            </td>
                                            <td>September month fees</td>
                                            <td>05 Sep 2025</td>
                                            <td>Online</td>
                                            <td>$700.50</td>
                                            <td>5%</td>
                                            <td>$15</td>
                                            <td>$350.00</td>
                                            <td>$350.50</td>
                                            <td>08 Sep 2025</td>
                                            <td><span
                                                    class="bg-warning-100 text-warning-600 px-20 py-4 radius-4 fw-medium text-sm">Partial</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">10</label>
                                                </div>
                                            </td>
                                            <td>October month fees</td>
                                            <td>05 Oct 2025</td>
                                            <td>Bank</td>
                                            <td>$700.50</td>
                                            <td>10%</td>
                                            <td>$20</td>
                                            <td>$700.50</td>
                                            <td>$0</td>
                                            <td>06 Oct 2025</td>
                                            <td><span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Paid</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">11</label>
                                                </div>
                                            </td>
                                            <td>November month fees</td>
                                            <td>05 Nov 2025</td>
                                            <td>Cash</td>
                                            <td>$700.50</td>
                                            <td>0%</td>
                                            <td>$0</td>
                                            <td>$0</td>
                                            <td>$700.50</td>
                                            <td>-</td>
                                            <td><span
                                                    class="bg-danger-100 text-danger-600 px-20 py-4 radius-4 fw-medium text-sm">Unpaid</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">12</label>
                                                </div>
                                            </td>
                                            <td>December month fees</td>
                                            <td>05 Dec 2025</td>
                                            <td>Online</td>
                                            <td>$700.50</td>
                                            <td>15%</td>
                                            <td>$0</td>
                                            <td>$595.00</td>
                                            <td>$105.50</td>
                                            <td>10 Dec 2025</td>
                                            <td><span
                                                    class="bg-warning-100 text-warning-600 px-20 py-4 radius-4 fw-medium text-sm">Partial</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-check style-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">13</label>
                                                </div>
                                            </td>
                                            <td>January month fees</td>
                                            <td>05 Jan 2026</td>
                                            <td>Bank</td>
                                            <td>$700.50</td>
                                            <td>10%</td>
                                            <td>$0</td>
                                            <td>$700.50</td>
                                            <td>$0</td>
                                            <td>06 Jan 2026</td>
                                            <td><span
                                                    class="bg-success-100 text-success-600 px-20 py-4 radius-4 fw-medium text-sm">Paid</span>
                                            </td>
                                        </tr>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Fees tab end -->

                    <!-- Exam tab start -->
                    <div class="tab-pane fade" id="pills-exam" role="tabpanel" aria-labelledby="pills-exam-tab"
                        tabindex="0">
                        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                            <div
                                class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                                <h6 class="text-lg fw-semibold mb-0">Exam </h6>
                            </div>
                            <div class="card-body p-20 d-flex flex-column gap-20">

                                <div class="border radius-8 overflow-hidden">
                                    <button type="button"
                                        class="custom-accordion-btn text-md fw-semibold text-secondary-light w-100 py-10 px-20 d-flex align-items-center gap-12 justify-content-between">
                                        First Semester
                                        <span class="arrow-icon text-lg d-flex line-height-1">
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="custom-accordion-content table-bottom-info-none">
                                        <table
                                            class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                            id="firstSemesterTable" data-page-length='10'>
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-start">Subject</th>
                                                    <th scope="col" class="text-start">Max Marks</th>
                                                    <th scope="col" class="text-start">Min Marks</th>
                                                    <th scope="col" class="text-start">Marks Obtained</th>
                                                    <th scope="col" class="text-start">Grade</th>
                                                    <th scope="col" class="text-start">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start">Bangla</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">English</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60</td>
                                                    <td class="text-start">B+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">ICT</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">70</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Physics </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Chemistry </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">48 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Mathematics</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Rank: 30
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total: 600
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total Obtain Marks: 398
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Grande: A
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Results: Pass
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="border radius-8 overflow-hidden">
                                    <button type="button"
                                        class="custom-accordion-btn text-md fw-semibold text-secondary-light w-100 py-10 px-20 d-flex align-items-center gap-12 justify-content-between">
                                        Monthly Test (Jun-2025)
                                        <span class="arrow-icon text-lg d-flex line-height-1">
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="custom-accordion-content table-bottom-info-none">
                                        <table
                                            class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                            id="monthlyTestJun" data-page-length='10'>
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-start">Subject</th>
                                                    <th scope="col" class="text-start">Max Marks</th>
                                                    <th scope="col" class="text-start">Min Marks</th>
                                                    <th scope="col" class="text-start">Marks Obtained</th>
                                                    <th scope="col" class="text-start">Grade</th>
                                                    <th scope="col" class="text-start">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start">Bangla</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">English</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60</td>
                                                    <td class="text-start">B+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">ICT</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">70</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Physics </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Chemistry </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">48 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Mathematics</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Rank: 30
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total: 600
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total Obtain Marks: 398
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Grande: A
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Results: Pass
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="border radius-8 overflow-hidden">
                                    <button type="button"
                                        class="custom-accordion-btn text-md fw-semibold text-secondary-light w-100 py-10 px-20 d-flex align-items-center gap-12 justify-content-between">
                                        Weekly Test(Jun-2025)
                                        <span class="arrow-icon text-lg d-flex line-height-1">
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="custom-accordion-content table-bottom-info-none">
                                        <table
                                            class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                            id="weeklyTestJun" data-page-length='10'>
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-start">Subject</th>
                                                    <th scope="col" class="text-start">Max Marks</th>
                                                    <th scope="col" class="text-start">Min Marks</th>
                                                    <th scope="col" class="text-start">Marks Obtained</th>
                                                    <th scope="col" class="text-start">Grade</th>
                                                    <th scope="col" class="text-start">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start">Bangla</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">English</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60</td>
                                                    <td class="text-start">B+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">ICT</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">70</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Physics </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Chemistry </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">48 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Mathematics</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Rank: 30
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total: 600
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total Obtain Marks: 398
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Grande: A
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Results: Pass
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="border radius-8 overflow-hidden">
                                    <button type="button"
                                        class="custom-accordion-btn text-md fw-semibold text-secondary-light w-100 py-10 px-20 d-flex align-items-center gap-12 justify-content-between">
                                        Weekly Test(May-2025)
                                        <span class="arrow-icon text-lg d-flex line-height-1">
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="custom-accordion-content table-bottom-info-none">
                                        <table
                                            class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                            id="weeklyTestMay" data-page-length='10'>
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-start">Subject</th>
                                                    <th scope="col" class="text-start">Max Marks</th>
                                                    <th scope="col" class="text-start">Min Marks</th>
                                                    <th scope="col" class="text-start">Marks Obtained</th>
                                                    <th scope="col" class="text-start">Grade</th>
                                                    <th scope="col" class="text-start">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start">Bangla</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">English</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60</td>
                                                    <td class="text-start">B+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">ICT</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">70</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Physics </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Chemistry </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">48 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Mathematics</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Rank: 30
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total: 600
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total Obtain Marks: 398
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Grande: A
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Results: Pass
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="border radius-8 overflow-hidden">
                                    <button type="button"
                                        class="custom-accordion-btn text-md fw-semibold text-secondary-light w-100 py-10 px-20 d-flex align-items-center gap-12 justify-content-between">
                                        weeklyTestMay
                                        <span class="arrow-icon text-lg d-flex line-height-1">
                                            <i class="ri-arrow-down-s-line"></i>
                                        </span>
                                    </button>
                                    <div class="custom-accordion-content table-bottom-info-none">
                                        <table
                                            class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                            id="monthlyTestMay" data-page-length='10'>
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-start">Subject</th>
                                                    <th scope="col" class="text-start">Max Marks</th>
                                                    <th scope="col" class="text-start">Min Marks</th>
                                                    <th scope="col" class="text-start">Marks Obtained</th>
                                                    <th scope="col" class="text-start">Grade</th>
                                                    <th scope="col" class="text-start">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start">Bangla</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">English</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60</td>
                                                    <td class="text-start">B+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">ICT</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">70</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Physics </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">60 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Chemistry </td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">48 </td>
                                                    <td class="text-start">B</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start">Mathematics</td>
                                                    <td class="text-start">100.00</td>
                                                    <td class="text-start">35.00</td>
                                                    <td class="text-start">80</td>
                                                    <td class="text-start">A+</td>
                                                    <td class="text-start">
                                                        <span
                                                            class="bg-success-100 text-success-600 px-16 py-2 radius-4 fw-medium text-sm">Pass</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Rank: 30
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total: 600
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Total Obtain Marks: 398
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Grande: A
                                                    </td>
                                                    <td
                                                        class="text-primary-light fw-semibold text-md border-top border-bottom border-neutral-200 text-start bg-neutral-50">
                                                        Results: Pass
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Exam tab start -->

                    <!-- Library tab start -->
                    <div class="tab-pane fade" id="pills-library" role="tabpanel" aria-labelledby="pills-library-tab"
                        tabindex="0">
                        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                            <div
                                class="card-header border-bottom bg-base py-10 px-20 d-flex align-items-center justify-content-between">
                                <h6 class="text-lg fw-semibold mb-0">Library </h6>
                            </div>
                            <div class="card-body p-0 dataTable-wrapper">
                                <div
                                    class="d-flex flex-wrap align-items-center gap-24 justify-content-between px-20 py-16">
                                    <div class="d-flex flex-wrap align-items-center gap-16">
                                        <form class="navbar-search dt-search m-0">
                                            <input type="text" class="dt-input bg-transparent radius-4"
                                                aria-controls="dataTable" name="search" placeholder="Search...">
                                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                                        </form>
                                        <div class="">
                                            <select class="form-control form-select">
                                                <option value="Year 2025/2026">Year 2025/2026</option>
                                                <option value="Year 2026/2027">Year 2026/2027</option>
                                                <option value="Year 2027/2028">Year 2027/2028</option>
                                                <option value="Year 2028/2029">Year 2028/2029</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-8 text-secondary-light">
                                        <span class="">
                                            Rows per page:
                                        </span>
                                        <div class="dt-length">
                                            <select name="dataTable_length" aria-controls="dataTable"
                                                class="dt-input form-control form-select">
                                                <option value="5">5</option>
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <table class="table bordered-table mb-0 table-heading-dark-mode w-100 data-table"
                                    id="dataTableLibrary" data-page-length='10'>
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-start">S.L</th>
                                            <th scope="col" class="text-start">Book Name</th>
                                            <th scope="col" class="text-start">Book Category</th>
                                            <th scope="col" class="text-start">Book Number</th>
                                            <th scope="col" class="text-start">Taken ON</th>
                                            <th scope="col" class="text-start">Last Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">01</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/thumbs/library-img1.png" alt="Library Image"
                                                        class="flex-shrink-0 me-12 radius-4 w-36-px h-36-px">
                                                    <div class="">
                                                        <h6
                                                            class="text-md mb-0 fw-medium flex-grow-1 text-secondary-light">
                                                            Marigold (NCERT)
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-start">English</td>
                                            <td class="text-start">8512</td>
                                            <td class="text-start"> 05 May 2025</td>
                                            <td class="text-start">05 Jun 2025</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">02</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/thumbs/library-img2.png" alt="Library Image"
                                                        class="flex-shrink-0 me-12 radius-4 w-36-px h-36-px">
                                                    <div class="">
                                                        <h6
                                                            class="text-md mb-0 fw-medium flex-grow-1 text-secondary-light">
                                                            Number Magic
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-start">Mathematics</td>
                                            <td class="text-start">85620</td>
                                            <td class="text-start"> 05 May 2025</td>
                                            <td class="text-start">05 Jun 2025</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">03</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/thumbs/library-img3.png" alt="Library Image"
                                                        class="flex-shrink-0 me-12 radius-4 w-36-px h-36-px">
                                                    <div class="">
                                                        <h6
                                                            class="text-md mb-0 fw-medium flex-grow-1 text-secondary-light">
                                                            Mental Math
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-start">Mathematics</td>
                                            <td class="text-start">8512</td>
                                            <td class="text-start"> 05 May 2025</td>
                                            <td class="text-start">05 Jun 2025</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">04</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/thumbs/library-img4.png" alt="Library Image"
                                                        class="flex-shrink-0 me-12 radius-4 w-36-px h-36-px">
                                                    <div class="">
                                                        <h6
                                                            class="text-md mb-0 fw-medium flex-grow-1 text-secondary-light">
                                                            Our Environment
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-start">Environmental Studies</td>
                                            <td class="text-start">85620</td>
                                            <td class="text-start"> 05 May 2025</td>
                                            <td class="text-start">05 Jun 2025</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">05</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <img src="assets/images/thumbs/library-img5.png" alt="Library Image"
                                                        class="flex-shrink-0 me-12 radius-4 w-36-px h-36-px">
                                                    <div class="">
                                                        <h6
                                                            class="text-md mb-0 fw-medium flex-grow-1 text-secondary-light">
                                                            Brainvita
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-start">General Knowledge</td>
                                            <td class="text-start">8512</td>
                                            <td class="text-start"> 05 May 2025</td>
                                            <td class="text-start">05 Jun 2025</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Library tab start -->

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('monFormulaire');

        const btnSubmit = document.getElementById('btnSubmit');
        const btnLoader = document.getElementById('btnLoader');
        const btnText = document.getElementById('btnText');

        const oldPassword = document.getElementById('old_password');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');

        const strengthText = document.getElementById('passwordStrengthText');
        const matchText = document.getElementById('matchText');

        let isSubmitting = false;

        // =========================
        // TOGGLE PASSWORD
        // =========================
        window.togglePassword = function (id) {
            const input = document.getElementById(id);
            input.type = (input.type === 'password') ? 'text' : 'password';
        }

        // =========================
        // IMAGE PREVIEW
        // =========================
        const photo = document.getElementById('photo');

        if (photo) {
            photo.addEventListener('change', function (e) {

                const file = e.target.files[0];
                const preview = document.getElementById('previewPhoto');

                if (file && preview) {

                    const reader = new FileReader();

                    reader.onload = function (event) {
                        preview.src = event.target.result;
                        preview.style.display = 'block';
                    };

                    reader.readAsDataURL(file);
                }
            });
        }

        // =========================
        // REAL TIME VALIDATION (BORDERS)
        // =========================
        function setValid(input) {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
        }

        function setInvalid(input) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        }

        function resetState(input) {
            input.classList.remove('is-valid', 'is-invalid');
        }

        function checkInputs() {

            const oldVal = oldPassword.value.trim();
            const newVal = newPassword.value.trim();
            const confirmVal = confirmPassword.value.trim();

            if (!oldVal && !newVal && !confirmVal) {
                resetState(oldPassword);
                resetState(newPassword);
                resetState(confirmPassword);
                return;
            }

            oldVal ? setValid(oldPassword) : setInvalid(oldPassword);

            if (newVal.length >= 6) setValid(newPassword);
            else if (newVal.length > 0) setInvalid(newPassword);
            else resetState(newPassword);

            if (confirmVal.length > 0 && confirmVal === newVal) {
                setValid(confirmPassword);
            } else if (confirmVal.length > 0) {
                setInvalid(confirmPassword);
            } else {
                resetState(confirmPassword);
            }

            // MATCH TEXT
            if (matchText) {
                if (!confirmVal) {
                    matchText.textContent = '';
                } else if (newVal === confirmVal) {
                    matchText.textContent = "✔ Correspond";
                    matchText.style.color = "green";
                } else {
                    matchText.textContent = "✖ Ne correspond pas";
                    matchText.style.color = "red";
                }
            }

            // STRENGTH
            if (strengthText) {

                let value = newVal;
                let strength = 0;

                if (value.length >= 6) strength++;
                if (/[A-Z]/.test(value)) strength++;
                if (/[0-9]/.test(value)) strength++;
                if (/[@$!%*?&]/.test(value)) strength++;

                if (!value) {
                    strengthText.textContent = '';
                }
                else if (strength <= 1) {
                    strengthText.textContent = "Faible";
                    strengthText.style.color = "red";
                }
                else if (strength === 2 || strength === 3) {
                    strengthText.textContent = "Moyen";
                    strengthText.style.color = "orange";
                }
                else {
                    strengthText.textContent = "Fort";
                    strengthText.style.color = "green";
                }
            }
        }

        oldPassword?.addEventListener('input', checkInputs);
        newPassword?.addEventListener('input', checkInputs);
        confirmPassword?.addEventListener('input', checkInputs);

        // =========================
        // SUBMIT FORM
        // =========================
        form.addEventListener('submit', function (e) {

            if (isSubmitting) {
                e.preventDefault();
                return;
            }

            const oldVal = oldPassword.value.trim();
            const newVal = newPassword.value.trim();
            const confirmVal = confirmPassword.value.trim();

            // photo seule autorisée
            if (!oldVal && !newVal && !confirmVal) {
                startLoading();
                isSubmitting = true;
                return;
            }

            if (!oldVal) return showError(e, "Ancien mot de passe requis.");
            if (!newVal) return showError(e, "Nouveau mot de passe requis.");
            if (!confirmVal) return showError(e, "Confirmation requise.");

            if (newVal.length < 6) return showError(e, "Minimum 6 caractères.");
            if (newVal !== confirmVal) return showError(e, "Les mots de passe ne correspondent pas.");

            startLoading();
            isSubmitting = true;
        });

        function showError(e, message) {
            e.preventDefault();

            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: message
            });
        }

        function startLoading() {

            if (btnSubmit) btnSubmit.disabled = true;
            if (btnLoader) btnLoader.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Traitement...';
        }

        // =========================
        // RESET FORM
        // =========================
        window.confirmerAnnulation = function () {

            Swal.fire({
                title: 'Confirmation',
                text: 'Confirmez-vous l\'annulation ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Non'
            }).then((result) => {

                if (result.isConfirmed) {

                    form.reset();

                    [oldPassword, newPassword, confirmPassword].forEach(el => {
                        el?.classList.remove('is-valid', 'is-invalid');
                    });

                    const preview = document.getElementById('previewPhoto');
                    if (preview) {
                        preview.src = '';
                        preview.style.display = 'none';
                    }

                    if (strengthText) strengthText.textContent = '';
                    if (matchText) matchText.textContent = '';

                    Swal.fire('Réinitialisé', '', 'success');
                }
            });
        }

    });
    </script>
@endsection
