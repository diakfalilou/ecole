@extends('ecoles.layout.app')
@section('containte')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Exonération de frais de scolarité</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Comptabilité / Exonération</span>
            </div>
        </div>
    </div>

    {{-- SELECTION ELEVE --}}
    <div class="row gy-3 mb-24">
        <div class="col-lg-12">
            <div class="shadow-1 radius-12 bg-base overflow-hidden">
                <div class="card-header border-bottom bg-base py-16 px-24">
                    <h6 class="text-lg fw-semibold mb-0">Sélection de l'élève</h6>
                </div>
                <div class="card-body p-20">
                    <div class="row gy-3">
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année scolaire <span class="text-danger-600">*</span></label>
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
                            <select id="niveauSelect" class="form-control form-select">
                                <option value="">Séléctionner le niveau</option>
                                @foreach ($niveaux as $niveau)
                                    <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                            <select id="classSelection" class="form-control form-select">
                                <option value="">Séléctionner une classe</option>
                            </select>
                        </div>
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Élève <span class="text-danger-600">*</span></label>
                            <select id="i_eleveId" class="form-control form-select">
                                <option value="">Séléctionner un élève</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FICHE ELEVE --}}
    <div id="eleveInfoBlock" class="row gy-3 mb-24" style="display:none;">
        <div class="col-lg-12">
            <div class="shadow-1 radius-12 bg-base overflow-hidden p-20">
                <div class="d-flex align-items-center gap-16 mb-20">
                    <div class="w-48-px h-48-px radius-8 overflow-hidden flex-shrink-0">
                        <img id="elevePhoto" src="/assets/images/thumbs/student-details-img.png"
                            alt="Photo élève" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div>
                        <h6 class="mb-0" id="eleveNomComplet">-</h6>
                        <span class="text-secondary-light text-sm" id="eleveMatriculeClasse">-</span>
                    </div>
                </div>
                <div class="row gy-3">
                    <div class="col-md-3">
                        <div class="bg-neutral-50 radius-8 p-16">
                            <span class="text-secondary-light text-sm d-block mb-4">Inscription</span>
                            <span class="fw-semibold" id="montantInscription">-</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-neutral-50 radius-8 p-16">
                            <span class="text-secondary-light text-sm d-block mb-4">Mensuelle / mois</span>
                            <span class="fw-semibold" id="montantMensuelle">-</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-neutral-50 radius-8 p-16">
                            <span class="text-secondary-light text-sm d-block mb-4">1ère tranche</span>
                            <span class="fw-semibold" id="montantTranche1">-</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-neutral-50 radius-8 p-16">
                            <span class="text-secondary-light text-sm d-block mb-4">Annuelle</span>
                            <span class="fw-semibold" id="montantAnnuelle">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORMULAIRE EXONERATION --}}
    <div id="exonerationBlock" style="display:none;">
        <div class="shadow-1 radius-12 bg-base overflow-hidden">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-lg fw-semibold mb-0">Détails de l'exonération</h6>
            </div>
            <div class="card-body p-20">

                {{-- Mode de paiement concerné --}}
                <div class="mb-24">
                    <label class="text-sm fw-semibold text-primary-light d-block mb-12">Mode de paiement concerné <span class="text-danger-600">*</span></label>
                    <div class="d-flex align-items-center gap-16 flex-wrap" id="modesPaiementContainer">
                        @foreach([
                            'inscription'  => 'Inscription',
                            'reinscription'=> 'Réinscription',
                            '1er_tranche'  => '1ère tranche',
                            '2eme_tranche' => '2ème tranche',
                            '3eme_tranche' => '3ème tranche',
                            'annuelle'     => 'Annuelle',
                        ] as $val => $label)
                        <div class="form-check d-flex align-items-center gap-8 m-0">
                            <input class="form-check-input m-0 mode-radio" type="radio" name="mode_paiement" id="mode_{{ $val }}" value="{{ $val }}">
                            <label class="form-check-label text-sm mb-0" for="mode_{{ $val }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Type d'exonération --}}
                <div class="mb-24" id="blocTypeExoneration" style="display:none;">
                    <label class="text-sm fw-semibold text-primary-light d-block mb-12">Type d'exonération <span class="text-danger-600">*</span></label>
                    <div class="d-flex align-items-center gap-16 flex-wrap">
                        <div class="form-check d-flex align-items-center gap-8 m-0">
                            <input class="form-check-input m-0" type="radio" name="type_exoneration" id="typePartielle" value="partielle" checked>
                            <label class="form-check-label text-sm mb-0" for="typePartielle">Partielle</label>
                        </div>
                        <div class="form-check d-flex align-items-center gap-8 m-0">
                            <input class="form-check-input m-0" type="radio" name="type_exoneration" id="typeTotale" value="totale">
                            <label class="form-check-label text-sm mb-0" for="typeTotale">Totale</label>
                        </div>
                    </div>
                </div>

                {{-- Montants --}}
                <div class="row gy-3 mb-24" id="blocMontants" style="display:none;">
                    <div class="col-md-3">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Montant initial</label>
                        <input id="montantInitialDisplay" class="form-control" type="text" readonly>
                    </div>
                    <div class="col-md-3" id="colPourcentage">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Pourcentage (%)</label>
                        <input id="pourcentageInput" class="form-control" type="number" min="1" max="100" value="50" placeholder="Ex: 50">
                    </div>
                    <div class="col-md-3">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Montant exonéré</label>
                        <input id="montantExonereDisplay" class="form-control" type="text" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Reste à payer</label>
                        <input id="resteAPayerDisplay" class="form-control" type="text" readonly>
                    </div>
                </div>

                {{-- Motif, date, autorisé par --}}
                <div class="row gy-3 mb-24" id="blocDetails" style="display:none;">
                    <div class="col-md-12">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Motif <span class="text-danger-600">*</span></label>
                        <textarea id="motifInput" class="form-control" rows="2" placeholder="Raison de l'exonération"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Date d'exonération <span class="text-danger-600">*</span></label>
                        <input id="dateExonerationInput" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Autorisé par <span class="text-danger-600">*</span></label>
                        <input id="autoriseParInput" type="text" class="form-control" placeholder="Nom du responsable">
                    </div>
                    <div class="col-md-12">
                        <label class="text-sm fw-semibold text-primary-light d-block mb-8">Justificatif (optionnel)</label>
                        <input id="justificatifInput" type="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end gap-2">
                        <button type="button" id="btnAnnuler" class="btn btn-outline-secondary px-24">Annuler</button>
                        <button type="button" id="btnEnregistrerExoneration" class="btn btn-primary-600 px-24">
                            <i class="ri-save-3-line me-2"></i> Enregistrer l'exonération
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- HISTORIQUE EXONERATIONS --}}
        <div class="shadow-1 radius-12 bg-base overflow-hidden mt-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-lg fw-semibold mb-0">Historique des exonérations</h6>
            </div>
            <div class="p-0" style="overflow-x:auto;">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Mode</th>
                            <th>Type</th>
                            <th>Montant initial</th>
                            <th>Montant exonéré</th>
                            <th>%</th>
                            <th>Motif</th>
                            <th>Autorisé par</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="historiqueExonerationBody">
                        <tr><td colspan="9" class="text-center">Sélectionnez un élève</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let slug = "{{ $slug }}";
