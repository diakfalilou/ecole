@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des comptes bancaires</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Comptabilité / Comptes bancaires</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Liste des comptes bancaires</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTransfertCompte">
                    <iconify-icon icon="mdi:bank-transfer"></iconify-icon> Transfert
                </button>
                <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNouveauCompte">
                    <iconify-icon icon="ic:baseline-plus"></iconify-icon> Nouveau compte
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="tableComptes">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Banque</th>
                            <th>N° Compte</th>
                            <th>Solde</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyComptes">
                        <tr><td colspan="7" class="text-center py-4">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Nouveau compte -->
<div class="modal fade" id="modalNouveauCompte" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formNouveauCompte">
                <div class="modal-header">
                    <h6 class="modal-title">Nouveau compte bancaire</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du compte *</label>
                        <input type="text" class="form-control" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banque *</label>
                        <input type="text" class="form-control" name="banque" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numéro de compte *</label>
                        <input type="text" class="form-control" name="numero_compte" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">IBAN</label>
                        <input type="text" class="form-control" name="iban">
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
<div class="modal fade" id="modalAlimenterCompte" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAlimenterCompte">
                <input type="hidden" name="compte_id" id="alimenterCompteId">
                <div class="modal-header">
                    <h6 class="modal-title">Alimenter le compte : <span id="alimenterCompteNom" class="text-primary-600"></span></h6>
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

