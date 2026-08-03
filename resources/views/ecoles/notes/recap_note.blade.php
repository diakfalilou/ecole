@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Récapitulatif des moyennes</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Evaluation / Récapitulatif</span>
            </div>
        </div>
    </div>

    <form id="studentForm" method="POST" action="" class="mt-24">
        @csrf
        {{-- FILTRE --}}
        <div class="row gy-3">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0">Paramétrage</h6>
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

    <div id="printableArea">
        {{-- EN-TÊTE D'IMPRESSION : caché à l'écran, visible uniquement à l'impression/PDF --}}
        <div class="print-header">
            <div class="print-header-top">
                @if(!empty($ecoleInfo['logo']))
                    <img src="{{ asset($ecoleInfo['logo']) }}" alt="Logo" class="print-logo">
                @endif
                <div class="print-ecole-info">
                    @if(!empty($ecoleInfo['ministere']))
                        <p class="print-ministere">{{ $ecoleInfo['ministere'] }}</p>
                    @endif
                    <h3>{{ $ecoleInfo['nom'] }}</h3>
                    @if(!empty($ecoleInfo['slogan']))
                        <p class="print-slogan">« {{ $ecoleInfo['slogan'] }} »</p>
                    @endif
                    <p>
                        @if(!empty($ecoleInfo['adresse'])) {{ $ecoleInfo['adresse'] }} @endif
                        @if(!empty($ecoleInfo['telephone1'])) — Tél : {{ $ecoleInfo['telephone1'] }} @endif
                        @if(!empty($ecoleInfo['telephone2'])) / {{ $ecoleInfo['telephone2'] }} @endif
                    </p>
                </div>
            </div>
            <h4 id="printTitre">Récapitulatif des moyennes</h4>
            <p id="printSousTitre"></p>
        </div>

        {{-- GRILLE RECAPITULATIVE (générée dynamiquement en JS) --}}
        <div id="recapTableContainer" class="mt-24"></div>
    </div>

</div>

<div id="toastContainer" class="position-fixed top-0 end-0 p-16" style="z-index: 1080;"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    const SLUG = "{{ $slug }}";

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

        if (type !== 'error') {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => toast.remove(), 250);
            }, 6000);
        }
    }

    // ===================== FETCH AVEC GESTION D'ERREUR CENTRALISÉE =====================
    async function fetchJson(url, options = {}) {
        options.headers = { 'Accept': 'application/json', ...(options.headers || {}) };

        let res;
        try {
            res = await fetch(url, options);
        } catch (networkErr) {
            showToast('Impossible de contacter le serveur. Vérifiez votre connexion.');
            throw networkErr;
        }

        let data = null;
        try { data = await res.json(); } catch (e) { /* réponse non-JSON */ }

        if (!res.ok) {
            let message = `Erreur ${res.status}`;
            if (data?.message) {
                message = data.message;
            } else if (data?.errors) {
                message = Object.values(data.errors).flat().join(' — ');
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

    // ===================== NIVEAU -> CLASSES + PERIODE =====================
    document.getElementById('niveauSelect').addEventListener('change', function () {
        let niveauId = this.value;
        let niveauNom = this.options[this.selectedIndex].text.trim().toLowerCase();

        let classeSelect = document.getElementById('classSelection');
        let periodeSelect = document.getElementById('id_niveau_mode');
        let labelPeriode = document.getElementById('labelPeriode');

        periodeSelect.innerHTML = '<option value="">Séléctionner</option>';
        document.getElementById('recapTableContainer').innerHTML = '';

        if (!niveauId) {
            classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
            labelPeriode.innerHTML = 'Séléctionner un niveau <span class="text-danger-600">*</span>';
            return;
        }

        classeSelect.innerHTML = '<option>Chargement...</option>';

        fetchJson(`/${SLUG}/recap-notes/classes/${niveauId}`)
            .then(data => {
                classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
                data.forEach(c => {
                    classeSelect.innerHTML += `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`;
                });
            })
            .catch(() => { classeSelect.innerHTML = '<option value="">Erreur de chargement</option>'; });

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

    // ===================== SOUMISSION DU FILTRE -> CHARGER LE RECAP =====================
    let currentMatieres = [], currentEleves = [], currentMois = [], currentCtx = null;

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

        let niveauNom = document.getElementById('niveauSelect').selectedOptions[0]?.text.trim().toLowerCase() || '';
        let maxNote = (niveauNom.includes('maternelle') || niveauNom.includes('primaire')) ? 10 : 20;

        let nomClasse = document.getElementById('classSelection').selectedOptions[0]?.text || '';
        document.getElementById('printSousTitre').textContent =
            `Classe : ${nomClasse} — Année scolaire : ${anneeScolaire} — ${periodeType.charAt(0).toUpperCase() + periodeType.slice(1)} ${periodeNumero}`;

        document.getElementById('recapTableContainer').innerHTML = '<p class="text-center py-24">Chargement des données...</p>';

        fetchJson(`/${SLUG}/recap-notes/donnees`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: JSON.stringify({
                annee_scolaire: anneeScolaire,
                niveau_id: niveauId,
                classe_id: classeId,
                periode_type: periodeType,
                periode_numero: periodeNumero,
            }),
        }).then(data => {
            currentCtx = { anneeScolaire, niveauId, classeId, periodeType, periodeNumero, maxNote };
            currentMatieres = data.matieres;
            currentEleves = data.eleves;
            currentMois = data.mois;
            renderRecapTable(data.notes);
        }).catch(() => {
            document.getElementById('recapTableContainer').innerHTML =
                '<p class="text-center text-danger-600 py-24">Impossible de charger les données. Voir le message d\'erreur ci-dessus.</p>';
        });
    });

    // ===================== CALCUL + AFFICHAGE DU RECAP =====================
    // Même hypothèse de calcul que sur la grille de saisie :
    //   Moyenne matière = (moyenne des notes de cours de la période + note de compo) / 2
    //   Moyenne générale = moyenne des matières pondérée par leur coefficient
    function noteKey(eleveId, matiereId, type, mois) {
        return `${eleveId}_${matiereId}_${type}_${mois || 'compo'}`;
    }

    function computeMoyenneMatiere(notesState, notesPresentes, eleveId, matiereId) {
        let somme = 0, nb = 0;
        currentMois.forEach(mo => {
            let key = noteKey(eleveId, matiereId, 'cours', mo);
            if (notesPresentes.has(key)) { somme += notesState[key]; nb++; }
        });
        let moyenneCours = nb ? somme / nb : null;

        let keyCompo = noteKey(eleveId, matiereId, 'compo', null);
        let noteCompo = notesPresentes.has(keyCompo) ? notesState[keyCompo] : null;

        if (moyenneCours === null && noteCompo === null) return null; // aucune note saisie
        if (moyenneCours === null) return noteCompo;
        if (noteCompo === null) return moyenneCours;
        return (moyenneCours + noteCompo) / 2;
    }

    function getAppreciation(moyenne, maxNote) {
        if (moyenne === null) return { label: '—', classe: '' };

        let ratio = moyenne / maxNote;

        if (ratio >= 0.8)  return { label: 'Excellent',   classe: 'apprec-excellent' };
        if (ratio >= 0.7)  return { label: 'Très bien',   classe: 'apprec-tresbien' };
        if (ratio >= 0.6)  return { label: 'Bien',        classe: 'apprec-bien' };
        if (ratio >= 0.5)  return { label: 'Assez bien',  classe: 'apprec-assezbien' };
        if (ratio >= 0.4)  return { label: 'Passable',    classe: 'apprec-passable' };
        return { label: 'Insuffisant', classe: 'apprec-insuffisant' };
    }

    function renderRecapTable(notesBrutes) {
        if (!currentEleves.length) {
            document.getElementById('recapTableContainer').innerHTML =
                '<p class="text-center py-24">Aucun élève trouvé pour cette classe.</p>';
            return;
        }
        if (!currentMatieres.length) {
            document.getElementById('recapTableContainer').innerHTML =
                '<p class="text-center py-24">Aucune matière associée à cette classe.</p>';
            return;
        }

        // Reconstruction de l'état des notes à partir des données brutes reçues du serveur
        let notesState = {};
        let notesPresentes = new Set(); // clés effectivement saisies (pour ne pas compter les mois vides comme 0)
        notesBrutes.forEach(n => {
            let key = noteKey(n.eleve_id, n.matiere_id, n.type, n.mois);
            notesState[key] = parseFloat(n.note) || 0;
            notesPresentes.add(key);
        });

        let recapRows = []; // pour l'export Excel

        let html = `
            <div class="grid-toolbar no-print">
                <span class="grid-toolbar-info">
                    Récapitulatif — ${currentCtx.periodeType.charAt(0).toUpperCase() + currentCtx.periodeType.slice(1)} ${currentCtx.periodeNumero}
                </span>
                <div class="grid-toolbar-actions">
                    <button type="button" id="btnExportRecap" class="btn-tool">📊 Exporter Excel</button>
                    <button type="button" id="btnPrintRecap" class="btn-tool">🖨️ Imprimer / PDF</button>
                </div>
            </div>
            <div class="notes-grid-wrapper"><table class="notes-grid recap-grid"><thead><tr>
                <th class="col-eleve sticky-col">Élève</th>`;
        currentMatieres.forEach(m => { html += `<th>${m.nom}</th>`; });
        html += `<th class="col-general">Moyenne générale</th><th class="col-appreciation">Appréciation</th></tr></thead><tbody>`;

        currentEleves.forEach((el, idx) => {
            html += `<tr class="${idx % 2 === 0 ? 'row-even' : 'row-odd'}"><td class="col-eleve sticky-col">${el.nom} ${el.prenom}</td>`;
            let sumPonderee = 0;
            let coefUtilise = 0; // ne compte que les matières où au moins une note a été saisie
            let ligneExport = [`${el.nom} ${el.prenom}`];

            currentMatieres.forEach(m => {
                let moyenne = computeMoyenneMatiere(notesState, notesPresentes, el.id, m.id);
                let coef = parseFloat(m.coefficient) || 1;

                if (moyenne !== null) {
                    sumPonderee += moyenne * coef;
                    coefUtilise += coef;
                    html += `<td class="cell-recap">${moyenne.toFixed(2)}</td>`;
                    ligneExport.push(Number(moyenne.toFixed(2)));
                } else {
                    html += `<td class="cell-recap text-secondary-light">—</td>`;
                    ligneExport.push('—');
                }
            });

            let moyenneGenerale = coefUtilise ? sumPonderee / coefUtilise : null;
            let appreciation = getAppreciation(moyenneGenerale, currentCtx.maxNote);
            html += `<td class="cell-recap cell-general">${moyenneGenerale !== null ? moyenneGenerale.toFixed(2) : '—'}</td>`;
            html += `<td class="cell-recap ${appreciation.classe}">${appreciation.label}</td></tr>`;
            ligneExport.push(moyenneGenerale !== null ? Number(moyenneGenerale.toFixed(2)) : '—');
            ligneExport.push(appreciation.label);
            recapRows.push(ligneExport);
        });

        html += '</tbody></table></div>';

        document.getElementById('recapTableContainer').innerHTML = html;

        document.getElementById('btnExportRecap').addEventListener('click', function () {
            let headers = ['Élève', ...currentMatieres.map(m => m.nom), 'Moyenne générale', 'Appréciation'];
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.aoa_to_sheet([headers, ...recapRows]);
            XLSX.utils.book_append_sheet(wb, ws, 'Récapitulatif');
            XLSX.writeFile(wb, `recap_${currentCtx.periodeType}_${currentCtx.periodeNumero}_${currentCtx.anneeScolaire}.xlsx`);
        });

        document.getElementById('btnPrintRecap').addEventListener('click', () => window.print());
    }
</script>

<style>
    .sticky-col { position: sticky; left: 0; z-index: 2; }

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

    .notes-grid .col-eleve {
        text-align: left !important;
        min-width: 190px;
        background: #f9fafb;
        color: #101828;
        font-weight: 600;
        border-right: 2px solid #dfe3e8 !important;
    }

    .notes-grid td.col-eleve { z-index: 2; background: #fff; }
    .notes-grid tbody tr.row-odd td.col-eleve { background: #fafbfc; }
    .notes-grid tbody tr.row-odd { background: #fafbfc; }
    .notes-grid tbody tr:hover { background: #eef4ff; }
    .notes-grid tbody tr:hover td.col-eleve { background: #eef4ff; }

    .notes-grid td {
        border-bottom: 1px solid #eaecf0;
        border-right: 1px solid #eaecf0;
        text-align: center;
        vertical-align: middle;
    }

    .recap-grid th, .recap-grid td { min-width: 90px; }

    .recap-grid .col-general { background: #eef4ff; color: #1d2939; }

    .cell-recap { padding: 8px 6px !important; font-weight: 500; }

    .cell-recap.cell-general {
        background: #eef4ff;
        font-weight: 700;
        color: #1849a9;
    }

    .col-appreciation { min-width: 110px; }

    .apprec-excellent   { color: #0f5132; font-weight: 700; }
    .apprec-tresbien    { color: #146c43; font-weight: 700; }
    .apprec-bien        { color: #1849a9; font-weight: 600; }
    .apprec-assezbien   { color: #664d03; font-weight: 600; }
    .apprec-passable    { color: #b45309; font-weight: 600; }
    .apprec-insuffisant { color: #b02a37; font-weight: 700; }

    .grid-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    .grid-toolbar-info { font-size: 13px; color: #475467; }
    .grid-toolbar-actions { display: flex; gap: 8px; }

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

    .btn-tool:hover { background: #f4f6f8; border-color: #98a2b3; }

    /* ===== En-tête d'impression (masqué à l'écran) ===== */
    .print-header { display: none; }

    @media print {
        .print-header {
            display: block !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #1849a9;
            padding-bottom: 12px;
        }

        .print-header-top {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 10px;
        }

        .print-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .print-ecole-info { flex: 1; }

        .print-ecole-info h3 {
            margin: 0 0 2px;
            font-size: 18px;
            font-weight: 700;
            color: #101828;
        }

        .print-ministere {
            margin: 0 0 2px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #475467;
        }

        .print-slogan {
            margin: 0 0 4px;
            font-size: 12px;
            font-style: italic;
            color: #475467;
        }

        .print-ecole-info p {
            margin: 0;
            font-size: 12px;
            color: #333;
        }

        #printTitre {
            margin: 8px 0 2px;
            font-size: 16px;
            text-align: center;
            text-transform: uppercase;
            font-weight: 700;
        }

        #printSousTitre {
            margin: 0;
            font-size: 13px;
            text-align: center;
            color: #333;
        }
    }

    /* ===== Impression : n'imprime QUE le contenu ciblé (#printableArea), quel que soit le layout de la page ===== */
    @media print {
        body * { visibility: hidden; }

        #printableArea, #printableArea * { visibility: visible; }

        #printableArea {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .no-print { display: none !important; }

        .notes-grid-wrapper {
            max-height: none !important;
            overflow: visible !important;
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }

        .notes-grid thead th { position: static !important; }
        .col-eleve { position: static !important; }
    }
</style>

@endsection
