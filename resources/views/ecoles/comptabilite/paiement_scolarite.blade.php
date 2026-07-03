@extends('ecoles.layout.app')
@section('containte')
<!-- SweetAlert2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<!-- Toastify -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div class="">
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Paiement scolarité</h1>
            <div class="">
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Comptabilité / Paiement scolarité</span>
            </div>
        </div>
    </div>

    <form id="studentForm" method="POST" action="" enctype="multipart/form-data" class="mt-24">
        @csrf

        {{-- FILTRE --}}
        <div class="row gy-3">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0">Paramétrage des tranches de paiement</h6>
                    </div>
                    <div class="card-body p-20">
                        <div class="row gy-3 mb-24">
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                    Année scolaire <span class="text-danger-600">*</span>
                                </label>
                                <select id="anneescolaireSelect" class="form-control form-select">
                                    @foreach ($data_anneescolaire as $annee)
                                        <option value="{{ $annee->v_annesclaire }}" {{ $annee->v_annesclaire == $annee_courante ? 'selected' : '' }}>
                                            {{ $annee->v_annesclaire }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau <span class="text-danger-600">*</span></label>
                                <select required name="niveau_id" id="niveauSelect" class="form-control form-select">
                                    <option value="">Séléctionner le niveau</option>
                                    @foreach ($niveaux as $niveau)
                                        <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <label for="classSelection" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                                <select required id="classSelection" name="classe_id" class="form-control form-select">
                                    <option value="">Séléctionner une classe</option>
                                </select>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <label for="i_eleveId" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Élèves <span class="text-danger-600">*</span></label>
                                <select required id="i_eleveId" name="i_eleveId" class="form-control form-select">
                                    <option value="">Séléctionner un élève</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BLOC INFOS ELEVE --}}
        <div id="eleveInfoBlock" class="mt-24" style="display:none;">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex gap-32 flex-md-row flex-column">

                        {{-- Photo + infos --}}
                        <div class="max-w-300-px w-100 text-center">
                            <figure class="mb-24 w-120-px h-120-px mx-auto rounded-circle overflow-hidden">
                                <img id="elevePhoto" src="" alt="Student Image" class="w-100 h-100 object-fit-cover">
                            </figure>
                            {{-- Badges exonérations --}}
                            <div id="badgesExonerations" class="mb-12 d-flex flex-wrap gap-8 justify-content-center" style="display:none !important;"></div>
                            <h2 class="h6 text-primary-light mb-16 fw-semibold" id="eleveNomPrenom"></h2>
                            <p style="text-align:left" class="mb-0">Matricule: <span id="eleveMatricule" class="text-primary-600 fw-semibold"></span></p>
                            <p style="text-align:left" class="mb-0">Niveau : <span id="eleveNiveau" class="text-primary-light fw-semibold"></span></p>
                            <p style="text-align:left" class="mb-0">Classe : <span id="eleveClasse" class="text-primary-light fw-semibold"></span></p>
                        </div>

                        <div><span class="h-100 w-1-px bg-neutral-200"></span></div>

                        {{-- Cadres paiement --}}
                        <div class="flex-grow-1 row gy-3">

                            {{-- CADRE 1 : INSCRIPTION / REINSCRIPTION --}}
                            <div class="col-md-12">
                                <div class="border radius-8 p-16">
                                    <h6 class="text-sm fw-semibold text-primary-light mb-12">Inscription / Réinscription</h6>
                                    <div class="d-flex align-items-center gap-24 flex-wrap mb-16">
                                        <div class="form-check d-flex align-items-center gap-8 m-0">
                                            <input class="form-check-input m-0" type="radio" name="type_inscription" id="typeInscription" value="inscription">
                                            <label class="form-check-label text-sm mb-0" for="typeInscription">Inscription</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center gap-8 m-0">
                                            <input class="form-check-input m-0" type="radio" name="type_inscription" id="typeReinscription" value="reinscription">
                                            <label class="form-check-label text-sm mb-0" for="typeReinscription">Réinscription</label>
                                        </div>
                                    </div>
                                    <div id="blocInscription" class="row gy-3" style="display:none;">
                                        <div class="col-4">
                                            <label class="text-sm fw-semibold text-primary-light d-block mb-8">Montant</label>
                                            <input id="montantInscription" class="form-control" type="text" placeholder="0.00" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label class="text-sm fw-semibold text-primary-light d-block mb-8">Confirmez le montant</label>
                                            <input id="montantInscriptionConfirm" class="form-control" type="text" placeholder="0.00">
                                        </div>
                                        <div class="col-4">
                                            <label class="text-sm fw-semibold text-primary-light d-block mb-8">Validation</label>
                                            <button id="btnPayerInscription" type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">
                                                <i class="ri-save-3-line me-2"></i> Enregistrer
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <p id="msgInscriptionDejaPaye" class="text-danger-600 text-sm fw-semibold mb-0" style="display:none;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CADRE 2 : TRANCHES / MENSUELLE --}}
                            <div class="col-md-12">
                                <div class="border radius-8 p-16">
                                    <h6 class="text-sm fw-semibold text-primary-light mb-12">Mode de paiement (Mensuelle / Tranches / Annuelle)</h6>
                                    <div class="d-flex align-items-center gap-24 flex-wrap mb-16">
                                        <div class="form-check d-flex align-items-center gap-8 m-0">
                                            <input class="form-check-input m-0" type="radio" name="mode_paiement" id="modeMensuelle" value="mensuelle">
                                            <label class="form-check-label text-sm mb-0" for="modeMensuelle">Mensuelle</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center gap-8 m-0">
                                            <input class="form-check-input m-0" type="radio" name="mode_paiement" id="modeTranche1" value="1er_tranche">
                                            <label class="form-check-label text-sm mb-0" for="modeTranche1">1ère tranche</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center gap-8 m-0">
                                            <input class="form-check-input m-0" type="radio" name="mode_paiement" id="modeTranche2" value="2eme_tranche">
                                            <label class="form-check-label text-sm mb-0" for="modeTranche2">2ème tranche</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center gap-8 m-0">
                                            <input class="form-check-input m-0" type="radio" name="mode_paiement" id="modeTranche3" value="3eme_tranche">
                                            <label class="form-check-label text-sm mb-0" for="modeTranche3">3ème tranche</label>
                                        </div>
                                        <div class="form-check d-flex align-items-center gap-8 m-0">
                                            <input class="form-check-input m-0" type="radio" name="mode_paiement" id="modeAnnuelle" value="annuelle">
                                            <label class="form-check-label text-sm mb-0" for="modeAnnuelle">Annuelle</label>
                                        </div>
                                    </div>

                                    <div id="blocMois" class="mb-16" style="display:none;">
                                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Sélectionnez les mois à payer</label>
                                        <div id="moisCheckboxContainer" class="d-flex gap-16 flex-wrap"></div>
                                    </div>

                                    <div id="blocTranche" class="row gy-3" style="display:none;">
                                        <div class="col-4" id="colResteAPayer">
                                            <label class="text-sm fw-semibold text-primary-light d-block mb-8">Reste à payer</label>
                                            <input id="resteAPayer" class="form-control" type="text" placeholder="0.00" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label class="text-sm fw-semibold text-primary-light d-block mb-8">Montant à payer</label>
                                            <input id="montantTranche" class="form-control" type="text" placeholder="0.00">
                                        </div>
                                        <div class="col-4">
                                            <label class="text-sm fw-semibold text-primary-light d-block mb-8">Validation</label>
                                            <button id="btnPayerTranche" type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">
                                                <i class="ri-save-3-line me-2"></i> Enregistrer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TABLEAU HISTORIQUE --}}
                            <div class="col-md-12">
                                <div class="border radius-8 p-16">
                                    <h6 class="text-sm fw-semibold text-primary-light mb-12">Historique des paiements</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Mode</th>
                                                    <th>Mois</th>
                                                    <th>Montant</th>
                                                    <th>N° Reçu</th>
                                                </tr>
                                            </thead>
                                            <tbody id="historiquePaiementsBody">
                                                <tr><td colspan="6" class="text-center">Aucun paiement</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

 <script>