<!-- MODAL: Transfert compte -->
<div class="modal fade" id="modalTransfertCompte" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTransfertCompte">
                <div class="modal-header">
                    <h6 class="modal-title">Transfert</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Compte source *</label>
                        <select class="form-control" name="compte_source_id" id="transfertCompteSourceSelect" required></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type de transfert *</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input type-transfert-compte" type="radio" name="type_transfert" value="compte" checked>
                                <label class="form-check-label">Vers un autre compte</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input type-transfert-compte" type="radio" name="type_transfert" value="caisse">
                                <label class="form-check-label">Vers une caisse</label>
                            </div>
                        </div>
                    </div>

                    <div id="blocCompteDest" class="mb-3">
                        <label class="form-label">Compte destination *</label>
                        <select class="form-control" name="compte_dest_id" id="transfertCompteDestSelect"></select>
                    </div>

                    <div id="blocCaisseDest" class="mb-3 d-none">
                        <label class="form-label">Caisse destination *</label>
                        <select class="form-control" name="caisse_dest_id" id="transfertCaisseDestSelect"></select>
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
<div class="modal fade" id="modalDetailsCompte" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Historique du compte : <span id="detailsCompteNom" class="text-primary-600"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Type</label>
                        <select class="form-control form-control-sm" id="filtreTypeCompte">
                            <option value="">Tous</option>
                            <option value="alimentation">Alimentation</option>
                            <option value="transfert_sortant">Transfert sortant</option>
                            <option value="transfert_entrant">Transfert entrant</option>
                            <option value="vers_caisse">Vers caisse</option>
                            <option value="depuis_caisse">Depuis caisse</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Du</label>
                        <input type="date" class="form-control form-control-sm" id="filtreDateDebutCompte">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Au</label>
                        <input type="date" class="form-control form-control-sm" id="filtreDateFinCompte">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-sm btn-primary-600 w-100" id="btnFiltrerCompte">Filtrer</button>
                        <button class="btn btn-sm btn-outline-secondary" id="btnResetFiltreCompte"><iconify-icon icon="mdi:refresh"></iconify-icon></button>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-2">
                    <button class="btn btn-sm btn-outline-success" id="btnExportCsvCompte"><iconify-icon icon="mdi:file-delimited-outline"></iconify-icon> CSV</button>
                    <button class="btn btn-sm btn-outline-success" id="btnExportExcelCompte"><iconify-icon icon="mdi:file-excel-outline"></iconify-icon> Excel</button>
                </div>

                <div class="table-responsive">
                    <table class="table bordered-table mb-0" id="tableMouvementsCompte">
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
                        <tbody id="tbodyMouvementsCompte">
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
    const baseUrl = `/${slug}/compte-bancaire`;
    const caisseUrl = `/${slug}/caisse`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let comptesCache = [];
    let caissesCache = [];
    let mouvementsCache = [];
    let compteCourant = null;

    function toast(message, type = 'success') {
        Toastify({
            text: message, duration: 3500, gravity: 'top', position: 'right',
            style: { background: type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#1565c0') }
        }).showToast();
    }

    function loader(show, title = 'Traitement en cours...') {
        if (show) {
            Swal.fire({ title, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
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
            : '<span class="badge bg-danger-focus text-danger-600">Suspendu</span>';
    }

    function labelType(type) {
        const labels = {
            alimentation: 'Alimentation',
            transfert_sortant: 'Transfert sortant',
            transfert_entrant: 'Transfert entrant',
            vers_caisse: 'Vers caisse',
            depuis_caisse: 'Depuis caisse'
        };
        return labels[type] || type;
    }

    async function apiFetch(url, options = {}) {
        options.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, options.headers || {});
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Une erreur est survenue');
        return data;
    }

    // ---------- Chargement comptes ----------
    async function chargerComptes() {
        try {
            const data = await apiFetch(`${baseUrl}/list`);
            comptesCache = data.data;
            renderComptes();
            remplirSelectComptes();
        } catch (e) { toast(e.message, 'error'); }
    }

    async function chargerCaissesPourSelect() {
        try {
            const data = await apiFetch(`${caisseUrl}/list`);
            caissesCache = data.data;
            const select = document.getElementById('transfertCaisseDestSelect');
            select.innerHTML = caissesCache.filter(c => c.statut === 'active')
                .map(c => `<option value="${c.id}">${c.nom} (${formatMontant(c.solde)})</option>`).join('');
        } catch (e) { toast(e.message, 'error'); }
    }

    function renderComptes() {
        const tbody = document.getElementById('tbodyComptes');
        if (!comptesCache.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Aucun compte enregistré</td></tr>';
            return;
        }
        tbody.innerHTML = comptesCache.map(c => `
            <tr>
                <td class="fw-semibold">${c.nom}</td>
                <td>${c.banque}</td>
                <td>${c.numero_compte}</td>
                <td>${formatMontant(c.solde)}</td>
                <td>${badgeStatut(c.statut)}</td>
                <td>${new Date(c.created_at).toLocaleDateString('fr-FR')}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-outline-success" ${c.statut === 'suspendu' ? 'disabled' : ''} onclick="compteAlimenter(${c.id}, '${c.nom}')" title="Alimenter"><iconify-icon icon="mdi:cash-plus"></iconify-icon></button>
                        <button class="btn btn-sm btn-outline-primary" onclick="compteDetails(${c.id}, '${c.nom}')" title="Détails"><iconify-icon icon="mdi:eye-outline"></iconify-icon></button>
                        <button class="btn btn-sm btn-outline-${c.statut === 'active' ? 'danger' : 'success'}" onclick="compteSuspendre(${c.id}, '${c.nom}', '${c.statut}')" title="${c.statut === 'active' ? 'Suspendre' : 'Réactiver'}">
                            <iconify-icon icon="${c.statut === 'active' ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'}"></iconify-icon>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function remplirSelectComptes() {
        const sourceSelect = document.getElementById('transfertCompteSourceSelect');
        const destSelect = document.getElementById('transfertCompteDestSelect');
        const options = comptesCache.filter(c => c.statut === 'active')
            .map(c => `<option value="${c.id}">${c.nom} - ${c.banque} (${formatMontant(c.solde)})</option>`).join('');
        sourceSelect.innerHTML = options;
        destSelect.innerHTML = options;
    }

    // ---------- Nouveau compte ----------
    document.getElementById('formNouveauCompte').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const confirm = await Swal.fire({ title: 'Confirmer la création ?', icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, créer', cancelButtonText: 'Annuler' });
        if (!confirm.isConfirmed) return;

        loader(true, 'Création en cours...');
        try {
            const data = await apiFetch(`${baseUrl}/store`, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalNouveauCompte')).hide();
            chargerComptes();
        } catch (e) { loader(false); toast(e.message, 'error'); }
    });

    // ---------- Alimenter ----------
    window.compteAlimenter = function (id, nom) {
        document.getElementById('alimenterCompteId').value = id;
        document.getElementById('alimenterCompteNom').textContent = nom;
        document.getElementById('formAlimenterCompte').reset();
        document.getElementById('alimenterCompteId').value = id;
        new bootstrap.Modal(document.getElementById('modalAlimenterCompte')).show();
    };

    document.getElementById('formAlimenterCompte').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const confirm = await Swal.fire({ title: 'Confirmer l\'alimentation ?', icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, alimenter', cancelButtonText: 'Annuler' });
        if (!confirm.isConfirmed) return;

        loader(true, 'Alimentation en cours...');
        try {
            const data = await apiFetch(`${baseUrl}/alimenter`, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalAlimenterCompte')).hide();
            chargerComptes();
        } catch (e) { loader(false); toast(e.message, 'error'); }
    });

    // ---------- Transfert ----------
    document.querySelectorAll('.type-transfert-compte').forEach(radio => {
        radio.addEventListener('change', function () {
            const versCompte = this.value === 'compte';
            document.getElementById('blocCompteDest').classList.toggle('d-none', !versCompte);
            document.getElementById('blocCaisseDest').classList.toggle('d-none', versCompte);
        });
    });

    document.getElementById('formTransfertCompte').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const confirm = await Swal.fire({ title: 'Confirmer le transfert ?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, transférer', cancelButtonText: 'Annuler' });
        if (!confirm.isConfirmed) return;

        loader(true, 'Transfert en cours...');
        try {
            const data = await apiFetch(`${baseUrl}/transfert`, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalTransfertCompte')).hide();
            chargerComptes();
        } catch (e) { loader(false); toast(e.message, 'error'); }
    });

    // ---------- Suspendre / réactiver ----------
    window.compteSuspendre = async function (id, nom, statutActuel) {
        const action = statutActuel === 'active' ? 'suspendre' : 'réactiver';
        const confirm = await Swal.fire({ title: `Voulez-vous ${action} le compte "${nom}" ?`, icon: 'warning', showCancelButton: true, confirmButtonText: `Oui, ${action}`, cancelButtonText: 'Annuler' });
        if (!confirm.isConfirmed) return;

        loader(true);
        try {
            const data = await apiFetch(`${baseUrl}/${id}/suspendre`, { method: 'POST' });
            loader(false);
            toast(data.message, 'success');
            chargerComptes();
        } catch (e) { loader(false); toast(e.message, 'error'); }
    };

    // ---------- Détails / historique ----------
    window.compteDetails = async function (id, nom) {
        compteCourant = id;
        document.getElementById('detailsCompteNom').textContent = nom;
        document.getElementById('filtreTypeCompte').value = '';
        document.getElementById('filtreDateDebutCompte').value = '';
        document.getElementById('filtreDateFinCompte').value = '';
        new bootstrap.Modal(document.getElementById('modalDetailsCompte')).show();
        await chargerMouvementsCompte();
    };

    async function chargerMouvementsCompte() {
        const tbody = document.getElementById('tbodyMouvementsCompte');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Chargement...</td></tr>';

        const params = new URLSearchParams();
        const type = document.getElementById('filtreTypeCompte').value;
        const dateDebut = document.getElementById('filtreDateDebutCompte').value;
        const dateFin = document.getElementById('filtreDateFinCompte').value;
        if (type) params.append('type', type);
        if (dateDebut) params.append('date_debut', dateDebut);
        if (dateFin) params.append('date_fin', dateFin);

        try {
            const data = await apiFetch(`${baseUrl}/${compteCourant}/mouvements?${params.toString()}`);
            mouvementsCache = data.data;
            renderMouvementsCompte();
        } catch (e) { toast(e.message, 'error'); }
    }

    function renderMouvementsCompte() {
        const tbody = document.getElementById('tbodyMouvementsCompte');
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
                <td>${m.compte_destination_nom ? 'Vers : ' + m.compte_destination_nom : (m.caisse_nom ? 'Caisse : ' + m.caisse_nom : '-')}</td>
                <td>${m.user_nom ?? '-'}</td>
            </tr>
        `).join('');
    }

    document.getElementById('btnFiltrerCompte').addEventListener('click', chargerMouvementsCompte);
    document.getElementById('btnResetFiltreCompte').addEventListener('click', function () {
        document.getElementById('filtreTypeCompte').value = '';
        document.getElementById('filtreDateDebutCompte').value = '';
        document.getElementById('filtreDateFinCompte').value = '';
        chargerMouvementsCompte();
    });

    // ---------- Export CSV / Excel ----------
    function getExportRows() {
        return mouvementsCache.map(m => ({
            'Date': new Date(m.created_at).toLocaleString('fr-FR'),
            'Type': labelType(m.type),
            'Montant': m.montant,
            'Solde après': m.solde_apres,
            'Motif': m.motif ?? '',
            'Détails': m.compte_destination_nom ? ('Vers ' + m.compte_destination_nom) : (m.caisse_nom ?? ''),
            'Utilisateur': m.user_nom ?? ''
        }));
    }

    document.getElementById('btnExportCsvCompte').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const headers = Object.keys(rows[0]);
        let csv = headers.join(';') + '\n';
        rows.forEach(r => { csv += headers.map(h => `"${String(r[h]).replace(/"/g, '""')}"`).join(';') + '\n'; });
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `historique_compte_${Date.now()}.csv`;
        link.click();
        toast('Export CSV effectué', 'success');
    });

    document.getElementById('btnExportExcelCompte').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Mouvements');
        XLSX.writeFile(wb, `historique_compte_${Date.now()}.xlsx`);
        toast('Export Excel effectué', 'success');
    });

    // ---------- Init ----------
    chargerComptes();
    chargerCaissesPourSelect();
})();
</script>

@endsection
