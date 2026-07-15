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

        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">
                Liste des écoles
            </h1>

            <div>
                <a href="{{ route('index.home.admin') }}"
                   class="text-secondary-light hover-text-primary hover-underline">

                    Dashboard

                </a>

                <span class="text-secondary-light">
                    / Écoles
                </span>
            </div>
        </div>

        <!-- Button -->
        <a href="{{ route('add.ecole') }}"
           class="btn btn-primary-600 d-flex align-items-center gap-6">

            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>

            Ajouter une école

        </a>

    </div>

    <div class="mt-24">

        <div class="card h-100">

            <div class="card-body p-0 dataTable-wrapper">

                <!-- HEADER -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">

                    <div class="d-flex flex-wrap align-items-center gap-16">

                        <!-- EXPORT -->
                        <div class="dropdown">

                            <button type="button"
                                    class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">

                                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">

                                    <i class="ri-file-upload-line text-md line-height-1"></i>

                                    Export

                                </span>

                                <span>
                                    <i class="ri-arrow-down-s-line"></i>
                                </span>

                            </button>

                            <ul class="dropdown-menu p-12 border bg-base shadow">

                                <li>

                                    <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">

                                        <i class="ri-file-3-line"></i>

                                        PDF

                                    </button>

                                </li>

                                <li>

                                    <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                                        <i class="ri-file-excel-line"></i>
                                        Excel
                                    </button>

                                </li>

                            </ul>

                        </div>

                        <!-- SEARCH -->
                        <form class="navbar-search dt-search m-0">

                            <input type="text"
                                   class="dt-input bg-transparent radius-4"
                                   name="search"
                                   placeholder="Rechercher une école...">
                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>

                        </form>

                    </div>

                    <!-- ROWS -->
                    <div class="d-flex align-items-center gap-8 text-secondary-light">

                        <span>
                            Rows per page:
                        </span>

                        <div class="dt-length">

                            <select class="dt-input form-control form-select">

                                <option value="5">5</option>

                                <option value="10" selected>10</option>

                                <option value="25">25</option>

                                <option value="50">50</option>

                                <option value="100">100</option>

                            </select>

                        </div>

                    </div>

                </div>

                <!-- TABLE -->
                <div class="p-0">

                    <table class="table bordered-table mb-0 data-table" id="dataTable">

                        <thead>

                            <tr>

                                <th width="5%">#</th>
                                <th>Logo</th>
                                <th>École</th>
                                <th>Établissement</th>
                                <th>Code</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Directeur</th>
                                <th>Adresse</th>
                                <th>État</th>
                                <th>Liens/th>
                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($ecoles as $key => $ecole)

                            <tr>

                                <!-- ID -->
                                <td>
                                    {{ $key + 1 }}
                                </td>

                                <!-- LOGO -->
                                <td>

                                    @if($ecole->logo)

                                        <img
                                            src="{{ asset($ecole->logo) }}"
                                            alt="Logo"
                                            width="50"
                                            height="50"
                                            class="rounded-circle border"
                                            style="object-fit: cover;">

                                    @else

                                        <img
                                            src="{{ asset('assets/images/default-school.png') }}"
                                            alt="Logo"
                                            width="50"
                                            height="50"
                                            class="rounded-circle border"
                                            style="object-fit: cover;">

                                    @endif

                                </td>

                                <!-- NOM ECOLE -->
                                <td>
                                    {{ $ecole->v_nomecole }}
                                </td>

                                <!-- ETABLISSEMENT -->
                                <td>
                                    {{ $ecole->v_nometablissement ?? '-' }}
                                </td>

                                <!-- CODE -->
                                <td>
                                    {{ $ecole->v_codeecole }}
                                </td>

                                <!-- TELEPHONE -->
                                <td>
                                    {{ $ecole->v_telephone1ecole }}
                                </td>

                                <!-- EMAIL -->
                                <td>
                                    {{ $ecole->v_adressemailv_telephone1ecole }}
                                </td>

                                <!-- DIRECTEUR -->
                                <td>
                                    {{ $ecole->v_nomdirecteurecole }}
                                </td>

                                <!-- ADRESSE -->
                                <td>
                                    {{ $ecole->t_adresseecole }}
                                </td>

                                <!-- ETAT -->
                                <td>

                                    @if($ecole->bt_etat_ecole == 1)

                                        <span class="badge bg-success">
                                            Actif
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Inactif
                                        </span>

                                    @endif

                                </td>
                                <td>
                                    <a target="_blanck" href="{{'http://127.0.0.1:8000/'.$ecole->v_slugecole }}"> {{'http://127.0.0.1:8000/'.$ecole->v_slugecole }} </a>
                                </td>
                                <!-- ACTION -->
                                <td>

                                    <div class="btn-group">

                                        <button type="button"
                                                class="text-primary-light text-xl"
                                                data-bs-toggle="dropdown"
                                                data-bs-display="static"
                                                aria-expanded="false">

                                            <iconify-icon icon="tabler:dots-vertical"></iconify-icon>

                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-lg-end border p-12">

                                            <!-- DETAILS -->
                                            <li>

                                                <a href="#"
                                                   class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">

                                                    <i class="ri-eye-line"></i>

                                                    Détails

                                                </a>

                                            </li>

                                            <!-- EDIT -->
                                            <li>

                                              <a href="{{ route('ecoles.edite', [
    'id_ecole' => $ecole->i_idecole
]) }}"
   class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
    <i class="ri-edit-2-line"></i>
    Modifier
</a>

                                            </li>

                                            <!-- STATUS -->
                                            <li>

                                                <button
                                                    class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6"
                                                    type="button">

                                                    <i class="ri-error-warning-line"></i>

                                                    Désactiver

                                                </button>

                                            </li>

                                            <!-- DELETE -->
                                            <li>

                                                <button
                                                    class="dropdown-item rounded text-danger bg-hover-danger-100 d-flex align-items-center gap-2 py-6"
                                                    type="button">

                                                    <i class="ri-delete-bin-6-line"></i>

                                                    Supprimer

                                                </button>

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
