@extends('admin.layout.app')

@section('container')

<div class="dashboard-main-body">

    <!-- HEADER -->
    <div class="breadcrumb d-flex justify-content-between align-items-center mb-24">

        <div>
            <h1 class="h6 fw-semibold text-primary-light">
                Gestion des menus dynamiques
            </h1>
            <span class="text-secondary-light">
                Créer vos menus et sous-menus personnalisés
            </span>
        </div>

        <div>
            <a href="#" class="btn btn-outline-primary">
                ← Retour
            </a>
        </div>

    </div>

    <!-- GRID -->
    <div class="row g-4">

        <!-- CREATE MENU -->
        <div class="col-lg-4">
            <div class="card shadow-1 radius-12">
                <div class="card-header border-bottom">
                    <h6 class="mb-0">Créer un menu</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('menus.store') }}" method="POST">
                        @csrf
                        <!-- Nom menu -->
                        <div class="mb-3">
                            <label class="form-label">Nom du menu</label>
                            <input type="text"
                                name="nom_menu"
                                class="form-control"
                                placeholder="Ex: Élèves"
                                required>
                        </div>
                        <!-- Icône -->
                        <div class="mb-3">
                            <label class="form-label">Icône (Remix Icon)</label>
                            <input type="text"
                                name="icon_menu"
                                class="form-control"
                                placeholder="ri-user-line">
                        </div>
                        <!-- Lien -->
                        <div class="mb-3">
                            <label class="form-label">Lien (optionnel)</label>
                            <input type="text"
                                name="lien_menu"
                                class="form-control"
                                placeholder="/eleves">
                        </div>
                        <!-- Ordre -->
                        <div class="mb-3">
                            <label class="form-label">Ordre d’affichage</label>
                            <input type="number"
                                name="ordre_menu"
                                class="form-control"
                                value="0">
                        </div>
                        <!-- Parent menu -->
                        <div class="mb-3">
                            <label class="form-label">Menu parent (optionnel)</label>
                            <select name="parent_id" class="form-control">
                                <option value="">Menu principal</option>

                                @foreach(DB::table('tblmenus')->whereNull('parent_id')->orderBy('ordre_menu')->get() as $menu)
                                    <option value="{{ $menu->id }}">
                                        {{ $menu->nom_menu }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <!-- BUTTON -->
                        <button type="submit" class="btn btn-primary w-100">
                            Ajouter menu
                        </button>

                    </form>

                </div>

            </div>
        </div>

        <!-- CREATE SUBMENU -->
        <div class="col-lg-4">

            <div class="card shadow-1 radius-12">

                <div class="card-header border-bottom">
                    <h6 class="mb-0">Créer un sous-menu</h6>
                </div>

                <div class="card-body">

                    <form action="{{ route('sousmenus.store') }}" method="POST">
                        @csrf

                        <!-- MENU PARENT -->
                        <div class="mb-3">
                            <label class="form-label">Menu parent</label>

                            <select name="menu_id" class="form-control" required>
                                <option value="">-- Choisir menu --</option>

                                @foreach(DB::table('tblmenus')->whereNull('parent_id')->get() as $menu)
                                    <option value="{{ $menu->id }}">
                                        {{ $menu->nom_menu }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <!-- NOM -->
                        <div class="mb-3">
                            <label class="form-label">Nom sous-menu</label>
                            <input type="text"
                                name="nom_sousmenu"
                                class="form-control"
                                placeholder="Ex: Liste"
                                required>
                        </div>

                        <!-- ROUTE -->
                        <div class="mb-3">
                            <label class="form-label">Route</label>
                            <input type="text"
                                name="route_sousmenu"
                                class="form-control"
                                placeholder="/eleves/liste">
                        </div>

                        <!-- ICONE -->
                        <div class="mb-3">
                            <label class="form-label">Icône</label>
                            <input type="text"
                                name="icon_sousmenu"
                                class="form-control"
                                placeholder="ri-list-check">
                        </div>

                        <!-- ORDRE -->
                        <div class="mb-3">
                            <label class="form-label">Ordre</label>
                            <input type="number"
                                name="ordre_sousmenu"
                                class="form-control"
                                value="0">
                        </div>

                        <!-- BUTTON -->
                        <button type="submit" class="btn btn-success w-100">
                            Ajouter sous-menu
                        </button>

                    </form>

                </div>

            </div>

        </div>
        <!-- PREVIEW -->
        <div class="col-lg-4">
            <div class="card shadow-1 radius-12">

                <div class="card-header border-bottom">
                    <h6 class="mb-0">Aperçu du menu</h6>
                </div>

                <div class="card-body">

                    <div class="menu-preview">

                        @foreach($menus1 as $menu)

                            <div class="menu-item mb-2">

                                <!-- MENU PRINCIPAL -->
                                <div class="menu-title d-flex align-items-center justify-content-between p-2 bg-light radius-8"
                                    style="cursor:pointer;"
                                    onclick="toggleMenu(this)">

                                    <div>
                                        <i class="{{ data_get($menu, 'icon_menu') }} me-2"></i>
                                        {{ data_get($menu, 'nom_menu') }}
                                    </div>

                                    <i class="ri-arrow-down-s-line"></i>

                                </div>

                                <!-- SOUS MENUS -->
                                <ul class="submenu list-unstyled ms-4 mt-2">

                                    @foreach($sousmenus->where('menu_id', data_get($menu, 'id')) as $sub)

                                        <li class="py-1">
                                            <i class="{{ data_get($sub, 'icon_sousmenu') }} me-1"></i>
                                            {{ data_get($sub, 'nom_sousmenu') }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                            @endforeach

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function toggleMenu(el) {

        const submenu = el.nextElementSibling;
        const icon = el.querySelector('.ri-arrow-down-s-line');

        // toggle actif
        const isOpen = submenu.classList.contains('open');

        if (isOpen) {
            submenu.style.maxHeight = null;
            submenu.style.opacity = "0";
            submenu.style.transform = "translateY(-5px)";
            submenu.classList.remove('open');
        } else {
            submenu.style.maxHeight = submenu.scrollHeight + "px";
            submenu.style.opacity = "1";
            submenu.style.transform = "translateY(0)";
            submenu.classList.add('open');
        }

        if (icon) {
            icon.classList.toggle('rotate');
        }
    }
</script>
<style>
    .submenu {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transform: translateY(-5px);
        transition: all 0.35s ease;
    }

    .submenu.open {
        max-height: 300px;
        opacity: 1;
        transform: translateY(0);
    }

    /* icône rotation */
    .rotate {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }

    /* style menu */
    .menu-title {
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .menu-title:hover {
        background: #f3f6ff;
        border-radius: 8px;
    }
    </style>

@endsection
