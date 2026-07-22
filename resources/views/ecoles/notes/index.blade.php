@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des notes de cours et compos</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Evaluation / Notes</span>
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
                        <div class="row gy-3 mb-24 align-items-end">
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
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <label for="id_niveau_mode" id="labelPeriode" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                    Séléctionner un niveau <span class="text-danger-600">*</span>
                                </label>
                                <select required id="id_niveau_mode" name="id_niveau_mode" class="form-control form-select">
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

    {{-- GRILLE DE SAISIE DES NOTES (générée dynamiquement en JS) --}}
    <div id="notesTableContainer" class="mt-24"></div>

</div>

<div id="toastContainer" class="position-fixed top-0 end-0 p-16" style="z-index: 1080;"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    const SLUG = "{{ $slug }}";
    let currentMaxNote = 20;

    // ===================== TOAST =====================
    function showToast(message, type = 'error') {
        const colors = {
            error:   { bg: '#f8d7da', border: '#f5c2c7', text: '#842029', icon: '✕' },
            success: { bg: '#d1e7dd', border: '#badbcc', text: '#0f5132', icon: '✓' },
            warning: { bg: '#fff3cd', border: '#ffecb5', text: '#664d03', icon: '!' }
        };
        const c = colors[type] || colors.error;

        const toast = document.createElement('div');
        toast.style.cssText = `
            background:${c.bg}; border:1px solid ${c.border}; color:${c.text};
            border-radius:8px; padding:12px 16px; margin-bottom:10px; min-width:280px; max-width:480px;
            box-shadow:0 4px 12px rgba(0,0,0,0.15); display:flex; align-items:flex-start; gap:10px;
            font-size:13px; line-height:1.4; word-break:break-word; opacity:0; transform:translateX(20px); transition:all .25s ease;
        `;
        toast.innerHTML = `
            <span style="font-weight:700;">${c.icon}</span>
            <span style="flex:1;">${message}</span>
            <span style="cursor:pointer; font-weight:700; line-height:1;" onclick="this.parentElement.remove()">×</span>
        `;

        document.getElementById('toastContainer').appendChild(toast);
        requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(0)'; });

        // Les erreurs restent affichées jusqu'à fermeture manuelle (message potentiellement long à lire)
        if (type !== 'error') {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => toast.remove(), 250);
            }, 6000);
        }
    }

    // ===================== FETCH AVEC GESTION D'ERREUR CENTRALISÉE =====================
    // Remonte le vrai message renvoyé par Laravel (erreur 500, validation 422, 403, etc.)
    async function fetchJson(url, options = {}) {
        options.headers = {
            'Accept': 'application/json',
            ...(options.headers || {})
        };

        let res;
        try {
            res = await fetch(url, options);
        } catch (networkErr) {
            showToast('Impossible de contacter le serveur. Vérifiez votre connexion.');
            throw networkErr;
        }

        let data = null;
        try { data = await res.json(); } catch (e) { /* réponse non-JSON (ex: page d'erreur HTML) */ }

        if (!res.ok) {
            let message = `Erreur ${res.status}`;

            if (data?.message) {
                message = data.message;
                // En mode debug, Laravel renvoie aussi 'exception', 'file', 'line' : on les affiche
                if (data.exception) {
                    let shortFile = (data.file || '').split(/[\\/]/).pop();
                    message += ` — [${data.exception}] dans ${shortFile}:${data.line}`;
                }
            } else if (data?.errors) {
                // Erreurs de validation Laravel : { errors: { champ: ["message"] } }
                message = Object.values(data.errors).flat().join(' — ');
            } else if (res.status === 500) {
                message = 'Erreur interne du serveur (500). Réponse non-JSON reçue — vérifiez que APP_DEBUG=true et storage/logs/laravel.log.';
            } else if (res.status === 404) {
                message = 'Ressource introuvable (404).';
            } else if (res.status === 403) {
                message = "Vous n'avez pas la permission d'effectuer cette action.";
            }

            showToast(message);
            throw new Error(message);
        }

        return data;
    }

    // Répartition des mois par type et numéro de période
    const MOIS_PAR_PERIODE = {
        trimestre: {
            1: ['Octobre', 'Novembre', 'Décembre'],
            2: ['Janvier', 'Février', 'Mars'],
            3: ['Avril', 'Mai', 'Juin']
        },
        semestre: {
            1: ['Octobre', 'Novembre', 'Décembre', 'Janvier', 'Février'],
            2: ['Mars', 'Avril', 'Mai', 'Juin']
        }
    };

    // ===================== NIVEAU -> CLASSES + PERIODE =====================
    document.getElementById('niveauSelect').addEventListener('change', function () {
        let niveauId = this.value;
        let niveauNom = this.options[this.selectedIndex].text.trim().toLowerCase();

        let classeSelect = document.getElementById('classSelection');
        let periodeSelect = document.getElementById('id_niveau_mode');
        let labelPeriode = document.getElementById('labelPeriode');

        // Reset
        periodeSelect.innerHTML = '<option value="">Séléctionner</option>';
        document.getElementById('notesTableContainer').innerHTML = '';

        if (!niveauId) {
            classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
            labelPeriode.innerHTML = 'Séléctionner un niveau <span class="text-danger-600">*</span>';
            return;
        }

        classeSelect.innerHTML = '<option>Chargement...</option>';

        // 1. Charger les classes liées au niveau
        fetchJson(`/${SLUG}/notes/classes/${niveauId}`)
            .then(data => {
                classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
                data.forEach(c => {
                    classeSelect.innerHTML += `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`;
                });
            })
            .catch(() => { classeSelect.innerHTML = '<option value="">Erreur de chargement</option>'; });

        // 2. Déterminer trimestre(s) ou semestre(s) selon le nom du niveau
        //    Primaire -> 3 trimestres | tous les autres -> 2 semestres
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

        // 3. Plafond des notes : Maternelle et Primaire -> /10 | autres niveaux -> /20
        currentMaxNote = (niveauNom.includes('maternelle') || niveauNom.includes('primaire')) ? 10 : 20;
    });

    // ===================== SOUMISSION DU FILTRE -> GENERATION DE LA GRILLE =====================
    document.getElementById('studentForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let anneeScolaire = document.getElementById('anneescolaireSelect').value;
        let niveauId = document.getElementById('niveauSelect').value;
        let classeId = document.getElementById('classSelection').value;
        let periodeSelect = document.getElementById('id_niveau_mode');
        let periodeNumero = periodeSelect.value;
        let periodeType = periodeSelect.options[periodeSelect.selectedIndex]?.dataset.type;

        if (!niveauId || !classeId || !periodeNumero) {
            showToast('Merci de sélectionner tous les champs.', 'warning');
            return;
        }

        let mois = MOIS_PAR_PERIODE[periodeType][periodeNumero];

        document.getElementById('notesTableContainer').innerHTML = '<p class="text-center py-24">Chargement des données...</p>';

        Promise.all([
            fetchJson(`/${SLUG}/notes/matieres/${classeId}`),
            fetchJson(`/${SLUG}/notes/eleves/${classeId}?` + new URLSearchParams({ annee_scolaire: anneeScolaire })),
            fetchJson(`/${SLUG}/notes/data?` + new URLSearchParams({
                annee_scolaire: anneeScolaire, niveau_id: niveauId, classe_id: classeId,
                periode_type: periodeType, periode_numero: periodeNumero
            }))
        ]).then(([matieres, eleves, notesExistantes]) => {
            renderNotesTable(matieres, eleves, mois, periodeType, periodeNumero, notesExistantes,
                { anneeScolaire, niveauId, classeId, maxNote: currentMaxNote });
        }).catch(() => {
            // Le message d'erreur détaillé est déjà affiché en toast par fetchJson()
            document.getElementById('notesTableContainer').innerHTML =
                '<p class="text-center text-danger-600 py-24">Impossible de charger les données. Voir le message d\'erreur ci-dessus.</p>';
        });
    });

    // ===================== CONSTRUCTION DE LA GRILLE =====================
    let currentMatieres = [], currentEleves = [], currentMois = [];
    let notesState = {}; // clé: "eleveId_matiereId_type_mois" (ou "_compo" pour la compo) => valeur

    function noteKey(eleveId, matiereId, type, mois) {
        return `${eleveId}_${matiereId}_${type}_${mois || 'compo'}`;
    }

    function renderNotesTable(matieres, eleves, mois, periodeType, periodeNumero, notesExistantes, ctx) {
        if (!eleves.length) {
            document.getElementById('notesTableContainer').innerHTML =
                '<p class="text-center py-24">Aucun élève trouvé pour cette classe.</p>';
            return;
        }
        if (!matieres.length) {
            document.getElementById('notesTableContainer').innerHTML =
                '<p class="text-center py-24">Aucune matière associée à cette classe.</p>';
            return;
        }

        // Contexte courant utilisé par la sauvegarde auto (perte de focus) et le récapitulatif
        currentCtx = { ...ctx, periodeType, periodeNumero };
        currentMatieres = matieres;
        currentEleves = eleves;
        currentMois = mois;

        // Réinitialise l'état et le pré-remplit avec les notes existantes (sinon 0 par défaut)
        notesState = {};
        eleves.forEach(el => {
            matieres.forEach(m => {
                mois.forEach(mo => { notesState[noteKey(el.id, m.id, 'cours', mo)] = 0; });
                notesState[noteKey(el.id, m.id, 'compo', null)] = 0;
            });
        });
        notesExistantes.forEach(n => {
            notesState[noteKey(n.eleve_id, n.matiere_id, n.type, n.mois)] = parseFloat(n.note) || 0;
        });

        let maxNote = ctx.maxNote;

        let html = `
            <div class="print-header">
                <h4>Relevé de notes</h4>
                <p>Année scolaire : <strong>${ctx.anneeScolaire}</strong> —
                   Classe : <strong>${document.getElementById('classSelection').selectedOptions[0]?.text || ''}</strong> —
                   ${periodeType.charAt(0).toUpperCase() + periodeType.slice(1)} ${periodeNumero}</p>
            </div>
            <div class="grid-toolbar no-print">
                <span class="grid-toolbar-info">Barème : notes sur <strong>${maxNote}</strong></span>
                <div class="grid-toolbar-actions">
                    <button type="button" id="btnBulletinAnnuel" class="btn-tool">📅 Bulletin annuel</button>
                    <button type="button" id="btnExportExcel" class="btn-tool">📊 Exporter Excel</button>
                    <button type="button" id="btnPrintPdf" class="btn-tool">🖨️ Imprimer / PDF</button>
                </div>
            </div>
            <div class="notes-grid-wrapper"><table class="notes-grid" id="gridNotes"><thead>`;

        // Ligne 1 : nom des matières
        html += '<tr><th rowspan="2" class="col-eleve sticky-col">Élève</th>';
        matieres.forEach(m => {
            html += `<th colspan="${mois.length + 2}" class="col-matiere">${m.nom}</th>`;
        });
        html += '</tr>';

        // Ligne 2 : sous-colonnes (mois + uniforme + compo)
        html += '<tr>';
        matieres.forEach(() => {
            mois.forEach(mo => { html += `<th class="col-mois">${mo.substring(0, 3)}</th>`; });
            html += `<th class="col-uniforme" title="Appliquer une note uniforme sur toute la période">Uniforme</th><th class="col-compo">Compo</th>`;
        });
        html += '</tr></thead><tbody>';

        // Lignes élèves
        eleves.forEach((el, idx) => {
            html += `<tr class="${idx % 2 === 0 ? 'row-even' : 'row-odd'}"><td class="col-eleve sticky-col">${el.nom} ${el.prenom}</td>`;
            matieres.forEach(m => {
                mois.forEach(mo => {
                    let val = notesState[noteKey(el.id, m.id, 'cours', mo)];
                    html += `<td class="cell-note"><input type="number" step="0.01" min="0" max="${maxNote}"
                                class="note-input" data-eleve="${el.id}" data-matiere="${m.id}"
                                data-type="cours" data-mois="${mo}" value="${val}"></td>`;
                });
                // Case "Uniforme" : recopie la 1ère note de cours sur tous les mois de la matière
                html += `<td class="cell-uniforme">
                            <input type="checkbox" class="mode-unique"
                                   data-eleve="${el.id}" data-matiere="${m.id}"
                                   title="Appliquer la 1ère note à tous les mois">
                          </td>`;
                // Compo
                let valCompo = notesState[noteKey(el.id, m.id, 'compo', null)];
                html += `<td class="cell-note cell-compo"><input type="number" step="0.01" min="0" max="${maxNote}"
                            class="note-input" data-eleve="${el.id}" data-matiere="${m.id}"
                            data-type="compo" data-mois="" value="${valCompo}"></td>`;
            });
            html += '</tr>';
        });

        html += `</tbody></table></div>
                  <p class="notes-grid-hint no-print">Les notes sont enregistrées automatiquement dès que vous quittez une case (perte de focus).</p>
                  <div id="summaryContainer" class="mt-24"></div>
                  <div id="bulletinContainer" class="mt-24"></div>`;

        document.getElementById('notesTableContainer').innerHTML = html;

        let gridWrapper = document.querySelector('.notes-grid-wrapper');

        // Met à jour notesState en direct à chaque frappe, et rafraîchit le récapitulatif
        gridWrapper.addEventListener('input', function (e) {
            if (!e.target.classList.contains('note-input')) return;
            let { eleve, matiere, type, mois: moisAttr } = e.target.dataset;
            notesState[noteKey(eleve, matiere, type, moisAttr)] = parseFloat(e.target.value) || 0;
            renderSummaryTable();
        });

        // Sauvegarde auto à la perte de focus (délégation sur le tableau)
        gridWrapper.addEventListener('focusout', function (e) {
            if (!e.target.classList.contains('note-input')) return;
            handleNoteBlur(e.target);
        });

        // Comportement de la case "Uniforme" (délégation sur le tableau)
        gridWrapper.addEventListener('change', function (e) {
            if (!e.target.classList.contains('mode-unique')) return;
            if (!e.target.checked) return;

            let { eleve: eleveId, matiere: matiereId } = e.target.dataset;
            let inputs = gridWrapper.querySelectorAll(
                `.note-input[data-eleve="${eleveId}"][data-matiere="${matiereId}"][data-type="cours"]`
            );
            let first = inputs[0];
            inputs.forEach(inp => {
                inp.value = first.value;
                notesState[noteKey(eleveId, matiereId, 'cours', inp.dataset.mois)] = parseFloat(first.value) || 0;
            });
            renderSummaryTable();
            saveNotes(Array.from(inputs), Array.from(inputs));
        });

        document.getElementById('btnBulletinAnnuel').addEventListener('click', loadBulletinAnnuel);
        document.getElementById('btnExportExcel').addEventListener('click', exportExcel);
        document.getElementById('btnPrintPdf').addEventListener('click', () => window.print());

        renderSummaryTable();
    }

    // ===================== TABLEAU RÉCAPITULATIF =====================
    // Hypothèse de calcul (modifiable si besoin) :
    //   Moyenne matière = (moyenne des notes de cours de la période + note de compo) / 2
    //   Moyenne générale = moyenne des matières pondérée par leur coefficient
    function computeMoyenneMatiere(eleveId, matiereId) {
        let sum = 0;
        currentMois.forEach(mo => { sum += notesState[noteKey(eleveId, matiereId, 'cours', mo)] || 0; });
        let moyenneCours = currentMois.length ? sum / currentMois.length : 0;

        let noteCompo = notesState[noteKey(eleveId, matiereId, 'compo', null)] || 0;

        return (moyenneCours + noteCompo) / 2;
    }

    function renderSummaryTable() {
        if (!currentEleves.length || !currentMatieres.length) return;

        let html = `<div class="grid-toolbar no-print"><span class="grid-toolbar-info">Tableau récapitulatif des moyennes</span></div>
                     <div class="notes-grid-wrapper"><table class="notes-grid recap-grid"><thead><tr>
                        <th class="col-eleve sticky-col">Élève</th>`;
        currentMatieres.forEach(m => { html += `<th>${m.nom}</th>`; });
        html += `<th class="col-general">Moyenne générale</th></tr></thead><tbody>`;

        let totalCoef = currentMatieres.reduce((s, m) => s + (parseFloat(m.coefficient) || 1), 0);

        currentEleves.forEach((el, idx) => {
            html += `<tr class="${idx % 2 === 0 ? 'row-even' : 'row-odd'}"><td class="col-eleve sticky-col">${el.nom} ${el.prenom}</td>`;
            let sumPonderee = 0;
            currentMatieres.forEach(m => {
                let moyenne = computeMoyenneMatiere(el.id, m.id);
                let coef = parseFloat(m.coefficient) || 1;
                sumPonderee += moyenne * coef;
                html += `<td class="cell-recap">${moyenne.toFixed(2)}</td>`;
            });
            let moyenneGenerale = totalCoef ? sumPonderee / totalCoef : 0;
            html += `<td class="cell-recap cell-general">${moyenneGenerale.toFixed(2)}</td></tr>`;
        });

        html += '</tbody></table></div>';

        document.getElementById('summaryContainer').innerHTML = html;
    }

    // ===================== BULLETIN ANNUEL (période par période + annuelle) =====================
    // Hypothèse de calcul : moyenne annuelle = moyenne simple des moyennes générales de chaque période
    // (non pondérée par le nombre de mois — dis-moi si tu veux une pondération différente)
    function loadBulletinAnnuel() {
        if (!currentCtx) return;

        let container = document.getElementById('bulletinContainer');
        container.innerHTML = '<p class="text-center py-16 no-print">Chargement du bulletin annuel...</p>';

        fetchJson(`/${SLUG}/notes/recap-annuel?` + new URLSearchParams({
            annee_scolaire: currentCtx.anneeScolaire,
            niveau_id: currentCtx.niveauId,
            classe_id: currentCtx.classeId,
            periode_type: currentCtx.periodeType
        }))
        .then(allNotes => renderBulletinAnnuel(allNotes))
        .catch(() => { container.innerHTML = ''; });
    }

    function renderBulletinAnnuel(allNotes) {
        let periodeType = currentCtx.periodeType;
        let nbPeriodes = Object.keys(MOIS_PAR_PERIODE[periodeType]).length;
        let libelle = periodeType.charAt(0).toUpperCase() + periodeType.slice(1);

        // Index : "periodeNumero_eleveId_matiereId_type_mois" => note
        let idx = {};
        allNotes.forEach(n => {
            let key = `${n.periode_numero}_${n.eleve_id}_${n.matiere_id}_${n.type}_${n.mois || 'compo'}`;
            idx[key] = parseFloat(n.note) || 0;
        });

        let totalCoef = currentMatieres.reduce((s, m) => s + (parseFloat(m.coefficient) || 1), 0);

        // Moyenne générale pondérée d'un élève pour une période donnée
        function moyennePeriode(eleveId, periodeNumero) {
            let moisListe = MOIS_PAR_PERIODE[periodeType][periodeNumero];
            let sumPonderee = 0;
            currentMatieres.forEach(m => {
                let sumCours = 0;
                moisListe.forEach(mo => { sumCours += idx[`${periodeNumero}_${eleveId}_${m.id}_cours_${mo}`] || 0; });
                let moyenneCours = moisListe.length ? sumCours / moisListe.length : 0;
                let noteCompo = idx[`${periodeNumero}_${eleveId}_${m.id}_compo_compo`] || 0;
                let moyenneMatiere = (moyenneCours + noteCompo) / 2;
                let coef = parseFloat(m.coefficient) || 1;
                sumPonderee += moyenneMatiere * coef;
            });
            return totalCoef ? sumPonderee / totalCoef : 0;
        }

        let html = `<div class="grid-toolbar no-print">
                        <span class="grid-toolbar-info">Bulletin annuel — moyenne générale par ${periodeType}</span>
                        <div class="grid-toolbar-actions">
                            <button type="button" id="btnExportBulletin" class="btn-tool">📊 Exporter Excel</button>
                        </div>
                     </div>
                     <div class="notes-grid-wrapper"><table class="notes-grid recap-grid"><thead><tr>
                        <th class="col-eleve sticky-col">Élève</th>`;
        for (let p = 1; p <= nbPeriodes; p++) { html += `<th>${libelle} ${p}</th>`; }
        html += `<th class="col-general">Moyenne annuelle</th></tr></thead><tbody>`;

        let bulletinRows = []; // conservé pour l'export Excel

        currentEleves.forEach((el, i) => {
            let moyennesPeriodes = [];
            for (let p = 1; p <= nbPeriodes; p++) { moyennesPeriodes.push(moyennePeriode(el.id, p)); }
            let moyenneAnnuelle = moyennesPeriodes.reduce((a, b) => a + b, 0) / nbPeriodes;

            html += `<tr class="${i % 2 === 0 ? 'row-even' : 'row-odd'}"><td class="col-eleve sticky-col">${el.nom} ${el.prenom}</td>`;
            moyennesPeriodes.forEach(m => { html += `<td class="cell-recap">${m.toFixed(2)}</td>`; });
            html += `<td class="cell-recap cell-general">${moyenneAnnuelle.toFixed(2)}</td></tr>`;

            bulletinRows.push([`${el.nom} ${el.prenom}`, ...moyennesPeriodes.map(m => Number(m.toFixed(2))), Number(moyenneAnnuelle.toFixed(2))]);
        });

        html += '</tbody></table></div>';

        document.getElementById('bulletinContainer').innerHTML = html;

        document.getElementById('btnExportBulletin').addEventListener('click', function () {
            let headers = ['Élève', ...Array.from({ length: nbPeriodes }, (_, p) => `${libelle} ${p + 1}`), 'Moyenne annuelle'];
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.aoa_to_sheet([headers, ...bulletinRows]);
            XLSX.utils.book_append_sheet(wb, ws, 'Bulletin annuel');
            XLSX.writeFile(wb, `bulletin_annuel_${currentCtx.anneeScolaire}.xlsx`);
        });
    }

    // ===================== EXPORT EXCEL =====================
    function exportExcel() {
        let wb = XLSX.utils.book_new();

        // Feuille 1 : notes détaillées (telles qu'affichées dans la grille)
        let gridData = [];
        let headerRow1 = ['Élève'];
        let headerRow2 = [''];
        currentMatieres.forEach(m => {
            currentMois.forEach(mo => { headerRow1.push(m.nom); headerRow2.push(mo.substring(0, 3)); });
            headerRow1.push(m.nom, m.nom);
            headerRow2.push('Uniforme', 'Compo');
        });
        gridData.push(headerRow1, headerRow2);

        currentEleves.forEach(el => {
            let row = [`${el.nom} ${el.prenom}`];
            currentMatieres.forEach(m => {
                currentMois.forEach(mo => {
                    let inp = document.querySelector(
                        `.note-input[data-eleve="${el.id}"][data-matiere="${m.id}"][data-type="cours"][data-mois="${mo}"]`
                    );
                    row.push(inp ? parseFloat(inp.value) || 0 : 0);
                });
                let chk = document.querySelector(`.mode-unique[data-eleve="${el.id}"][data-matiere="${m.id}"]`);
                row.push(chk?.checked ? 'Oui' : 'Non');
                let compoInp = document.querySelector(
                    `.note-input[data-eleve="${el.id}"][data-matiere="${m.id}"][data-type="compo"]`
                );
                row.push(compoInp ? parseFloat(compoInp.value) || 0 : 0);
            });
            gridData.push(row);
        });

        let wsNotes = XLSX.utils.aoa_to_sheet(gridData);
        XLSX.utils.book_append_sheet(wb, wsNotes, 'Notes détaillées');

        // Feuille 2 : récapitulatif des moyennes
        let recapData = [['Élève', ...currentMatieres.map(m => m.nom), 'Moyenne générale']];
        let totalCoef = currentMatieres.reduce((s, m) => s + (parseFloat(m.coefficient) || 1), 0);

        currentEleves.forEach(el => {
            let row = [`${el.nom} ${el.prenom}`];
            let sumPonderee = 0;
            currentMatieres.forEach(m => {
                let moyenne = computeMoyenneMatiere(el.id, m.id);
                let coef = parseFloat(m.coefficient) || 1;
                sumPonderee += moyenne * coef;
                row.push(Number(moyenne.toFixed(2)));
            });
            row.push(Number((totalCoef ? sumPonderee / totalCoef : 0).toFixed(2)));
            recapData.push(row);
        });

        let wsRecap = XLSX.utils.aoa_to_sheet(recapData);
        XLSX.utils.book_append_sheet(wb, wsRecap, 'Récapitulatif');

        let nomFichier = `notes_${currentCtx.periodeType}_${currentCtx.periodeNumero}_${currentCtx.anneeScolaire}.xlsx`;
        XLSX.writeFile(wb, nomFichier);
    }

    // ===================== SAUVEGARDE AUTO (au blur) =====================
    let currentCtx = null;

    function handleNoteBlur(inputEl) {
        if (inputEl.value === '') inputEl.value = 0;

        let max = currentCtx?.maxNote ?? 20;
        let val = parseFloat(inputEl.value);
        if (isNaN(val) || val < 0) { inputEl.value = 0; }
        else if (val > max) {
            inputEl.value = max;
            showToast(`La note maximale pour ce niveau est ${max}.`, 'warning');
        }

        let eleveId = inputEl.dataset.eleve, matiereId = inputEl.dataset.matiere, type = inputEl.dataset.type;

        // Si "Uniforme" est cochée pour ce couple élève/matière, on sauvegarde tous les mois d'un coup
        if (type === 'cours') {
            let checkbox = document.querySelector(`.mode-unique[data-eleve="${eleveId}"][data-matiere="${matiereId}"]`);
            if (checkbox && checkbox.checked) {
                let siblings = document.querySelectorAll(
                    `.note-input[data-eleve="${eleveId}"][data-matiere="${matiereId}"][data-type="cours"]`
                );
                siblings.forEach(s => { s.value = inputEl.value; });
                saveNotes(Array.from(siblings), siblings);
                return;
            }
        }

        saveNotes([inputEl], [inputEl]);
    }

    function saveNotes(inputEls, cellsToFlag) {
        if (!currentCtx) return;

        let notes = inputEls.map(inp => ({
            eleve_id: inp.dataset.eleve,
            matiere_id: inp.dataset.matiere,
            type: inp.dataset.type,
            mois: inp.dataset.mois || null,
            note: inp.value
        }));

        cellsToFlag.forEach(inp => inp.classList.add('is-saving'));

        fetchJson(`/${SLUG}/notes/save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                annee_scolaire: currentCtx.anneeScolaire,
                niveau_id: currentCtx.niveauId,
                classe_id: currentCtx.classeId,
                periode_type: currentCtx.periodeType,
                periode_numero: currentCtx.periodeNumero,
                notes: notes
            })
        })
        .then(() => {
            cellsToFlag.forEach(inp => {
                inp.classList.remove('is-saving');
                inp.classList.add('is-saved');
                setTimeout(() => inp.classList.remove('is-saved'), 1200);
            });
            renderSummaryTable();
        })
        .catch(() => {
            cellsToFlag.forEach(inp => {
                inp.classList.remove('is-saving');
                inp.classList.add('is-error');
            });
        });
    }
</script>

<style>
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 2;
    }

    .notes-grid-wrapper {
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 10px;
        overflow: auto;
        max-height: 72vh;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
    }

    .notes-grid {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 13px;
        font-family: inherit;
    }

    .notes-grid thead th {
        background: #f4f6f8;
        color: #344054;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .02em;
        font-size: 11px;
        border-bottom: 1px solid #dfe3e8;
        border-right: 1px solid #eaecf0;
        padding: 8px 6px;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 3;
        white-space: nowrap;
    }

    .notes-grid thead tr:first-child th {
        top: 0;
        z-index: 4;
    }

    .notes-grid thead tr:last-child th {
        top: 33px; /* hauteur approx. de la 1ère ligne d'en-tête */
    }

    .notes-grid .col-eleve {
        text-align: left !important;
        min-width: 190px;
        background: #f9fafb;
        color: #101828;
        font-weight: 600;
        border-right: 2px solid #dfe3e8 !important;
    }

    .notes-grid td.col-eleve {
        z-index: 2;
        background: #fff;
    }

    .notes-grid tbody tr.row-even td.col-eleve { background: #fff; }
    .notes-grid tbody tr.row-odd td.col-eleve { background: #fafbfc; }

    .notes-grid tbody tr.row-odd { background: #fafbfc; }
    .notes-grid tbody tr:hover { background: #eef4ff; }
    .notes-grid tbody tr:hover td.col-eleve { background: #eef4ff; }

    .notes-grid td {
        border-bottom: 1px solid #eaecf0;
        border-right: 1px solid #eaecf0;
        padding: 0;
        text-align: center;
        vertical-align: middle;
    }

    .cell-note {
        padding: 2px !important;
    }

    .cell-compo {
        background: #fcfaf3;
    }

    .note-input {
        width: 52px;
        text-align: center;
        border: 1px solid transparent;
        border-radius: 5px;
        padding: 5px 2px;
        font-size: 13px;
        background: transparent;
        outline: none;
        transition: background .15s, border-color .15s;
        -moz-appearance: textfield;
    }

    .note-input::-webkit-outer-spin-button,
    .note-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .note-input:hover { background: #f4f6f8; }
    .note-input:focus { background: #fff; border-color: #6c8bff; box-shadow: 0 0 0 2px rgba(108,139,255,.15); }

    .note-input.is-saving { border-color: #f0b429; background: #fffaf0; }
    .note-input.is-saved   { border-color: #12b76a; background: #ecfdf3; }
    .note-input.is-error   { border-color: #f04438; background: #fef3f2; }

    .cell-uniforme { background: #f9fafb; }

    .cell-uniforme .mode-unique {
        -webkit-appearance: checkbox !important;
        -moz-appearance: checkbox !important;
        appearance: checkbox !important;
        width: 16px !important;
        height: 16px !important;
        min-width: 16px;
        display: inline-block !important;
        opacity: 1 !important;
        visibility: visible !important;
        position: static !important;
        margin: 0 !important;
        cursor: pointer;
        accent-color: #1849a9;
    }

    .col-uniforme, .col-compo { min-width: 46px; }

    .notes-grid-hint {
        margin-top: 10px;
        font-size: 12.5px;
        color: #667085;
        font-style: italic;
    }

    /* ===== Barre d'outils (barème + export) ===== */
    .grid-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    .grid-toolbar-info {
        font-size: 13px;
        color: #475467;
    }

    .grid-toolbar-actions {
        display: flex;
        gap: 8px;
    }

    .btn-tool {
        border: 1px solid #d0d5dd;
        background: #fff;
        color: #344054;
        font-size: 13px;
        font-weight: 600;
        padding: 7px 14px;
        border-radius: 7px;
        cursor: pointer;
        transition: background .15s, border-color .15s;
    }

    .btn-tool:hover {
        background: #f4f6f8;
        border-color: #98a2b3;
    }

    /* ===== Tableau récapitulatif ===== */
    .recap-grid th, .recap-grid td {
        min-width: 90px;
    }

    .recap-grid .col-general {
        background: #eef4ff;
        color: #1d2939;
    }

    .cell-recap {
        padding: 8px 6px !important;
        font-weight: 500;
    }

    .cell-recap.cell-general {
        background: #eef4ff;
        font-weight: 700;
        color: #1849a9;
    }

    /* ===== En-tête d'impression (masqué à l'écran) ===== */
    .print-header {
        display: none;
    }

    @media print {
        .print-header {
            display: block !important;
            margin-bottom: 16px;
        }

        .print-header h4 {
            margin: 0 0 4px;
            font-size: 18px;
        }

        .print-header p {
            margin: 0;
            font-size: 13px;
            color: #333;
        }
    }

    /* ===== Impression ===== */
    @media print {
        .breadcrumb, #studentForm, .no-print {
            display: none !important;
        }

        .notes-grid-wrapper {
            max-height: none !important;
            overflow: visible !important;
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }

        .notes-grid thead th {
            position: static !important;
        }

        .col-eleve {
            position: static !important;
        }

        .note-input {
            border: none !important;
            background: transparent !important;
        }
    }
</style>

@endsection
