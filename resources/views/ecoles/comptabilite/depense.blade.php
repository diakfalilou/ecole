@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des dépenses</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Comptabilité / Dépenses</span>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-24">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label mb-1">Catégorie</label>
                    <select class="form-control form-control-sm" id="filtreCategorie">
                        <option value="">Toutes</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Source</label>
                    <select class="form-control form-control-sm" id="filtreSourceType">
                        <option value="">Toutes</option>
                        <option value="caisse">Caisse</option>
                        <option value="compte_bancaire">Compte bancaire</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Statut</label>
                    <select class="form-control form-control-sm" id="filtreStatut">
                        <option value="">Tous</option>
                        <option value="validee">Validée</option>
                        <option value="annulee">Annulée</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Du</label>
                    <input type="date" class="form-control form-control-sm" id="filtreDateDebut">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Au</label>
                    <input type="date" class="form-control form-control-sm" id="filtreDateFin">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-primary-600 w-100" id="btnFiltrer">Filtrer</button>
                    <button class="btn btn-sm btn-outline-secondary" id="btnResetFiltre"><iconify-icon icon="mdi:refresh"></iconify-icon></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Totaux -->
    <div class="d-flex gap-3 mb-24 flex-wrap">
        <div class="p-3 rounded-3" style="background:#fef2f2; flex:1;">
            <div class="fs-5 fw-bold text-danger" id="totalDepenses">0</div>
            <div class="text-secondary-light small">Total dépenses (période affichée)</div>
        </div>
        <div class="p-3 rounded-3" style="background:#f8fafc; flex:1;">
            <div class="fs-5 fw-bold text-dark" id="nbDepenses">0</div>
            <div class="text-secondary-light small">Nombre de dépenses</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Liste des dépenses</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-success" id="btnExportCsv"><iconify-icon icon="mdi:file-delimited-outline"></iconify-icon> CSV</button>
                <button class="btn btn-sm btn-outline-success" id="btnExportExcel"><iconify-icon icon="mdi:file-excel-outline"></iconify-icon> Excel</button>
                <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalNouvelleDepense">
                    <iconify-icon icon="ic:baseline-plus"></iconify-icon> Nouvelle dépense
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="tableDepenses">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Catégorie</th>
                            <th>Bénéficiaire</th>
                            <th>Source</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Justificatif</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDepenses">
                        <tr><td colspan="9" class="text-center py-4">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Nouvelle dépense -->