let modaliteActuelle = null;
let eleveActuel = null;

function formatGNF(v) {
    return new Intl.NumberFormat('fr-FR').format(Math.round(v || 0)) + ' GNF';
}

function afficherToast(message, type) {
    Toastify({
        text: message, duration: 4000, gravity: 'top', position: 'right',
        style: { background: type === 'success' ? '#28a745' : '#dc3545' },
        close: true,
    }).showToast();
}

function resetTout() {
    eleveActuel = null;
    modaliteActuelle = null;
    document.getElementById('eleveInfoBlock').style.display = 'none';
    document.getElementById('exonerationBlock').style.display = 'none';
    document.querySelectorAll('input[name="mode_paiement"]').forEach(r => r.checked = false);
    document.querySelectorAll('input[name="type_exoneration"]').forEach(r => { if (r.value === 'partielle') r.checked = true; });
    document.getElementById('blocTypeExoneration').style.display = 'none';
    document.getElementById('blocMontants').style.display = 'none';
    document.getElementById('blocDetails').style.display = 'none';
    document.getElementById('pourcentageInput').value = 50;
    document.getElementById('pourcentageInput').disabled = false;
    document.getElementById('historiqueExonerationBody').innerHTML =
        '<tr><td colspan="9" class="text-center">Sélectionnez un élève</td></tr>';
}

