@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Fiches de notes (vierges à imprimer)</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Evaluation / Fiches de notes</span>
            </div>
        </div>
    </div>

    {{-- FILTRE --}}
    <form id="ficheForm" class="mt-24 no-print">
        <div class="row gy-3">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0">Générer les fiches</h6>
                    </div>
                    <div class="card-body p-20">
                        <div class="row gy-3 align-items-end">
                            <div class="col-xxl-3 col-xl-3 col-sm-6">
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
                            <div class="col-xxl-3 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau <span class="text-danger-600">*</span></label>
                                <select required id="niveauSelect" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                    @foreach ($niveaux as $niveau)
                                        <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                                <select required id="classSelection" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-2 col-sm-6">
                                <label id="labelPeriode" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                    Période <span class="text-danger-600">*</span>
                                </label>
                                <select required id="periodeSelect" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-1 col-xl-1 col-sm-6">
                                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">
                                    Générer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- FICHES GÉNÉRÉES --}}
    <div id="fichesContainer" class="mt-24"></div>

</div>

<div id="toastContainer" class="position-fixed top-0 end-0 p-16 no-print" style="z-index: 1080;"></div>

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
                if (data.exception) {
                    let shortFile = (data.file || '').split(/[\\/]/).pop();
                    message += ` — [${data.exception}] dans ${shortFile}:${data.line}`;
                }
            } else if (data?.errors) {
                message = Object.values(data.errors).flat().join(' — ');
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
        let periodeSelect = document.getElementById('periodeSelect');
        let labelPeriode = document.getElementById('labelPeriode');

        classeSelect.innerHTML = '<option value="">Séléctionner</option>';
        periodeSelect.innerHTML = '<option value="">Séléctionner</option>';

        if (!niveauId) {
            labelPeriode.innerHTML = 'Période <span class="text-danger-600">*</span>';
            return;
        }

        fetchJson(`/${SLUG}/notes/classes/${niveauId}`)
            .then(data => {
                classeSelect.innerHTML = '<option value="">Séléctionner</option>';
                data.forEach(c => { classeSelect.innerHTML += `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`; });
            })
            .catch(() => { classeSelect.innerHTML = '<option value="">Erreur de chargement</option>'; });

        let type, nombre;
        if (niveauNom.includes('primaire')) { type = 'trimestre'; nombre = 3; }
        else { type = 'semestre'; nombre = 2; }

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

    // ===================== GÉNÉRATION DES FICHES =====================
    document.getElementById('ficheForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let anneeScolaire = document.getElementById('anneescolaireSelect').value;
        let niveauId = document.getElementById('niveauSelect').value;
        let classeId = document.getElementById('classSelection').value;
        let periodeSelect = document.getElementById('periodeSelect');
        let periodeNumero = periodeSelect.value;
        let periodeType = periodeSelect.options[periodeSelect.selectedIndex]?.dataset.type;

        if (!niveauId || !classeId || !periodeNumero) {
            showToast('Merci de sélectionner tous les champs.', 'warning');
            return;
        }

        document.getElementById('fichesContainer').innerHTML = '<p class="text-center py-24">Génération des fiches...</p>';

        fetchJson(`/${SLUG}/fiche-notes/data?` + new URLSearchParams({
            annee_scolaire: anneeScolaire, niveau_id: niveauId, classe_id: classeId,
            periode_type: periodeType, periode_numero: periodeNumero
        }))
        .then(data => {
            try {
                renderFiches(data);
            } catch (err) {
                console.error(err);
                showToast('Erreur lors de l\'affichage : ' + err.message);
            }
        })
        .catch(() => {
            document.getElementById('fichesContainer').innerHTML =
                '<p class="text-center text-danger-600 py-24">Impossible de générer les fiches.</p>';
        });
    });

    // ===================== CONSTRUCTION D'UNE FICHE (tableau élèves x mois/compo) =====================
    // matieresAffichees : tableau de { id, nom } -> une seule matière (collège/lycée) ou plusieurs (maternelle/primaire)
    function buildFicheTable(data, matieresAffichees, titreMatiere) {
        let colspanParMatiere = data.mois.length + 1; // mois + compo

        let html = `
            <div class="fiche-sheet">
                <div class="fiche-header">
                    <div class="fiche-etab">
                        <div class="fiche-etab-nom">${data.ecole.nom}</div>
                        ${data.ecole.logo ? `<img src="${data.ecole.logo}" class="fiche-logo" alt="Logo">` : ''}
                    </div>
                    <div class="fiche-titre">
                        <h3>FICHE DE SAISIE DES NOTES</h3>
                        <span>${titreMatiere ? titreMatiere + ' — ' : ''}${data.classe} — ${data.periode_type.charAt(0).toUpperCase() + data.periode_type.slice(1)} ${data.periode_numero} — ${data.annee_scolaire}</span>
                    </div>
                </div>

                <div class="fiche-infos">
                    <div><span>Niveau</span><strong>${data.niveau}</strong></div>
                    <div><span>Classe</span><strong>${data.classe}</strong></div>
                    <div><span>Barème</span><strong>Notes sur ${data.max_note}</strong></div>
                    <div><span>Effectif</span><strong>${data.eleves.length} élève(s)</strong></div>
                </div>

                <div class="fiche-table-wrapper">
                <table class="fiche-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="fiche-col-eleve">Élève</th>
                            ${matieresAffichees.map(m => `<th colspan="${colspanParMatiere}">${m.nom}</th>`).join('')}
                        </tr>
                        <tr>
                            ${matieresAffichees.map(() =>
                                data.mois.map(mo => `<th class="fiche-col-mois">${mo.substring(0, 3)}</th>`).join('') +
                                `<th class="fiche-col-compo">Compo.</th>`
                            ).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${data.eleves.map((el, i) => `
                            <tr>
                                <td class="fiche-col-eleve">${i + 1}. ${el.nom} ${el.prenom}</td>
                                ${matieresAffichees.map(() =>
                                    data.mois.map(() => `<td class="fiche-cell"></td>`).join('') +
                                    `<td class="fiche-cell fiche-cell-compo"></td>`
                                ).join('')}
                            </tr>
                        `).join('')}
                  </tbody>
                </table>
                </div>
                <div class="fiche-footer">
                    <div class="fiche-signature">
                        <span>Nom et signature de l'enseignant</span>
                        <div class="fiche-signature-line"></div>
                    </div>
                    <div class="fiche-date">
                        <span>Date de remise</span>
                        <div class="fiche-signature-line"></div>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    // ===================== RENDU (combiné ou par matière selon le niveau) =====================
    function renderFiches(data) {
        if (!data.eleves.length) {
            document.getElementById('fichesContainer').innerHTML =
                '<p class="text-center py-24">Aucun élève trouvé pour cette classe.</p>';
            return;
        }
        if (!data.matieres.length) {
            document.getElementById('fichesContainer').innerHTML =
                '<p class="text-center py-24">Aucune matière associée à cette classe.</p>';
            return;
        }

        let html = `<div class="fiche-toolbar no-print">
                        <button type="button" id="btnPrintFiches" class="btn-tool">🖨️ Imprimer toutes les fiches</button>
                    </div>`;

        if (data.combiner) {
            // Maternelle / Primaire : une seule fiche avec toutes les matières
            html += buildFicheTable(data, data.matieres, null);
        } else {
            // Collège / Lycée : une fiche distincte par matière (saut de page entre chaque)
            html += data.matieres.map((m, idx) => `
                <div class="${idx > 0 ? 'fiche-page-break' : ''}">
                    ${buildFicheTable(data, [m], m.nom)}
                </div>
            `).join('');
        }

        document.getElementById('fichesContainer').innerHTML = html;

        document.getElementById('btnPrintFiches').addEventListener('click', () => window.print());
    }
</script>

<style>
    .fiche-toolbar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 14px;
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

    .btn-tool:hover { background: #f4f6f8; border-color: #98a2b3; }

    /* ===== Feuille de la fiche (style classique / utilitaire) ===== */
    .fiche-sheet {
        background: #fff;
        width: 100%;
        margin: 0 0 40px;
        padding: 24px 0;
        font-family: Arial, sans-serif;
        color: #1d2939;
    }

    .fiche-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #344054;
        padding-bottom: 12px;
        margin-bottom: 18px;
        gap: 16px;
    }

    .fiche-etab {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .fiche-etab-nom {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 14px;
    }

    .fiche-logo {
        max-height: 40px;
        max-width: 100px;
        object-fit: contain;
    }

    .fiche-titre {
        text-align: right;
    }

    .fiche-titre h3 {
        margin: 0;
        font-size: 16px;
        letter-spacing: .03em;
    }

    .fiche-titre span {
        font-size: 12.5px;
        color: #475467;
    }

    .fiche-infos {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .fiche-infos span {
        display: block;
        font-size: 10.5px;
        text-transform: uppercase;
        color: #98a2b3;
    }

    .fiche-infos strong {
        font-size: 13.5px;
    }

    .fiche-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .fiche-table thead {
        display: table-header-group; /* répète les en-têtes sur chaque page imprimée */
    }

    .fiche-table thead th {
        background: #f4f6f8;
        border: 1px solid #d0d5dd;
        padding: 7px 6px;
        text-align: center;
        font-size: 11px;
        text-transform: uppercase;
        color: #344054;
    }

    .fiche-col-eleve {
        text-align: left !important;
        min-width: 170px;
    }

    .fiche-table td {
        border: 1px solid #d0d5dd;
        padding: 0;
        height: 26px;
    }

    .fiche-cell {
        min-width: 42px;
    }

    .fiche-cell-compo {
        background: #fcfaf3;
    }

    .fiche-col-mois { min-width: 42px; }
    .fiche-col-compo { min-width: 46px; background: #f4f0e2; }

    .fiche-table tbody tr:nth-child(even) { background: #fcfcfd; }

    .fiche-footer {
        display: flex;
        justify-content: space-between;
        gap: 40px;
        margin-top: 30px;
    }

    .fiche-signature, .fiche-date {
        flex: 1;
        text-align: center;
    }

    .fiche-signature span, .fiche-date span {
        font-size: 11.5px;
        color: #475467;
    }

    .fiche-signature-line {
        margin-top: 26px;
        border-top: 1px solid #98a2b3;
    }

    /* Saut de page entre chaque fiche matière (collège/lycée) */
    .fiche-page-break {
        page-break-before: always;
    }

    .fiche-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .fiche-table {
        width: 100%;
        min-width: max-content; /* le tableau garde sa largeur naturelle, le wrapper scrolle */
    }

    /* ===== Impression : uniquement les fiches, en paysage ===== */
    /* ===== Impression : uniquement les fiches, en paysage ===== */
    @page {
        size: A4 landscape;
        margin: 8mm;
    }

    @media print {
        html, body {
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }

        /* Neutralise tout conteneur parent du layout qui limiterait la hauteur/le scroll
           (dashboard-main-body et autres wrappers du template) */
        .dashboard-main-body,
        .dashboard-main-body * {
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }

        body * { visibility: hidden; }
        #fichesContainer, #fichesContainer * { visibility: visible; }

        #fichesContainer {
            position: static !important; /* plus fiable que absolute, qui peut rester clippé par un ancêtre */
            width: 100% !important;
            overflow: visible !important;
        }

        .no-print, .breadcrumb { display: none !important; }

        .fiche-sheet {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 0 20px !important;
            width: 100% !important;
        }

        /* Le scroll horizontal n'a aucun sens sur papier : on le désactive complètement */
        .fiche-table-wrapper {
            overflow: visible !important;
            width: 100% !important;
            -webkit-overflow-scrolling: auto !important;
        }

        .fiche-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed;
            font-size: 10px;
        }

        .fiche-table th,
        .fiche-table td {
            padding: 3px 2px !important;
        }

        .fiche-col-eleve {
            min-width: 0 !important;
            width: 140px;
        }

        .fiche-col-mois,
        .fiche-cell,
        .fiche-col-compo,
        .fiche-cell-compo {
            min-width: 0 !important;
        }

        body {
            background: #fff !important;
        }

        .fiche-page-break {
            page-break-before: always;
        }

        .fiche-table {
            page-break-inside: avoid;
        }

        .fiche-table tr {
            page-break-inside: avoid;
        }
    }
</style>

@endsection