let slug = "{{ $slug }}";

const MOIS = [
    { num: 10, nom: 'Octobre' },
    { num: 11, nom: 'Novembre' },
    { num: 12, nom: 'Décembre' },
    { num: 1,  nom: 'Janvier' },
    { num: 2,  nom: 'Février' },
    { num: 3,  nom: 'Mars' },
    { num: 4,  nom: 'Avril' },
    { num: 5,  nom: 'Mai' },
    { num: 6,  nom: 'Juin' },
    { num: 7,  nom: 'Juillet' },
];

let statutPaiementActuel = null;

// ===================== NIVEAU -> CLASSES =====================
document.getElementById('niveauSelect').addEventListener('change', function () {
    let niveauId = this.value;
    let classeSelect = document.getElementById('classSelection');
    classeSelect.innerHTML = '<option>Chargement...</option>';
    if (!niveauId) { classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>'; return; }
    fetch('/get-classes-by-niveau/' + niveauId)
        .then(res => res.json())
        .then(data => {
            classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
            data.forEach(c => { classeSelect.innerHTML += '<option value="' + c.i_classe_id + '">' + c.v_nom_classe + '</option>'; });
        })
        .catch(() => { classeSelect.innerHTML = '<option>Erreur chargement</option>'; });
});

// ===================== CLASSE -> ELEVES =====================
document.getElementById('classSelection').addEventListener('change', function () {
    let classeId = this.value;
    let eleveSelect = document.getElementById('i_eleveId');
    eleveSelect.innerHTML = '<option>Chargement...</option>';
    resetTout();
    if (!classeId) { eleveSelect.innerHTML = '<option value="">Séléctionner un élève</option>'; return; }
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    fetch('/get-eleves-by-classe/' + classeId + '?annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            eleveSelect.innerHTML = '<option value="">Séléctionner un élève</option>';
            if (data.length === 0) { eleveSelect.innerHTML += '<option disabled>Aucun élève trouvé</option>'; return; }
            data.forEach(e => {
                let mat = e.v_matricule ? ' (' + e.v_matricule + ')' : '';
                eleveSelect.innerHTML += '<option value="' + e.i_eleve_id + '">' + e.v_nom + ' ' + e.v_prenom + mat + '</option>';
            });
        })
        .catch(() => { eleveSelect.innerHTML = '<option>Erreur chargement</option>'; });
});

