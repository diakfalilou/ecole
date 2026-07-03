@extends('admin.layout.app')
@section('container')

<style>
    .my-sidebar {
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .my-sidebar.active {
        transform: translateX(0);
    }

    .edit-sidebar {
        transform: translateX(100%);
        transition: all 0.3s ease;
    }

    .edit-sidebar.active {
        transform: translateX(0);
    }
</style>
<div class="dashboard-main-body">

        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div class="">
                <h1 class="fw-semibold mb-4 h6 text-primary-light">Année sclaire </h1>
                <div class="">
                    <a href=" {{route('index.home.admin')}} " class="text-secondary-light hover-text-primary hover-underline">Dashboard </a>

                    <span class="text-secondary-light">/ Année scolaire</span>
                </div>
            </div>
            <!-- Button -->
            <a type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" href="{{route('add.etablissement')}}">
                <span class="d-flex text-md">
                    <i class="ri-add-large-line"></i>
                </span>
                Ajouter un établissement
            </a>
        </div>

        <div class="mt-24">
            <div class="card h-100">
                <div class="card-body p-0 dataTable-wrapper">

                    <div
                        class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                        <div class="d-flex flex-wrap align-items-center gap-16">
                            <div class="dropdown">
                                <button type="button"
                                    class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20 "
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">
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
                            <form class="navbar-search dt-search m-0">
                                <input type="text" class="dt-input bg-transparent radius-4" aria-controls="dataTable"
                                    name="search" placeholder="Search...">
                                <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                            </form>
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

                    <div class="p-0">
                        <table class="table bordered-table mb-0 data-table" id="dataTable">
                           <thead>
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col">Logo</th>
                                    <th scope="col">Nom établissement</th>
                                    <th scope="col">Adresse</th>
                                    <th scope="col">Téléphone 1</th>
                                    <th scope="col">Téléphone 2</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Fondateur</th>
                                    <th scope="col">Utilisateur</th>
                                    <th scope="col">État</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($etablissements as $key => $etablissement)
                                <tr>
                                    <!-- # -->
                                    <td>{{ $key + 1 }}</td>
                                    <!-- Logo -->
                                    <td>
                                        @if($etablissement->logo)
                                            <img
                                                src="{{ asset($etablissement->logo) }}"
                                                alt="Logo"
                                                width="50"
                                                height="50"
                                                class="rounded-circle object-fit-cover border"
                                                style="object-fit: cover;"
                                            >
                                        @else
                                            <img
                                                src="{{ asset('assets/images/default-school.png') }}"
                                                alt="Logo par défaut"
                                                width="50"
                                                height="50"
                                                class="rounded-circle object-fit-cover border"
                                                style="object-fit: cover;"
                                            >
                                        @endif
                                    </td>
                                    <!-- Nom -->
                                    <td>{{ $etablissement->v_nometablissement }}</td>
                                    <!-- Adresse -->
                                    <td>{{ $etablissement->t_adresseetablissement }}</td>
                                    <!-- Téléphone 1 -->
                                    <td>{{ $etablissement->v_telephone1etablissement }}</td>
                                    <!-- Téléphone 2 -->
                                    <td>{{ $etablissement->v_telephone2etablissement }}</td>
                                    <!-- Email -->
                                    <td>{{ $etablissement->v_adressemailv_telephone1etablissement }}</td>
                                    <!-- Fondateur -->
                                    <td>{{ $etablissement->v_nomfondateurv_telephone1etablissement }}</td>
                                    <!-- User -->
                                    <td>{{ $etablissement->i_userID }}</td>
                                    <!-- État -->
                                    <td>
                                        @if($etablissement->bt_etat_etablissement == 1)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <!-- ACTION -->
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="text-primary-light text-xl"
                                                data-bs-toggle="dropdown" data-bs-display="static"
                                                aria-expanded="false">
                                                <iconify-icon icon="tabler:dots-vertical"></iconify-icon>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
                                                <li>
                                                    <a href="student-list.html"
                                                        class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
                                                        <i class="ri-user-3-line"></i>
                                                        Détails
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="edit-teacher.html"
                                                        class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
                                                        <i class="ri-edit-2-line"></i>
                                                        Modifié
                                                    </a>
                                                </li>
                                                <li>
                                                    <button
                                                        class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6"
                                                        type="button">
                                                        <i class="ri-error-warning-line"></i>
                                                        Inactive
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6" data-bs-toggle="modal" data-bs-target="#exampleModalDelete"><i class="ri-delete-bin-6-line"></i>Delete</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection
