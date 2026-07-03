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
                Liste des Utilisateurs
            </h1>

            <div>
                <a href="{{ route('index.home.admin') }}"
                   class="text-secondary-light hover-text-primary hover-underline">

                    Dashboard

                </a>

                <span class="text-secondary-light">
                    / Utilisateurs
                </span>
            </div>
        </div>
        <!-- Button -->
        <a href="{{ route('add.users') }}" class="btn btn-primary-600 d-flex align-items-center gap-6">
            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>
            Ajouter un utilisateur
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
                            <th>Photo</th>
                            <th>Nom & Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Service</th>
                            <th>État</th>
                            <th>Date création</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key => $user)
                            <tr>
                                <!-- # -->
                                <td>{{ $key + 1 }}</td>
                                <!-- PHOTO -->
                               <td>
                                    @if($user->logo)
                                        <img src="{{ asset($user->logo) }}"
                                            width="40"
                                            height="40"
                                            style="object-fit: cover; border-radius: 6px;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light"
                                            style="width:40px; height:40px; border-radius:6px;">
                                            <i class="ri-user-3-line text-muted fz-24"></i>
                                        </div>
                                    @endif
                                </td>
                                <!-- NAME -->
                                <td>{{ $user->name }}</td>

                                <!-- EMAIL -->
                                <td>{{ $user->email }}</td>

                                <!-- TELEPHONE -->
                                <td>{{ $user->telephone }}</td>

                                <!-- ROLE -->
                                <td>
                                    @if($user->roles == 0)
                                        <span class="badge bg-danger">Administrateur</span>
                                    @elseif($user->roles == 1)
                                        <span class="badge bg-primary">Fondateur</span>
                                    @else
                                        <span class="badge bg-success">Utilisateur</span>
                                    @endif
                                </td>

                                <!-- SERVICE (école / établissement) -->
                                <td>
                                    {{ $user->etablissement_id }}
                                </td>

                                <!-- ETAT -->
                                <td>
                                    @if($user->del_user == 0)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Supprimé</span>
                                    @endif
                                </td>

                                <!-- DATE -->
                                <td>
                                    {{ $user->created_at }}
                                </td>

                                <!-- ACTION -->
                                <td class="text-nowrap">
                                    <!-- Voir -->
                                    <button class="btn btn-sm btn-primary">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <!-- Modifier -->
                                    <button class="btn btn-sm btn-warning">
                                        <i class="ri-edit-2-line"></i>
                                    </button>
                                    <!-- Supprimer -->
                                    <button class="btn btn-sm btn-danger">
                                        <i class="ri-delete-bin-6-line"></i>
                                    </button>
                                    <!-- Settings -->
                                  <form action="{{ route('users.menus') }}" method="GET" class="d-inline">
                                    <input value="{{ $user->id}}" name="id" type="hidden">
                                    <button type="submit" class="btn btn-sm btn-dark">
                                        <i class="ri-settings-3-line"></i>
                                    </button>

                                </form>
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
