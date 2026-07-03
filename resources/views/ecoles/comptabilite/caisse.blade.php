@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion de la caisse</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Comptabilité / Caisse</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Liste des caisses</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTransfert">
                    <iconify-icon icon="mdi:bank-transfer"></iconify-icon> Transfert
                </button>
                <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNouvelleCaisse">
                    <iconify-icon icon="ic:baseline-plus"></iconify-icon> Nouvelle caisse
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="tableCaisses">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Solde</th>
                            <th>Statut</th>
                            <th>Créée le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCaisses">
                        <tr><td colspan="6" class="text-center py-4">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Nouvelle caisse -->
<div class="modal fade" id="modalNouvelleCaisse" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formNouvelleCaisse">
                <div class="modal-header">
                    <h6 class="modal-title">Nouvelle caisse</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom de la caisse *</label>
                        <input type="text" class="form-control" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Solde initial</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="solde_initial" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary-600">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Alimenter -->
<div class="modal fade" id="modalAlimenter" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAlimenter">
                <input type="hidden" name="caisse_id" id="alimenterCaisseId">
                <div class="modal-header">
                    <h6 class="modal-title">Alimenter la caisse : <span id="alimenterCaisseNom" class="text-primary-600"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Montant *</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="montant" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <input type="text" class="form-control" name="motif">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success-600">Alimenter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Transfert -->
<div class="modal fade" id="modalTransfert" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTransfert">
                <div class="modal-header">
                    <h6 class="modal-title">Transfert</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Caisse source *</label>
                        <select class="form-control" name="caisse_source_id" id="transfertSourceSelect" required></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type de transfert *</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input type-transfert" type="radio" name="type_transfert" value="interne" checked>
                                <label class="form-check-label">Vers une autre caisse</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input type-transfert" type="radio" name="type_transfert" value="bancaire">
                                <label class="form-check-label">Vers un compte bancaire</label>
                            </div>
                        </div>
                    </div>

                    <div id="blocInterne" class="mb-3">
                        <label class="form-label">Caisse destination *</label>
                        <select class="form-control" name="caisse_dest_id" id="transfertDestSelect"></select>
                    </div>

                    <div id="blocBancaire" class="mb-3 d-none">
                        <label class="form-label">Compte bancaire destination *</label>
                        <select class="form-control" name="compte_dest_id" id="transfertCompteBancaireSelect"></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Montant *</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="montant" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <input type="text" class="form-control" name="motif">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary-600">Transférer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Détails / historique -->