// ===================== NIVEAU -> CLASSES =====================
document.getElementById('niveauSelect').addEventListener('change', function () {
    let niveauId = this.value;
    let classeSelect = document.getElementById('classSelection');
    classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
    document.getElementById('i_eleveId').innerHTML = '<option value="">Séléctionner un élève</option>';
    resetTout();
    if (!niveauId) return;
    fetch('/get-classes-by-niveau/' + niveauId)
        .then(res => res.json())
        .then(data => {
            data.forEach(c => { classeSelect.innerHTML += '<option value="' + c.i_classe_id + '">' + c.v_nom_classe + '</option>'; });
        });
});

// ===================== CLASSE -> ELEVES =====================
document.getElementById('classSelection').addEventListener('change', function () {
    let classeId = this.value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    let eleveSelect = document.getElementById('i_eleveId');
    eleveSelect.innerHTML = '<option value="">Séléctionner un élève</option>';
    resetTout();
    if (!classeId) return;
    fetch('/get-eleves-by-classe/' + classeId + '?annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) { eleveSelect.innerHTML += '<option disabled>Aucun élève trouvé</option>'; return; }
            data.forEach(e => {
                let mat = e.v_matricule ? ' (' + e.v_matricule + ')' : '';
                eleveSelect.innerHTML += '<option value="' + e.i_eleve_id + '">' + e.v_nom + ' ' + e.v_prenom + mat + '</option>';
            });
        });
});

// ===================== ELEVE -> FICHE + MODALITE =====================
document.getElementById('i_eleveId').addEventListener('change', function () {
    let eleveId = this.value;
    let classeId = document.getElementById('classSelection').value;
    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    resetTout();
    if (!eleveId || !classeId) return;

    // Charger la modalite de la classe
    fetch('/' + slug + '/get-modalite-exoneration?classe_id=' + classeId + '&annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            modaliteActuelle = data;
            eleveActuel = { id: eleveId, classeId: classeId, anneeScolaire: anneeScolaire };

            // Infos élève (depuis le select)
            let option = document.querySelector('#i_eleveId option:checked');
            let texte = option ? option.textContent.trim() : '';
            let parts = texte.split('(');
            document.getElementById('eleveNomComplet').textContent = parts[0].trim();
            document.getElementById('eleveMatriculeClasse').textContent = parts[1] ? parts[1].replace(')', '').trim() : '';

            // Par ceci :
            fetch('/get-eleve-info/' + eleveId)
                .then(res => res.json())
                .then(info => {
                    document.getElementById('elevePhoto').src = info.v_photo
                        ? '/' + info.v_photo
                        : '/assets/images/thumbs/student-details-img.png';
                });

            // Montants de la modalité
            document.getElementById('montantInscription').textContent = formatGNF(data.d_pirx_inscription);
            document.getElementById('montantMensuelle').textContent   = formatGNF(data.d_prix_mensuelle);
            document.getElementById('montantTranche1').textContent    = formatGNF(data.d_tranche1);
            document.getElementById('montantAnnuelle').textContent    = formatGNF(data.d_tranche_annuelle);

            document.getElementById('eleveInfoBlock').style.display  = 'block';
            document.getElementById('exonerationBlock').style.display = 'block';

            chargerHistoriqueExoneration(eleveId, anneeScolaire);
        })
        .catch(() => afficherToast('Erreur lors du chargement des modalités.', 'error'));
});

