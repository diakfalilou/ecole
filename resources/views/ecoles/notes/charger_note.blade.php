@extends('ecoles.layout.app')

@section('containte')

    <div class="dashboard-main-body">

        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div>
                <h1 class="fw-semibold mb-4 h6 text-primary-light">Chargement des notes</h1>
                <div>
                    <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                    <span class="text-secondary-light"> / Evaluation / Charger note</span>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ETAPE 1 : FILTRE --}}
        {{-- ============================================================ --}}
        <form id="studentForm" method="POST" action="" enctype="multipart/form-data" class="mt-24">
            @csrf
            <div class="row gy-3">
                <div class="col-lg-12">
                    <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                            <h6 class="text-lg fw-semibold mb-0">Paramétrage des tranches de paiement</h6>
                        </div>
                        <div class="card-body p-20">
                            <div class="row gy-3 mb-24 align-items-end">

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                        Année scolaire <span class="text-danger-600">*</span>
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
                                    <select required name="niveau_id" id="niveauSelect" class="form-control form-select">
                                        <option value="">Séléctionner le niveau</option>
                                        @foreach ($niveaux as $niveau)
                                            <option value="{{ $niveau->i_niveauID }}">
                                                {{ $niveau->v_niveaux }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <label for="classSelection" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                                    <select required id="classSelection" name="classe_id" class="form-control form-select">
                                        <option value="">Séléctionner une classe</option>
                                    </select>
                                </div>

                                {{-- Matière --}}
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <label for="matiereSelect" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière <span class="text-danger-600">*</span></label>
                                    <select required id="matiereSelect" name="matiere_id" class="form-control form-select">
                                        <option value="">Séléctionner une matière</option>
                                    </select>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <label for="id_niveau_mode" id="labelPeriode" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                        Séléctionner un niveau <span class="text-danger-600">*</span>
                                    </label>
                                    <select required id="id_niveau_mode" name="id_niveau_mode" class="form-control form-select">
                                        <option value="">Séléctionner</option>
                                    </select>
                                </div>

                                {{-- Type de note (Cours / Composition) --}}
                                <div class="col-xxl-2 col-xl-3 col-sm-6">
                                    <label for="typeNoteSelect" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                        Type de note <span class="text-danger-600">*</span>
                                    </label>
                                    <select required id="typeNoteSelect" name="type_note" class="form-control form-select">
                                        <option value="">Séléctionner</option>
                                        <option value="cours">Note de cours</option>
                                        <option value="compo">Note de composition</option>
                                    </select>
                                </div>

                                {{-- Mois (visible uniquement si type_note = cours) --}}
                                <div class="col-xxl-2 col-xl-3 col-sm-6" id="moisWrapper" style="display:none;">
                                    <label for="moisSelect" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                        Mois <span class="text-danger-600">*</span>
                                    </label>
                                    <select id="moisSelect" name="mois" class="form-control form-select">
                                        <option value="">Séléctionner</option>
                                    </select>
                                </div>

                                <div class="col-xxl-1 col-xl-2 col-sm-6">
                                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">
                                        Valider
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- ============================================================ --}}
        {{-- ETAPE 2 : UPLOAD DU FICHIER --}}
        {{-- ============================================================ --}}
        <div class="row gy-3 mt-24" id="uploadSection" style="display:none;">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0">Charger le fichier de notes</h6>
                    </div>
                    <div class="card-body p-20">
                        <div class="row gy-3 align-items-end">
                            <div class="col-lg-9">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                    Fichier (Excel, CSV, PDF, JPEG, PNG) <span class="text-danger-600">*</span>
                                </label>
                                <input type="file" id="fichierInput" class="form-control"
                                       accept=".xlsx,.xls,.csv,.pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-lg-3">
                                <button type="button" id="btnChargerFichier" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">
                                    Charger le fichier
                                </button>
                            </div>
                        </div>
                        <div id="uploadStatus" class="mt-16"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ETAPE 3 : TABLEAU EDITABLE --}}
        {{-- ============================================================ --}}
        <div class="row gy-3 mt-24" id="tableauSection" style="display:none;">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0" id="titreTableau">Notes extraites</h6>
                        <button type="button" id="btnEnregistrerNotes" class="btn btn-success-600 radius-8 px-20 py-11">
                            Enregistrer les notes
                        </button>
                    </div>
                    <div class="card-body p-20">
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0" id="tableNotes">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th>Élève</th>
                                        <th id="entetColonneNote" style="width:150px;">Note</th>
                                        <th style="width:180px;">Statut</th>
                                    </tr>
                                </thead>
                                <tbody id="corpsTableNotes">
                                    {{-- rempli dynamiquement en JS --}}
                                </tbody>
                            </table>
                        </div>
                        <div id="enregistrementStatus" class="mt-16"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<script>
    const SLUG = "{{ $slug }}";

    // Répartition des mois par type de période et numéro
    const MOIS_PAR_PERIODE = {
        trimestre: {
            1: ['Octobre', 'Novembre', 'Décembre'],
            2: ['Janvier', 'Février', 'Mars'],
            3: ['Avril', 'Mai', 'Juin']
        },
        semestre: {
            1: ['Octobre', 'Novembre', 'Décembre', 'Janvier'],
            2: ['Février', 'Mars', 'Avril', 'Mai']
        }
    };

    const niveauSelect     = document.getElementById('niveauSelect');
    const classeSelect     = document.getElementById('classSelection');
    const matiereSelect    = document.getElementById('matiereSelect');
    const labelPeriode     = document.getElementById('labelPeriode');
    const periodeSelect    = document.getElementById('id_niveau_mode');
    const typeNoteSelect   = document.getElementById('typeNoteSelect');
    const moisWrapper      = document.getElementById('moisWrapper');
    const moisSelect       = document.getElementById('moisSelect');
    const anneeSelect      = document.getElementById('anneescolaireSelect');

    const studentForm      = document.getElementById('studentForm');
    const uploadSection     = document.getElementById('uploadSection');
    const fichierInput      = document.getElementById('fichierInput');
    const btnChargerFichier = document.getElementById('btnChargerFichier');
    const uploadStatus      = document.getElementById('uploadStatus');

    const tableauSection    = document.getElementById('tableauSection');
    const titreTableau      = document.getElementById('titreTableau');
    const entetColonneNote  = document.getElementById('entetColonneNote');
    const corpsTableNotes   = document.getElementById('corpsTableNotes');
    const btnEnregistrerNotes = document.getElementById('btnEnregistrerNotes');
    const enregistrementStatus = document.getElementById('enregistrementStatus');

    let currentBatchId = null;

    // ===================== NIVEAU -> CLASSES + PERIODE =====================
    niveauSelect.addEventListener('change', function () {
        let niveauId = this.value;
        let niveauNom = this.options[this.selectedIndex].text.trim().toLowerCase();

        periodeSelect.innerHTML = '<option value="">Séléctionner</option>';
        moisWrapper.style.display = 'none';
        moisSelect.innerHTML = '<option value="">Séléctionner</option>';
        typeNoteSelect.value = '';
        matiereSelect.innerHTML = '<option value="">Séléctionner une matière</option>';

        if (!niveauId) {
            classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
            labelPeriode.innerHTML = 'Séléctionner un niveau <span class="text-danger-600">*</span>';
            return;
        }

        classeSelect.innerHTML = '<option>Chargement...</option>';

        fetch(`/${SLUG}/notes/classes/${niveauId}`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
                data.forEach(c => {
                    classeSelect.innerHTML += `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`;
                });
            })
            .catch(() => {
                classeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });

        let type, nombre;
        if (niveauNom.includes('primaire')) {
            type = 'trimestre';
            nombre = 3;
        } else {
            type = 'semestre';
            nombre = 2;
        }

        let libelle = type.charAt(0).toUpperCase() + type.slice(1);
        labelPeriode.innerHTML = libelle + ' <span class="text-danger-600">*</span>';

        for (let i = 1; i <= nombre; i++) {
            let option = document.createElement('option');
            option.value = i;
            option.dataset.type = type;
            option.textContent = libelle + ' ' + i;
            periodeSelect.appendChild(option);
        }
    });

    // ===================== CLASSE -> MATIERES =====================
    classeSelect.addEventListener('change', function () {
        let classeId = this.value;
        matiereSelect.innerHTML = '<option value="">Séléctionner une matière</option>';

        if (!classeId) return;

        matiereSelect.innerHTML = '<option>Chargement...</option>';

        fetch(`/${SLUG}/notes/matieres/${classeId}`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                matiereSelect.innerHTML = '<option value="">Séléctionner une matière</option>';
                data.forEach(m => {
                    matiereSelect.innerHTML += `<option value="${m.id}">${m.nom}</option>`;
                });
            })
            .catch(() => {
                matiereSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    });

    // ===================== TYPE NOTE / MOIS =====================
    function remplirMois() {
        moisSelect.innerHTML = '<option value="">Séléctionner</option>';

        if (typeNoteSelect.value !== 'cours' || !periodeSelect.value) return;

        let periodeType = periodeSelect.options[periodeSelect.selectedIndex]?.dataset.type;
        let periodeNumero = periodeSelect.value;

        if (!periodeType || !MOIS_PAR_PERIODE[periodeType] || !MOIS_PAR_PERIODE[periodeType][periodeNumero]) return;

        MOIS_PAR_PERIODE[periodeType][periodeNumero].forEach(mo => {
            let option = document.createElement('option');
            option.value = mo;
            option.textContent = mo;
            moisSelect.appendChild(option);
        });
    }

    typeNoteSelect.addEventListener('change', function () {
        if (this.value === 'cours') {
            moisWrapper.style.display = '';
            moisSelect.setAttribute('required', 'required');
            remplirMois();
        } else {
            moisWrapper.style.display = 'none';
            moisSelect.removeAttribute('required');
            moisSelect.value = '';
        }
    });

    periodeSelect.addEventListener('change', function () {
        if (typeNoteSelect.value === 'cours') {
            remplirMois();
        }
    });

    // ===================== SOUMISSION DU FILTRE -> AFFICHER L'UPLOAD =====================
    studentForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!studentForm.checkValidity()) {
            studentForm.reportValidity();
            return;
        }

        // Réinitialise les étapes suivantes
        uploadStatus.innerHTML = '';
        enregistrementStatus.innerHTML = '';
        tableauSection.style.display = 'none';
        corpsTableNotes.innerHTML = '';
        currentBatchId = null;

        uploadSection.style.display = '';
        uploadSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    function getFiltre() {
        return {
            annee_scolaire: anneeSelect.value,
            niveau_id: niveauSelect.value,
            classe_id: classeSelect.value,
            matiere_id: matiereSelect.value,
            periode_type: periodeSelect.options[periodeSelect.selectedIndex]?.dataset.type || '',
            periode_numero: periodeSelect.value,
            type_note: typeNoteSelect.value,
            mois: moisSelect.value,
        };
    }

    // ===================== UPLOAD DU FICHIER =====================
    btnChargerFichier.addEventListener('click', function () {
        if (!fichierInput.files.length) {
            uploadStatus.innerHTML = '<div class="text-danger-600">Veuillez sélectionner un fichier.</div>';
            return;
        }

        const filtre = getFiltre();
        const formData = new FormData();
        formData.append('fichier', fichierInput.files[0]);
        Object.keys(filtre).forEach(key => formData.append(key, filtre[key]));

        btnChargerFichier.disabled = true;
        btnChargerFichier.textContent = 'Traitement en cours...';
        uploadStatus.innerHTML = '<div class="text-secondary-light">Lecture et extraction du fichier, veuillez patienter...</div>';

        fetch(`/${SLUG}/notes/import`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]').value,
            },
            body: formData,
        })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur inconnue');
                return data;
            })
            .then(data => {
                currentBatchId = data.batch_id;
                uploadStatus.innerHTML = `<div class="text-success-600">Fichier traité avec succès (${data.eleves.length} élève(s)).</div>`;
                afficherTableau(data);
            })
            .catch(err => {
                uploadStatus.innerHTML = `<div class="text-danger-600">${err.message}</div>`;
            })
            .finally(() => {
                btnChargerFichier.disabled = false;
                btnChargerFichier.textContent = 'Charger le fichier';
            });
    });

    // ===================== AFFICHAGE DU TABLEAU EDITABLE =====================
    function afficherTableau(data) {
        const filtre = getFiltre();
        const nomColonne = filtre.type_note === 'compo' ? 'Composition' : filtre.mois;

        titreTableau.textContent = `Notes — ${nomColonne}`;
        entetColonneNote.textContent = `Note (${nomColonne}) /20`;

        corpsTableNotes.innerHTML = '';

        data.eleves.forEach((eleve, index) => {
            const tr = document.createElement('tr');

            let badge = '';
            if (eleve.statut_match === 'exact') {
                badge = '<span class="badge bg-success-focus text-success-600">Correspondance trouvée</span>';
            } else if (eleve.statut_match === 'probable') {
                badge = `<span class="badge bg-warning-focus text-warning-600" title="Nom lu : ${eleve.nom_extrait || ''}">À vérifier</span>`;
            } else {
                badge = '<span class="badge bg-danger-focus text-danger-600">Non trouvé dans le fichier</span>';
            }

            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${eleve.nom} ${eleve.prenom}</td>
                <td>
                    <input type="number" class="form-control note-input" min="0" max="20" step="0.25"
                           data-eleve-id="${eleve.eleve_id}"
                           value="${eleve.note !== null && eleve.note !== undefined ? eleve.note : ''}">
                </td>
                <td>${badge}</td>
            `;
            corpsTableNotes.appendChild(tr);
        });

        tableauSection.style.display = '';
        tableauSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ===================== ENREGISTREMENT DEFINITIF =====================
    btnEnregistrerNotes.addEventListener('click', function () {
        if (!currentBatchId) {
            enregistrementStatus.innerHTML = '<div class="text-danger-600">Aucun import en cours.</div>';
            return;
        }

        const filtre = getFiltre();
        const lignes = Array.from(document.querySelectorAll('.note-input')).map(input => ({
            eleve_id: input.dataset.eleveId,
            note: input.value === '' ? null : parseFloat(input.value),
        }));

        btnEnregistrerNotes.disabled = true;
        btnEnregistrerNotes.textContent = 'Enregistrement...';
        enregistrementStatus.innerHTML = '';

        fetch(`/${SLUG}/notes/enregistrer`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({
                batch_id: currentBatchId,
                ...filtre,
                lignes: lignes,
            }),
        })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur inconnue');
                return data;
            })
            .then(data => {
                enregistrementStatus.innerHTML = `<div class="text-success-600">${data.nb_notes_enregistrees} note(s) enregistrée(s) avec succès.</div>`;
            })
            .catch(err => {
                enregistrementStatus.innerHTML = `<div class="text-danger-600">${err.message}</div>`;
            })
            .finally(() => {
                btnEnregistrerNotes.disabled = false;
                btnEnregistrerNotes.textContent = 'Enregistrer les notes';
            });
    });
</script>

@endsection