// ===================== ELEVE -> INFOS + STATUT + HISTORIQUE =====================
document.getElementById('i_eleveId').addEventListener('change', function () {
    let eleveId = this.value;
    let infoBlock = document.getElementById('eleveInfoBlock');
    resetTout();
    if (!eleveId) { infoBlock.style.display = 'none'; return; }
    fetch('/get-eleve-info/' + eleveId)
        .then(res => res.json())
        .then(eleve => {
            document.getElementById('elevePhoto').src = eleve.v_photo ? '/' + eleve.v_photo : '/assets/images/thumbs/student-details-img.png';
            document.getElementById('eleveNomPrenom').textContent = eleve.v_nom + ' ' + eleve.v_prenom;
            document.getElementById('eleveMatricule').textContent = eleve.v_matricule ?? 'N/A';
            document.getElementById('eleveNiveau').textContent    = eleve.v_niveaux ?? 'N/A';
            document.getElementById('eleveClasse').textContent    = eleve.v_nom_classe ?? 'N/A';
            infoBlock.style.display = 'block';
        })
        .catch(() => { infoBlock.style.display = 'none'; });
    chargerStatutPaiement();
    chargerHistorique();
});

// ===================== STATUT PAIEMENT =====================
function chargerStatutPaiement() {
    let eleveId       = document.getElementById('i_eleveId').value;
    let classeId      = document.getElementById('classSelection').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    if (!eleveId || !classeId) return;
    fetch('/' + slug + '/get-statut-paiement/' + eleveId + '?classe_id=' + classeId + '&annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            statutPaiementActuel = data;
            appliquerRestrictionsModes();
            afficherBadgesExonerations(); // ← ajouter ici
        })
        .catch(err => console.error(err));
}

// ===================== RESTRICTIONS MODES (cadre 2 uniquement) =====================

// ===================== RESTRICTIONS MODES =====================
function appliquerRestrictionsModes() {
    if (!statutPaiementActuel) return;

    let moisPayes      = (statutPaiementActuel.mois_payes || []).map(Number);
    let tranchesPayees = statutPaiementActuel.total_par_tranche || {};
    let exonerations   = statutPaiementActuel.exonerations || {};

    let aMoisPaye = moisPayes.length > 0;
    let tranchesAvecPaiement = Object.keys(tranchesPayees).filter(k =>
        ['1er_tranche', '2eme_tranche', '3eme_tranche', 'annuelle'].includes(k)
        && parseFloat(tranchesPayees[k]) > 0
    );
    let aTranchePaye = tranchesAvecPaiement.length > 0;

    // ---- Helpers ----
    function griser(id, message) {
        let el = document.getElementById(id);
        if (!el) return;
        el.disabled = true;
        el.checked  = false;
        el.closest('.form-check').style.opacity = '0.4';
        el.closest('.form-check').title = message;
    }

    function activer(id) {
        let el = document.getElementById(id);
        if (!el) return;
        el.disabled = false;
        el.closest('.form-check').style.opacity = '1';
        el.closest('.form-check').title = '';
    }

    // ---- Tout réinitialiser d'abord ----
    ['typeInscription', 'typeReinscription'].forEach(id => activer(id));
    ['modeMensuelle', 'modeTranche1', 'modeTranche2', 'modeTranche3', 'modeAnnuelle'].forEach(id => activer(id));

    let exoInscription   = exonerations['inscription'];
    let exoReinscription = exonerations['reinscription'];
    let exoMensuelle     = exonerations['mensuelle'];
    let exoTranche1      = exonerations['1er_tranche'];
    let exoTranche2      = exonerations['2eme_tranche'];
    let exoTranche3      = exonerations['3eme_tranche'];
    let exoAnnuelle      = exonerations['annuelle'];

    // ======================================================
    // REGLE 1 : exonération inscription → griser inscription ET réinscription (vice versa)
    // ======================================================
    if (exoInscription) {
        let msg = 'Exonéré à ' + exoInscription.pourcentage + '% en Inscription par ' + exoInscription.autorise_par;
        griser('typeInscription', msg);
        griser('typeReinscription', msg);
    }
    if (exoReinscription) {
        let msg = 'Exonéré à ' + exoReinscription.pourcentage + '% en Réinscription par ' + exoReinscription.autorise_par;
        griser('typeInscription', msg);
        griser('typeReinscription', msg);
    }

    // ======================================================
    // REGLE 2 : exonération mensuelle (peu importe le mois) → griser 1ère, 2ème, 3ème, annuelle
    // ======================================================
    if (exoMensuelle) {
        let msg = 'Désactivé : exonération mensuelle active (par ' + exoMensuelle.autorise_par + ')';
        griser('modeTranche1', msg);
        griser('modeTranche2', msg);
        griser('modeTranche3', msg);
        griser('modeAnnuelle', msg);
    }

    // ======================================================
    // REGLE 3 : exonération annuelle
    // - Totale → griser TOUT (mensuelle, 1ère, 2ème, 3ème, annuelle)
    // - Partielle → griser mensuelle, 1ère, 2ème, 3ème — laisser annuelle active
    // ======================================================
    if (exoAnnuelle) {
        let msg = 'Désactivé : exonération annuelle active (par ' + exoAnnuelle.autorise_par + ')';
        griser('modeMensuelle', msg);
        griser('modeTranche1', msg);
        griser('modeTranche2', msg);
        griser('modeTranche3', msg);
        if (exoAnnuelle.type === 'totale') {
            griser('modeAnnuelle', 'Exonéré à 100% annuelle par ' + exoAnnuelle.autorise_par);
        }
        // Si partielle → annuelle reste active (déjà activée au reset)
    }

    // ======================================================
    // REGLE 4 : exonération partielle ou totale sur 1ère, 2ème ou 3ème tranche
    // → griser mensuelle ET annuelle
    // ======================================================
    if (exoTranche1 || exoTranche2 || exoTranche3) {
        let auteur = (exoTranche1 || exoTranche2 || exoTranche3).autorise_par;
        let msg    = 'Désactivé : exonération sur une tranche active (par ' + auteur + ')';
        griser('modeMensuelle', msg);
        griser('modeAnnuelle', msg);

        // Griser la tranche spécifique si exonération totale
        if (exoTranche1 && exoTranche1.type === 'totale') griser('modeTranche1', 'Exonéré à 100% 1ère tranche par ' + exoTranche1.autorise_par);
        if (exoTranche2 && exoTranche2.type === 'totale') griser('modeTranche2', 'Exonéré à 100% 2ème tranche par ' + exoTranche2.autorise_par);
        if (exoTranche3 && exoTranche3.type === 'totale') griser('modeTranche3', 'Exonéré à 100% 3ème tranche par ' + exoTranche3.autorise_par);
    }

    // ======================================================
    // REGLES PAIEMENTS (indépendantes des exonérations)
    // ======================================================

    // Mois déjà payés → griser tranches et annuelle (sauf si exo déjà grisé)
    if (aMoisPaye) {
        ['modeTranche1', 'modeTranche2', 'modeTranche3', 'modeAnnuelle'].forEach(id => {
            let el = document.getElementById(id);
            if (el && !el.disabled) {
                griser(id, 'Désactivé : un paiement mensuel a déjà été effectué');
            }
        });
    }

    // Tranche payée → griser mensuelle + autres tranches non commencées
    if (aTranchePaye) {
        let mensuelleEl = document.getElementById('modeMensuelle');
        if (mensuelleEl && !mensuelleEl.disabled) {
            griser('modeMensuelle', 'Désactivé : un paiement par tranche a déjà été effectué');
        }

        let mapTranches = {
            '1er_tranche' : 'modeTranche1',
            '2eme_tranche': 'modeTranche2',
            '3eme_tranche': 'modeTranche3',
            'annuelle'    : 'modeAnnuelle',
        };
        Object.entries(mapTranches).forEach(([valeur, id]) => {
            let el = document.getElementById(id);
            if (!el || el.disabled) return;
            if (!tranchesAvecPaiement.includes(valeur)) {
                griser(id, 'Désactivé : une autre tranche est déjà en cours');
            }
        });
    }
}

