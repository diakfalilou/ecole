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
            <button type="button"
                class="btn btn-primary-600 d-flex align-items-center gap-6"
                data-bs-toggle="modal"
                data-bs-target="#addRoleModal">

                <span class="d-flex text-md">
                    <i class="ri-add-large-line"></i>
                </span>

                Add année scolaire
            </button>

           <!-- Modal -->
            <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 radius-16">

                        <!-- Header -->
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title">Ajouter une année scolaire</h5>

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close">
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="anneeForm" action="{{ route('annee.scolaire.store') }}" method="POST">
                            @csrf

                            <div class="modal-body">

                                <div class="row g-3">

                                    <!-- Année scolaire -->
                                    <div class="col-md-12">

                                        <label class="form-label fw-semibold">
                                            Année scolaire
                                        </label>

                                    <input type="text"
                            name="v_annesclaire"
                            id="v_annesclaire"
                            class="form-control"
                            placeholder="2025-2026"
                            maxlength="9"
                            required>

                            <small id="error_annee"
                                class="text-danger d-none">
                            </small>

                        </div>

                        <!-- Début -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Année début
                            </label>

                            <input type="text"
                                name="v_debutanneesclaire"
                                id="v_debutanneesclaire"
                                class="form-control"
                                placeholder="2025"
                                maxlength="4"
                                required>

                            <small id="error_debut"
                                class="text-danger d-none">
                            </small>

                        </div>

                        <!-- Fin -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Année fin
                            </label>

                            <input type="text"
                                name="v_finanneesclaire"
                                id="v_finanneesclaire"
                                class="form-control"
                                placeholder="2026"
                                maxlength="4"
                                required>

                            <small id="error_fin"
                                class="text-danger d-none">
                            </small>

                        </div>

                        <!-- Expiration -->
                        <div class="col-md-12">

                            <label class="form-label fw-semibold">
                                Date expiration
                            </label>

                            <input type="date"
                                name="d_dateexpirationanneescolaire"
                                class="form-control"
                                required>

                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-top">

                    <button type="button"
                        class="btn btn-outline-danger"
                        data-bs-dismiss="modal">

                        Annuler
                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        Enregistrer
                    </button>

                </div>

            </form>

            <script>

            $(document).ready(function () {

                // =========================
                // Année scolaire format
                // =========================

                $('#v_annesclaire').on('keyup', function () {

                    let valeur = $(this).val();

                    let regex = /^\d{4}-\d{4}$/;

                    if (!regex.test(valeur)) {

                        $('#error_annee')
                            .removeClass('d-none')
                            .text("Format obligatoire : 2025-2026");

                        $(this).addClass('is-invalid');

                    } else {

                        $('#error_annee')
                            .addClass('d-none');

                        $(this).removeClass('is-invalid');

                    }

                });

                // =========================
                // Début & Fin validation
                // =========================

                $('#v_debutanneesclaire, #v_finanneesclaire').on('keyup', function () {

                    let debut = $('#v_debutanneesclaire').val();

                    let fin = $('#v_finanneesclaire').val();

                    let regexYear = /^\d{4}$/;

                    // Vérification début
                    if (debut !== '' && !regexYear.test(debut)) {

                        $('#error_debut')
                            .removeClass('d-none')
                            .text("L'année doit contenir 4 chiffres");

                        $('#v_debutanneesclaire').addClass('is-invalid');

                    } else {

                        $('#error_debut')
                            .addClass('d-none');

                        $('#v_debutanneesclaire').removeClass('is-invalid');

                    }

                    // Vérification fin
                    if (fin !== '' && !regexYear.test(fin)) {

                        $('#error_fin')
                            .removeClass('d-none')
                            .text("L'année doit contenir 4 chiffres");

                        $('#v_finanneesclaire').addClass('is-invalid');

                    } else {

                        $('#error_fin')
                            .addClass('d-none');

                        $('#v_finanneesclaire').removeClass('is-invalid');

                    }

                    // Début > Fin
                    if (
                        regexYear.test(debut) &&
                        regexYear.test(fin)
                    ) {

                        if (parseInt(debut) > parseInt(fin)) {

                            $('#error_fin')
                                .removeClass('d-none')
                                .text("L'année début ne peut pas être supérieure à l'année fin");

                            $('#v_finanneesclaire').addClass('is-invalid');

                        } else {

                            $('#error_fin')
                                .addClass('d-none');

                            $('#v_finanneesclaire').removeClass('is-invalid');

                        }

                    }

                });

            });

            </script>

        </div>
    </div>
            </div>
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

                                    <th scope="col" width="5%">
                                        #
                                    </th>

                                    <th scope="col">
                                        Année scolaire
                                    </th>

                                    <th scope="col">
                                        Début
                                    </th>

                                    <th scope="col">
                                        Fin
                                    </th>

                                    <th scope="col">
                                        Expiration
                                    </th>

                                    <th scope="col">
                                        Date création
                                    </th>

                                    <th scope="col" width="10%">
                                        Action
                                    </th>

                                </tr>
                            </thead>

                            <tbody>

                                @forelse($annees as $key => $annee)

                                    <tr>

                                        <td>
                                            {{ $key + 1 }}
                                        </td>

                                        <td>
                                            <span class="fw-semibold">
                                                {{ $annee->v_annesclaire }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $annee->v_debutanneesclaire }}
                                        </td>

                                        <td>
                                            {{ $annee->v_finanneesclaire }}
                                        </td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($annee->d_dateexpirationanneescolaire)->format('d/m/Y') }}
                                        </td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($annee->d_datecreationanneesclaire)->format('d/m/Y H:i') }}
                                        </td>

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

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">

                                                            <i class="ri-edit-2-line"></i>
                                                            Modifier

                                                        </button>
                                                    </li>

                                                    <li>

                                                        <button type="button"
                                                            class="dropdown-item rounded text-danger-600 bg-hover-danger-100 d-flex align-items-center gap-2 py-6 btn-delete"
                                                            data-id="{{ $annee->i_idanneesclaire }}">

                                                            <i class="ri-delete-bin-6-line"></i>
                                                            Supprimer

                                                        </button>

                                                    </li>

                                                </ul>

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7" class="text-center py-4">

                                            Aucune année scolaire trouvée.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- Modal Delete -->
