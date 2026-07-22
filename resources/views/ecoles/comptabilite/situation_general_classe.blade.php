@extends('ecoles.layout.app')

@section('containte')

<div class="dashboard-main-body sgp">

<style>
    .sgp {
        --sgp-ink: #1e2432;
        --sgp-ink-soft: #6b7280;
        --sgp-border: #e6e9f2;
        --sgp-bg-soft: #f7f8fb;

        --sgp-primary: #3958d8;
        --sgp-primary-soft: #eef1fd;

        --sgp-success: #17945d;
        --sgp-success-soft: #e8f7f0;

        --sgp-warning: #b3760a;
        --sgp-warning-soft: #fdf3df;

        --sgp-danger: #c5392b;
        --sgp-danger-soft: #fdedec;
    }

    /* ===== Cartes générales, plus douces ===== */
    .sgp .card {
        border: 1px solid var(--sgp-border);

        box-shadow: 0 1px 2px rgba(16, 24, 40, .03), 0 2px 8px rgba(16, 24, 40, .04);
    }
    .sgp .card-header {
        background: transparent;
        border-bottom: 1px solid var(--sgp-border);
        padding: 16px 22px;
    }
    .sgp .card-header h6 {
        font-weight: 600;
        color: var(--sgp-ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sgp .card-body { padding: 22px; }

    /* ===== Fil d'Ariane ===== */
    .sgp-toolbar {
        background: #fff;
        border: 1px solid var(--sgp-border);

        padding: 18px 22px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .03);
    }
    .sgp-toolbar h1 {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--sgp-ink);
    }
    .sgp-toolbar h1 iconify-icon {
        color: var(--sgp-primary);
        font-size: 20px;
    }
    .sgp-crumb iconify-icon {
        font-size: 14px;
        vertical-align: -2px;
        margin: 0 4px;
        color: var(--sgp-ink-soft);
    }
    .sgp-toolbar .btn {

        font-weight: 500;
        font-size: 13.5px;
    }

    /* ===== Filtres ===== */
    .sgp .form-label,
    .sgp label {
        font-size: 12.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--sgp-ink-soft);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .sgp .form-select,
    .sgp .form-control {

        border: 1px solid var(--sgp-border);
        background: var(--sgp-bg-soft);
        padding: 9px 14px;
        font-size: 14px;
    }
    .sgp .form-select:focus,
    .sgp .form-control:focus {
        background: #fff;
        border-color: var(--sgp-primary);
        box-shadow: 0 0 0 3px var(--sgp-primary-soft);
    }
    .sgp-btn-primary {
        background: var(--sgp-primary);
        border: none;
        color: #fff;

        padding: 9px 20px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .sgp-btn-primary:hover { opacity: .92; color: #fff; }
    .sgp-btn-ghost {
        background: var(--sgp-bg-soft);
        border: 1px solid var(--sgp-border);
        color: var(--sgp-ink-soft);

        padding: 9px 18px;
        font-size: 13.5px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .sgp-btn-ghost:hover { background: #eef0f4; color: var(--sgp-ink); }

    /* ===== Cartes indicateurs (KPI) ===== */
    .sgp-kpi {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
    }
    .sgp-kpi-icon {
        flex-shrink: 0;
        width: 48px;
        height: 48px;

        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .sgp-kpi-icon.primary { background: var(--sgp-primary-soft); color: var(--sgp-primary); }
    .sgp-kpi-icon.success { background: var(--sgp-success-soft); color: var(--sgp-success); }
    .sgp-kpi-icon.warning { background: var(--sgp-warning-soft); color: var(--sgp-warning); }
    .sgp-kpi-label {
        font-size: 12.5px;
        color: var(--sgp-ink-soft);
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .sgp-kpi-value {
        font-size: 22px;
        font-weight: 700;
        color: var(--sgp-ink);
        margin: 0;
        line-height: 1.2;
    }

    /* ===== Tableau ===== */
    .sgp-table-wrap {

        overflow: hidden;
        border: 1px solid var(--sgp-border);
    }
    .sgp .table.bordered-table {
        margin-bottom: 0;
        font-size: 13.5px;
    }
    .sgp .table.bordered-table thead th {
        background: var(--sgp-bg-soft);
        color: var(--sgp-ink-soft);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
        border-bottom: 1px solid var(--sgp-border);
        padding: 12px 14px;
        white-space: nowrap;
    }
    .sgp .table.bordered-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        border-bottom: 1px solid var(--sgp-border);
        color: var(--sgp-ink);
    }
    .sgp .table.bordered-table tbody tr:last-child td { border-bottom: none; }
    .sgp .table.bordered-table tbody tr:hover { background: var(--sgp-bg-soft); }

    .sgp-pill-niveau {
        display: inline-block;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--sgp-primary);
        background: var(--sgp-primary-soft);

        padding: 3px 10px;
    }
    .sgp-classe-nom { font-weight: 600; }

    .sgp-taux-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 130px;
    }
    .sgp-taux-cell .track {
        flex: 1;
        height: 7px;

        background: var(--sgp-border);
        overflow: hidden;
    }
    .sgp-taux-cell .fill {
        height: 100%;

    }
    .sgp-taux-cell .fill.success { background: var(--sgp-success); }
    .sgp-taux-cell .fill.warning { background: var(--sgp-warning); }
    .sgp-taux-cell .fill.danger  { background: var(--sgp-danger); }
    .sgp-taux-cell .pct {
        font-weight: 700;
        font-size: 12.5px;
        min-width: 38px;
        text-align: right;
    }
    .sgp-taux-cell .pct.success { color: var(--sgp-success); }
    .sgp-taux-cell .pct.warning { color: var(--sgp-warning); }
    .sgp-taux-cell .pct.danger  { color: var(--sgp-danger); }

    .sgp-effectif-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        font-size: 13px;
    }
    .sgp-effectif-dot .dot {
        width: 7px;
        height: 7px;

    }
    .sgp-effectif-dot .dot.success { background: var(--sgp-success); }
    .sgp-effectif-dot .dot.danger  { background: var(--sgp-danger); }

    .sgp-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 11px;

    }
    .sgp-badge::before {
        content: '';
        width: 6px;
        height: 6px;

    }
    .sgp-badge.success { background: var(--sgp-success-soft); color: var(--sgp-success); }
    .sgp-badge.success::before { background: var(--sgp-success); }
    .sgp-badge.warning { background: var(--sgp-warning-soft); color: var(--sgp-warning); }
    .sgp-badge.warning::before { background: var(--sgp-warning); }
    .sgp-badge.danger  { background: var(--sgp-danger-soft);  color: var(--sgp-danger); }
    .sgp-badge.danger::before  { background: var(--sgp-danger); }

    .sgp-montant-paye  { color: var(--sgp-success); font-weight: 600; }
    .sgp-montant-reste { color: var(--sgp-danger);  font-weight: 600; }

    /* ===== Cartes résumé ===== */
    .sgp-resume-card .card-body { padding: 20px 22px; }
    .sgp-resume-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--sgp-ink);
        font-size: 14px;
        margin-bottom: 14px;
    }
    .sgp-resume-title iconify-icon { color: var(--sgp-primary); font-size: 17px; }

    .sgp-rank-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px solid var(--sgp-border);
        font-size: 13.5px;
    }
    .sgp-rank-row:last-child { border-bottom: none; }
    .sgp-rank-row .nom { color: var(--sgp-ink); font-weight: 500; }
    .sgp-rank-row .val { font-weight: 700; }
    .sgp-rank-row .val.success { color: var(--sgp-success); }
    .sgp-rank-row .val.warning { color: var(--sgp-warning); }
    .sgp-rank-row .val.danger  { color: var(--sgp-danger); }
    .sgp-empty { color: var(--sgp-ink-soft); font-size: 13px; font-style: italic; }

    .sgp-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px solid var(--sgp-border);
        font-size: 13.5px;
    }
    .sgp-summary-row:last-child { border-bottom: none; }
    .sgp-summary-row .label { color: var(--sgp-ink-soft); }
    .sgp-summary-row .value { font-weight: 700; color: var(--sgp-ink); }
    .sgp-summary-row .value.primary { color: var(--sgp-primary); }
    .sgp-summary-row .value.success { color: var(--sgp-success); }
    .sgp-summary-row .value.danger  { color: var(--sgp-danger); }

    @media print {
        .sgp-toolbar .d-flex.gap-2 { display: none !important; }
    }