// ===================== MONTANT INSCRIPTION (avec exo) =====================
function chargerMontantInscription() {
    let type          = document.querySelector('input[name="type_inscription"]:checked')?.value;
    let classeId      = document.getElementById('classSelection').value;
    let eleveId       = document.getElementById('i_eleveId').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    if (!type || !classeId || !anneeScolaire) return;

    fetch('/' + slug + '/get-montant-tranche?classe_id=' + classeId + '&type_inscription=' + type
        + '&mode_paiement=' + type + '&annee_scolaire=' + anneeScolaire + '&eleve_id=' + eleveId)
        .then(res => res.json())
        .then(data => {
            document.getElementById('montantInscription').value        = data.montant ?? 0;
            document.getElementById('montantInscriptionConfirm').value = '';

            // Afficher un message si exonération partielle
            let msg = document.getElementById('msgInscriptionDejaPaye');
            if (data.montant_exo > 0 && data.type_exo === 'partielle') {
                msg.textContent   = 'Exonération partielle appliquée : -' + data.montant_exo
                                  + ' GNF (autorisé par ' + data.autorise_par + ')';
                msg.style.display = 'block';
                msg.style.color   = '#198754'; // vert
            }
        });
}

// ===================== RESTE A PAYER TRANCHE (avec exo) =====================
function chargerResteAPayerTranche(mode) {
    let classeId      = document.getElementById('classSelection').value;
    let eleveId       = document.getElementById('i_eleveId').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;

    fetch('/' + slug + '/get-montant-tranche?classe_id=' + classeId + '&type_inscription=' + mode
        + '&mode_paiement=' + mode + '&annee_scolaire=' + anneeScolaire + '&eleve_id=' + eleveId)
        .then(res => res.json())
        .then(data => {
            let total    = parseFloat(data.montant) || 0; // déjà déduit de l'exo
            let dejaPaye = parseFloat(statutPaiementActuel?.total_par_tranche?.[mode]) || 0;
            let reste    = total - dejaPaye;

            document.getElementById('resteAPayer').value         = reste.toFixed(2);
            document.getElementById('resteAPayer').dataset.reste = reste;
        });
}

// ===================== CADRE 1 : INSCRIPTION / REINSCRIPTION =====================
document.querySelectorAll('input[name="type_inscription"]').forEach(radio => {
    radio.addEventListener('change', function () {
        let classeId = document.getElementById('classSelection').value;
        let eleveId  = document.getElementById('i_eleveId').value;
        if (!classeId || !eleveId) {
            afficherToast('Veuillez sélectionner une classe et un élève.', 'error');
            this.checked = false; return;
        }
        document.getElementById('blocInscription').style.display = 'flex';
        chargerMontantInscription();
        appliquerStatutInscription();
    });
});



function appliquerStatutInscription() {
    if (!statutPaiementActuel) return;
    let type      = document.querySelector('input[name="type_inscription"]:checked')?.value;
    let dejaPayee = (type === 'inscription'   && statutPaiementActuel.inscription_payee)
                 || (type === 'reinscription' && statutPaiementActuel.reinscription_payee);
    let confirmInput = document.getElementById('montantInscriptionConfirm');
    let btn          = document.getElementById('btnPayerInscription');
    let msg          = document.getElementById('msgInscriptionDejaPaye');
    if (dejaPayee) {
        confirmInput.disabled = true; btn.disabled = true; btn.classList.add('disabled');
        msg.textContent = (type === 'inscription' ? 'Inscription' : 'Réinscription') + ' déjà payée';
        msg.style.display = 'block';
    } else {
        confirmInput.disabled = false; btn.disabled = false; btn.classList.remove('disabled');
        msg.style.display = 'none';
    }
}

