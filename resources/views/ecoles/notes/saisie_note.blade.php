@extends('ecoles.layout.app')

@section('containte')

    <div class="dashboard-main-body">

        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div>
                <h1 class="fw-semibold mb-4 h6 text-primary-light">Saisie des notes</h1>
                <div>
                    <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                    <span class="text-secondary-light"> / Evaluation / Saisie des note</span>
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
        {{-- ETAPE 2 : TABLEAU DE SAISIE DIRECTE --}}
        {{-- ============================================================ --}}
        <div class="row gy-3 mt-24" id="tableauSection" style="display:none;">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0" id="titreTableau">Saisie des notes</h6>
                    </div>
                    <div class="card-body p-20">
                        <div id="chargementStatus" class="mb-16"></div>
                        <div class="table-responsive">
                            <table class="table bordered-table mb-0" id="tableNotes">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th>Élève</th>
                                        <th id="entetColonneNote" style="width:150px;">Note</th>
                                    </tr>
                                </thead>
                                <tbody id="corpsTableNotes">
                                    {{-- rempli dynamiquement en JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Conteneur des toasts --}}
    <div id="toastContainer" style="position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:10px;"></div>

    <style>
        .toast-note {
            min-width: 260px;
            max-width: 360px;
            padding: 12px 16px;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            opacity: 0;
            transform: translateX(20px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .toast-note.show { opacity: 1; transform: translateX(0); }
        .toast-note.success { background-color: #16a34a; }
        .toast-note.error { background-color: #dc2626; }
    </style>

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

    let currentMaxNote = 20;

    const niveauSelect     = document.getElementById('niveauSelect');
    const classeSelect     = document.getElementById('classSelection');
    const matiereSelect    = document.getElementById('matiereSelect');
    const labelPeriode     = document.getElementById('labelPeriode');
    const periodeSelect    = document.getElementById('id_niveau_mode');
    const typeNoteSelect   = document.getElementById('typeNoteSelect');
    const moisWrapper      = document.getElementById('moisWrapper');
    const moisSelect       = document.getElementById('moisSelect');
    const anneeSelect      = document.getElementById('anneescolaireSelect');

    const studentForm       = document.getElementById('studentForm');

    const tableauSection    = document.getElementById('tableauSection');
    const titreTableau      = document.getElementById('titreTableau');
    const entetColonneNote  = document.getElementById('entetColonneNote');
    const corpsTableNotes   = document.getElementById('corpsTableNotes');
    const chargementStatus  = document.getElementById('chargementStatus');
    const toastContainer    = document.getElementById('toastContainer');

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

        // Route dédiée saisie-note pour éviter le conflit avec ChargerNoteController
        fetch(`/${SLUG}/saisie-notes/classes/${niveauId}`, { headers: { 'Accept': 'application/json' } })
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

        // Barème : Maternelle et Primaire -> notes sur 10 | Collège et Lycée -> notes sur 20
        currentMaxNote = (niveauNom.includes('maternelle') || niveauNom.includes('primaire')) ? 10 : 20;
    });

    // ===================== CLASSE -> MATIERES =====================
    classeSelect.addEventListener('change', function () {
        let classeId = this.value;
        matiereSelect.innerHTML = '<option value="">Séléctionner une matière</option>';

        if (!classeId) return;

        matiereSelect.innerHTML = '<option>Chargement...</option>';

        fetch(`/${SLUG}/saisie-notes/matieres/${classeId}`, { headers: { 'Accept': 'application/json' } })
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

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]').value;
    }

    // ===================== TOAST =====================
    function afficherToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-note ${type}`;
        toast.textContent = message;
        toastContainer.appendChild(toast);

        // force reflow pour déclencher la transition
        requestAnimationFrame(() => toast.classList.add('show'));

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ===================== SOUMISSION DU FILTRE -> CHARGER LE TABLEAU =====================
    studentForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!studentForm.checkValidity()) {
            studentForm.reportValidity();
            return;
        }

        const filtre = getFiltre();

        chargementStatus.innerHTML = '<div class="text-secondary-light">Chargement des élèves...</div>';
        corpsTableNotes.innerHTML = '';
        tableauSection.style.display = '';
        tableauSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

        fetch(`/${SLUG}/saisie-notes/eleves`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify(filtre),
        })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur inconnue');
                return data;
            })
            .then(data => {
                chargementStatus.innerHTML = '';
                afficherTableau(data, filtre);
            })
            .catch(err => {
                chargementStatus.innerHTML = `<div class="text-danger-600">${err.message}</div>`;
            });
    });

    // ===================== AFFICHAGE DU TABLEAU EDITABLE =====================
    function afficherTableau(data, filtre) {
        const nomColonne = filtre.type_note === 'compo' ? 'Composition' : filtre.mois;

        titreTableau.textContent = `Saisie des notes — ${nomColonne}`;
        entetColonneNote.textContent = `Note (${nomColonne}) /${currentMaxNote}`;

        corpsTableNotes.innerHTML = '';

        if (!data.eleves.length) {
            corpsTableNotes.innerHTML = '<tr><td colspan="3" class="text-center text-secondary-light">Aucun élève inscrit dans cette classe.</td></tr>';
            return;
        }

        data.eleves.forEach((eleve, index) => {
            const tr = document.createElement('tr');
            const valeurInitiale = (eleve.note !== null && eleve.note !== undefined) ? eleve.note : '';
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${eleve.nom} ${eleve.prenom}</td>
                <td>
                    <input type="number" class="form-control note-input" min="0" max="${currentMaxNote}" step="0.25"
                           data-eleve-id="${eleve.eleve_id}"
                           data-valeur-initiale="${valeurInitiale}"
                           value="${valeurInitiale}">
                </td>
            `;
            corpsTableNotes.appendChild(tr);
        });

        // Enregistrement automatique à la perte de focus (blur)
        document.querySelectorAll('.note-input').forEach(input => {
            input.addEventListener('blur', function () {
                const estValide = validerEtCorrigerNote(this);
                if (!estValide) return; // note invalide -> valeur restaurée, pas d'enregistrement, pas de toast succès

                const valeurActuelle = this.value;
                const valeurInitiale = this.dataset.valeurInitiale;

                // rien n'a changé -> pas d'appel inutile
                if (valeurActuelle === valeurInitiale) return;

                enregistrerUneNote(this, filtre);
            });
        });
    }

    // ===================== VALIDATION DU BARÈME =====================
    // Vérifie que la note est dans l'intervalle [0, currentMaxNote].
    // Si invalide : avertit l'utilisateur et restaure la dernière valeur valide (pas 0).
    // Retourne true si la valeur est correcte (ou vide), false si elle a été corrigée.
    function validerEtCorrigerNote(input) {
        if (input.value === '') return true; // champ vide autorisé (note pas encore saisie)

        const valeur = parseFloat(input.value);

        if (isNaN(valeur) || valeur < 0 || valeur > currentMaxNote) {
            afficherToast(`Note invalide : la note doit être comprise entre 0 et ${currentMaxNote}.`, 'error');
            input.value = input.dataset.valeurInitiale || '';
            return false;
        }

        return true;
    }

    // ===================== ENREGISTREMENT D'UNE SEULE NOTE (au blur) =====================
    function enregistrerUneNote(input, filtre) {
        const eleveId = input.dataset.eleveId;
        const note = input.value === '' ? null : parseFloat(input.value);

        input.disabled = true;

        fetch(`/${SLUG}/saisie-notes/enregistrer`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({
                ...filtre,
                lignes: [{ eleve_id: eleveId, note: note }],
            }),
        })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur inconnue');
                return data;
            })
            .then(() => {
                input.dataset.valeurInitiale = input.value;
                afficherToast('Note enregistrée avec succès.', 'success');
            })
            .catch(err => {
                afficherToast(err.message || "Échec de l'enregistrement.", 'error');
            })
            .finally(() => {
                input.disabled = false;
            });
    }
</script>

@endsection