// ===================== CHOIX DU MODE DE PAIEMENT =====================
document.querySelectorAll('.mode-radio').forEach(radio => {
    radio.addEventListener('change', function () {
        document.getElementById('blocTypeExoneration').style.display = 'block';
        document.getElementById('blocMontants').style.display        = 'block';
        document.getElementById('blocDetails').style.display         = 'block';
        calculerMontants();
    });
});

// ===================== CHOIX DU TYPE D'EXONERATION =====================
document.querySelectorAll('input[name="type_exoneration"]').forEach(radio => {
    radio.addEventListener('change', function () {
        let isTotale = this.value === 'totale';
        let pInput = document.getElementById('pourcentageInput');
        document.getElementById('colPourcentage').style.display = isTotale ? 'none' : 'block';
        if (isTotale) { pInput.value = 100; pInput.disabled = true; }
        else { pInput.value = 50; pInput.disabled = false; }
        calculerMontants();
    });
});

document.getElementById('pourcentageInput').addEventListener('input', calculerMontants);

function getMontantInitial() {
    if (!modaliteActuelle) return 0;
    let mode = document.querySelector('input[name="mode_paiement"]:checked')?.value;
    let map = {
        'inscription'  : modaliteActuelle.d_pirx_inscription,
        'reinscription': modaliteActuelle.d_prix_reinscription,
        'mensuelle'    : modaliteActuelle.d_prix_mensuelle,
        '1er_tranche'  : modaliteActuelle.d_tranche1,
        '2eme_tranche' : modaliteActuelle.d_tranche2,
        '3eme_tranche' : modaliteActuelle.d_tranche3,
        'annuelle'     : modaliteActuelle.d_tranche_annuelle,
    };
    return parseFloat(map[mode] || 0);
}

function calculerMontants() {
    let montantInitial = getMontantInitial();
    let pourcentage    = parseFloat(document.getElementById('pourcentageInput').value) || 0;
    let montantExonere = (montantInitial * pourcentage) / 100;
    let reste          = montantInitial - montantExonere;

    document.getElementById('montantInitialDisplay').value  = formatGNF(montantInitial);
    document.getElementById('montantExonereDisplay').value  = formatGNF(montantExonere);
    document.getElementById('resteAPayerDisplay').value     = formatGNF(reste);
}

// ===================== HISTORIQUE EXONERATIONS =====================
function chargerHistoriqueExoneration(eleveId, anneeScolaire) {
    fetch('/' + slug + '/get-historique-exoneration/' + eleveId + '?annee_scolaire=' + anneeScolaire)
        .then(res => res.json())
        .then(data => {
            let tbody = document.getElementById('historiqueExonerationBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Aucune exonération</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(e =>
                '<tr>' +
                '<td>' + e.date + '</td>' +
                '<td>' + e.mode + '</td>' +
                '<td>' + e.type + '</td>' +
                '<td>' + formatGNF(e.montant_initial) + '</td>' +
                '<td>' + formatGNF(e.montant_exonere) + '</td>' +
                '<td>' + e.pourcentage + '%</td>' +
                '<td>' + e.motif + '</td>' +
                '<td>' + e.autorise_par + '</td>' +
                '<td><span class="badge ' + (e.statut === 'active' ? 'bg-success' : 'bg-danger') + '">' + e.statut + '</span></td>' +
                '</tr>'
            ).join('');
        })
        .catch(err => console.error(err));
}