</style>

    {{-- ===========================
        FIL D'ARIANE
    ============================--}}

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

            <button onclick="window.print()" class="btn btn-primary-600">
                <iconify-icon icon="solar:printer-outline"></iconify-icon>
                Imprimer
            </button>

        </div>

    </div>


    {{-- ===========================
        FILTRES
    ============================--}}
    <div class="card mb-24 mt-24">

        <div class="card-header">
            <h6 class="mb-0">
                <iconify-icon icon="solar:tuning-2-outline"></iconify-icon>
                Filtres
            </h6>
        </div>

        <div class="card-body">

            <form method="GET" action="{{ route('situation.general.classe', $slug) }}">

                <div class="row gy-3">

                    <div class="col-lg-3">
                        <label>
                            <iconify-icon icon="solar:calendar-outline"></iconify-icon>
                            Année scolaire
                        </label>

                        <select name="annee_scolaire" class="form-control form-select">
                            @foreach ($data_anneescolaire as $a)
                                <option value="{{ $a->v_annesclaire }}" {{ $annee == $a->v_annesclaire ? 'selected' : '' }}>
                                    {{ $a->v_annesclaire }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-lg-3">
                        <label>
                            <iconify-icon icon="solar:layers-outline"></iconify-icon>
                            Niveau
                        </label>

                        <select name="niveau_id" class="form-control form-select">
                            <option value="">Tous les niveaux</option>
                            @foreach ($niveaux as $n)
                                <option value="{{ $n->i_niveauID }}" {{ $niveau_id == $n->i_niveauID ? 'selected' : '' }}>
                                    {{ $n->v_niveaux }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-lg-3">
                        <label>
                            <iconify-icon icon="solar:users-group-rounded-outline"></iconify-icon>
                            Classe
                        </label>

                        <select name="classe_id" class="form-control form-select">
                            <option value="">Toutes les classes</option>
                            @foreach ($toutesLesClasses as $c)
                                <option value="{{ $c->i_classe_id }}" {{ $classe_id == $c->i_classe_id ? 'selected' : '' }}>
                                    {{ $c->v_nom_classe }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-lg-3 d-flex align-items-end">

                        <button type="submit" class="sgp-btn-primary me-2">
                            <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                            Rechercher
                        </button>

                        <a href="{{ route('situation.general.classe', $slug) }}" class="sgp-btn-ghost">
                            <iconify-icon icon="solar:restart-outline"></iconify-icon>
                            Réinitialiser
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ===========================
        INDICATEURS
    ============================--}}
    <div class="row gy-4 mb-24">

        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="sgp-kpi">
                    <div class="sgp-kpi-icon primary">
                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                    </div>
                    <div>
                        <div class="sgp-kpi-label">Classes</div>
                        <h3 style="font-size: 14px!important" class="sgp-kpi-value">{{ count($situationClasses) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="sgp-kpi">
                    <div class="sgp-kpi-icon primary">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                    </div>
                    <div>
                        <div class="sgp-kpi-label">Élèves inscrits</div>
                        <h3 style="font-size: 14px!important" class="sgp-kpi-value">{{ $totalEffectifGlobal }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="sgp-kpi">
                    <div class="sgp-kpi-icon primary">
                        <iconify-icon icon="solar:wallet-outline"></iconify-icon>
                    </div>
                    <div>
                        <div class="sgp-kpi-label">Total attendu</div>
                        <h3 style="font-size: 14px!important" class="sgp-kpi-value">{{ number_format($totalAttenduGlobal, 0, ',', ' ') }} <span style="font-size:13px;font-weight:600;color:var(--sgp-ink-soft);">GNF</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="sgp-kpi">
                    <div class="sgp-kpi-icon success">
                        <iconify-icon icon="solar:hand-money-bold-duotone"></iconify-icon>
                    </div>
                    <div>
                        <div class="sgp-kpi-label">Total encaissé</div>
                        <h3 style="font-size: 14px!important" class="sgp-kpi-value">{{ number_format($totalPayeGlobal, 0, ',', ' ') }} <span style="font-size:13px;font-weight:600;color:var(--sgp-ink-soft);">GNF</span></h3>
                    </div>
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
                <iconify-icon icon="solar:document-text-outline"></iconify-icon>
                Situation des paiements par classe
            </h6>

        </div>

        <div class="card-body">

            <div class="sgp-table-wrap table-responsive">

                <table class="table bordered-table">

                    <thead>

                    <tr>

                        <th>Niveau</th>

                        <th>Classe</th>

                        <th>Effectif</th>

                        <th class="text-end">Total attendu</th>

                        <th class="text-end">Total payé</th>

                        <th class="text-end">Reste à payer</th>

                        <th>Taux</th>

                        <th class="text-center">À jour</th>

                        <th class="text-center">Débiteurs</th>

                        <th class="text-center">Statut</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse ($situationClasses as $s)

                    <tr>

                        <td><span class="sgp-pill-niveau">{{ $s['niveau'] }}</span></td>

                        <td><span class="sgp-classe-nom">{{ $s['classe'] }}</span></td>

                        <td>{{ $s['effectif'] }}</td>

                        <td class="text-end">{{ number_format($s['total_attendu'], 0, ',', ' ') }}</td>

                        <td class="text-end">
                            <span class="sgp-montant-paye">{{ number_format($s['total_paye'], 0, ',', ' ') }}</span>
                        </td>

                        <td class="text-end">
                            <span class="sgp-montant-reste">{{ number_format($s['reste'], 0, ',', ' ') }}</span>
                        </td>

                        <td>
                            <div class="sgp-taux-cell">
                                <div class="track">
                                    <div class="fill {{ $s['statut_class'] }}" style="width:{{ $s['taux'] }}%"></div>
                                </div>
                                <span class="pct {{ $s['statut_class'] }}">{{ $s['taux'] }}%</span>
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="sgp-effectif-dot">
                                <span class="dot success"></span>
                                {{ $s['eleves_a_jour'] }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="sgp-effectif-dot">
                                <span class="dot danger"></span>
                                {{ $s['eleves_debiteurs'] }}
                            </span>
                        </td>

                        <td class="text-center">

                            <span class="sgp-badge {{ $s['statut_class'] }}">
                                {{ $s['statut_label'] }}
                            </span>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="10" class="text-center py-16 text-secondary-light">
                            Aucune classe trouvée pour ces critères
                        </td>
                    </tr>

                    @endforelse

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

            <div class="card sgp-resume-card h-100">

                <div class="card-body">

                    <div class="sgp-resume-title">
                        <iconify-icon icon="solar:cup-star-bold-duotone"></iconify-icon>
                        Classes les plus performantes
                    </div>

                    @forelse ($classesPerformantes as $c)
                        <div class="sgp-rank-row">
                            <span class="nom">{{ $c['classe'] }}</span>
                            <span class="val {{ $c['statut_class'] }}">{{ $c['taux'] }}%</span>
                        </div>
                    @empty
                        <p class="sgp-empty">Aucune donnée</p>
                    @endforelse

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card sgp-resume-card h-100">

                <div class="card-body">

                    <div class="sgp-resume-title">
                        <iconify-icon icon="solar:danger-triangle-bold-duotone"></iconify-icon>
                        Classes à suivre
                    </div>

                    @forelse ($classesASuivre as $c)
                        <div class="sgp-rank-row">
                            <span class="nom">{{ $c['classe'] }}</span>
                            <span class="val {{ $c['statut_class'] }}">{{ $c['taux'] }}%</span>
                        </div>
                    @empty
                        <p class="sgp-empty">Aucune donnée</p>
                    @endforelse

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card sgp-resume-card h-100">

                <div class="card-body">

                    <div class="sgp-resume-title">
                        <iconify-icon icon="solar:pie-chart-3-bold-duotone"></iconify-icon>
                        Résumé général
                    </div>

                    <div class="sgp-summary-row">
                        <span class="label">Total attendu</span>
                        <span class="value primary">{{ number_format($totalAttenduGlobal, 0, ',', ' ') }} GNF</span>
                    </div>

                    <div class="sgp-summary-row">
                        <span class="label">Total encaissé</span>
                        <span class="value success">{{ number_format($totalPayeGlobal, 0, ',', ' ') }} GNF</span>
                    </div>

                    <div class="sgp-summary-row">
                        <span class="label">Reste à encaisser</span>
                        <span class="value danger">{{ number_format($totalResteGlobal, 0, ',', ' ') }} GNF</span>
                    </div>

                    <div class="sgp-summary-row">
                        <span class="label">Taux global</span>
                        <span class="value">{{ $tauxGlobal }} %</span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
