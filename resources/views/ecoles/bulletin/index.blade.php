@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Bulletin de notes</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Evaluation / Bulletin</span>
            </div>
        </div>
    </div>

    {{-- FILTRE --}}
    <form id="bulletinForm" class="mt-24 no-print">
        <div class="row gy-3">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0">Générer un bulletin</h6>
                    </div>
                    <div class="card-body p-20">
                        <div class="row gy-3 align-items-end">
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
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
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau <span class="text-danger-600">*</span></label>
                                <select required id="niveauSelect" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                    @foreach ($niveaux as $niveau)
                                        <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                                <select required id="classSelection" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Élève <span class="text-danger-600">*</span></label>
                                <select required id="eleveSelect" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <label id="labelPeriode" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                    Période <span class="text-danger-600">*</span>
                                </label>
                                <select required id="periodeSelect" class="form-control form-select">
                                    <option value="">Séléctionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">
                                    Générer le bulletin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- BULLETIN GÉNÉRÉ --}}
    <div id="bulletinContainer" class="mt-24"></div>

</div>

<div id="toastContainer" class="position-fixed top-0 end-0 p-16 no-print" style="z-index: 1080;"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
        let eleveSelect = document.getElementById('eleveSelect');
        let periodeSelect = document.getElementById('periodeSelect');
        let labelPeriode = document.getElementById('labelPeriode');

        classeSelect.innerHTML = '<option value="">Séléctionner</option>';
        eleveSelect.innerHTML = '<option value="">Séléctionner</option>';
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
        if (niveauNom.includes('primaire') || niveauNom.includes('maternelle')) { type = 'trimestre'; nombre = 3; }
        else { type = 'semestre'; nombre = 2; }

        let libelle = type.charAt(0).toUpperCase() + type.slice(1);
        labelPeriode.innerHTML = 'Période <span class="text-danger-600">*</span>';

        for (let i = 1; i <= nombre; i++) {
            let option = document.createElement('option');
            option.value = i;
            option.dataset.type = type;
            option.textContent = libelle + ' ' + i;
            periodeSelect.appendChild(option);
        }

        // Option Annuelle
        let optionAnnuelle = document.createElement('option');
        optionAnnuelle.value = 'annuelle';
        optionAnnuelle.dataset.type = 'annuelle';
        optionAnnuelle.textContent = "Annuelle (Moyenne de l'année)";
        periodeSelect.appendChild(optionAnnuelle);
    });

    // ===================== CLASSE -> ÉLÈVES =====================
    document.getElementById('classSelection').addEventListener('change', function () {
        let classeId = this.value;
        let eleveSelect = document.getElementById('eleveSelect');
        let anneeScolaire = document.getElementById('anneescolaireSelect').value;

        eleveSelect.innerHTML = '<option value="">Séléctionner</option>';
        if (!classeId) return;

        eleveSelect.innerHTML = '<option>Chargement...</option>';
        fetchJson(`/${SLUG}/notes/eleves/${classeId}?` + new URLSearchParams({ annee_scolaire: anneeScolaire }))
            .then(data => {
                eleveSelect.innerHTML = '<option value="">Séléctionner</option>';
                data.forEach(el => { eleveSelect.innerHTML += `<option value="${el.id}">${el.nom} ${el.prenom}</option>`; });
            })
            .catch(() => { eleveSelect.innerHTML = '<option value="">Erreur de chargement</option>'; });
    });

    // ===================== GÉNÉRATION DU BULLETIN =====================
   document.getElementById('bulletinForm').addEventListener('submit', function (e) {
    e.preventDefault();

    let anneeScolaire = document.getElementById('anneescolaireSelect').value;
    let niveauId = document.getElementById('niveauSelect').value;
    let classeId = document.getElementById('classSelection').value;
    let eleveId = document.getElementById('eleveSelect').value;
    let periodeSelect = document.getElementById('periodeSelect');
    let periodeOption = periodeSelect.options[periodeSelect.selectedIndex];
    let periodeType = periodeOption?.dataset.type;

    if (!niveauId || !classeId || !eleveId || !periodeType) {
        showToast('Merci de sélectionner tous les champs.', 'warning');
        return;
    }

    document.getElementById('bulletinContainer').innerHTML = '<p class="text-center py-24">Génération du bulletin...</p>';

    let params = {
        annee_scolaire: anneeScolaire,
        niveau_id: niveauId,
        classe_id: classeId,
        eleve_id: eleveId,
        periode_type: periodeType
    };

    if (periodeType !== 'annuelle') {
        params.periode_numero = periodeSelect.value;
    }

    fetchJson(`/${SLUG}/bulletin/data?` + new URLSearchParams(params))
    .then(data => {
        try {
            renderBulletin(data);
        } catch (renderErr) {
            console.error(renderErr);
            showToast('Erreur lors de l\'affichage du bulletin : ' + renderErr.message);
            document.getElementById('bulletinContainer').innerHTML =
                '<p class="text-center text-danger-600 py-24">Erreur d\'affichage. Voir le message ci-dessus.</p>';
        }
    })
    .catch(() => {
        // Le message d'erreur détaillé est déjà affiché en toast par fetchJson()
        document.getElementById('bulletinContainer').innerHTML =
            '<p class="text-center text-danger-600 py-24">Impossible de générer le bulletin.</p>';
    });
});

    // ===================== GRAPHIQUE STATISTIQUES (barres) =====================
    function buildStatsChart(stats, maxNote) {
        const width = 520, height = 150, barWidth = 90, gap = 60, baseY = 115, topPad = 15;
        const scale = v => (v / maxNote) * (baseY - topPad);

        const bars = [
            { label: 'Plus forte',  value: stats.moyenne_max,    color: '#12b76a' },
            { label: 'Classe',      value: stats.moyenne_classe, color: '#1849a9' },
            { label: 'Plus faible', value: stats.moyenne_min,    color: '#f04438' }
        ];

        const startX = (width - (bars.length * barWidth + (bars.length - 1) * gap)) / 2;

        let barsHtml = bars.map((b, i) => {
            let h = Math.max(scale(b.value), 2);
            let x = startX + i * (barWidth + gap);
            let y = baseY - h;
            return `
                <text x="${x + barWidth / 2}" y="${y - 8}" text-anchor="middle" font-size="13" font-weight="700" fill="${b.color}">${b.value.toFixed(2)}</text>
                <rect x="${x}" y="${y}" width="${barWidth}" height="${h}" rx="4" fill="${b.color}" opacity="0.85"></rect>
                <text x="${x + barWidth / 2}" y="${baseY + 18}" text-anchor="middle" font-size="11.5" fill="#475467">${b.label}</text>
            `;
        }).join('');

        return `
            <svg viewBox="0 0 ${width} ${height}" width="100%" height="150" style="margin-top:10px;">
                <line x1="20" y1="${baseY}" x2="${width - 20}" y2="${baseY}" stroke="#d0d5dd" stroke-width="1"></line>
                ${barsHtml}
            </svg>
        `;
    }

    function renderBulletin(data) {
        let libellePeriode = data.periode_type === 'annuelle'
            ? 'Bulletin Annuel'
            : data.periode_type.charAt(0).toUpperCase() + data.periode_type.slice(1) + ' ' + data.periode_numero;

        let lignesHtml = data.lignes.map(l => `
            <tr>
                <td class="bt-matiere">${l.matiere}</td>
                <td class="bt-num">${l.coefficient}</td>
                <td class="bt-num">${l.moyenne_cours.toFixed(2)}</td>
                <td class="bt-num">${l.note_compo.toFixed(2)}</td>
                <td class="bt-num bt-moyenne">${l.moyenne_matiere.toFixed(2)}</td>
                <td class="bt-appreciation">${l.appreciation}</td>
            </tr>
        `).join('');

        let html = `
            <div class="bulletin-toolbar no-print">
                <button type="button" id="btnPrintBulletin" class="btn-tool">🖨️ Imprimer / PDF</button>
                <button type="button" id="btnExportBulletin" class="btn-tool">📊 Exporter Excel</button>
            </div>

            <div class="bulletin-sheet">
                ${data.ecole.logo ? `<div class="bulletin-watermark" style="background-image:url('${data.ecole.logo}')"></div>` : ''}

                <div class="bulletin-header">
                    <div class="bulletin-etablissement">
                        <div class="bulletin-etab-nom">${data.ecole.nom}</div>
                        <div class="bulletin-etab-sub">Année scolaire ${data.annee_scolaire}</div>
                        ${data.ecole.logo ? `<img src="${data.ecole.logo}" class="bulletin-logo" alt="Logo établissement">` : ''}
                    </div>
                    <div class="bulletin-titre">
                        <h2>BULLETIN DE NOTES</h2>
                        <span>${libellePeriode}</span>
                    </div>
                    <div id="bulletinQr" class="bulletin-qr"></div>
                </div>

                <div class="bulletin-infos">
                    <div><span>Élève</span><strong>${data.eleve.nom} ${data.eleve.prenom}</strong></div>
                    <div><span>Matricule</span><strong>${data.eleve.matricule || '—'}</strong></div>
                    <div><span>Classe</span><strong>${data.classe}</strong></div>
                    <div><span>Niveau</span><strong>${data.niveau}</strong></div>
                    <div><span>Barème</span><strong>/ ${data.max_note}</strong></div>
                </div>

                <table class="bulletin-table">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Coef.</th>
                            <th>Moy. cours</th>
                            <th>Compo.</th>
                            <th>Moyenne</th>
                            <th>Appréciation</th>
                        </tr>
                    </thead>
                    <tbody>${lignesHtml}</tbody>
                    <tfoot>
                        <tr>
                            <td class="bt-total-label">Total</td>
                            <td class="bt-num">${data.totaux.coefficient}</td>
                            <td class="bt-num">${data.totaux.moyenne_cours.toFixed(2)}</td>
                            <td class="bt-num">${data.totaux.note_compo.toFixed(2)}</td>
                            <td class="bt-num bt-moyenne">${data.totaux.moyenne_matiere.toFixed(2)}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="bulletin-resume">
                    <div class="bulletin-moyenne">
                        <span>Moyenne générale</span>
                        <strong>${data.moyenne_generale.toFixed(2)} / ${data.max_note}</strong>
                    </div>
                    <div class="bulletin-rang">
                        <span>Rang</span>
                        <strong>${data.rang_label} / ${data.effectif}</strong>
                    </div>
                    <div class="bulletin-appreciation">
                        <span>Appréciation</span>
                        <strong>${data.appreciation}</strong>
                    </div>
                </div>

                <div class="bulletin-stats">
                    <div class="bulletin-stat-title">Statistiques de la classe (${data.classe} — ${libellePeriode})</div>
                    <div class="bulletin-stats-grid">
                        <div class="bulletin-stat-item">
                            <span>Moyenne la plus forte</span>
                            <strong class="stat-forte">${data.stats.moyenne_max.toFixed(2)}</strong>
                        </div>
                        <div class="bulletin-stat-item">
                            <span>Moyenne de la classe</span>
                            <strong>${data.stats.moyenne_classe.toFixed(2)}</strong>
                        </div>
                        <div class="bulletin-stat-item">
                            <span>Moyenne la plus faible</span>
                            <strong class="stat-faible">${data.stats.moyenne_min.toFixed(2)}</strong>
                        </div>
                    </div>
                    ${buildStatsChart(data.stats, data.max_note)}
                </div>

                <div class="bulletin-signatures">
                    <div class="bulletin-signature">
                        <span>Le Titulaire</span>
                        <div class="bulletin-signature-line"></div>
                    </div>
                    <div class="bulletin-signature">
                        <span>Le Directeur</span>
                        <div class="bulletin-signature-line"></div>
                    </div>
                    <div class="bulletin-signature">
                        <span>Les Parents</span>
                        <div class="bulletin-signature-line"></div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('bulletinContainer').innerHTML = html;

        // ===== Génération du QR code (isolée : un échec ici ne doit jamais effacer le bulletin déjà affiché) =====
        if (typeof QRCode === 'undefined') {
            showToast('La librairie QR Code n\'a pas pu se charger (CDN bloqué ou hors-ligne). Le reste du bulletin fonctionne normalement.', 'warning');
        } else {
            try {
                let qrData = [
                    `${data.eleve.nom} ${data.eleve.prenom}`,
                    data.classe,
                    `${libellePeriode} ${data.annee_scolaire}`,
                    `${data.moyenne_generale.toFixed(2)}/${data.max_note}`,
                    data.rang_label + '/' + data.effectif
                ].join(' - ')
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, ''); // retire les accents pour rester compact

                new QRCode(document.getElementById('bulletinQr'), {
                    text: qrData,
                    width: 72,
                    height: 72,
                    colorDark: '#1d2939',
                    colorLight: '#ffffff',
                    typeNumber: 0,
                    correctLevel: QRCode.CorrectLevel.L
                });
            } catch (qrErr) {
                console.warn('QR code non généré (contenu trop long ou caractères non supportés) :', qrErr.message);
                let qrBlock = document.getElementById('bulletinQr');
                if (qrBlock) qrBlock.style.display = 'none';
            }
        }

        document.getElementById('btnPrintBulletin').addEventListener('click', () => window.print());

        document.getElementById('btnExportBulletin').addEventListener('click', function () {
            let rows = [['Matière', 'Coefficient', 'Moyenne cours', 'Compo', 'Moyenne', 'Appréciation']];
            data.lignes.forEach(l => rows.push([l.matiere, l.coefficient, l.moyenne_cours, l.note_compo, l.moyenne_matiere, l.appreciation]));
            rows.push(['Total', data.totaux.coefficient, data.totaux.moyenne_cours, data.totaux.note_compo, data.totaux.moyenne_matiere, '']);
            rows.push([]);
            rows.push(['Moyenne générale', '', '', '', '', data.moyenne_generale]);
            rows.push(['Rang', `${data.rang_label} / ${data.effectif}`]);
            rows.push(['Appréciation générale', data.appreciation]);

            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.aoa_to_sheet(rows);
            XLSX.utils.book_append_sheet(wb, ws, 'Bulletin');
            XLSX.writeFile(wb, `bulletin_${data.eleve.nom}_${data.eleve.prenom}_${libellePeriode}.xlsx`.replace(/\s+/g, '_'));
        });


    }
</script>

<style>
    /* ===== Toolbar ===== */
    .bulletin-toolbar {
        display: flex;
        gap: 8px;
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

    /* ===== Feuille du bulletin (style classique / officiel) ===== */
    .bulletin-sheet {
        background: #fff;
        max-width: 850px;
        margin: 0 auto;
        padding: 40px 48px;
        border: 1px solid #dfe3e8;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(16, 24, 40, 0.06);
        font-family: Georgia, 'Times New Roman', serif;
        color: #1d2939;
        position: relative;
        overflow: hidden;
    }

    /* ===== Filigrane (logo en fond) ===== */
    .bulletin-watermark {
        position: absolute;
        inset: 0;
        background-repeat: no-repeat;
        background-position: center;
        background-size: 55%;
        opacity: 0.06;
        pointer-events: none;
        z-index: 0;
    }

    .bulletin-header, .bulletin-infos, .bulletin-table, .bulletin-resume,
    .bulletin-stats, .bulletin-signatures {
        position: relative;
        z-index: 1;
    }

    .bulletin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 3px double #344054;
        padding-bottom: 16px;
        margin-bottom: 24px;
        gap: 16px;
    }

    .bulletin-etablissement {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .bulletin-logo {
        max-height: 54px;
        max-width: 140px;
        object-fit: contain;
    }

    .bulletin-qr {
        flex-shrink: 0;
    }

    .bulletin-etab-nom {
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .bulletin-etab-sub {
        font-size: 12.5px;
        color: #667085;
        margin-top: 2px;
        font-family: Arial, sans-serif;
    }

    .bulletin-titre {
        text-align: right;
    }

    .bulletin-titre h2 {
        margin: 0;
        font-size: 20px;
        letter-spacing: .05em;
        color: #1d2939;
    }

    .bulletin-titre span {
        font-size: 13px;
        color: #475467;
        font-family: Arial, sans-serif;
    }

    .bulletin-infos {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px 24px;
        margin-bottom: 24px;
        font-family: Arial, sans-serif;
    }

    .bulletin-infos > div {
        display: flex;
        flex-direction: column;
        border-bottom: 1px solid #eaecf0;
        padding-bottom: 6px;
    }

    .bulletin-infos span {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #98a2b3;
        margin-bottom: 2px;
    }

    .bulletin-infos strong {
        font-size: 14px;
        color: #1d2939;
    }

    .bulletin-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 26px;
        font-family: Arial, sans-serif;
        font-size: 13.5px;
    }

    .bulletin-table thead th {
        background: #f4f6f8;
        border: 1px solid #d0d5dd;
        padding: 9px 10px;
        text-align: center;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .02em;
        color: #344054;
    }

    .bulletin-table thead th:first-child { text-align: left; }

    .bulletin-table td {
        border: 1px solid #eaecf0;
        padding: 8px 10px;
    }

    .bt-matiere { font-weight: 600; }
    .bt-num { text-align: center; }
    .bt-moyenne { font-weight: 700; background: #f9fafb; }

    .bulletin-table tbody tr:nth-child(even) { background: #fcfcfd; }

    .bt-total-label {
        text-align: right;
        font-weight: 600;
        font-family: Arial, sans-serif;
        border: none !important;
        padding-right: 12px !important;
    }

    .bulletin-resume {
        display: flex;
        gap: 24px;
        margin-bottom: 40px;
        font-family: Arial, sans-serif;
    }

    .bulletin-moyenne, .bulletin-rang, .bulletin-appreciation {
        flex: 1;
        border: 1px solid #d0d5dd;
        border-radius: 8px;
        padding: 14px 18px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .bulletin-moyenne {
        background: #eef4ff;
        border-color: #b8cbf5;
    }

    .bulletin-rang {
        background: #fff7ed;
        border-color: #fbd9a8;
    }

    .bulletin-moyenne span, .bulletin-rang span, .bulletin-appreciation span {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #667085;
    }

    .bulletin-moyenne strong {
        font-size: 22px;
        color: #1849a9;
    }

    .bulletin-rang strong {
        font-size: 22px;
        color: #b54708;
    }

    .bulletin-appreciation strong {
        font-size: 18px;
        color: #1d2939;
    }

    /* ===== Statistiques de la classe ===== */
    .bulletin-stats {
        border: 1px solid #eaecf0;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 30px;
        font-family: Arial, sans-serif;
        background: #fbfbfc;
    }

    .bulletin-stat-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #667085;
        margin-bottom: 10px;
    }

    .bulletin-stats-grid {
        display: flex;
        gap: 20px;
    }

    .bulletin-stat-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
        text-align: center;
    }

    .bulletin-stat-item span {
        font-size: 11.5px;
        color: #667085;
    }

    .bulletin-stat-item strong {
        font-size: 18px;
        color: #1d2939;
    }

    .stat-forte { color: #12b76a !important; }
    .stat-faible { color: #f04438 !important; }

    .bulletin-signatures {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-top: 50px;
        font-family: Arial, sans-serif;
    }

    .bulletin-signature {
        flex: 1;
        text-align: center;
    }

    .bulletin-signature span {
        font-size: 12px;
        color: #475467;
    }

    .bulletin-signature-line {
        margin-top: 40px;
        border-top: 1px solid #98a2b3;
    }

   /* ===== Impression : n'imprimer QUE le bulletin, tout sur 1 page A4 ===== */
    @page {
        size: A4;
        margin: 10mm;
    }

    @media print {
        html, body {
            height: auto !important;
            overflow: visible !important;
        }

        body * {
            visibility: hidden;
        }

        .bulletin-sheet, .bulletin-sheet * {
            visibility: visible;
        }

        .bulletin-sheet {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            max-width: 100%;
            box-shadow: none !important;
            border: none !important;
            margin: 0;
            padding: 8px 14px;
            zoom: 0.82;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .no-print, .breadcrumb {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .bulletin-watermark {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Réduction des marges/tailles pour que tout tienne sur une page A4 */
        .bulletin-header {
            margin-bottom: 12px;
            padding-bottom: 10px;
        }

        .bulletin-infos {
            margin-bottom: 12px;
            gap: 8px 20px;
        }

        .bulletin-table {
            margin-bottom: 14px;
        }

        .bulletin-table th,
        .bulletin-table td {
            padding: 5px 8px;
        }

        .bulletin-resume {
            margin-bottom: 16px;
        }

        .bulletin-moyenne, .bulletin-rang, .bulletin-appreciation {
            padding: 8px 12px;
        }

        .bulletin-stats {
            margin-bottom: 14px;
            padding: 10px 14px;
        }

        .bulletin-signatures {
            margin-top: 20px;
        }

        .bulletin-signature-line {
            margin-top: 24px;
        }

        /* Empêche la coupure au milieu du tableau ou des blocs clés */
        .bulletin-table, .bulletin-resume, .bulletin-stats, .bulletin-signatures {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
    .bulletin-stats svg {
            height: 90px !important;
            margin-top: 4px !important;
        }
</style>

@endsection