document.getElementById('btnPayerInscription').addEventListener('click', function () {
    let montant        = document.getElementById('montantInscription').value;
    let montantConfirm = document.getElementById('montantInscriptionConfirm').value;
    let type           = document.querySelector('input[name="type_inscription"]:checked')?.value;
    let classeId       = document.getElementById('classSelection').value;
    let niveauId       = document.getElementById('niveauSelect').value;
    let eleveId        = document.getElementById('i_eleveId').value;
    let anneeScolaire  = document.getElementById('anneescolaireSelect').value;
    if (!type)                  { afficherToast('Choisissez Inscription ou Réinscription.', 'error'); return; }
    if (!montant || montant==0) { afficherToast('Montant invalide.', 'error'); return; }
    if (!montantConfirm)        { afficherToast('Veuillez confirmer le montant.', 'error'); return; }
    if (parseFloat(montant) !== parseFloat(montantConfirm)) { afficherToast('Le montant confirmé ne correspond pas.', 'error'); return; }
    Swal.fire({
        title: 'Confirmer le paiement ?',
        html: 'Type : <strong>' + type + '</strong><br>Montant : <strong>' + montant + '</strong>',
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Oui, enregistrer', cancelButtonText: 'Annuler', confirmButtonColor: '#0d6efd',
    }).then(result => {
        if (result.isConfirmed) {
            envoyerPaiement({
                eleve_id: eleveId, classe_id: classeId, niveau_id: niveauId,
                type_inscription: type, mode_paiement: type,
                montant: montant, annee_scolaire: anneeScolaire, slug: slug,
            }, () => {
                document.getElementById('blocInscription').style.display = 'none';
                document.querySelectorAll('input[name="type_inscription"]').forEach(r => r.checked = false);
                // NE PAS appeler chargerStatutPaiement ici pour ne pas affecter le cadre 2
                chargerHistorique();
                // Mettre à jour uniquement le statut inscription sans toucher aux restrictions
                chargerStatutInscriptionSeulement();
            });
        }
    });
});

// Recharger uniquement le statut inscription/réinscription sans appliquer les restrictions modes
function chargerStatutInscriptionSeulement() {
    let eleveId       = document.getElementById('i_eleveId').value;
    let classeId      = document.getElementById('classSelection').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    if (!eleveId || !classeId) return;
    fetch('/' + slug + '/get-statut-paiement/' + eleveId + '?classe_id=' + classeId + '&annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            // Mettre à jour uniquement les infos inscription/réinscription
            if (statutPaiementActuel) {
                statutPaiementActuel.inscription_payee   = data.inscription_payee;
                statutPaiementActuel.reinscription_payee = data.reinscription_payee;
            } else {
                statutPaiementActuel = data;
            }
            // NE PAS appeler appliquerRestrictionsModes ici
        })
        .catch(err => console.error(err));
}

// ===================== CADRE 2 : TRANCHES / MENSUELLE =====================
document.querySelectorAll('input[name="mode_paiement"]').forEach(radio => {
    radio.addEventListener('change', function () {
        let classeId = document.getElementById('classSelection').value;
        let eleveId  = document.getElementById('i_eleveId').value;
        if (!classeId || !eleveId) {
            afficherToast('Veuillez sélectionner une classe et un élève.', 'error');
            this.checked = false; return;
        }
        let mode = this.value;
        document.getElementById('blocTranche').style.display = 'flex';
        if (mode === 'mensuelle') {
            document.getElementById('blocMois').style.display       = 'block';
            document.getElementById('colResteAPayer').style.display = 'none';
            document.getElementById('montantTranche').readOnly      = true;
            document.getElementById('montantTranche').value         = '';
            genererCheckboxMois();
        } else {
            document.getElementById('blocMois').style.display       = 'none';
            document.getElementById('colResteAPayer').style.display = 'block';
            document.getElementById('montantTranche').readOnly      = false;
            document.getElementById('montantTranche').value         = '';
            chargerResteAPayerTranche(mode);
        }
    });
});

function genererCheckboxMois() {
    let container = document.getElementById('moisCheckboxContainer');
    container.innerHTML = '';
    let moisPayes = (statutPaiementActuel?.mois_payes || []).map(Number);
    MOIS.forEach(m => {
        let estPaye = moisPayes.includes(m.num);
        container.innerHTML +=
            '<div class="form-check d-flex align-items-center gap-8 m-0">' +
            '<input class="form-check-input mois-checkbox m-0" type="checkbox" value="' + m.num + '" id="mois_' + m.num + '" ' + (estPaye ? 'disabled checked' : '') + '>' +
            '<label class="form-check-label text-sm mb-0" for="mois_' + m.num + '">' + m.nom + (estPaye ? ' ✓' : '') + '</label>' +
            '</div>';
    });
    container.querySelectorAll('.mois-checkbox').forEach(cb => cb.addEventListener('change', calculerMontantMensuel));
}

function calculerMontantMensuel() {
    let classeId      = document.getElementById('classSelection').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    fetch('/' + slug + '/get-montant-tranche?classe_id=' + classeId + '&type_inscription=mensuelle&mode_paiement=mensuelle&annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            let prix       = parseFloat(data.montant) || 0;
            let moisCoches = document.querySelectorAll('.mois-checkbox:checked:not(:disabled)').length;
            document.getElementById('montantTranche').value = (prix * moisCoches).toFixed(2);
        });
}

