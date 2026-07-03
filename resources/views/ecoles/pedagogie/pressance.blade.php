@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    .eleve-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; transition: all .15s ease; background: #fff; }
    .eleve-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,.06); }
    .eleve-photo { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; background: #f1f5f9; }
    .statut-btn { border: 1.5px solid #e5e7eb; background: #fff; border-radius: 8px; padding: 6px 12px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all .15s ease; color: #64748b; }
    .statut-btn iconify-icon { font-size: 16px; vertical-align: -3px; }
    .statut-btn.active[data-statut="present"] { background: #ecfdf5; border-color: #10b981; color: #059669; }
    .statut-btn.active[data-statut="absent"] { background: #fef2f2; border-color: #ef4444; color: #dc2626; }
    .statut-btn.active[data-statut="retard"] { background: #fffbeb; border-color: #f59e0b; color: #d97706; }
    .statut-btn.active[data-statut="permission"] { background: #eff6ff; border-color: #3b82f6; color: #2563eb; }
    .stat-pill { border-radius: 10px; padding: 10px 16px; text-align: center; flex: 1; }
    .motif-box { display: none; }
    .motif-box.show { display: block; }
</style>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion de l'appel</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Pédagogie / Appel en classe</span>
            </div>
        </div>
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabAppel" type="button">Faire l'appel</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabHistorique" type="button" id="btnTabHistorique">Historique & Stats</button>
            </li>
        </ul>
    </div>

    <div class="tab-content">

        <!-- ================= ONGLET APPEL ================= -->
        <div class="tab-pane fade show active" id="tabAppel">
            <div class="card mb-24">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1">Classe *</label>
                            <select class="form-control" id="selectClasse"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Année scolaire *</label>
                            <select class="form-control" id="selectAnnee"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Date de l'appel *</label>
                            <input type="date" class="form-control" id="inputDate">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary-600 w-100" id="btnChargerAppel">
                                <iconify-icon icon="mdi:clipboard-check-outline"></iconify-icon> Charger la liste
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="zoneAppel" class="d-none">
                <!-- Compteurs -->
                <div class="d-flex gap-3 mb-24 flex-wrap">
                    <div class="stat-pill" style="background:#ecfdf5;">
                        <div class="fs-5 fw-bold text-success">{{-- --}}<span id="cntPresent">0</span></div>
                        <div class="text-secondary-light small">Présents</div>
                    </div>
                    <div class="stat-pill" style="background:#fef2f2;">
                        <div class="fs-5 fw-bold text-danger"><span id="cntAbsent">0</span></div>
                        <div class="text-secondary-light small">Absents</div>
                    </div>
                    <div class="stat-pill" style="background:#fffbeb;">
                        <div class="fs-5 fw-bold" style="color:#d97706;"><span id="cntRetard">0</span></div>
                        <div class="text-secondary-light small">Retards</div>
                    </div>
                    <div class="stat-pill" style="background:#eff6ff;">
                        <div class="fs-5 fw-bold text-primary"><span id="cntPermission">0</span></div>
                        <div class="text-secondary-light small">Permissionnaires</div>
                    </div>
                    <div class="stat-pill" style="background:#f8fafc;">
                        <div class="fs-5 fw-bold text-dark"><span id="cntTotal">0</span></div>
                        <div class="text-secondary-light small">Total élèves</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0">Liste des élèves — <span id="classeNomAffiche" class="text-primary-600"></span></h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-success" id="btnTousPresents">
                                <iconify-icon icon="mdi:check-all"></iconify-icon> Tout marquer présent
                            </button>
                            <button class="btn btn-primary-600" id="btnEnregistrerAppel">
                                <iconify-icon icon="mdi:content-save-check-outline"></iconify-icon> Enregistrer l'appel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="rechercheEleve" placeholder="Rechercher un élève (nom, prénom, matricule)...">
                        </div>
                        <div id="listeEleves" class="d-flex flex-column gap-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= ONGLET HISTORIQUE ================= -->
        <div class="tab-pane fade" id="tabHistorique">
            <div class="card mb-24">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label mb-1">Classe *</label>
                            <select class="form-control form-control-sm" id="histClasse"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Année *</label>
                            <select class="form-control form-control-sm" id="histAnnee"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Statut</label>
                            <select class="form-control form-control-sm" id="histStatut">
                                <option value="">Tous</option>
                                <option value="present">Présent</option>
                                <option value="absent">Absent</option>
                                <option value="retard">Retard</option>
                                <option value="permission">Permissionnaire</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Du</label>
                            <input type="date" class="form-control form-control-sm" id="histDateDebut">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Au</label>
                            <input type="date" class="form-control form-control-sm" id="histDateFin">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button class="btn btn-sm btn-primary-600 w-100" id="btnHistFiltrer">Filtrer</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-24">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Taux de présence par élève</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="tableStats">
                            <thead>
                                <tr>
                                    <th>Élève</th>
                                    <th class="text-center">Présents</th>
                                    <th class="text-center">Absents</th>
                                    <th class="text-center">Retards</th>
                                    <th class="text-center">Permissions</th>
                                    <th class="text-center">Total appels</th>
                                    <th class="text-center">Taux présence</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyStats">
                                <tr><td colspan="7" class="text-center py-4">Sélectionnez une classe et une année</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Historique détaillé</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success" id="btnExportCsvHist"><iconify-icon icon="mdi:file-delimited-outline"></iconify-icon> CSV</button>
                        <button class="btn btn-sm btn-outline-success" id="btnExportExcelHist"><iconify-icon icon="mdi:file-excel-outline"></iconify-icon> Excel</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="tableHistorique">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Matricule</th>
                                    <th>Élève</th>
                                    <th>Statut</th>
                                    <th>Motif</th>
                                    <th>Fait par</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyHistorique">
                                <tr><td colspan="6" class="text-center py-4">Sélectionnez une classe et une année</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    const slug = @json($slug);
    const baseUrl = `/${slug}/pressance`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let classesCache = [];
    let anneesCache = [];
    let elevesCache = [];
    let mouvementsHistCache = [];
    let classeSelectionnee = null;
    let niveauSelectionne = null;

    // ---------- Helpers ----------
    function toast(message, type = 'success') {
        Toastify({
            text: message, duration: 3500, gravity: 'top', position: 'right',
            style: { background: type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#1565c0') }
        }).showToast();
    }

    function loader(show, title = 'Traitement en cours...') {
        if (show) Swal.fire({ title, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        else Swal.close();
    }

    async function apiFetch(url, options = {}) {
        options.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, options.headers || {});
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Une erreur est survenue');
        return data;
    }

    function todayStr() {
        const d = new Date();
        return d.toISOString().split('T')[0];
    }

    function isSunday(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        return d.getDay() === 0; // 0 = dimanche
    }

    function labelStatut(s) {
        return { present: 'Présent', absent: 'Absent', retard: 'Retard', permission: 'Permissionnaire' }[s] || s;
    }

    // ---------- Chargement initial : classes + années ----------
    async function initSelects() {
        try {
            const [dataClasses, dataAnnees] = await Promise.all([
                apiFetch(`${baseUrl}/classes`),
                apiFetch(`${baseUrl}/annees`),
            ]);
            classesCache = dataClasses.data;
            anneesCache = dataAnnees.data;

            const optionsClasses = classesCache.map(c => `<option value="${c.i_classe_id}" data-niveau="${c.i_niveau_id}">${c.v_nom_classe}</option>`).join('');
            const optionsAnnees = anneesCache.map(a => `<option value="${a}">${a}</option>`).join('');

            document.getElementById('selectClasse').innerHTML = `<option value="">-- Choisir --</option>` + optionsClasses;
            document.getElementById('selectAnnee').innerHTML = `<option value="">-- Choisir --</option>` + optionsAnnees;
            document.getElementById('histClasse').innerHTML = `<option value="">-- Choisir --</option>` + optionsClasses;
            document.getElementById('histAnnee').innerHTML = `<option value="">-- Choisir --</option>` + optionsAnnees;

            document.getElementById('inputDate').value = todayStr();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    // ---------- Blocage dimanche sur le champ date ----------
    document.getElementById('inputDate').addEventListener('change', function () {
        if (this.value && isSunday(this.value)) {
            toast("L'appel ne se fait pas le dimanche. Choisissez un jour du lundi au samedi.", 'error');
            this.value = todayStr();
        }
    });

    // ---------- Charger la liste des élèves pour l'appel ----------
    document.getElementById('btnChargerAppel').addEventListener('click', async function () {
        const classeSelect = document.getElementById('selectClasse');
        const classeId = classeSelect.value;
        const annee = document.getElementById('selectAnnee').value;
        const date = document.getElementById('inputDate').value;

        if (!classeId || !annee || !date) {
            toast('Veuillez sélectionner la classe, l\'année et la date.', 'error');
            return;
        }
        if (isSunday(date)) {
            toast("L'appel ne se fait pas le dimanche.", 'error');
            return;
        }

        classeSelectionnee = classeId;
        niveauSelectionne = classeSelect.selectedOptions[0].dataset.niveau;

        loader(true, 'Chargement de la liste...');
        try {
            const params = new URLSearchParams({ classe_id: classeId, annee, date });
            const data = await apiFetch(`${baseUrl}/eleves?${params.toString()}`);
            elevesCache = data.data;
            loader(false);

            document.getElementById('classeNomAffiche').textContent = classeSelect.selectedOptions[0].textContent + ' — ' + date;
            document.getElementById('zoneAppel').classList.remove('d-none');
            renderEleves();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    function renderEleves(filtre = '') {
        const container = document.getElementById('listeEleves');
        let liste = elevesCache;

        if (filtre) {
            const f = filtre.toLowerCase();
            liste = liste.filter(el =>
                el.v_nom.toLowerCase().includes(f) ||
                el.v_prenom.toLowerCase().includes(f) ||
                (el.v_matricule || '').toLowerCase().includes(f)
            );
        }

        if (!liste.length) {
            container.innerHTML = '<div class="text-center py-4 text-secondary-light">Aucun élève trouvé</div>';
            updateCompteurs();
            return;
        }

     const baseUrl = window.location.origin + "/";

container.innerHTML = liste.map(el => `
    <div class="eleve-card" data-eleve-id="${el.i_eleve_id}">
        <div class="d-flex align-items-center gap-3 flex-wrap">

            <img src="${el.v_photo ? baseUrl + el.v_photo : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(el.v_nom + ' ' + el.v_prenom) + '&background=e2e8f0&color=475569'}"
                 class="eleve-photo"
                 alt="${el.v_nom} ${el.v_prenom}">

            <div style="min-width:180px;">
                <div class="fw-semibold">${el.v_nom} ${el.v_prenom}</div>
                <div class="text-secondary-light small">${el.v_matricule || '-'}</div>
            </div>

            <div class="d-flex gap-2 flex-wrap ms-auto statut-group" data-inscription-id="${el.i_inscription_id}">
                <button type="button" class="statut-btn ${el.v_statut === 'present' ? 'active' : ''}" data-statut="present">
                    <iconify-icon icon="mdi:check-circle-outline"></iconify-icon> Présent
                </button>

                <button type="button" class="statut-btn ${el.v_statut === 'absent' ? 'active' : ''}" data-statut="absent">
                    <iconify-icon icon="mdi:close-circle-outline"></iconify-icon> Absent
                </button>

                <button type="button" class="statut-btn ${el.v_statut === 'retard' ? 'active' : ''}" data-statut="retard">
                    <iconify-icon icon="mdi:clock-alert-outline"></iconify-icon> Retard
                </button>

                <button type="button" class="statut-btn ${el.v_statut === 'permission' ? 'active' : ''}" data-statut="permission">
                    <iconify-icon icon="mdi:file-document-outline"></iconify-icon> Permission
                </button>
            </div>
        </div>

        <div class="motif-box mt-2 ${el.v_statut !== 'present' ? 'show' : ''}">
            <input type="text"
                   class="form-control form-control-sm motif-input"
                   placeholder="Motif / observation..."
                   value="${el.v_motif ?? ''}">
        </div>
    </div>
`).join('');

        // Écouteurs sur les boutons de statut
        container.querySelectorAll('.eleve-card').forEach(card => {
            card.querySelectorAll('.statut-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    card.querySelectorAll('.statut-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const motifBox = card.querySelector('.motif-box');
                    motifBox.classList.toggle('show', this.dataset.statut !== 'present');
                    updateCompteurs();
                });
            });
        });

        updateCompteurs();
    }

    function updateCompteurs() {
        const cards = document.querySelectorAll('#listeEleves .eleve-card');
        let present = 0, absent = 0, retard = 0, permission = 0;
        cards.forEach(card => {
            const actif = card.querySelector('.statut-btn.active');
            const s = actif ? actif.dataset.statut : 'present';
            if (s === 'present') present++;
            else if (s === 'absent') absent++;
            else if (s === 'retard') retard++;
            else if (s === 'permission') permission++;
        });
        document.getElementById('cntPresent').textContent = present;
        document.getElementById('cntAbsent').textContent = absent;
        document.getElementById('cntRetard').textContent = retard;
        document.getElementById('cntPermission').textContent = permission;
        document.getElementById('cntTotal').textContent = cards.length;
    }

    document.getElementById('rechercheEleve').addEventListener('input', function () {
        renderEleves(this.value);
    });

    document.getElementById('btnTousPresents').addEventListener('click', function () {
        document.querySelectorAll('#listeEleves .eleve-card').forEach(card => {
            card.querySelectorAll('.statut-btn').forEach(b => b.classList.remove('active'));
            card.querySelector('.statut-btn[data-statut="present"]').classList.add('active');
            card.querySelector('.motif-box').classList.remove('show');
        });
        updateCompteurs();
        toast('Tous les élèves ont été marqués présents', 'info');
    });

    // ---------- Enregistrer l'appel ----------
    document.getElementById('btnEnregistrerAppel').addEventListener('click', async function () {
        const cards = document.querySelectorAll('#listeEleves .eleve-card');
        if (!cards.length) { toast('Aucun élève à enregistrer.', 'error'); return; }

        const appel = [];
        cards.forEach(card => {
            const actif = card.querySelector('.statut-btn.active');
            const statut = actif ? actif.dataset.statut : 'present';
            const motif = card.querySelector('.motif-input').value.trim();
            appel.push({
                eleve_id: card.dataset.eleveId,
                inscription_id: card.querySelector('.statut-group').dataset.inscriptionId,
                statut: statut,
                motif: motif || null,
                observation: null,
            });
        });

        const nbAbsents = appel.filter(a => a.statut === 'absent').length;
        const confirm = await Swal.fire({
            title: 'Confirmer l\'enregistrement de l\'appel ?',
            html: `<div class="text-start">${nbAbsents > 0 ? `<b>${nbAbsents}</b> absence(s) seront enregistrées.<br>` : ''}Cette action mettra à jour l'appel du jour pour cette classe.</div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler'
        });
        if (!confirm.isConfirmed) return;

        loader(true, 'Enregistrement de l\'appel...');
        try {
            const data = await apiFetch(`${baseUrl}/save`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    classe_id: classeSelectionnee,
                    niveau_id: niveauSelectionne,
                    annee: document.getElementById('selectAnnee').value,
                    date: document.getElementById('inputDate').value,
                    appel: appel
                })
            });
            loader(false);
            toast(data.message, 'success');
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    // ================= ONGLET HISTORIQUE =================
    document.getElementById('btnHistFiltrer').addEventListener('click', async function () {
        const classeId = document.getElementById('histClasse').value;
        const annee = document.getElementById('histAnnee').value;

        if (!classeId || !annee) {
            toast('Veuillez sélectionner une classe et une année.', 'error');
            return;
        }

        loader(true, 'Chargement de l\'historique...');
        try {
            await Promise.all([chargerHistorique(), chargerStats()]);
            loader(false);
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    async function chargerHistorique() {
        const params = new URLSearchParams({
            classe_id: document.getElementById('histClasse').value,
            annee: document.getElementById('histAnnee').value,
        });
        const statut = document.getElementById('histStatut').value;
        const dateDebut = document.getElementById('histDateDebut').value;
        const dateFin = document.getElementById('histDateFin').value;
        if (statut) params.append('statut', statut);
        if (dateDebut) params.append('date_debut', dateDebut);
        if (dateFin) params.append('date_fin', dateFin);

        const data = await apiFetch(`${baseUrl}/historique?${params.toString()}`);
        mouvementsHistCache = data.data;
        renderHistorique();
    }

    function renderHistorique() {
        const tbody = document.getElementById('tbodyHistorique');
        if (!mouvementsHistCache.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Aucun enregistrement trouvé</td></tr>';
            return;
        }
        tbody.innerHTML = mouvementsHistCache.map(m => `
            <tr>
                <td>${m.d_date_appel}</td>
                <td>${m.v_matricule ?? '-'}</td>
                <td>${m.v_nom} ${m.v_prenom}</td>
                <td>${labelStatut(m.v_statut)}</td>
                <td>${m.v_motif ?? '-'}</td>
                <td>${m.user_nom ?? '-'}</td>
            </tr>
        `).join('');
    }

    async function chargerStats() {
        const params = new URLSearchParams({
            classe_id: document.getElementById('histClasse').value,
            annee: document.getElementById('histAnnee').value,
        });
        const data = await apiFetch(`${baseUrl}/stats?${params.toString()}`);
        renderStats(data.data);
    }

    function renderStats(stats) {
        const tbody = document.getElementById('tbodyStats');
        if (!stats.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Aucune donnée</td></tr>';
            return;
        }
        tbody.innerHTML = stats.map(s => {
            const taux = s.total_appels > 0 ? ((s.nb_present / s.total_appels) * 100).toFixed(1) : 0;
            return `
                <tr>
                    <td class="fw-semibold">${s.nom_complet}</td>
                    <td class="text-center">${s.nb_present}</td>
                    <td class="text-center">${s.nb_absent}</td>
                    <td class="text-center">${s.nb_retard}</td>
                    <td class="text-center">${s.nb_permission}</td>
                    <td class="text-center">${s.total_appels}</td>
                    <td class="text-center">
                        <span class="badge ${taux >= 80 ? 'bg-success-focus text-success-600' : (taux >= 50 ? 'bg-warning-focus text-warning-600' : 'bg-danger-focus text-danger-600')}">${taux}%</span>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // ---------- Export CSV / Excel de l'historique ----------
    function getExportRows() {
        return mouvementsHistCache.map(m => ({
            'Date': m.d_date_appel,
            'Matricule': m.v_matricule ?? '',
            'Élève': m.v_nom + ' ' + m.v_prenom,
            'Statut': labelStatut(m.v_statut),
            'Motif': m.v_motif ?? '',
            'Fait par': m.user_nom ?? ''
        }));
    }

    document.getElementById('btnExportCsvHist').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const headers = Object.keys(rows[0]);
        let csv = headers.join(';') + '\n';
        rows.forEach(r => { csv += headers.map(h => `"${String(r[h]).replace(/"/g, '""')}"`).join(';') + '\n'; });
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `historique_appel_${Date.now()}.csv`;
        link.click();
        toast('Export CSV effectué', 'success');
    });

    document.getElementById('btnExportExcelHist').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Historique appel');
        XLSX.writeFile(wb, `historique_appel_${Date.now()}.xlsx`);
        toast('Export Excel effectué', 'success');
    });

    // ---------- Init ----------
    initSelects();
})();
</script>

@endsection
