@extends('admin.layout.app')
@section('container')
<div class="dashboard-main-body">

        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div class="">
                <h1 class="fw-semibold mb-4 h6 text-primary-light">Liste des contrats écoles</h1>
                <div class="">
                    <a href="index-2.html" class="text-secondary-light hover-text-primary hover-underline">Accueille </a>
                    <span class="text-secondary-light">/ Contrat</span>
                </div>
            </div>
            <a href="{{route('add.contrat')}}" type="button" class="btn btn-primary-600 d-flex align-items-center gap-6">
                <span class="d-flex text-md">
                    <i class="ri-add-large-line"></i>
                </span>
                Créer un contrat
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
                        <table class="table bordered-table mb-0 data-table" id="dataTable" data-page-length="10">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check style-check d-flex align-items-center">
                                            <input class="form-check-input" type="checkbox">
                                            <label class="form-check-label">N°</label>
                                        </div>
                                    </th>

                                    <th>Année scolaire</th>
                                    <th>Établissement</th>
                                    <th>École</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Total jours</th>
                                    <th>Jours restant</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($contrats as $index => $contrat)

                                    <tr>

                                        <td>
                                            <div class="form-check style-check d-flex align-items-center">
                                                <input class="form-check-input" type="checkbox">
                                                <label class="form-check-label">
                                                    {{ $index + 1 }}
                                                </label>
                                            </div>
                                        </td>

                                        <td>
                                            {{ $contrat->v_annee_scolaire }}
                                        </td>

                                        <td>
                                            {{ $contrat->v_nometablissement }}
                                        </td>

                                        <td>
                                            {{ $contrat->v_nomecole }}
                                        </td>

                                        <td class="start-date">
                                            {{ \Carbon\Carbon::parse($contrat->d_datedebut)->format('Y-m-d') }}
                                        </td>

                                        <td class="end-date">
                                            {{ \Carbon\Carbon::parse($contrat->d_datefin)->format('Y-m-d') }}
                                        </td>

                                        <td class="total-days">-</td>

                                        <td class="remaining-days">-</td>

                                        <td>
                                            {{ number_format($contrat->d_montant, 0, ',', ' ') }} GNF
                                        </td>

                                        <td>
                                            @if($contrat->b_desabled == 1)

                                                <span class="bg-success-100 text-success-600 px-24 py-4 radius-4 fw-medium text-sm">
                                                    Actif
                                                </span>

                                            @else

                                                <span class="bg-danger-100 text-danger-600 px-24 py-4 radius-4 fw-medium text-sm">
                                                    Suspendu
                                                </span>

                                            @endif
                                        </td>

                                        <td>
                                            <div class="btn-group">

                                                <button type="button"
                                                    class="text-primary-light text-xl"
                                                    data-bs-toggle="dropdown"
                                                    data-bs-display="static">
                                                    <iconify-icon icon="tabler:dots-vertical"></iconify-icon>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-lg-end border p-12">

                                                    {{-- <li>
                                                        <button disabled type="button"
                                                            class="dropdown-item editContratBtn"
                                                            data-id="{{ $contrat->i_contrat_id }}">
                                                            <i class="ri-edit-2-line"></i>
                                                            Modifier
                                                        </button>
                                                    </li> --}}

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item toggleContratStatusBtn"
                                                            data-id="{{ $contrat->i_contrat_id }}"
                                                            data-status="{{ $contrat->b_desabled }}">

                                                            @if($contrat->b_desabled == 1)
                                                                <i class="ri-pause-circle-line"></i>
                                                                Suspendre
                                                            @else
                                                                <i class="ri-play-circle-line"></i>
                                                                Activer
                                                            @endif

                                                        </button>
                                                    </li>

                                                </ul>

                                            </div>
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            Aucun contrat enregistré
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

    <form id="statusContratForm" method="POST" style="display:none;">
        @csrf
        @method('PATCH')
    </form>

    <script>
        document.querySelectorAll('.toggleContratStatusBtn').forEach(button => {

            button.addEventListener('click', function() {

                let id = this.dataset.id;
                let status = this.dataset.status;

                let actionText = status == 1
                    ? 'suspendre'
                    : 'activer';

                Swal.fire({
                    title: 'Confirmation',
                    text: `Confirmez-vous la volonté de ${actionText} ce contrat ?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui',
                    cancelButtonText: 'Annuler'
                }).then((result) => {

                    if(result.isConfirmed){

                        Swal.fire({
                            title: 'Traitement en cours...',
                            text: 'Veuillez patienter',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        let form = document.getElementById('statusContratForm');

                        form.action =
                            "{{ url('contrat') }}/" +
                            id +
                            "/status";

                        form.submit();
                    }

                });

            });

        });
    </script>

   <script>
        document.addEventListener("DOMContentLoaded", function () {

            const rows = document.querySelectorAll("#dataTable tbody tr");

            rows.forEach(row => {

                const startTd = row.querySelector(".start-date");
                const endTd = row.querySelector(".end-date");
                const totalTd = row.querySelector(".total-days");
                const remainingTd = row.querySelector(".remaining-days");

                if (!startTd || !endTd) return;

                const startDate = new Date(startTd.innerText.trim());
                const endDate = new Date(endTd.innerText.trim());
                const today = new Date();

                startDate.setHours(0,0,0,0);
                endDate.setHours(0,0,0,0);
                today.setHours(0,0,0,0);

                // Total jours
                const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

                // Jours restants
                let remainingDays = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));

                if (remainingDays < 0) remainingDays = 0;

                if (totalTd) {
                    totalTd.innerHTML = totalDays + " jours";
                }

                if (remainingTd) {

                    remainingTd.innerHTML = remainingDays + " jours";

                    // ✅ ICI TU METS TON CODE COULEUR
                    if (remainingDays <= 0) {
                        remainingTd.classList.add("text-danger");
                    } else if (remainingDays < 30) {
                        remainingTd.classList.add("text-warning");
                    } else {
                        remainingTd.classList.add("text-success");
                    }
                }

            });

        });
    </script>

@endsection