// ===================== ENREGISTRER EXONERATION =====================
document.getElementById('btnEnregistrerExoneration').addEventListener('click', function () {
    let mode         = document.querySelector('input[name="mode_paiement"]:checked')?.value;
    let type         = document.querySelector('input[name="type_exoneration"]:checked')?.value;
    let pourcentage  = document.getElementById('pourcentageInput').value;
    let motif        = document.getElementById('motifInput').value;
    let date         = document.getElementById('dateExonerationInput').value;
    let autorisePar  = document.getElementById('autoriseParInput').value;

    if (!mode)        { afficherToast('Choisissez un mode de paiement.', 'error'); return; }
    if (!type)        { afficherToast('Choisissez le type d\'exonération.', 'error'); return; }
    if (!motif)       { afficherToast('Le motif est obligatoire.', 'error'); return; }
    if (!date)        { afficherToast('La date est obligatoire.', 'error'); return; }
    if (!autorisePar) { afficherToast('Le responsable est obligatoire.', 'error'); return; }

    let montantInitial = getMontantInitial();
    let montantExonere = (montantInitial * parseFloat(pourcentage)) / 100;

    Swal.fire({
        title: 'Confirmer l\'exonération ?',
        html: 'Mode : <strong>' + mode + '</strong><br>Type : <strong>' + type + '</strong><br>Montant exonéré : <strong>' + formatGNF(montantExonere) + '</strong>',
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Oui, enregistrer', cancelButtonText: 'Annuler', confirmButtonColor: '#0d6efd',
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Enregistrement...', allowOutsideClick: false, allowEscapeKey: false, didOpen: () => Swal.showLoading() });

        let formData = new FormData();
        formData.append('eleve_id',        eleveActuel.id);
        formData.append('classe_id',       eleveActuel.classeId);
        formData.append('annee_scolaire',  eleveActuel.anneeScolaire);
        formData.append('mode_paiement',   mode);
        formData.append('type_exoneration',type);
        formData.append('pourcentage',     pourcentage);
        formData.append('montant_initial', montantInitial);
        formData.append('montant_exonere', montantExonere);
        formData.append('motif',           motif);
        formData.append('date_exoneration',date);
        formData.append('autorise_par',    autorisePar);
        formData.append('slug',            slug);
        let fichier = document.getElementById('justificatifInput').files[0];
        if (fichier) formData.append('justificatif', fichier);
        formData.append('_token', document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}');

        fetch('/' + slug + '/enregistrer-exoneration', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json().then(json => ({ status: res.status, body: json })))
            .then(({ status, body }) => {
                Swal.close();
                if (status === 200 && body.success) {
                    afficherToast('Exonération enregistrée avec succès !', 'success');
                    chargerHistoriqueExoneration(eleveActuel.id, eleveActuel.anneeScolaire);
                    document.querySelectorAll('input[name="mode_paiement"]').forEach(r => r.checked = false);
                    document.getElementById('blocTypeExoneration').style.display = 'none';
                    document.getElementById('blocMontants').style.display        = 'none';
                    document.getElementById('blocDetails').style.display         = 'none';
                    document.getElementById('motifInput').value      = '';
                    document.getElementById('autoriseParInput').value = '';
                    document.getElementById('justificatifInput').value = '';
                } else {
                    afficherToast(body.message || 'Erreur lors de l\'enregistrement.', 'error');
                }
            })
            .catch(() => { Swal.close(); afficherToast('Erreur réseau.', 'error'); });
    });
});

document.getElementById('btnAnnuler').addEventListener('click', function () {
    document.querySelectorAll('input[name="mode_paiement"]').forEach(r => r.checked = false);
    document.getElementById('blocTypeExoneration').style.display = 'none';
    document.getElementById('blocMontants').style.display        = 'none';
    document.getElementById('blocDetails').style.display         = 'none';
});
</script>
@endsection