<div class="modal fade" id="modalDetails" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Historique de la caisse : <span id="detailsCaisseNom" class="text-primary-600"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Type</label>
                        <select class="form-control form-control-sm" id="filtreType">
                            <option value="">Tous</option>
                            <option value="alimentation">Alimentation</option>
                            <option value="depense">Dépense</option>
                            <option value="transfert_sortant">Transfert sortant</option>
                            <option value="transfert_entrant">Transfert entrant</option>
                            <option value="transfert_vers_compte">Vers compte bancaire</option>
                            <option value="transfert_depuis_compte">Depuis compte bancaire</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Du</label>
                        <input type="date" class="form-control form-control-sm" id="filtreDateDebut">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Au</label>
                        <input type="date" class="form-control form-control-sm" id="filtreDateFin">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-sm btn-primary-600 w-100" id="btnFiltrer">Filtrer</button>
                        <button class="btn btn-sm btn-outline-secondary" id="btnResetFiltre"><iconify-icon icon="mdi:refresh"></iconify-icon></button>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-2">
                    <button class="btn btn-sm btn-outline-success" id="btnExportCsv"><iconify-icon icon="mdi:file-delimited-outline"></iconify-icon> CSV</button>
                    <button class="btn btn-sm btn-outline-success" id="btnExportExcel"><iconify-icon icon="mdi:file-excel-outline"></iconify-icon> Excel</button>
                </div>

                <div class="table-responsive">
                    <table class="table bordered-table mb-0" id="tableMouvements">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Solde après</th>
                                <th>Motif</th>
                                <th>Détails</th>
                                <th>Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyMouvements">
                            <tr><td colspan="7" class="text-center py-4">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const slug = @json($slug);
    const baseUrl = `/${slug}/caisse`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let caissesCache = [];
    let mouvementsCache = [];
    let caisseCourante = null;

    function toast(message, type = 'success') {
        Toastify({
            text: message,
            duration: 3500,
            gravity: 'top',
            position: 'right',
            style: {
                background: type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#1565c0')
            }
        }).showToast();
    }

    function loader(show, title = 'Traitement en cours...') {
        if (show) {
            Swal.fire({
                title: title,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        } else {
            Swal.close();
        }
    }

    function formatMontant(v) {
        return parseFloat(v).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function badgeStatut(statut) {
        return statut === 'active'
            ? '<span class="badge bg-success-focus text-success-600">Active</span>'
            : '<span class="badge bg-danger-focus text-danger-600">Suspendue</span>';
    }

    function labelType(type) {
        const labels = {
            alimentation: 'Alimentation',
            depense: 'Dépense',
            transfert_sortant: 'Transfert sortant',
            transfert_entrant: 'Transfert entrant',
            transfert_vers_compte: 'Vers compte bancaire',
            transfert_depuis_compte: 'Depuis compte bancaire'
        };
        return labels[type] || type;
    }

    async function apiFetch(url, options = {}) {
        options.headers = Object.assign({
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        }, options.headers || {});
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw new Error(data.message || 'Une erreur est survenue');
        }
        return data;
    }

    // ---------- Chargement de la liste des caisses ----------
    async function chargerCaisses() {
        try {
            const data = await apiFetch(`${baseUrl}/list`);
            caissesCache = data.data;
            renderCaisses();
            remplirSelectsCaisses();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    async function chargerComptesPourSelect() {
        try {
            const data = await apiFetch(`/${slug}/compte-bancaire/list`);
            const select = document.getElementById('transfertCompteBancaireSelect');
            select.innerHTML = data.data
                .filter(c => c.statut === 'active')
                .map(c => `<option value="${c.id}">${c.nom} - ${c.banque} (${formatMontant(c.solde)})</option>`)
                .join('');
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    function renderCaisses() {
        const tbody = document.getElementById('tbodyCaisses');
        if (!caissesCache.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Aucune caisse enregistrée</td></tr>';
            return;
        }
        tbody.innerHTML = caissesCache.map(c => `
            <tr>
                <td class="fw-semibold">${c.nom}</td>
                <td>${c.description ?? '-'}</td>
                <td>${formatMontant(c.solde)}</td>
                <td>${badgeStatut(c.statut)}</td>
                <td>${new Date(c.created_at).toLocaleDateString('fr-FR')}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-outline-success" ${c.statut === 'suspendue' ? 'disabled' : ''} onclick="caisseAlimenter(${c.id}, '${c.nom}')" title="Alimenter"><iconify-icon icon="mdi:cash-plus"></iconify-icon></button>
                        <button class="btn btn-sm btn-outline-primary" onclick="caisseDetails(${c.id}, '${c.nom}')" title="Détails"><iconify-icon icon="mdi:eye-outline"></iconify-icon></button>
                        <button class="btn btn-sm btn-outline-${c.statut === 'active' ? 'danger' : 'success'}" onclick="caisseSuspendre(${c.id}, '${c.nom}', '${c.statut}')" title="${c.statut === 'active' ? 'Suspendre' : 'Réactiver'}">
                            <iconify-icon icon="${c.statut === 'active' ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'}"></iconify-icon>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function remplirSelectsCaisses() {
        const sourceSelect = document.getElementById('transfertSourceSelect');
        const destSelect = document.getElementById('transfertDestSelect');
        const options = caissesCache
            .filter(c => c.statut === 'active')
            .map(c => `<option value="${c.id}">${c.nom} (${formatMontant(c.solde)})</option>`).join('');
        sourceSelect.innerHTML = options;
        destSelect.innerHTML = options;
    }

    // ---------- Nouvelle caisse ----------
    document.getElementById('formNouvelleCaisse').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        const confirm = await Swal.fire({
            title: 'Confirmer la création ?',
            text: 'Une nouvelle caisse va être créée.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, créer',
            cancelButtonText: 'Annuler'
        });
        if (!confirm.isConfirmed) return;

        loader(true, 'Création en cours...');
        try {
            const data = await apiFetch(`${baseUrl}/store`, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalNouvelleCaisse')).hide();
            chargerCaisses();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    // ---------- Alimenter ----------
    window.caisseAlimenter = function (id, nom) {
        document.getElementById('alimenterCaisseId').value = id;
        document.getElementById('alimenterCaisseNom').textContent = nom;
        document.getElementById('formAlimenter').reset();
        document.getElementById('alimenterCaisseId').value = id;
        new bootstrap.Modal(document.getElementById('modalAlimenter')).show();
    };

    document.getElementById('formAlimenter').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        const confirm = await Swal.fire({
            title: 'Confirmer l\'alimentation ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, alimenter',
            cancelButtonText: 'Annuler'
        });
        if (!confirm.isConfirmed) return;

        loader(true, 'Alimentation en cours...');
        try {
            const data = await apiFetch(`${baseUrl}/alimenter`, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalAlimenter')).hide();
            chargerCaisses();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    // ---------- Transfert ----------
    document.querySelectorAll('.type-transfert').forEach(radio => {
        radio.addEventListener('change', function () {
            const isInterne = this.value === 'interne';
            document.getElementById('blocInterne').classList.toggle('d-none', !isInterne);
            document.getElementById('blocBancaire').classList.toggle('d-none', isInterne);
        });
    });

    document.getElementById('formTransfert').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        const confirm = await Swal.fire({
            title: 'Confirmer le transfert ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, transférer',
            cancelButtonText: 'Annuler'
        });
        if (!confirm.isConfirmed) return;

        loader(true, 'Transfert en cours...');
        try {
            const data = await apiFetch(`${baseUrl}/transfert`, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalTransfert')).hide();
            chargerCaisses();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    // ---------- Suspendre / réactiver ----------
    window.caisseSuspendre = async function (id, nom, statutActuel) {
        const action = statutActuel === 'active' ? 'suspendre' : 'réactiver';
        const confirm = await Swal.fire({
            title: `Voulez-vous ${action} la caisse "${nom}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Oui, ${action}`,
            cancelButtonText: 'Annuler'
        });
        if (!confirm.isConfirmed) return;

        loader(true);
        try {
            const data = await apiFetch(`${baseUrl}/${id}/suspendre`, { method: 'POST' });
            loader(false);
            toast(data.message, 'success');
            chargerCaisses();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    };

    // ---------- Détails / historique ----------
    window.caisseDetails = async function (id, nom) {
        caisseCourante = id;
        document.getElementById('detailsCaisseNom').textContent = nom;
        document.getElementById('filtreType').value = '';
        document.getElementById('filtreDateDebut').value = '';
        document.getElementById('filtreDateFin').value = '';
        new bootstrap.Modal(document.getElementById('modalDetails')).show();
        await chargerMouvements();
    };

    async function chargerMouvements() {
        const tbody = document.getElementById('tbodyMouvements');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Chargement...</td></tr>';

        const params = new URLSearchParams();
        const type = document.getElementById('filtreType').value;
        const dateDebut = document.getElementById('filtreDateDebut').value;
        const dateFin = document.getElementById('filtreDateFin').value;
        if (type) params.append('type', type);
        if (dateDebut) params.append('date_debut', dateDebut);
        if (dateFin) params.append('date_fin', dateFin);

        try {
            const data = await apiFetch(`${baseUrl}/${caisseCourante}/mouvements?${params.toString()}`);
            mouvementsCache = data.data;
            renderMouvements();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    function renderMouvements() {
        const tbody = document.getElementById('tbodyMouvements');
        if (!mouvementsCache.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Aucun mouvement trouvé</td></tr>';
            return;
        }
        tbody.innerHTML = mouvementsCache.map(m => `
            <tr>
                <td>${new Date(m.created_at).toLocaleString('fr-FR')}</td>
                <td>${labelType(m.type)}</td>
                <td>${formatMontant(m.montant)}</td>
                <td>${formatMontant(m.solde_apres)}</td>
                <td>${m.motif ?? '-'}</td>
                <td>${m.caisse_destination_nom ? 'Vers : ' + m.caisse_destination_nom : (m.banque_nom ? 'Banque : ' + m.banque_nom + (m.banque_compte ? ' (' + m.banque_compte + ')' : '') : '-')}</td>
                <td>${m.user_nom ?? '-'}</td>
            </tr>
        `).join('');
    }

    document.getElementById('btnFiltrer').addEventListener('click', chargerMouvements);
    document.getElementById('btnResetFiltre').addEventListener('click', function () {
        document.getElementById('filtreType').value = '';
        document.getElementById('filtreDateDebut').value = '';
        document.getElementById('filtreDateFin').value = '';
        chargerMouvements();
    });

    // ---------- Export CSV / Excel (côté JS) ----------
    function getExportRows() {
        return mouvementsCache.map(m => ({
            'Date': new Date(m.created_at).toLocaleString('fr-FR'),
            'Type': labelType(m.type),
            'Montant': m.montant,
            'Solde après': m.solde_apres,
            'Motif': m.motif ?? '',
            'Détails': m.caisse_destination_nom ? ('Vers ' + m.caisse_destination_nom) : (m.banque_nom ?? ''),
            'Utilisateur': m.user_nom ?? ''
        }));
    }

    document.getElementById('btnExportCsv').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }

        const headers = Object.keys(rows[0]);
        let csv = headers.join(';') + '\n';
        rows.forEach(r => {
            csv += headers.map(h => `"${String(r[h]).replace(/"/g, '""')}"`).join(';') + '\n';
        });

        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `historique_caisse_${Date.now()}.csv`;
        link.click();
        toast('Export CSV effectué', 'success');
    });

    document.getElementById('btnExportExcel').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }

        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Mouvements');
        XLSX.writeFile(wb, `historique_caisse_${Date.now()}.xlsx`);
        toast('Export Excel effectué', 'success');
    });

    // ---------- Init ----------
    chargerCaisses();
    chargerComptesPourSelect();
})();
</script>

@endsection