function chargerResteAPayerTranche(mode) {
    let classeId      = document.getElementById('classSelection').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    fetch('/' + slug + '/get-montant-tranche?classe_id=' + classeId + '&type_inscription=' + mode + '&mode_paiement=' + mode + '&annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            let total    = parseFloat(data.montant) || 0;
            let dejaPaye = parseFloat(statutPaiementActuel?.total_par_tranche?.[mode]) || 0;
            let reste    = total - dejaPaye;
            document.getElementById('resteAPayer').value         = reste.toFixed(2);
            document.getElementById('resteAPayer').dataset.reste = reste;
        });
}

document.getElementById('btnPayerTranche').addEventListener('click', function () {
    let mode          = document.querySelector('input[name="mode_paiement"]:checked')?.value;
    let classeId      = document.getElementById('classSelection').value;
    let niveauId      = document.getElementById('niveauSelect').value;
    let eleveId       = document.getElementById('i_eleveId').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    let montant       = document.getElementById('montantTranche').value;
    if (!mode)                    { afficherToast('Choisissez un mode de paiement.', 'error'); return; }
    if (!montant || montant <= 0) { afficherToast('Montant invalide.', 'error'); return; }

    if (mode === 'mensuelle') {
        let moisCoches = Array.from(document.querySelectorAll('.mois-checkbox:checked:not(:disabled)')).map(cb => parseInt(cb.value));
        if (moisCoches.length === 0) { afficherToast('Veuillez sélectionner au moins un mois.', 'error'); return; }
        let prixParMois = parseFloat(montant) / moisCoches.length;
        Swal.fire({
            title: 'Confirmer le paiement ?',
            html: 'Mois : <strong>' + moisCoches.length + '</strong><br>Total : <strong>' + montant + '</strong>',
            icon: 'question', showCancelButton: true,
            confirmButtonText: 'Oui, enregistrer', cancelButtonText: 'Annuler', confirmButtonColor: '#0d6efd',
        }).then(result => {
            if (result.isConfirmed) {
                envoyerPaiement({
                    eleve_id: eleveId, classe_id: classeId, niveau_id: niveauId,
                    mode_paiement: 'mensuelle', mois: moisCoches, montant_par_mois: prixParMois,
                    montant: montant, annee_scolaire: anneeScolaire, slug: slug,
                }, () => {
                    document.getElementById('montantTranche').value      = '';
                    document.querySelectorAll('input[name="mode_paiement"]').forEach(r => r.checked = false);
                    document.getElementById('blocMois').style.display    = 'none';
                    document.getElementById('blocTranche').style.display = 'none';
                    chargerStatutPaiement(); chargerHistorique();
                });
            }
        });
        return;
    }

    let reste = parseFloat(document.getElementById('resteAPayer').dataset.reste) || 0;
    if (parseFloat(montant) > reste) { afficherToast('Le montant dépasse le reste à payer (' + reste.toFixed(2) + ').', 'error'); return; }

    Swal.fire({
        title: 'Confirmer le paiement ?',
        html: 'Mode : <strong>' + mode + '</strong><br>Montant : <strong>' + montant + '</strong>',
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Oui, enregistrer', cancelButtonText: 'Annuler', confirmButtonColor: '#0d6efd',
    }).then(result => {
        if (result.isConfirmed) {
            envoyerPaiement({
                eleve_id: eleveId, classe_id: classeId, niveau_id: niveauId,
                mode_paiement: mode, montant: montant, annee_scolaire: anneeScolaire, slug: slug,
            }, () => {
                document.getElementById('montantTranche').value      = '';
                document.querySelectorAll('input[name="mode_paiement"]').forEach(r => r.checked = false);
                document.getElementById('blocTranche').style.display = 'none';
                chargerStatutPaiement(); chargerHistorique();
            });
        }
    });
});

// ===================== ENVOI GENERIQUE =====================
function envoyerPaiement(data, onSuccess) {
    Swal.fire({ title: 'Enregistrement en cours...', allowOutsideClick: false, allowEscapeKey: false, didOpen: () => Swal.showLoading() });
    let token = document.querySelector('input[name="_token"]').value;
    fetch('/' + slug + '/enregistrer-paiement', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify(data)
    })
        .then(res => res.json().then(json => ({ status: res.status, body: json })))
        .then(({ status, body }) => {
            Swal.close();
            if (status === 200 && body.success) {
                afficherToast('Paiement enregistré ! Reçu : ' + body.numero_recu, 'success');
                if (onSuccess) onSuccess();
            } else {
                afficherToast(body.message || "Erreur lors de l'enregistrement.", 'error');
            }
        })
        .catch(err => { Swal.close(); console.error(err); afficherToast('Erreur réseau.', 'error'); });
}

