@extends('ecoles.layout.app')
@section('containte')
<div class="dashboard-main-body">

    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div class="">
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des sections</h1>
            <div class="">
                <a href="index-2.html" class="text-secondary-light hover-text-primary hover-underline">Accueille </a>
                <span class="text-secondary-light">/ Section</span>
            </div>
        </div>

        <button type="button"
            class="btn btn-primary-600 d-flex align-items-center gap-6"
            data-bs-toggle="modal"
            data-bs-target="#addSectionModal">
            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>
            Ajouter une section
        </button>
    </div>

    <div class="mt-24">
        <div class="card h-100">
            <div class="card-body p-0 dataTable-wrapper">

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">

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
                            <input type="text" class="dt-input bg-transparent radius-4"
                                name="search" placeholder="Search...">
                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                        </form>

                    </div>

                    <div class="d-flex align-items-center gap-8 text-secondary-light">
                        <span>Rows per page:</span>

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

                <div class="p-0">
                    <table class="table bordered-table mb-0 data-table" id="dataTable">

                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">N°</label>
                                    </div>
                                </th>
                                <th>Sections</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($sections as $index => $section)
                            <tr>
                                <td>
                                    <div class="form-check style-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">{{ $index + 1 }}</label>
                                    </div>
                                </td>

                                <td>{{ $section->v_sections }}</td>

                                <td>
                                    @if($section->b_desabled == 1)
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
                                            data-bs-toggle="dropdown">
                                            <iconify-icon icon="tabler:dots-vertical"></iconify-icon>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-lg-end border p-12">

                                            <li>
                                                <button type="button"
                                                    class="dropdown-item rounded editSectionBtn"
                                                    data-id="{{ $section->i_niveauID }}"
                                                    data-section="{{ $section->v_sections }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    Modifier
                                                </button>
                                            </li>

                                            <li>
                                                <button type="button"
                                                    class="dropdown-item toggleStatusBtn"
                                                    data-id="{{ $section->i_niveauID }}"
                                                    data-status="{{ $section->b_desabled }}">

                                                    @if($section->b_desabled == 1)
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
                                <td colspan="4" class="text-center py-4">
                                    Aucune section enregistrée
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

{{-- STATUS FORM --}}
<form id="statusForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<script>
document.querySelectorAll('.toggleStatusBtn').forEach(button => {
    button.addEventListener('click', function () {

        let id = this.dataset.id;
        let status = this.dataset.status;

        let actionText = status == 1 ? 'suspendre' : 'activer';

        Swal.fire({
            title: 'Confirmation',
            text: `Confirmez-vous la volonté de ${actionText} cette section ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler'
        }).then((result) => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Traitement en cours...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                let form = document.getElementById('statusForm');
                form.action = "{{ url($slug.'/section') }}/" + id + "/status";
                form.submit();
            }

        });

    });
});
</script>

{{-- ADD MODAL --}}
<div class="modal fade" id="addSectionModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Ajout d'une nouvelle section</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('section.store', $slug) }}" method="POST" id="sectionForm">
                @csrf

                <div class="modal-body">
                    <label>Nom de la section</label>
                    <input type="text" name="section" class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button type="reset" class="btn btn-danger">Fermer</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editSectionModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Modifier la section</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editSectionForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <input type="hidden" id="section_id">

                    <label>Nom de la section</label>
                    <input type="text" id="edit_section" name="section" class="form-control">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
/**
 * OUVERTURE MODAL EDIT SECTION
 */
document.querySelectorAll('.editSectionBtn').forEach(button => {
    button.addEventListener('click', function () {

        let id = this.dataset.id;
        let section = this.dataset.section;

        document.getElementById('section_id').value = id;
        document.getElementById('edit_section').value = section;

        let form = document.getElementById('editSectionForm');
        form.action = "{{ url($slug.'/section') }}/" + id;

        new bootstrap.Modal(document.getElementById('editSectionModal')).show();
    });
});


/**
 * SUBMIT EDIT SECTION + LOADER
 */
document.getElementById('editSectionForm').addEventListener('submit', function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'Confirmation',
        text: 'Confirmez-vous la modification ?',
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


/**
 * SUBMIT ADD SECTION + LOADER
 */
document.getElementById('sectionForm').addEventListener('submit', function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'Confirmation',
        text: 'Confirmez-vous l\'ajout de la section ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui',
        cancelButtonText: 'Non'
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
@if(session('success'))
<script>
Swal.fire('Succès', '{{ session("success") }}', 'success');
</script>
@endif

@if(session('error'))
<script>
Swal.fire('Erreur', '{{ session("error") }}', 'error');
</script>
@endif

@endsection