<div class="modal fade" id="modalNouvelleDepense" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formNouvelleDepense" enctype="multipart/form-data">
                <div class="modal-header">
                    <h6 class="modal-title">Nouvelle dépense</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" placeholder="Ex: Achat fournitures scolaires" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Catégorie *</label>
                            <input type="text" class="form-control" name="categorie" list="listeCategories" placeholder="Ex: Fournitures" required>
                            <datalist id="listeCategories">
                                <option value="Fournitures scolaires">
                                <option value="Salaires">
                                <option value="Entretien & réparation">
                                <option value="Transport">
                                <option value="Électricité / Eau">
                                <option value="Communication / Internet">
                                <option value="Restauration">
                                <option value="Autre">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de la dépense *</label>
                            <input type="date" class="form-control" name="date_depense" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bénéficiaire / Fournisseur</label>
                        <input type="text" class="form-control" name="beneficiaire">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Payer depuis *</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input source-type" type="radio" name="source_type" value="caisse" checked>
                                <label class="form-check-label">Caisse</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input source-type" type="radio" name="source_type" value="compte_bancaire">
                                <label class="form-check-label">Compte bancaire</label>
                            </div>
                        </div>
                    </div>

                    <div id="blocSourceCaisse" class="mb-3">
                        <label class="form-label">Caisse source *</label>
                        <select class="form-control" name="source_id_caisse" id="selectCaisseSource"></select>
                    </div>

                    <div id="blocSourceCompte" class="mb-3 d-none">
                        <label class="form-label">Compte bancaire source *</label>
                        <select class="form-control" name="source_id_compte" id="selectCompteSource"></select>
                    </div>

                    <div class="mb-3">
                        <div class="p-3 rounded-3 mb-2" id="carteSoldeSource" style="background:#f0f9ff; border:1px solid #bae6fd;">
                            <div class="text-secondary-light small">Solde disponible</div>
                            <div class="fs-5 fw-bold text-primary" id="soldeDisponibleMontant">0.00</div>
                        </div>
                        <label class="form-label">Montant *</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="montant" id="inputMontantDepense" required>
                        <small class="text-danger d-none" id="alerteSoldeInsuffisant">⚠ Montant supérieur au solde disponible</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pièce justificative (facture, reçu...)</label>
                        <input type="file" class="form-control" name="piece" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary-600">Enregistrer la dépense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const slug = @json($slug);
    const baseUrl = `/${slug}/depense`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let depensesCache = [];
    let caissesCache = [];
    let comptesCache = [];

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

    function formatMontant(v) {
        return parseFloat(v).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function badgeStatut(statut) {
        return statut === 'validee'
            ? '<span class="badge bg-success-focus text-success-600">Validée</span>'
            : '<span class="badge bg-danger-focus text-danger-600">Annulée</span>';
    }

    async function apiFetch(url, options = {}) {
        options.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, options.headers || {});
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Une erreur est survenue');
        return data;
    }

    // ---------- Charger les sources (caisses + comptes) ----------
    async function chargerSources() {
        try {
            const data = await apiFetch(`${baseUrl}/sources`);
            console.log('Réponse /sources :', data); // DEBUG - à retirer une fois le souci résolu

            caissesCache = data.caisses || [];
            comptesCache = data.comptes || [];

            console.log('Nombre de caisses reçues :', caissesCache.length);
            console.log('Nombre de comptes reçus :', comptesCache.length);
            console.log('Détail comptes :', comptesCache);

            document.getElementById('selectCaisseSource').innerHTML = caissesCache
                .map(c => `<option value="${c.id}" data-solde="${c.solde}">${c.nom} (${formatMontant(c.solde)})</option>`).join('');

            if (comptesCache.length === 0) {
                document.getElementById('selectCompteSource').innerHTML = '<option value="">Aucun compte bancaire actif trouvé</option>';
            } else {
                document.getElementById('selectCompteSource').innerHTML = comptesCache
                    .map(c => `<option value="${c.id}" data-solde="${c.solde}">${c.nom} - ${c.banque} (${formatMontant(c.solde)})</option>`).join('');
            }

            majSoldeDisponible();
        } catch (e) {
            console.error('Erreur chargerSources :', e);
            toast(e.message, 'error');
        }
    }

    function majSoldeDisponible() {
        const type = document.querySelector('.source-type:checked').value;
        const select = type === 'caisse' ? document.getElementById('selectCaisseSource') : document.getElementById('selectCompteSource');
        const solde = parseFloat(select.selectedOptions[0]?.dataset.solde || 0);
        document.getElementById('soldeDisponibleMontant').textContent = formatMontant(solde);
        verifierMontant();
    }

    function verifierMontant() {
        const type = document.querySelector('.source-type:checked').value;
        const select = type === 'caisse' ? document.getElementById('selectCaisseSource') : document.getElementById('selectCompteSource');
        const solde = parseFloat(select.selectedOptions[0]?.dataset.solde || 0);
        const montant = parseFloat(document.getElementById('inputMontantDepense').value || 0);

        const alerte = document.getElementById('alerteSoldeInsuffisant');
        const btnSubmit = document.querySelector('#formNouvelleDepense button[type="submit"]');

        if (montant > solde && montant > 0) {
            alerte.classList.remove('d-none');
            btnSubmit.disabled = true;
        } else {
            alerte.classList.add('d-none');
            btnSubmit.disabled = false;
        }
    }

    document.getElementById('inputMontantDepense').addEventListener('input', verifierMontant);
    document.querySelectorAll('.source-type').forEach(radio => {
        radio.addEventListener('change', function () {
            const isCaisse = this.value === 'caisse';
            document.getElementById('blocSourceCaisse').classList.toggle('d-none', !isCaisse);
            document.getElementById('blocSourceCompte').classList.toggle('d-none', isCaisse);
            majSoldeDisponible();
        });
    });
    document.getElementById('selectCaisseSource').addEventListener('change', majSoldeDisponible);
    document.getElementById('selectCompteSource').addEventListener('change', majSoldeDisponible);

    // ---------- Charger les dépenses ----------
    async function chargerDepenses() {
        const tbody = document.getElementById('tbodyDepenses');
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4">Chargement...</td></tr>';

        const params = new URLSearchParams();
        const categorie = document.getElementById('filtreCategorie').value;
        const sourceType = document.getElementById('filtreSourceType').value;
        const statut = document.getElementById('filtreStatut').value;
        const dateDebut = document.getElementById('filtreDateDebut').value;
        const dateFin = document.getElementById('filtreDateFin').value;
        if (categorie) params.append('categorie', categorie);
        if (sourceType) params.append('source_type', sourceType);
        if (statut) params.append('statut', statut);
        if (dateDebut) params.append('date_debut', dateDebut);
        if (dateFin) params.append('date_fin', dateFin);

        try {
            const data = await apiFetch(`${baseUrl}/list?${params.toString()}`);
            depensesCache = data.data;
            renderDepenses();
            remplirFiltreCategories();
            majTotaux();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    function remplirFiltreCategories() {
        const select = document.getElementById('filtreCategorie');
        const valeurActuelle = select.value;
        const categories = [...new Set(depensesCache.map(d => d.categorie))];
        select.innerHTML = '<option value="">Toutes</option>' + categories.map(c => `<option value="${c}">${c}</option>`).join('');
        select.value = valeurActuelle;
    }

    function majTotaux() {
        const validees = depensesCache.filter(d => d.statut === 'validee');
        const total = validees.reduce((sum, d) => sum + parseFloat(d.montant), 0);
        document.getElementById('totalDepenses').textContent = formatMontant(total);
        document.getElementById('nbDepenses').textContent = depensesCache.length;
    }

    function renderDepenses() {
        const tbody = document.getElementById('tbodyDepenses');
        if (!depensesCache.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4">Aucune dépense enregistrée</td></tr>';
            return;
        }
        tbody.innerHTML = depensesCache.map(d => `
            <tr class="${d.statut === 'annulee' ? 'opacity-50' : ''}">
                <td>${new Date(d.date_depense).toLocaleDateString('fr-FR')}</td>
                <td class="fw-semibold">${d.libelle}</td>
                <td>${d.categorie}</td>
                <td>${d.beneficiaire ?? '-'}</td>
                <td>${d.source_type === 'caisse' ? '<i class="mdi mdi-cash"></i> ' + (d.caisse_nom ?? '-') : '<i class="mdi mdi-bank"></i> ' + (d.compte_nom ?? '-')}</td>
                <td class="text-danger fw-semibold">- ${formatMontant(d.montant)}</td>
                <td>${badgeStatut(d.statut)}</td>
                <td>${d.piece_justificative ? `<a href="/storage/${d.piece_justificative}" target="_blank" class="btn btn-sm btn-outline-secondary"><iconify-icon icon="mdi:paperclip"></iconify-icon></a>` : '-'}</td>
                <td class="text-center">
                    ${d.statut === 'validee' ? `
                        <button class="btn btn-sm btn-outline-danger" onclick="depenseAnnuler(${d.id}, '${d.libelle.replace(/'/g, "\\'")}')" title="Annuler">
                            <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                        </button>
                    ` : '-'}
                </td>
            </tr>
        `).join('');
    }

    document.getElementById('btnFiltrer').addEventListener('click', chargerDepenses);
    document.getElementById('btnResetFiltre').addEventListener('click', function () {
        document.getElementById('filtreCategorie').value = '';
        document.getElementById('filtreSourceType').value = '';
        document.getElementById('filtreStatut').value = '';
        document.getElementById('filtreDateDebut').value = '';
        document.getElementById('filtreDateFin').value = '';
        chargerDepenses();
    });

    // ---------- Nouvelle dépense ----------
    document.getElementById('formNouvelleDepense').addEventListener('submit', async function (e) {
        e.preventDefault();

        const sourceType = document.querySelector('.source-type:checked').value;
        const sourceId = sourceType === 'caisse'
            ? document.getElementById('selectCaisseSource').value
            : document.getElementById('selectCompteSource').value;

        if (!sourceId) {
            toast('Veuillez sélectionner une source valide.', 'error');
            return;
        }

        const formData = new FormData(this);
        formData.append('source_id', sourceId);

        const confirm = await Swal.fire({
            title: 'Confirmer cette dépense ?',
            text: 'Le montant sera immédiatement retiré de la source sélectionnée.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler'
        });
        if (!confirm.isConfirmed) return;

        loader(true, 'Enregistrement de la dépense...');
        try {
            const data = await apiFetch(`${baseUrl}/store`, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            this.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalNouvelleDepense')).hide();
            chargerDepenses();
            chargerSources();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    // ---------- Annuler une dépense ----------
    window.depenseAnnuler = async function (id, libelle) {
        const { value: motif, isConfirmed } = await Swal.fire({
            title: `Annuler la dépense "${libelle}" ?`,
            text: 'Le montant sera remboursé automatiquement à la source d\'origine.',
            icon: 'warning',
            input: 'text',
            inputPlaceholder: 'Motif de l\'annulation (optionnel)',
            showCancelButton: true,
            confirmButtonText: 'Oui, annuler la dépense',
            cancelButtonText: 'Retour'
        });
        if (!isConfirmed) return;

        loader(true, 'Annulation en cours...');
        try {
            const data = await apiFetch(`${baseUrl}/${id}/annuler`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ motif: motif || null })
            });
            loader(false);
            toast(data.message, 'success');
            chargerDepenses();
            chargerSources();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    };

    // ---------- Export CSV / Excel ----------
    function getExportRows() {
        return depensesCache.map(d => ({
            'Date': new Date(d.date_depense).toLocaleDateString('fr-FR'),
            'Libellé': d.libelle,
            'Catégorie': d.categorie,
            'Bénéficiaire': d.beneficiaire ?? '',
            'Source': d.source_type === 'caisse' ? ('Caisse - ' + (d.caisse_nom ?? '')) : ('Compte - ' + (d.compte_nom ?? '')),
            'Montant': d.montant,
            'Statut': d.statut === 'validee' ? 'Validée' : 'Annulée'
        }));
    }

    document.getElementById('btnExportCsv').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const headers = Object.keys(rows[0]);
        let csv = headers.join(';') + '\n';
        rows.forEach(r => { csv += headers.map(h => `"${String(r[h]).replace(/"/g, '""')}"`).join(';') + '\n'; });
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `depenses_${Date.now()}.csv`;
        link.click();
        toast('Export CSV effectué', 'success');
    });

    document.getElementById('btnExportExcel').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Dépenses');
        XLSX.writeFile(wb, `depenses_${Date.now()}.xlsx`);
        toast('Export Excel effectué', 'success');
    });

    // ---------- Init ----------
    chargerSources();
    chargerDepenses();
})();
</script>

@endsection