// ===================== HISTORIQUE =====================
function chargerHistorique() {
    let eleveId = document.getElementById('i_eleveId').value;
    if (!eleveId) return;
    fetch('/' + slug + '/get-historique-paiement/' + eleveId)
        .then(res => res.json())
        .then(data => {
            let tbody = document.getElementById('historiquePaiementsBody');
            if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun paiement</td></tr>'; return; }
            tbody.innerHTML = data.map(p =>
                '<tr><td>' + p.date + '</td><td>' + p.type + '</td><td>' + p.mode + '</td><td>' + p.mois + '</td><td>' + parseFloat(p.montant).toFixed(2) + '</td><td>' + p.numero_recu + '</td></tr>'
            ).join('');
        })
        .catch(err => console.error(err));
}

// ===================== RESET GLOBAL (une seule déclaration) =====================
function resetTout() {
    statutPaiementActuel = null;

    // Réactiver tous les modes du cadre 2
    ['modeMensuelle', 'modeTranche1', 'modeTranche2', 'modeTranche3', 'modeAnnuelle'].forEach(id => {
        let el = document.getElementById(id);
        if (el) {
            el.disabled = false;
            el.checked  = false;
            el.closest('.form-check').style.opacity = '1';
            el.closest('.form-check').title = '';
        }
    });

    // Ajouter dans resetTout() :
    let badgesContainer = document.getElementById('badgesExonerations');
    if (badgesContainer) {
        badgesContainer.innerHTML = '';
        badgesContainer.style.display = 'none';
    }

    // Cadre 1
    document.querySelectorAll('input[name="type_inscription"]').forEach(r => r.checked = false);
    document.getElementById('blocInscription').style.display        = 'none';
    document.getElementById('montantInscription').value             = '';
    document.getElementById('montantInscriptionConfirm').value      = '';
    document.getElementById('montantInscriptionConfirm').disabled   = false;
    document.getElementById('btnPayerInscription').disabled         = false;
    document.getElementById('btnPayerInscription').classList.remove('disabled');
    document.getElementById('msgInscriptionDejaPaye').style.display = 'none';

    // Cadre 2
    document.getElementById('blocMois').style.display               = 'none';
    document.getElementById('blocTranche').style.display            = 'none';
    document.getElementById('montantTranche').value                 = '';
    document.getElementById('resteAPayer').value                    = '';
    document.getElementById('moisCheckboxContainer').innerHTML      = '';

    // Historique
    document.getElementById('historiquePaiementsBody').innerHTML    = '<tr><td colspan="6" class="text-center">Aucun paiement</td></tr>';
}

function afficherBadgesExonerations() {
    let container = document.getElementById('badgesExonerations');
    container.innerHTML = '';

    let exonerations = statutPaiementActuel?.exonerations || {};
    if (Object.keys(exonerations).length === 0) {
        container.style.display = 'none';
        return;
    }

    let labels = {
        'inscription'  : 'Inscription',
        'reinscription': 'Réinscription',
        'mensuelle'    : 'Mensuelle',
        '1er_tranche'  : '1ère tranche',
        '2eme_tranche' : '2ème tranche',
        '3eme_tranche' : '3ème tranche',
        'annuelle'     : 'Annuelle',
    };

    Object.entries(exonerations).forEach(([mode, exo]) => {
        let couleur = exo.type === 'totale' ? 'bg-danger' : 'bg-warning text-dark';
        let texte   = exo.type === 'totale'
            ? 'Exonéré à 100% en ' + (labels[mode] || mode)
            : 'Exonéré à ' + exo.pourcentage + '% en ' + (labels[mode] || mode);

        container.innerHTML +=
            '<span class="badge ' + couleur + ' px-8 py-4" style="font-size:11px; border-radius:6px;" title="Autorisé par : ' + exo.autorise_par + '">' +
            '<i class="ri-shield-check-line me-4"></i>' + texte +
            '</span>';
    });

    container.style.display = 'flex';
}

// ===================== TOAST =====================
function afficherToast(message, type) {
    type = type || 'success';
    Toastify({
        text: message, duration: 4000, gravity: 'top', position: 'right',
        style: { background: type === 'success' ? '#28a745' : '#dc3545' },
        close: true,
    }).showToast();
}


</script>


@endsection
