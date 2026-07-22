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
                            <div class="col-xxl-2 col-xl-2 col-sm-6">
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
                            <div class="col-xxl-2 col-xl-2 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau <span class="text-danger-600">*</span></label>
                                <select required id="niveauSelect" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                    @foreach ($niveaux as $niveau)
                                        <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-2 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                                <select required id="classSelection" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-2 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière</label>
                                <select id="matiereSelect" class="form-control form-select">
                                    <option value="">Toutes les matières</option>
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
                            <div class="col-xxl-2 col-xl-2 col-sm-6">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

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
        let matiereSelect = document.getElementById('matiereSelect');
        let periodeSelect = document.getElementById('periodeSelect');
        let labelPeriode = document.getElementById('labelPeriode');

        classeSelect.innerHTML = '<option value="">Séléctionner</option>';
        matiereSelect.innerHTML = '<option value="">Toutes les matières</option>';
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

    // ===================== CLASSE -> MATIÈRES (pour le filtre optionnel) =====================
    document.getElementById('classSelection').addEventListener('change', function () {
        let classeId = this.value;
        let matiereSelect = document.getElementById('matiereSelect');

        matiereSelect.innerHTML = '<option value="">Toutes les matières</option>';
        if (!classeId) return;

        fetchJson(`/${SLUG}/notes/matieres/${classeId}`)
            .then(data => {
                data.forEach(m => { matiereSelect.innerHTML += `<option value="${m.id}">${m.nom}</option>`; });
            })
            .catch(() => { /* si ça échoue, l'utilisateur garde simplement "Toutes les matières" */ });
    });

    // ===================== GÉNÉRATION DES FICHES =====================
    document.getElementById('ficheForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let anneeScolaire = document.getElementById('anneescolaireSelect').value;
        let niveauId = document.getElementById('niveauSelect').value;
        let classeId = document.getElementById('classSelection').value;
        let matiereId = document.getElementById('matiereSelect').value;
        let periodeSelect = document.getElementById('periodeSelect');
        let periodeNumero = periodeSelect.value;
        let periodeType = periodeSelect.options[periodeSelect.selectedIndex]?.dataset.type;

        if (!niveauId || !classeId || !periodeNumero) {
            showToast('Merci de sélectionner tous les champs.', 'warning');
            return;
        }

        document.getElementById('fichesContainer').innerHTML = '<p class="text-center py-24">Génération des fiches...</p>';

        let params = {
            annee_scolaire: anneeScolaire, niveau_id: niveauId, classe_id: classeId,
            periode_type: periodeType, periode_numero: periodeNumero
        };
        if (matiereId) params.matiere_id = matiereId;

        fetchJson(`/${SLUG}/fiche-notes/data?` + new URLSearchParams(params))
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
    // matieresAffichees : tableau de { id, nom } -> une seule matière (sélection explicite ou collège/lycée) ou plusieurs (maternelle/primaire sans filtre)
    let ficheCounter = 0;

    function buildFicheTable(data, matieresAffichees, titreMatiere) {
        let colspanParMatiere = data.mois.length + 1; // mois + compo
        let ficheId = `fiche-qr-${ficheCounter++}`;
        let libellePeriode = `${data.periode_type.charAt(0).toUpperCase() + data.periode_type.slice(1)} ${data.periode_numero}`;
        let ficheRef = `${data.classe} / ${titreMatiere || 'Toutes matières'} / ${libellePeriode} / ${data.annee_scolaire}`.toUpperCase();

        let html = `
            <div class="fiche-sheet">
                <div class="fiche-header">
                    <div class="fiche-etab">
                        ${data.ecole.logo ? `<img src="${data.ecole.logo}" class="fiche-logo" alt="Logo">` : ''}
                        <div class="fiche-etab-text">
                            <div class="fiche-etab-nom">${data.ecole.nom}</div>
                            <div class="fiche-etab-sub">Année scolaire ${data.annee_scolaire}</div>
                        </div>
                    </div>
                    <div class="fiche-titre">
                        <h3>Fiche de saisie des notes</h3>
                        <span>${titreMatiere ? titreMatiere + ' — ' : 'Toutes matières — '}${data.classe} — ${libellePeriode}</span>
                    </div>
                    <div class="fiche-qr-block">
                        <div class="fiche-qr" id="${ficheId}" data-qrtext="${ficheRef}"></div>
                        <span class="fiche-qr-label">Réf. document</span>
                    </div>
                </div>

                <table class="fiche-infos">
                    <tr>
                        <td><span>Niveau</span><strong>${data.niveau}</strong></td>
                        <td><span>Classe</span><strong>${data.classe}</strong></td>
                        <td><span>Matière(s)</span><strong>${titreMatiere || 'Toutes matières'}</strong></td>
                        <td><span>Période</span><strong>${libellePeriode}</strong></td>
                        <td><span>Barème</span><strong>Notes sur ${data.max_note}</strong></td>
                        <td><span>Effectif</span><strong>${data.eleves.length} élève(s)</strong></td>
                    </tr>
                </table>

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

                <div class="fiche-legende">
                    <span><i class="fiche-legende-swatch fiche-legende-mois"></i> Note de cours (par mois)</span>
                    <span><i class="fiche-legende-swatch fiche-legende-compo"></i> Note de composition</span>
                    <span class="fiche-legende-ref">Réf. ${ficheRef}</span>
                </div>

                <div class="fiche-footer">
                    <div class="fiche-signature">
                        <span>Nom et signature de l'enseignant</span>
                        <div class="fiche-signature-line"></div>
                    </div>
                    <div class="fiche-cachet">
                        <span>Cachet de l'établissement</span>
                        <div class="fiche-cachet-box"></div>
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

    // ===================== RENDU (combiné, matière unique, ou une fiche par matière) =====================
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

        if (data.matieres.length === 1) {
            // Une seule matière à afficher : soit un filtre explicite a été choisi,
            // soit la classe (collège/lycée) n'a qu'une matière -> une seule fiche, dans tous les cas
            html += buildFicheTable(data, data.matieres, data.matieres[0].nom);
        } else if (data.combiner) {
            // Maternelle / Primaire, aucune matière filtrée : une seule fiche avec toutes les matières
            html += buildFicheTable(data, data.matieres, null);
        } else {
            // Collège / Lycée, aucune matière filtrée : une fiche distincte par matière (saut de page entre chaque)
            html += data.matieres.map((m, idx) => `
                <div class="${idx > 0 ? 'fiche-page-break' : ''}">
                    ${buildFicheTable(data, [m], m.nom)}
                </div>
            `).join('');
        }

        document.getElementById('fichesContainer').innerHTML = html;

        // Génération des QR codes (un par fiche, référence courte pour rester sous la capacité du QR)
        if (typeof QRCode === 'undefined') {
            showToast('La librairie QR Code n\'a pas pu se charger (CDN bloqué ou hors-ligne). Les fiches restent utilisables sans QR.', 'warning');
        } else {
            document.querySelectorAll('.fiche-qr[data-qrtext]').forEach(el => {
                try {
                    new QRCode(el, {
                        text: el.dataset.qrtext,
                        width: 60,
                        height: 60,
                        colorDark: '#1f2d50',
                        colorLight: '#ffffff',
                        typeNumber: 0,
                        correctLevel: QRCode.CorrectLevel.L
                    });
                } catch (qrErr) {
                    console.error('QR non généré pour', el.id, qrErr);
                }
            });
        }

        document.getElementById('btnPrintFiches').addEventListener('click', () => window.print());
    }
</script>

<style>
    /* ===== PALETTE & TOKENS (Identité "Registre Scolaire Classique") ===== */
    .fiche-sheet, .fiche-toolbar {
        --navy: #1f2d50;
        --navy-deep: #16213a;
        --gold: #a9812f;
        --gold-soft: #f5efdd;
        --ink: #1c2230;
        --muted: #5b6472;
        --line: #232323;
        --line-soft: #c9cdd4;
        --paper: #fffefb;
    }

    /* ===== BARRE D'OUTILS & BOUTONS (ÉCRAN UNIQUEMENT) ===== */
    .fiche-toolbar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 14px;
    }

    .btn-tool {
        border: 1px solid var(--navy);
        background: #fff;
        color: var(--navy);
        font-size: 13px;
        font-weight: 600;
        padding: 7px 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background .15s, color .15s;
    }

    .btn-tool:hover {
        background: var(--navy);
        color: #fff;
    }

    /* ===== RESTRUCTURATION DE LA FEUILLE (ÉCRAN & FONDATIONS) ===== */
    .fiche-sheet {
        background: var(--paper);
        width: 100%;
        margin: 0 auto 40px;
        padding: 10px 30px 26px;
        border: 3px double var(--navy);
        box-shadow: 0 1px 6px rgba(16, 24, 40, 0.08);
        font-family: Georgia, 'Times New Roman', serif;
        color: var(--ink);
        box-sizing: border-box;
    }

    /* En-tête professionnel en 3 colonnes strictes (Grille) */
    .fiche-header {
        display: grid;
        grid-template-columns: 28% 44% 28%;
        align-items: center;
        border-bottom: 3px double var(--navy);
        padding-bottom: 12px;
        margin-bottom: 14px;
        gap: 10px;
    }

    /* Bloc Établissement (Gauche) */
    .fiche-etab {
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-start;
    }

    .fiche-etab-text {
        min-width: 0;
    }

    .fiche-etab-nom {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: .02em;
        color: var(--navy-deep);
        line-height: 1.2;
        word-break: break-word;
    }

    .fiche-etab-sub {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }

    .fiche-logo {
        max-height: 45px;
        width: auto;
        max-width: 60px;
        object-fit: contain;
    }

    /* Bloc Titre Principal (Centre) */
    .fiche-titre {
        text-align: center;
    }

    .fiche-titre h3 {
        margin: 0 0 4px 0;
        font-size: 16px;
        letter-spacing: .03em;
        font-weight: 700;
        color: var(--navy-deep);
        text-transform: uppercase;
    }

    .fiche-titre span {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: var(--muted);
        display: block;
        line-height: 1.3;
    }

    /* Bloc QR Code (Droite) */
    .fiche-qr-block {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        gap: 2px;
    }

    .fiche-qr {
        width: 55px;
        height: 55px;
        border: 1px solid var(--line-soft);
        padding: 2px;
        background: #fff;
    }

    .fiche-qr-label {
        font-family: Arial, sans-serif;
        font-size: 8.5px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--muted);
    }

    /* ===== BLOC D'INFORMATIONS OFFICIELLES ===== */
    .fiche-infos {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
        table-layout: fixed;
    }

    .fiche-infos td {
        border: 1px solid var(--line-soft);
        padding: 6px 10px;
        font-family: Arial, sans-serif;
    }

    .fiche-infos span {
        display: block;
        font-size: 9.5px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--muted);
        margin-bottom: 2px;
    }

    .fiche-infos strong {
        font-size: 13px;
        color: var(--ink);
        font-weight: 700;
    }

    /* ===== TABLEAU DE SAISIE DE NOTES ===== */
    .fiche-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .fiche-table {
        width: 100%;
        min-width: max-content;
        border-collapse: collapse;
        font-size: 12.5px;
        border: 1.5px solid var(--line);
    }

    .fiche-table thead {
        display: table-header-group;
    }

    .fiche-table thead th {
        background: var(--navy);
        color: #fff;
        border: 1px solid var(--navy-deep);
        padding: 8px 6px;
        text-align: center;
        font-size: 10.5px;
        font-family: Arial, sans-serif;
        text-transform: uppercase;
        letter-spacing: .02em;
        font-weight: 700;
    }

    .fiche-col-eleve {
        text-align: left !important;
        min-width: 180px;
        font-family: Arial, sans-serif;
    }

    .fiche-table td {
        border: 1px solid var(--line-soft);
        padding: 0;
        height: 27px;
    }

    .fiche-table tbody td.fiche-col-eleve {
        padding: 0 8px;
        font-size: 12px;
    }

    .fiche-cell { min-width: 40px; }
    .fiche-col-mois { min-width: 40px; }
    .fiche-col-compo { min-width: 46px; background: var(--gold); }
    .fiche-cell-compo { background: var(--gold-soft); }

    /* ===== LÉGENDE DE BAS DE PAGE ===== */
    .fiche-legende {
        display: flex;
        align-items: center;
        gap: 22px;
        flex-wrap: wrap;
        margin-top: 10px;
        font-family: Arial, sans-serif;
        font-size: 10.5px;
        color: var(--muted);
    }

    .fiche-legende-swatch {
        display: inline-block;
        width: 10px;
        height: 10px;
        border: 1px solid var(--line-soft);
        margin-right: 4px;
        vertical-align: middle;
    }

    .fiche-legende-mois { background: #fff; }
    .fiche-legende-compo { background: var(--gold-soft); }

    .fiche-legende-ref {
        margin-left: auto;
        font-size: 9.5px;
        color: #98a2b3;
        letter-spacing: .02em;
    }

    /* ===== ZONE DE SIGNATURE ET VALIDATION ===== */
    .fiche-footer {
        display: flex;
        justify-content: space-between;
        gap: 30px;
        margin-top: 34px;
        font-family: Arial, sans-serif;
    }

    .fiche-signature, .fiche-date, .fiche-cachet {
        flex: 1;
        text-align: center;
    }

    .fiche-signature span, .fiche-date span, .fiche-cachet span {
        font-size: 11px;
        color: var(--muted);
    }

    .fiche-signature-line {
        margin-top: 28px;
        border-top: 1px solid var(--ink);
    }

    .fiche-cachet-box {
        margin: 10px auto 0;
        width: 84px;
        height: 60px;
        border: 1px dashed var(--line-soft);
        border-radius: 50%;
    }

    .fiche-page-break {
        page-break-before: always;
    }

    /* ===== CONFIGURATION IMPRESSION PORTRAIT CIBLÉE (A4) ===== */
    @page {
        size: A4 portrait;
        margin: 3mm;
    }

    @media print {
        html, body {
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        /* Remise à zéro forcée de la structure globale du template */
        .dashboard-main-body,
        .dashboard-main-body * {
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        body * { visibility: hidden; }
        #fichesContainer, #fichesContainer * { visibility: visible; }

        #fichesContainer {
            position: static !important;
            width: 100% !important;
            overflow: visible !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .no-print, .breadcrumb { display: none !important; }

        /* Ajustement drastique des marges de la feuille */
        .fiche-sheet {
            box-shadow: none !important;
            border: none !important;
            padding: 0mm 2mm 3mm !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* En-tête optimisé pour le papier */
        .fiche-header {
            display: grid !important;
            grid-template-columns: 30% 45% 25% !important;
            align-items: flex-start !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            margin-bottom: 8px !important;
        }

        .fiche-etab, .fiche-qr-block {
            margin-top: 0 !important;
        }

        .fiche-etab-nom {
            font-size: 11px !important;
        }

        .fiche-titre {
            margin-top: -5px !important;
        }

        .fiche-titre h3 {
            font-size: 14px !important;
            margin: 0 0 1px 0 !important;
        }

        .fiche-titre span {
            font-size: 10px !important;
        }

        .fiche-qr {
            width: 45px !important;
            height: 45px !important;
        }

        .fiche-qr canvas, .fiche-qr img {
            width: 100% !important;
            height: 100% !important;
        }

        /* ===== RESSERREMENT ET DIMINUTION DE L'ÉCRITURE DU TABLEAU ===== */
        .fiche-table-wrapper {
            overflow: visible !important;
            width: 100% !important;
        }

        .fiche-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed !important; /* Force le strict respect des dimensions en pixels */
            font-size: 8.5px !important; /* Écriture diminuée pour optimiser l'affichage global */
            border-collapse: collapse !important;
        }

        /* 1. COMPRESSION MAXIMALE DES CASES DE NOTES MENSUELES */
        .fiche-table th.fiche-col-mois,
        .fiche-table td.fiche-cell {
            width: 20px !important; /* Largeur ultra-réduite au strict minimum */
            min-width: 20px !important;
            max-width: 20px !important;
            padding: 2px 0px !important; /* Padding interne mis à zéro pour éviter tout saut de ligne */
            text-align: center !important;
        }

        /* COMPRESSION MAXIMUM DE LA COLONNE COMPOSITION */
        .fiche-table th.fiche-col-compo,
        .fiche-table td.fiche-cell-compo {
            width: 28px !important; /* Réduite également au minimum pour la compo */
            min-width: 28px !important;
            max-width: 28px !important;
            padding: 2px 0px !important;
            text-align: center !important;
        }

        /* 2. ALLOCATION MAXIMALE DU RESTE DE L'ESPACE À L'ÉLÈVE */
        .fiche-table th.fiche-col-eleve,
        .fiche-table td.fiche-col-eleve {
            width: auto !important; /* S'approprie la totalité de la largeur restante de la page A4 */
            font-size: 9px !important; /* Un poil plus lisible pour les noms */
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            padding-left: 5px !important;
            text-align: left !important;
        }

        .fiche-table th,
        .fiche-table td {
            vertical-align: middle;
            border: 1px solid var(--line) !important;
            height: 22px !important; /* Hauteur des lignes légèrement réduite pour faire tenir plus d'élèves */
        }

        .fiche-table thead th {
            background: none !important;
            color: var(--navy-deep) !important;
            font-size: 8px !important;
        }

        /* Maintien de la couleur de fond de la compo */
        .fiche-cell-compo,
        .fiche-col-compo {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .fiche-legende-ref,
        .fiche-cachet-box {
            display: none;
        }

        .fiche-page-break {
            page-break-before: always;
        }

        .fiche-table,
        .fiche-table tr {
            page-break-inside: avoid;
        }
    }



</style>

@endsection
