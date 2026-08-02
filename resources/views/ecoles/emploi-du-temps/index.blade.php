@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>

<style>
    .jour-colonne { min-width: 280px; flex: 1 1 0; }
    .jour-header { background: #eff6ff; padding: 10px; border-radius: 8px 8px 0 0; font-weight: 700; text-align: center; color: #1d4ed8; border: 1px solid #dbeafe; }
    .jour-body { border: 1px solid #dbeafe; border-top: none; border-radius: 0 0 8px 8px; min-height: 120px; padding: 8px; background: #fff; display: flex; flex-direction: column; gap: 8px; }
    .creneau-card { background: #f8fafc; border-left: 4px solid #2563eb; border-radius: 6px; padding: 8px 10px; position: relative; font-size: 13px; }
    .creneau-card .heure { font-weight: 700; color: #1e3a8a; }
    .creneau-card .matiere { font-weight: 600; margin-top: 2px; }
    .creneau-card .prof, .creneau-card .salle { color: #64748b; font-size: 12px; }
    .creneau-actions { position: absolute; top: 6px; right: 6px; display: flex; gap: 4px; }
    .creneau-actions i { cursor: pointer; font-size: 15px; }
    .creneau-actions .edit-icon { color: #2563eb; }
    .creneau-actions .delete-icon { color: #dc2626; }
    .empty-jour { color: #94a3b8; font-size: 12px; text-align: center; margin-top: 20px; }
</style>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Emploi du temps</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Pedagogie / Emploi du temps</span>
            </div>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="card mb-24">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                        Annee scolaire <span class="text-danger-600">*</span>
                    </label>
                    <select id="anneescolaireSelect" class="form-control form-select">
                        @foreach ($data_anneescolaire as $annee)
                            <option value="{{ $annee->v_annee_scolaire }}" {{ $annee->v_annee_scolaire == $annee_courante ? 'selected' : '' }}>
                                {{ $annee->v_annee_scolaire }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau <span class="text-danger-600">*</span></label>
                    <select id="niveauSelect" class="form-control form-select">
                        <option value="">Selectionner le niveau</option>
                        @foreach ($niveaux as $niveau)
                            <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                    <select id="classeSelect" class="form-control form-select">
                        <option value="">Selectionner un niveau d'abord</option>
                    </select>
                </div>
                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <button type="button" id="btnAfficher" class="btn btn-primary-600 w-100">
                        <iconify-icon icon="mdi:calendar-search"></iconify-icon> Afficher l'emploi du temps
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- EMPLOI DU TEMPS --}}
    <div id="zoneEmploiDuTemps" class="d-none">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="mb-0">Emploi du temps - <span id="classeNomAffiche" class="text-primary-600"></span></h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" id="btnImprimer">
                        <iconify-icon icon="mdi:printer-outline"></iconify-icon> Imprimer
                    </button>
                    <button class="btn btn-primary-600" id="btnAjouterCreneau">
                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon> Ajouter un creneau
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="timetableGrid" class="d-flex gap-3" style="overflow-x:auto;">
                    {{-- genere en JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL AJOUT / EDITION CRENEAU --}}
<div class="modal fade" id="modalCreneau" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalCreneauTitre">Ajouter un creneau</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="creneauId">
                <div class="mb-16">
                    <label class="form-label">Jour <span class="text-danger-600">*</span></label>
                    <select id="creneauJour" class="form-control form-select">
                        <option>Lundi</option>
                        <option>Mardi</option>
                        <option>Mercredi</option>
                        <option>Jeudi</option>
                        <option>Vendredi</option>
                        <option>Samedi</option>
                    </select>
                </div>
                <div class="row mb-16">
                    <div class="col-6">
                        <label class="form-label">Heure debut <span class="text-danger-600">*</span></label>
                        <input type="time" id="creneauHeureDebut" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Heure fin <span class="text-danger-600">*</span></label>
                        <input type="time" id="creneauHeureFin" class="form-control">
                    </div>
                </div>
                <div class="mb-16">
                    <label class="form-label">Matiere <span class="text-danger-600">*</span></label>
                    <select id="creneauMatiere" class="form-control form-select">
                        <option value="">Chargement...</option>
                    </select>
                </div>
                <div class="mb-16">
                    <label class="form-label">Professeur</label>
                    <select id="creneauProfesseur" class="form-control form-select">
                        <option value="">Chargement...</option>
                    </select>
                </div>
                <div class="mb-16">
                    <label class="form-label">Salle</label>
                    <input type="text" id="creneauSalle" class="form-control" placeholder="Ex: Salle 12">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary-600" id="btnValiderCreneau">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

{{-- Config injectee depuis le back-end : uniquement des donnees, aucune logique --}}
<script>
    window.emploiDuTempsConfig = {
        slug: @json($slug),
        ecole: {
            nom: @json($ecole->v_nomecole),
            slogan: @json($ecole->slogan),
            adresse: @json($ecole->t_adresseecole),
            telephone: @json($ecole->v_telephone1ecole),
            directeur: @json($ecole->v_nomdirecteurecole),
            ministere: @json($ecole->ministere),
            logo: @json($ecole->logo ? asset($ecole->logo) : asset('assets/images/logo-default.png'))
        }
    };
</script>

{{-- Toute la logique vit dans ce fichier JS externe, aucun risque de collision Blade/HTML/JS --}}
<script src="{{ asset('js/emploi-du-temps.js') }}"></script>
@endsection
