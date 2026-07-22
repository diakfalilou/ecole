@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Tableau des Résultats Scolaires</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Evaluation / Résultats</span>
            </div>
        </div>
    </div>

    {{-- FILTRE --}}
    <form id="resultatForm" class="mt-24 no-print">
        <div class="row gy-3">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0">Générer les Résultats</h6>
                    </div>
                    <div class="card-body p-20">
                        <div class="row gy-3 align-items-end">
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année scolaire *</label>
                                <select id="anneescolaireSelect" class="form-control form-select">
                                    @foreach ($data_anneescolaire as $annee)
                                        <option value="{{ $annee->v_annesclaire }}" {{ $annee->v_annesclaire == $annee_courante ? 'selected' : '' }}>
                                            {{ $annee->v_annesclaire }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau *</label>
                                <select required id="niveauSelect" class="form-control form-select">
                                    <option value="">Sélectionner</option>
                                    @foreach ($niveaux as $niveau)
                                        <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
                                <select required id="classSelection" class="form-control form-select">
                                    <option value="">Sélectionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-3 col-sm-6">
                                <label id="labelPeriode" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Période *</label>
                                <select required id="periodeSelect" class="form-control form-select">
                                    <option value="">Sélectionner</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-xl-12 col-sm-6">
                                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 w-100">Générer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- FEUILLES DE RÉSULTATS GÉNÉRÉES --}}
    <div id="resultatsContainer" class="mt-24"></div>

</div>

<div id="toastContainer" class="position-fixed top-0 end-0 p-16 no-print" style="z-index: 1080;"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    const SLUG = "{{ $slug }}";

    // Toast
    function showToast(message, type = 'error') {
        const colors = {
            error:   { bg: '#f8d7da', border: '#f5c2c7', text: '#842029', icon: '✕' },
            success: { bg: '#d1e7dd', border: '#badbcc', text: '#0f5132', icon: '✓' }
        };
        const c = colors[type] || colors.error;
        const toast = document.createElement('div');
        toast.style.cssText = `background:${c.bg}; border:1px solid ${c.border}; color:${c.text}; border-radius:8px; padding:12px 16px; margin-bottom:10px; min-width:280px; position:relative; box-shadow:0 4px 12px rgba(0,0,0,0.15); display:flex; gap:10px; font-size:13px;`;
        toast.innerHTML = `<strong>${c.icon}</strong><span style="flex:1;">${message}</span><span style="cursor:pointer;" onclick="this.parentElement.remove()">×</span>`;
        document.getElementById('toastContainer').appendChild(toast);
        if (type !== 'error') setTimeout(() => toast.remove(), 5000);
    }

    // fetchJson avec propagation du message d'erreur réel
    async function fetchJson(url, options = {}) {
        options.headers = { 'Accept': 'application/json', ...(options.headers || {}) };
        let res = await fetch(url, options);
        if (!res.ok) {
            let errorDetail = '';
            try {
                const errJson = await res.json();
                errorDetail = errJson.message || JSON.stringify(errJson);
            } catch (e) {
                errorDetail = await res.text();
            }
            console.error('Erreur API:', res.status, errorDetail);
            showToast(`Erreur ${res.status} : ${errorDetail.substring(0, 200)}`);
            throw new Error(errorDetail);
        }
        return await res.json();
    }

    // Gestion Dynamique Niveau -> Classes & Périodes
    document.getElementById('niveauSelect').addEventListener('change', function () {
        let niveauId = this.value;
        let niveauNom = this.options[this.selectedIndex].text.trim().toLowerCase();
        let classeSelect = document.getElementById('classSelection');
        let periodeSelect = document.getElementById('periodeSelect');
        let labelPeriode = document.getElementById('labelPeriode');

        classeSelect.innerHTML = '<option value="">Sélectionner</option>';
        periodeSelect.innerHTML = '<option value="">Sélectionner</option>';

        if (!niveauId) return;

        fetchJson(`/${SLUG}/notes/classes/${niveauId}`).then(data => {
            data.forEach(c => { classeSelect.innerHTML += `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`; });
        });

        let type = niveauNom.includes('primaire') || niveauNom.includes('maternelle') ? 'trimestre' : 'semestre';
        let nombre = type === 'trimestre' ? 3 : 2;
        let libelle = type.charAt(0).toUpperCase() + type.slice(1);
        labelPeriode.innerHTML = 'Période *';

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

    // Submit et Appel AJAX
    document.getElementById('resultatForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let periodeOption = document.getElementById('periodeSelect').options[document.getElementById('periodeSelect').selectedIndex];

        let params = {
            annee_scolaire: document.getElementById('anneescolaireSelect').value,
            niveau_id: document.getElementById('niveauSelect').value,
            classe_id: document.getElementById('classSelection').value,
            periode_type: periodeOption.dataset.type,
            periode_numero: periodeOption.dataset.type === 'annuelle' ? '' : periodeOption.value
        };

        document.getElementById('resultatsContainer').innerHTML = '<p class="text-center py-24">Calcul et génération du tableau des résultats...</p>';

        fetchJson(`/${SLUG}/resultats/data?` + new URLSearchParams(params))
            .then(data => { renderResultats(data); })
            .catch((err) => {
                document.getElementById('resultatsContainer').innerHTML =
                    `<p class="text-center text-danger-600 py-24">Erreur lors de la génération.<br><small>${err.message}</small></p>`;
            });
    });

    // Construction de la vue Résultat
    function renderResultats(data) {
        if (!data.eleves.length) {
            document.getElementById('resultatsContainer').innerHTML = '<p class="text-center py-24">Aucun élève trouvé.</p>';
            return;
        }

        let ficheId = `resultat-qr`;
        let libellePeriode = data.periode_type === 'annuelle'
            ? "Résultats Annuels"
            : `${data.periode_type.charAt(0).toUpperCase() + data.periode_type.slice(1)} ${data.periode_numero}`;
        let docRef = `RES / ${data.classe} / ${libellePeriode} / ${data.annee_scolaire}`.toUpperCase();

        let html = `
            <div class="fiche-toolbar no-print">
                <button type="button" id="btnPrint" class="btn-tool">🖨️ Imprimer le tableau des résultats</button>
            </div>

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
                        <h3>Tableau Récapitulatif des Résultats</h3>
                        <span>Classe : ${data.classe} — ${libellePeriode}</span>
                    </div>
                    <div class="fiche-qr-block">
                        <div class="fiche-qr" id="${ficheId}" data-qrtext="${docRef}"></div>
                        <span class="fiche-qr-label">Réf. Officielle</span>
                    </div>
                </div>

                <table class="fiche-infos">
                    <tr>
                        <td><span>Niveau</span><strong>${data.niveau}</strong></td>
                        <td><span>Classe</span><strong>${data.classe}</strong></td>
                        <td><span>Période</span><strong>${libellePeriode}</strong></td>
                        <td><span>Généralités</span><strong>Moyennes sur ${data.max_note || 20}</strong></td>
                        <td><span>Effectif</span><strong>${data.eleves.length} Éléve(s)</strong></td>
                    </tr>
                </table>

                <div class="fiche-table-wrapper">
                    <table class="fiche-table">
                        <thead>
                            <tr>
                                <th class="fiche-col-eleve">Nom & Prénoms de l'Élève</th>
                                ${data.matieres.map(m => `<th class="fiche-col-note-data" title="${m.nom}">${m.code || m.nom.substring(0,4).toUpperCase()}</th>`).join('')}
                                <th class="fiche-col-total">Total</th>
                                <th class="fiche-col-moyenne">Moy.</th>
                                <th class="fiche-col-rang">Rang</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.eleves.map((el, idx) => `
                                <tr>
                                    <td class="fiche-col-eleve">${idx + 1}. ${el.nom} ${el.prenom}</td>
                                    ${data.matieres.map(m => {
                                        let note = el.notes[m.id] !== undefined ? el.notes[m.id] : '-';
                                        return `<td class="fiche-cell text-center">${note}</td>`;
                                    }).join('')}
                                    <td class="fiche-cell text-center fw-bold">${el.total_points}</td>
                                    <td class="fiche-cell text-center fiche-cell-compo fw-bold">${el.moyenne}</td>
                                    <td class="fiche-cell text-center fw-bold">${el.rang}<sup>${el.rang === 1 ? 'er' : 'e'}</sup></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <div class="fiche-legende">
                    <span><i class="fiche-legende-swatch fiche-legende-mois"></i> Notes / Moyennes par matière</span>
                    <span><i class="fiche-legende-swatch fiche-legende-compo"></i> Moyenne Périodique Générale</span>
                </div>

                <div class="fiche-footer">
                    <div class="fiche-signature">
                        <span>Le Titulaire / Conseil des Enseignants</span>
                        <div class="fiche-signature-line"></div>
                    </div>
                    <div class="fiche-date">
                        <span>Date de Délibération</span>
                        <div class="fiche-signature-line"></div>
                    </div>
                    <div class="fiche-cachet">
                        <span>Visa de la Direction</span>
                        <div class="fiche-signature-line"></div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('resultatsContainer').innerHTML = html;

        // Génération QR (isolée : un échec ici ne doit jamais effacer le tableau déjà affiché)
        try {
            if (typeof QRCode !== 'undefined') {
                // Texte QR simplifié : sans accents, plus court, pour éviter l'overflow de la librairie
                let qrTexteSafe = docRef
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // retire les accents
                    .replace(/[^A-Z0-9 /\-]/g, ''); // garde uniquement caractères sûrs

                new QRCode(document.getElementById(ficheId), {
                    text: qrTexteSafe,
                    width: 50,
                    height: 50,
                    colorDark: '#1f2d50',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.L
                });
            }
        } catch (qrErr) {
            console.warn('QR code non généré (texte trop long ou caractères non supportés) :', qrErr.message);
            // On masque juste le bloc QR sans bloquer l'affichage du tableau
            let qrBlock = document.getElementById(ficheId);
            if (qrBlock) qrBlock.style.display = 'none';
        }

        document.getElementById('btnPrint').addEventListener('click', () => window.print());
    }
</script>

<style>
    .fiche-sheet, .fiche-toolbar {
        --navy: #1f2d50; --navy-deep: #16213a; --gold: #a9812f; --gold-soft: #f5efdd;
        --ink: #1c2230; --muted: #5b6472; --line: #232323; --line-soft: #c9cdd4; --paper: #fffefb;
    }
    .fiche-toolbar { display: flex; justify-content: flex-end; margin-bottom: 14px; }
    .btn-tool { border: 1px solid var(--navy); background: #fff; color: var(--navy); font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 4px; cursor: pointer; }
    .btn-tool:hover { background: var(--navy); color: #fff; }

    .fiche-sheet {
        background: var(--paper); width: 100%; margin: 0 auto 40px; padding: 10px 30px 26px;
        border: 3px double var(--navy); box-shadow: 0 1px 6px rgba(16, 24, 40, 0.08); font-family: Georgia, serif; color: var(--ink); box-sizing: border-box;
    }
    .fiche-header { display: grid; grid-template-columns: 28% 44% 28%; align-items: center; border-bottom: 3px double var(--navy); padding-bottom: 12px; margin-bottom: 14px; gap: 10px; }
    .fiche-etab { display: flex; align-items: center; gap: 10px; }
    .fiche-etab-nom { font-weight: 700; text-transform: uppercase; font-size: 13px; color: var(--navy-deep); line-height: 1.2; word-break: break-word; }
    .fiche-etab-sub { font-family: Arial, sans-serif; font-size: 11px; color: var(--muted); }
    .fiche-logo { max-height: 45px; max-width: 60px; object-fit: contain; }
    .fiche-titre { text-align: center; }
    .fiche-titre h3 { margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: var(--navy-deep); text-transform: uppercase; }
    .fiche-titre span { font-family: Arial, sans-serif; font-size: 11px; color: var(--muted); }
    .fiche-qr-block { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; }
    .fiche-qr { width: 55px; height: 55px; border: 1px solid var(--line-soft); padding: 2px; background: #fff; }
    .fiche-qr-label { font-family: Arial, sans-serif; font-size: 8.5px; text-transform: uppercase; color: var(--muted); }

    .fiche-infos { width: 100%; border-collapse: collapse; margin-bottom: 16px; table-layout: fixed; }
    .fiche-infos td { border: 1px solid var(--line-soft); padding: 6px 10px; font-family: Arial, sans-serif; }
    .fiche-infos span { display: block; font-size: 9.5px; text-transform: uppercase; color: var(--muted); }
    .fiche-infos strong { font-size: 13px; color: var(--ink); font-weight: 700; }

    .fiche-table-wrapper { width: 100%; overflow-x: auto; }
    .fiche-table { width: 100%; border-collapse: collapse; font-size: 12px; border: 1.5px solid var(--line); }
    .fiche-table thead th { background: var(--navy); color: #fff; border: 1px solid var(--navy-deep); padding: 8px 4px; text-align: center; font-size: 11px; font-family: Arial, sans-serif; text-transform: uppercase; }

    .fiche-col-eleve { text-align: left !important; min-width: 180px; padding-left: 8px !important; }
    .fiche-table td { border: 1px solid var(--line-soft); padding: 5px 4px; height: 25px; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }
    .fiche-cell-compo { background: var(--gold-soft); }

    .fiche-legende { display: flex; gap: 22px; margin-top: 10px; font-family: Arial, sans-serif; font-size: 10.5px; color: var(--muted); }
    .fiche-legende-swatch { display: inline-block; width: 10px; height: 10px; border: 1px solid var(--line-soft); margin-right: 4px; }
    .fiche-legende-mois { background: #fff; }
    .fiche-legende-compo { background: var(--gold-soft); }

    .fiche-footer { display: flex; justify-content: space-between; gap: 30px; margin-top: 34px; font-family: Arial, sans-serif; }
    .fiche-signature, .fiche-date, .fiche-cachet { flex: 1; text-align: center; }
    .fiche-signature span, .fiche-date span, .fiche-cachet span { font-size: 11px; color: var(--muted); }
    .fiche-signature-line { margin-top: 28px; border-top: 1px solid var(--ink); }

    /* ===== IMPRESSION SÉCURISÉE ET AGRESSIVE ===== */
    @page { size: A4 portrait; margin: 3mm; }
    @media print {
        html, body { height: auto !important; max-height: none !important; overflow: visible !important; margin: 0 !important; padding: 0 !important; background: #fff !important; }
        .dashboard-main-body, .dashboard-main-body * { height: auto !important; max-height: none !important; overflow: visible !important; margin-top: 0 !important; padding-top: 0 !important; }
        body * { visibility: hidden; }
        #resultatsContainer, #resultatsContainer * { visibility: visible; }
        #resultatsContainer { position: static !important; width: 100% !important; overflow: visible !important; }
        .no-print, .breadcrumb { display: none !important; }

        .fiche-sheet { box-shadow: none !important; border: none !important; padding: 0mm 2mm 3mm !important; margin: 0 !important; width: 100% !important; }
        .fiche-header { display: grid !important; grid-template-columns: 30% 45% 25% !important; align-items: flex-start !important; margin-bottom: 8px !important; }
        .fiche-titre { margin-top: -5px !important; }
        .fiche-titre h3 { font-size: 14px !important; margin: 0 0 1px 0 !important; }
        .fiche-titre span { font-size: 10px !important; }
        .fiche-qr { width: 45px !important; height: 45px !important; }

        .fiche-table { width: 100% !important; table-layout: fixed !important; font-size: 8.5px !important; border-collapse: collapse !important; }

        /* Compression des données chiffrées */
        .fiche-col-note-data, .fiche-col-total, .fiche-col-moyenne, .fiche-col-rang, .fiche-table td.fiche-cell {
            width: 22px !important; min-width: 22px !important; max-width: 22px !important; padding: 2px 0px !important; text-align: center !important;
        }
        .fiche-table th.fiche-col-eleve, .fiche-table td.fiche-col-eleve {
            width: 110px !important; max-width: 110px !important; font-size: 8.5px !important;
            white-space: normal !important; word-break: break-word !important; line-height: 1.15 !important;
            overflow: hidden !important; padding-left: 4px !important; text-align: left !important;
        }
        .fiche-table th, .fiche-table td { vertical-align: middle; border: 1px solid var(--line) !important; height: 22px !important; }
        .fiche-table thead th { background: none !important; color: var(--navy-deep) !important; font-size: 8px !important; }
        .fiche-cell-compo { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .fiche-page-break { page-break-before: always; }
        .fiche-table, .fiche-table tr { page-break-inside: avoid; }
    }
</style>

@endsection
