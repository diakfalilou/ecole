@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">

        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div class="">
                <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des classes</h1>
                <div class="">
                    <a href="index-2.html" class="text-secondary-light hover-text-primary hover-underline">Accueille </a>
                    <span class="text-secondary-light">/ classe</span>
                </div>
            </div>
            <button type="button"
                class="btn btn-primary-600 d-flex align-items-center gap-6"
                data-bs-toggle="modal"
                data-bs-target="#addSectionModal">
                <span class="d-flex text-md">
                    <i class="ri-add-large-line"></i>
                </span>
                Ajouter une classe
            </button>
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

                                    <th>Classe</th>
                                    <th>Capacité</th>
                                    <th>Niveau</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($classes as $index => $classe)

                                    <tr>

                                        {{-- N° --}}
                                        <td>
                                            <div class="form-check style-check d-flex align-items-center">
                                                <input class="form-check-input" type="checkbox">
                                                <label class="form-check-label">
                                                    {{ $index + 1 }}
                                                </label>
                                            </div>
                                        </td>

                                        {{-- Nom classe --}}
                                        <td>
                                            {{ $classe->v_nom_classe }}
                                        </td>

                                        {{-- Capacité --}}
                                        <td>
                                            {{ $classe->i_capacite }}
                                        </td>

                                        {{-- Niveau --}}
                                        <td>
                                            {{ $classe->v_niveaux }}
                                        </td>

                                        {{-- Section --}}
                                        <td>
                                            {{ $classe->v_sections  ?? 'Null' }}
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            @if($classe->b_desabled == 1)
                                                <span class="bg-success-100 text-success-600 px-24 py-4 radius-4 fw-medium text-sm">
                                                    Actif
                                                </span>
                                            @else
                                                <span class="bg-danger-100 text-danger-600 px-24 py-4 radius-4 fw-medium text-sm">
                                                    Suspendu
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td>
                                            <div class="btn-group">

                                                <button type="button"
                                                    class="text-primary-light text-xl"
                                                    data-bs-toggle="dropdown">
                                                    <iconify-icon icon="tabler:dots-vertical"></iconify-icon>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-lg-end border p-12">

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item d-flex align-items-center gap-2 py-6 editClasseBtn"
                                                            data-id="{{ $classe->i_classe_id }}"
                                                            data-nom="{{ $classe->v_nom_classe }}"
                                                            data-capacite="{{ $classe->i_capacite }}"
                                                            data-niveau="{{ $classe->i_niveau_id }}"
                                                            data-section="{{ $classe->i_section_id }}"
                                                            data-detail="{{ $classe->t_detail_classe }}">

                                                            <i class="ri-edit-2-line"></i>
                                                            Modifier
                                                        </button>
                                                    </li>

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item toggleClasseStatusBtn"
                                                            data-id="{{ $classe->i_classe_id }}"
                                                            data-status="{{ $classe->b_desabled }}">

                                                            @if($classe->b_desabled == 1)
                                                                <i class="ri-pause-circle-line"></i> Suspendre
                                                            @else
                                                                <i class="ri-play-circle-line"></i> Activer
                                                            @endif

                                                        </button>
                                                    </li>

                                                </ul>
                                            </div>
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            Aucune classe enregistrée
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
    <form id="statusClasseForm" method="POST" style="display:none;">
        @csrf
        @method('PATCH')
    </form>
    <!-- Add Section Modal -->
    <div style="margin-top:-8%" class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addSectionModalLabel">
                        Ajout d'une nouvelle classe
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="{{ route('classe.store', $slug) }}" method="POST" id="classeForm">
                    @csrf

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-6">
                                <label class="form-label">Niveaux</label>
                                <select name="i_niveau_id" class="form-control form-select" required>
                                    <option disabled selected>Selectionner un niveau</option>
                                    @foreach ($niveaux as $niveau)
                                        <option value="{{ $niveau->i_niveauID }}">
                                            {{ $niveau->v_niveaux }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Sections</label>
                                <select name="i_section_id" class="form-control form-select">
                                    <option value="">Selectionner une section</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->i_niveauID }}">
                                            {{ $section->v_sections }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row g-3 mt-2">

                            <div class="col-6">
                                <label class="form-label">Nom de la classe</label>
                                <input class="form-control" name="v_nom_classe" type="text" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Capacité</label>
                                <input class="form-control" name="i_capacite" type="number" required>
                            </div>

                        </div>

                        <div class="row g-3 mt-2">

                            <div class="col-12">
                                <label class="form-label">Détail de la classe</label>
                                <textarea class="form-control" name="t_detail_classe" rows="5"></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="reset" class="btn btn-danger">Fermer</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editClasseModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Modifier la classe</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editClasseForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        <input type="hidden" id="edit_classe_id">

                        <div class="row g-3">

                            <div class="col-6">
                                <label>Nom classe</label>
                                <input type="text" id="edit_nom" name="v_nom_classe" class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Capacité</label>
                                <input type="number" id="edit_capacite" name="i_capacite" class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Niveau</label>
                                <select id="edit_niveau" name="i_niveau_id" class="form-control">
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->i_niveauID }}">
                                            {{ $niveau->v_niveaux }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label>Section</label>
                                <select id="edit_section" name="i_section_id" class="form-control">
                                    @foreach($sections as $section)
                                        <option value="{{ $section->i_niveauID }}">
                                            {{ $section->v_sections }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label>Détail</label>
                                <textarea id="edit_detail" name="t_detail_classe" class="form-control" rows="4"></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </div>

                </form>

            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.toggleClasseStatusBtn').forEach(button => {
            button.addEventListener('click', function () {

                let id = this.dataset.id;
                let status = this.dataset.status;

                let actionText = (status == 1) ? 'suspendre' : 'activer';

                Swal.fire({
                    title: 'Confirmation',
                    text: `Confirmez-vous de ${actionText} cette classe ?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui',
                    cancelButtonText: 'Annuler'
                }).then((result) => {

                    if (result.isConfirmed) {

                        Swal.fire({
                            title: 'Traitement en cours...',
                            text: 'Veuillez patienter',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        let form = document.getElementById('statusClasseForm');

                        form.action = "{{ url($slug.'/classe') }}/" + id + "/status";

                        form.submit();
                    }

                });

            });
        });
        </script>

    <script>
        /**
         * OUVERTURE MODAL + PRE-REMPLISSAGE
         */
        document.querySelectorAll('.editClasseBtn').forEach(button => {
            button.addEventListener('click', function () {

                document.getElementById('edit_classe_id').value = this.dataset.id;
                document.getElementById('edit_nom').value = this.dataset.nom;
                document.getElementById('edit_capacite').value = this.dataset.capacite;
                document.getElementById('edit_detail').value = this.dataset.detail;

                document.getElementById('edit_niveau').value = this.dataset.niveau;
                document.getElementById('edit_section').value = this.dataset.section;

                let form = document.getElementById('editClasseForm');
                form.action = "{{ url($slug.'/classe') }}/" + this.dataset.id;

                new bootstrap.Modal(document.getElementById('editClasseModal')).show();
            });
        });


        /**
         * SUBMIT MODIFICATION + CONFIRMATION + LOADER
         */
        document.getElementById('editClasseForm').addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirmation',
                text: 'Confirmez-vous la modification de la classe ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Modification en cours...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    this.submit();
                }
            });
        });

        </script>

    <script>
        document.getElementById('classeForm').addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirmation',
                text: 'Confirmez-vous l\'ajout de la classe ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Ajout en cours...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    this.submit();
                }
            });
        });
    </script>
@endsection
