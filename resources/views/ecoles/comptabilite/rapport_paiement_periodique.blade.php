@extends('ecoles.layout.app')
@section('containte')
<div class="dashboard-main-body">

    {{-- Breadcrumb --}}
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Rapport périodique des paiements</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Comptabilité / Rapport périodique</span>
            </div>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="ri-printer-line"></i> Imprimer
        </button>
    </div>

    {{-- Filtres --}}
    <div class="card mb-24">
        <div class="card-body">
            <form method="GET" action="{{ route('rapport.paiement.periodique', $slug) }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-sm fw-semibold">Période</label>
                    <div class="d-flex gap-2">
                        <input type="date" name="date_debut" class="form-control" value="{{ $date_debut }}">
                        <input type="date" name="date_fin"   class="form-control" value="{{ $date_fin }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-semibold">Année scolaire</label>
                    <select name="annee_scolaire" class="form-select">
                        @foreach ($data_anneescolaire as $a)
                            <option value="{{ $a->v_annesclaire }}" {{ $annee == $a->v_annesclaire ? 'selected' : '' }}>
                                {{ $a->v_annesclaire }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm fw-semibold">Classe</label>
                    <select name="classe_id" class="form-select">
                        <option value="">Toutes</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->i_classe_id }}" {{ $classe_id == $c->i_classe_id ? 'selected' : '' }}>
                                {{ $c->v_nom_classe }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ri-filter-line"></i> Filtrer
                    </button>
                    <a href="{{ route('rapport.paiement.periodique', $slug) }}" class="btn btn-light border btn-sm">
                        <i class="ri-refresh-line"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CADRE 1 : INSCRIPTION / REINSCRIPTION --}}
    {{-- ============================================================ --}}

    {{-- Récapitulatif — Inscription / Réinscription uniquement --}}
    @php
        $tauxInsc = $totalInscriptionDu > 0 ? round(($totalInscriptionPaye / $totalInscriptionDu) * 100, 1) : 0;
        $couleurTauxInsc = $tauxInsc >= 80 ? 'text-success-600' : ($tauxInsc >= 50 ? 'text-warning-600' : 'text-danger-600');
        $barreTauxInsc = $tauxInsc >= 80 ? 'bg-success' : ($tauxInsc >= 50 ? 'bg-warning' : 'bg-danger');
    @endphp
    <div class="row g-3 mb-16">
        <div class="col-12">
            <div class="text-secondary-light text-sm fw-semibold mb-8">
                <i class="ri-user-add-line me-4"></i> Récapitulatif — Inscription / Réinscription
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-primary-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Total encaissé</div>
                    <div class="h5 fw-semibold text-success-600 mb-0">
                        {{ number_format($totalInscriptionPaye, 0, ',', ' ') }} GNF
                    </div>
                    <small class="text-secondary-light">Inscription / Réinscription</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Total exonéré</div>
                    <div class="h5 fw-semibold text-warning-600 mb-0">
                        {{ number_format($totalInscriptionExo, 0, ',', ' ') }} GNF
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Total restant dû</div>
                    <div class="h5 fw-semibold text-danger-600 mb-0">
                        {{ number_format($totalInscriptionRestant, 0, ',', ' ') }} GNF
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Taux de recouvrement</div>
                    <div class="h5 fw-semibold {{ $couleurTauxInsc }} mb-8">{{ $tauxInsc }}%</div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar {{ $barreTauxInsc }}" style="width:{{ $tauxInsc }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-24">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
            <h6 class="fw-semibold mb-0">
                <i class="ri-user-add-line me-8 text-primary-600"></i>
                Inscription / Réinscription
            </h6>
            <div class="d-flex gap-16 text-sm">
                <span>Facturé : <strong>{{ number_format($totalInscriptionFacture, 0, ',', ' ') }} GNF</strong></span>
                <span class="text-warning-600">Exonéré : <strong>{{ number_format($totalInscriptionExo, 0, ',', ' ') }} GNF</strong></span>
                <span>Dû : <strong>{{ number_format($totalInscriptionDu, 0, ',', ' ') }} GNF</strong></span>
                <span class="text-success-600">Payé : <strong>{{ number_format($totalInscriptionPaye, 0, ',', ' ') }} GNF</strong></span>
                <span class="text-danger-600">Restant : <strong>{{ number_format($totalInscriptionRestant, 0, ',', ' ') }} GNF</strong></span>
            </div>
        </div>
        <div class="card-body p-0" style="overflow-x:auto;">
            <table class="table table-sm align-middle mb-0" style="min-width:900px;">
                <thead class="bg-neutral-50">
                    <tr>
                        <th>Élève</th>
                        <th>Matricule</th>
                        <th>Classe</th>
                        <th class="text-end">Facturé</th>
                        <th class="text-end text-warning-600">Exonération</th>
                        <th class="text-end">Dû</th>
                        <th class="text-end text-success-600">Payé</th>
                        <th class="text-end text-danger-600">Restant</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donneesEleves as $d)
                    <tr>
                        <td class="fw-semibold">{{ $d['nom'] }}</td>
                        <td>{{ $d['matricule'] }}</td>
                        <td>{{ $d['classe'] }}</td>
                        <td class="text-end">{{ number_format($d['insc_facture'], 0, ',', ' ') }}</td>
                        <td class="text-end text-warning-600">
                            @if($d['insc_exo'] > 0)
                                <span class="badge bg-warning-100 text-warning-600">
                                    -{{ number_format($d['insc_exo'], 0, ',', ' ') }}
                                </span>
                            @else — @endif
                        </td>
                        <td class="text-end">{{ number_format($d['insc_du'], 0, ',', ' ') }}</td>
                        <td class="text-end text-success-600">{{ number_format($d['insc_paye'], 0, ',', ' ') }}</td>
                        <td class="text-end">
                            @if($d['insc_restant'] > 0)
                                <span class="text-danger-600 fw-semibold">{{ number_format($d['insc_restant'], 0, ',', ' ') }}</span>
                            @else
                                <span class="text-success-600">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($d['insc_restant'] <= 0)
                                <span class="badge bg-success-100 text-success-600">Soldé</span>
                            @elseif($d['insc_paye'] > 0)
                                <span class="badge bg-warning-100 text-warning-600">Partiel</span>
                            @else
                                <span class="badge bg-danger-100 text-danger-600">Impayé</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-16 text-secondary-light">Aucun élève trouvé</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-neutral-50 fw-semibold">
                    <tr>
                        <td colspan="3">Totaux</td>
                        <td class="text-end">{{ number_format($totalInscriptionFacture, 0, ',', ' ') }}</td>
                        <td class="text-end text-warning-600">{{ number_format($totalInscriptionExo, 0, ',', ' ') }}</td>
                        <td class="text-end">{{ number_format($totalInscriptionDu, 0, ',', ' ') }}</td>
                        <td class="text-end text-success-600">{{ number_format($totalInscriptionPaye, 0, ',', ' ') }}</td>
                        <td class="text-end text-danger-600">{{ number_format($totalInscriptionRestant, 0, ',', ' ') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CADRE 2 : TRANCHES / MENSUELLE (SCOLARITÉ) --}}
    {{-- ============================================================ --}}

    {{-- Récapitulatif — Scolarité uniquement (mensuelle / tranches / annuelle) --}}
    @php
        $tauxScol = $totalTrancheDu > 0 ? round(($totalTranchePaye / $totalTrancheDu) * 100, 1) : 0;
        $couleurTauxScol = $tauxScol >= 80 ? 'text-success-600' : ($tauxScol >= 50 ? 'text-warning-600' : 'text-danger-600');
        $barreTauxScol = $tauxScol >= 80 ? 'bg-success' : ($tauxScol >= 50 ? 'bg-warning' : 'bg-danger');
    @endphp
    <div class="row g-3 mb-16">
        <div class="col-12">
            <div class="text-secondary-light text-sm fw-semibold mb-8">
                <i class="ri-bank-card-line me-4"></i> Récapitulatif — Scolarité (Mensuelle / Tranches / Annuelle)
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-primary-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Total encaissé</div>
                    <div class="h5 fw-semibold text-success-600 mb-0">
                        {{ number_format($totalTranchePaye, 0, ',', ' ') }} GNF
                    </div>
                    <small class="text-secondary-light">Scolarité</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Total exonéré</div>
                    <div class="h5 fw-semibold text-warning-600 mb-0">
                        {{ number_format($totalTrancheExo, 0, ',', ' ') }} GNF
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Total restant dû</div>
                    <div class="h5 fw-semibold text-danger-600 mb-0">
                        {{ number_format($totalTrancheRestant, 0, ',', ' ') }} GNF
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light text-sm mb-4">Taux de recouvrement</div>
                    <div class="h5 fw-semibold {{ $couleurTauxScol }} mb-8">{{ $tauxScol }}%</div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar {{ $barreTauxScol }}" style="width:{{ $tauxScol }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-24">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
            <h6 class="fw-semibold mb-0">
                <i class="ri-bank-card-line me-8 text-primary-600"></i>
                Paiement scolarité (Mensuelle / Tranches / Annuelle)
            </h6>
            <div class="d-flex gap-16 text-sm">
                <span>Facturé : <strong>{{ number_format($totalTrancheFacture, 0, ',', ' ') }} GNF</strong></span>
                <span class="text-warning-600">Exonéré : <strong>{{ number_format($totalTrancheExo, 0, ',', ' ') }} GNF</strong></span>
                <span>Dû : <strong>{{ number_format($totalTrancheDu, 0, ',', ' ') }} GNF</strong></span>
                <span class="text-success-600">Payé : <strong>{{ number_format($totalTranchePaye, 0, ',', ' ') }} GNF</strong></span>
                <span class="text-danger-600">Restant : <strong>{{ number_format($totalTrancheRestant, 0, ',', ' ') }} GNF</strong></span>
            </div>
        </div>
        <div class="card-body p-0" style="overflow-x:auto;">
            <table class="table table-sm align-middle mb-0" style="min-width:950px;">
                <thead class="bg-neutral-50">
                    <tr>
                        <th>Élève</th>
                        <th>Matricule</th>
                        <th>Classe</th>
                        <th>Mode</th>
                        <th class="text-end">Facturé</th>
                        <th class="text-end text-warning-600">Exonération</th>
                        <th class="text-end">Dû</th>
                        <th class="text-end text-success-600">Payé</th>
                        <th class="text-end text-danger-600">Restant</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donneesEleves as $d)
                    @if($d['mode_tranche'])
                    <tr>
                        <td class="fw-semibold">{{ $d['nom'] }}</td>
                        <td>{{ $d['matricule'] }}</td>
                        <td>{{ $d['classe'] }}</td>
                        <td>
                            <span class="badge bg-primary-100 text-primary-600 text-xs">
                                {{ $d['detail_mode'] }}
                            </span>
                        </td>
                        <td class="text-end">{{ number_format($d['tr_facture'], 0, ',', ' ') }}</td>
                        <td class="text-end">
                            @if($d['tr_exo'] > 0)
                                <span class="badge bg-warning-100 text-warning-600">
                                    -{{ number_format($d['tr_exo'], 0, ',', ' ') }}
                                </span>
                            @else — @endif
                        </td>
                        <td class="text-end">{{ number_format($d['tr_du'], 0, ',', ' ') }}</td>
                        <td class="text-end text-success-600">{{ number_format($d['tr_paye'], 0, ',', ' ') }}</td>
                        <td class="text-end">
                            @if($d['tr_restant'] > 0)
                                <span class="text-danger-600 fw-semibold">{{ number_format($d['tr_restant'], 0, ',', ' ') }}</span>
                            @else
                                <span class="text-success-600">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($d['tr_restant'] <= 0)
                                <span class="badge bg-success-100 text-success-600">Soldé</span>
                            @elseif($d['tr_paye'] > 0)
                                <span class="badge bg-warning-100 text-warning-600">Partiel</span>
                            @else
                                <span class="badge bg-danger-100 text-danger-600">Impayé</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="10" class="text-center py-16 text-secondary-light">Aucun élève trouvé</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-neutral-50 fw-semibold">
                    <tr>
                        <td colspan="4">Totaux</td>
                        <td class="text-end">{{ number_format($totalTrancheFacture, 0, ',', ' ') }}</td>
                        <td class="text-end text-warning-600">{{ number_format($totalTrancheExo, 0, ',', ' ') }}</td>
                        <td class="text-end">{{ number_format($totalTrancheDu, 0, ',', ' ') }}</td>
                        <td class="text-end text-success-600">{{ number_format($totalTranchePaye, 0, ',', ' ') }}</td>
                        <td class="text-end text-danger-600">{{ number_format($totalTrancheRestant, 0, ',', ' ') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
