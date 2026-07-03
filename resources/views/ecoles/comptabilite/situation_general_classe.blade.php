@extends('ecoles.layout.app')

@section('containte')

<div class="dashboard-main-body">

    {{-- ===========================
        FIL D'ARIANE
    ============================--}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">

        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">
                Situation générale des paiements par classe
            </h1>

            <div>
                <a href="#" class="text-secondary-light hover-text-primary hover-underline">
                    Accueil
                </a>

                <span class="text-secondary-light">
                    / Comptabilité / Situation générale des paiements
                </span>
            </div>
        </div>

        <div class="d-flex gap-2">

            <button class="btn btn-success-600">
                <iconify-icon icon="solar:file-download-outline"></iconify-icon>
                Export Excel
            </button>

            <button class="btn btn-danger-600">
                <iconify-icon icon="solar:file-download-outline"></iconify-icon>
                Export PDF
            </button>

            <button class="btn btn-primary-600">
                <iconify-icon icon="solar:printer-outline"></iconify-icon>
                Imprimer
            </button>

        </div>

    </div>


    {{-- ===========================
        FILTRES
    ============================--}}
    <div class="card mb-24">

        <div class="card-header">
            <h6 class="mb-0">
                Filtres
            </h6>
        </div>

        <div class="card-body">

            <div class="row gy-3">

                <div class="col-lg-3">
                    <label>Année scolaire</label>

                    <select class="form-control form-select">
                        <option>2026 - 2027</option>
                    </select>

                </div>

                <div class="col-lg-3">
                    <label>Niveau</label>

                    <select class="form-control form-select">
                        <option>Tous les niveaux</option>
                    </select>

                </div>

                <div class="col-lg-3">
                    <label>Classe</label>

                    <select class="form-control form-select">
                        <option>Toutes les classes</option>
                    </select>

                </div>

                <div class="col-lg-3 d-flex align-items-end">

                    <button class="btn btn-primary-600 me-2">
                        <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                        Rechercher
                    </button>

                    <button class="btn btn-outline-secondary">
                        Réinitialiser
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ===========================
        INDICATEURS
    ============================--}}
    <div class="row gy-4 mb-24">

        <div class="col-lg-3">

            <div class="card">

                <div class="card-body">

                    <h6>Classes</h6>

                    <h3>18</h3>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="card">

                <div class="card-body">

                    <h6>Élèves inscrits</h6>

                    <h3>624</h3>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="card">

                <div class="card-body">

                    <h6>Total attendu</h6>

                    <h3 class="text-primary">
                        425 000 000 GNF
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="card">

                <div class="card-body">

                    <h6>Total encaissé</h6>

                    <h3 class="text-success">
                        318 000 000 GNF
                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- ===========================
        TABLEAU
    ============================--}}
    <div class="card">

        <div class="card-header">

            <h6 class="mb-0">
                Situation des paiements par classe
            </h6>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table bordered-table">

                    <thead>

                    <tr>

                        <th>Niveau</th>

                        <th>Classe</th>

                        <th>Effectif</th>

                        <th>Total attendu</th>

                        <th>Total payé</th>

                        <th>Reste à payer</th>

                        <th>Taux</th>

                        <th>Élèves à jour</th>

                        <th>Élèves débiteurs</th>

                        <th>Statut</th>

                    </tr>

                    </thead>

                    <tbody>

                    <tr>

                        <td>6ème</td>

                        <td>6ème A</td>

                        <td>42</td>

                        <td>25 200 000</td>

                        <td class="text-success">
                            20 000 000
                        </td>

                        <td class="text-danger">
                            5 200 000
                        </td>

                        <td>

                            <div class="progress">

                                <div class="progress-bar bg-success"
                                     style="width:80%">

                                    80%

                                </div>

                            </div>

                        </td>

                        <td>

                            34

                        </td>

                        <td>

                            8

                        </td>

                        <td>

                            <span class="badge bg-warning">

                                Moyen

                            </span>

                        </td>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ===========================
        RESUME
    ============================--}}
    <div class="row mt-24">

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h6>Classes les plus performantes</h6>

                    <hr>

                    <p>CM2 A : 100%</p>

                    <p>CM2 B : 98%</p>

                    <p>5ème A : 95%</p>

                    <p>3ème A : 93%</p>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h6>Classes à suivre</h6>

                    <hr>

                    <p>6ème C : 68%</p>

                    <p>4ème B : 64%</p>

                    <p>2nde A : 58%</p>

                    <p>Tle SM : 52%</p>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h6>Résumé général</h6>

                    <hr>

                    <p>Total attendu : 425 000 000 GNF</p>

                    <p>Total encaissé : 318 000 000 GNF</p>

                    <p>Reste à encaisser : 107 000 000 GNF</p>

                    <p>Taux global : 74,8 %</p>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