<div class="modal fade" id="exampleModalDelete" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-sm modal-dialog-centered">

        <div class="modal-content radius-16 bg-base">

            <div class="modal-body pt-32 px-24 pb-24 text-center">

                <span class="mb-16 fs-1 line-height-1 text-danger">

                    <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>

                </span>

                <h6 class="text-lg fw-semibold text-primary-light mb-8">

                    Confirmation

                </h6>

                <p class="text-secondary-light mb-0">

                    Voulez-vous vraiment supprimer cette année scolaire ?

                </p>

                <form id="deleteForm" method="POST" class="mt-24">

                    @csrf
                    @method('DELETE')

                    <div class="d-flex align-items-center justify-content-center gap-3">

                        <button type="button"
                            class="flex-grow-1 border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8"
                            data-bs-dismiss="modal">

                            Annuler

                        </button>

                        <button type="submit"
                            class="flex-grow-1 btn btn-primary-600 border border-primary-600 text-md px-16 py-12 radius-8">

                            Oui, supprimer

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script>

    $(document).on('click', '.btn-delete', function () {

        let id = $(this).data('id');

        let url = "{{ route('annee.scolaire.delete', ':id') }}";

        url = url.replace(':id', id);

        $('#deleteForm').attr('action', url);

        // Bootstrap 5
        let modal = new bootstrap.Modal(document.getElementById('exampleModalDelete'));

        modal.show();

    });

</script>
<!-- Modal Delete Event end -->  <!-- jQuery library js -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

$(document).ready(function () {

    $('#anneeForm button[type="submit"]').on('click', function (e) {

        e.preventDefault();

        Swal.fire({
            title: 'Confirmation',
            text: "Confirmez-vous l'ajout de l'année scolaire ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler'

        }).then((result) => {

            if (result.isConfirmed) {

                $('#anneeForm').off('submit');

                $('#anneeForm').submit();

            }

        });

    });

});

</script>

@endsection
