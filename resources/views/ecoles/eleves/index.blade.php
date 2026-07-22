@extends('ecoles.layout.app')
@section('containte')






    <div class="dashboard-main-body">

        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
  <div>
    <h1 class="fw-semibold mb-4 h6 text-primary-light">
      Student List    </h1>

    <div>
      <a href="index-2.html" class="text-secondary-light hover-text-primary hover-underline">
        Dashboard
      </a>

              <a href="javascript:void(0)"
          class="text-secondary-light hover-text-primary hover-underline d-none">
          / Student        </a>

      <span class="text-secondary-light">
        / Student List      </span>
    </div>
  </div>

    @if(\App\Helpers\PermissionHelper::hasRoute('students.create'))

    <a href="{{ route('students.create', session('slug_ecole')) }}"
       class="btn btn-primary-600 d-flex align-items-center gap-6">

        <span class="d-flex text-md">
            <i class="ri-add-large-line"></i>
        </span>

        Add élève
    </a>

@endif
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
                            <div class="dropdown">
                                <button type="button"
                                    class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                                        Filter
                                    </span>
                                    <span class="">
                                        <i class="ri-arrow-down-s-line"></i>
                                    </span>
                                </button>
                                <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                                    <div
                                        class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                                        <span class="fw-semibold text-lg text-primary-light">Filter</span>
                                        <button type="button">
                                            <i class="ri-close-large-line"></i>
                                        </button>
                                    </div>

                                    <form action="#" class="p-16 d-grid grid-cols-2 gap-16">
                                        <div class="">
                                            <label for="class"
                                                class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Class</label>
                                            <select id="class" class="form-control form-select">
                                                <option value="Select" disabled>Select Class</option>
                                                <option value="Primary">Primary</option>
                                                <option value="SSC">SSC</option>
                                                <option value="HSC">HSC</option>
                                                <option value="Hons">Hons</option>
                                                <option value="Masters">Masters</option>
                                            </select>
                                        </div>
                                        <div class="">
                                            <label for="section"
                                                class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
                                            <select id="section" class="form-control form-select">
                                                <option value="Select">Select Section</option>
                                                <option value="Arts">Arts</option>
                                                <option value="Science">Science</option>
                                                <option value="Commerce">Commerce</option>
                                            </select>
                                        </div>
                                        <div class="">
                                            <label for="gender"
                                                class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Gender</label>
                                            <select id="gender" class="form-control form-select">
                                                <option value="Select">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="">
                                            <label for="status"
                                                class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Status</label>
                                            <select id="status" class="form-control form-select">
                                                <option value="Select">Select Status</option>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="">
                                            <button type="reset"
                                                class="btn btn-danger-200 text-danger-600 w-100">Reset</button>
                                        </div>
                                        <div class="">
                                            <button type="submit" class="btn btn-primary-600 w-100">Apply</button>
                                        </div>
                                    </form>
                                </div>
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

                    <div class="p-0">
                        <table class="table bordered-table mb-0 data-table" id="dataTable" data-page-length="10">

    <thead>
        <tr>
            <th>
                <div class="form-check style-check d-flex align-items-center">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label">S.L</label>
                </div>
            </th>

            <th>Matricule</th>
            <th>Élève</th>
            <th>Classe</th>
            <th>Date de naissance</th>
            <th>Genre</th>
            <th>Téléphone</th>
            <th>Parent</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

        @foreach($eleves as $key => $eleve)
        <tr>

            <td>
                <div class="form-check style-check d-flex align-items-center">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label">
                        {{ $key + 1 }}
                    </label>
                </div>
            </td>

            <td>
                <span class="text-primary-600">
                    {{ $eleve->v_matricule }}
                </span>
            </td>

            <td>
                <div class="d-flex align-items-center">

                    <img src="{{ $eleve->v_photo ? asset($eleve->v_photo) : asset('assets/images/thumbs/avatar-img1.png') }}"
                         alt="image"
                         class="flex-shrink-0 me-12 radius-8"
                         width="40">

                    <div>
                        <h6 class="text-md mb-0 fw-medium">
                            {{ $eleve->v_nom }} {{ $eleve->v_prenom }}
                        </h6>

                        <span class="text-sm text-muted">
                            ID: {{ $eleve->i_eleve_id }}
                        </span>
                    </div>

                </div>
            </td>

            <td>
                {{ $eleve->classe_name ?? '---' }}
            </td>

            <td>
                {{ $eleve->d_date_naissance }}
            </td>

            <td>
                {{ $eleve->v_genre }}
            </td>

            <td>
                {{ $eleve->v_telephone ?? '---' }}
            </td>

            <td>
                {{ $eleve->parent_name ?? 'Non défini' }}
            </td>

            <td>
                @if($eleve->b_desabled == 1)
                    <span class="bg-success-100 text-success-600 px-12 py-4 radius-4 fw-medium text-sm">
                        Actif
                    </span>
                @else
                    <span class="bg-danger-100 text-danger-600 px-12 py-4 radius-4 fw-medium text-sm">
                        Inactif
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
                        @foreach($actionsEleve as $action)
                            <li>
                                @if($action->route_sousmenu)
                                    <a href="{{ route($action->route_sousmenu, [$slug, $eleve->i_eleve_id]) }}"
                                    class="dropdown-item d-flex align-items-center gap-2 py-6">
                                        <i class="{{ $action->icon_sousmenu }}"></i>
                                        {{ $action->nom_sousmenu }}
                                    </a>
                                @else
                                    {{-- Action sans route (ex: modal, JS, paiement, etc.) --}}
                                    <button type="button"
                                            class="dropdown-item d-flex align-items-center gap-2 py-6"
                                            data-action="{{ $action->id }}">

                                        <i class="{{ $action->icon_sousmenu }}"></i>
                                        {{ $action->nom_sousmenu }}

                                    </button>
                                @endif
                            </li>
                        @endforeach
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
