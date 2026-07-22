@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #e5e7eb; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-size: 13px; }
    .info-value { font-weight: 600; font-size: 13px; }
    .matiere-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #eff6ff; color: #2563eb; font-size: 18px; }
</style>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des matières</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Pédagogie / Matières</span>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-24">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label mb-1">Recherche</label>
                    <input type="text" class="form-control form-control-sm" id="filtreRecherche" placeholder="Nom ou code de la matière...">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Niveau</label>
                    <select class="form-control form-control-sm" id="filtreNiveau">
                        <option value="">Tous les niveaux</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Statut</label>
                    <select class="form-control form-control-sm" id="filtreStatut">
                        <option value="">Tous</option>
                        <option value="active">Active</option>
                        <option value="suspendue">Suspendue</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-sm btn-primary-600 w-100" id="btnFiltrer">Filtrer</button>
                    <button class="btn btn-sm btn-outline-secondary" id="btnResetFiltre"><iconify-icon icon="mdi:refresh"></iconify-icon></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Liste des matières</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-success" id="btnExportCsv"><iconify-icon icon="mdi:file-delimited-outline"></iconify-icon> CSV</button>
                <button class="btn btn-sm btn-outline-success" id="btnExportExcel"><iconify-icon icon="mdi:file-excel-outline"></iconify-icon> Excel</button>
                <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="ouvrirModalAjout()">
                    <iconify-icon icon="ic:baseline-plus"></iconify-icon> Nouvelle matière
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="tableMatieres">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyMatieres">
                        <tr><td colspan="6" class="text-center py-4">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Ajout / Modification -->
<!-- MODAL: Ajout / Modification -->
<div class="modal fade" id="modalMatiere" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formMatiere">
                <input type="hidden" name="matiere_id" id="matiereId">
                <div class="modal-header">
                    <h6 class="modal-title" id="modalMatiereTitre">Nouvelle matière</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nom de la matière *</label>
                            <input type="text" class="form-control" name="nom" placeholder="Ex: Mathématiques" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" placeholder="Ex: MATH">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
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
<!-- MODAL: Détail -->
<div class="modal fade" id="modalDetailMatiere" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Détail de la matière</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="matiere-icon"><iconify-icon icon="mdi:book-open-page-variant-outline"></iconify-icon></div>
                    <div>
                        <h5 class="mb-1" id="detailNom"></h5>
                        <span class="badge" id="detailStatutBadge"></span>
                    </div>
                </div>
                <div class="info-row"><span class="info-label">Code</span><span class="info-value" id="detailCode"></span></div>
                <div class="info-row"><span class="info-label">Description</span><span class="info-value" id="detailDescription"></span></div>
                <div class="info-row" id="ligneMotifSuspension" style="display:none;"><span class="info-label">Motif suspension</span><span class="info-value text-danger" id="detailMotifSuspension"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const slug = @json($slug);
    const baseUrl = `/${slug}/matiere`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let matieresCache = [];
    let niveauxCache = [];

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

    function badgeStatut(statut) {
        return statut === 'active'
            ? '<span class="badge bg-success-focus text-success-600">Active</span>'
            : '<span class="badge bg-danger-focus text-danger-600">Suspendue</span>';
    }

    async function apiFetch(url, options = {}) {
        options.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, options.headers || {});
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Une erreur est survenue');
        return data;
    }

    // ---------- Chargement des niveaux ----------


    // ---------- Chargement liste ----------
    async function chargerMatieres() {
        const tbody = document.getElementById('tbodyMatieres');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Chargement...</td></tr>';

        const params = new URLSearchParams();
        const recherche = document.getElementById('filtreRecherche').value;
        const niveauId = document.getElementById('filtreNiveau').value;
        const statut = document.getElementById('filtreStatut').value;
        if (recherche) params.append('recherche', recherche);
        if (niveauId) params.append('niveau_id', niveauId);
        if (statut) params.append('statut', statut);

        try {
            const data = await apiFetch(`${baseUrl}/list?${params.toString()}`);
            matieresCache = data.data;
            renderMatieres();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

   function renderMatieres() {
    const tbody = document.getElementById('tbodyMatieres');
    if (!matieresCache.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Aucune matière enregistrée</td></tr>';
        return;
    }
    tbody.innerHTML = matieresCache.map(m => `
        <tr>
            <td><div class="matiere-icon"><iconify-icon icon="mdi:book-open-page-variant-outline"></iconify-icon></div></td>
            <td>${m.code ?? '-'}</td>
            <td class="fw-semibold">${m.nom}</td>
            <td>${m.description ?? '-'}</td>
            <td>${badgeStatut(m.statut)}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    <button class="btn btn-sm btn-outline-primary" onclick="voirDetail(${m.id})" title="Détail"><iconify-icon icon="mdi:eye-outline"></iconify-icon></button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="ouvrirModalModification(${m.id})" title="Modifier"><iconify-icon icon="mdi:pencil-outline"></iconify-icon></button>
                    <button class="btn btn-sm btn-outline-${m.statut === 'active' ? 'danger' : 'success'}" onclick="toggleSuspension(${m.id}, '${m.nom.replace(/'/g, "\\'")}', '${m.statut}')" title="${m.statut === 'active' ? 'Suspendre' : 'Réactiver'}">
                        <iconify-icon icon="${m.statut === 'active' ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'}"></iconify-icon>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

    document.getElementById('btnFiltrer').addEventListener('click', chargerMatieres);
    document.getElementById('filtreRecherche').addEventListener('keyup', function (e) {
        if (e.key === 'Enter') chargerMatieres();
    });
    document.getElementById('btnResetFiltre').addEventListener('click', function () {
        document.getElementById('filtreRecherche').value = '';
        document.getElementById('filtreNiveau').value = '';
        document.getElementById('filtreStatut').value = '';
        chargerMatieres();
    });

    // ---------- Ouvrir modal Ajout ----------
    window.ouvrirModalAjout = function () {
        document.getElementById('formMatiere').reset();
        document.getElementById('matiereId').value = '';
        document.getElementById('modalMatiereTitre').textContent = 'Nouvelle matière';
        new bootstrap.Modal(document.getElementById('modalMatiere')).show();
    };

    // ---------- Ouvrir modal Modification ----------
    window.ouvrirModalModification = function (id) {
    const m = matieresCache.find(x => x.id === id);
    if (!m) return;

    document.getElementById('formMatiere').reset();
    document.getElementById('matiereId').value = m.id;
    document.getElementById('modalMatiereTitre').textContent = 'Modifier : ' + m.nom;

    const form = document.getElementById('formMatiere');
    form.nom.value = m.nom ?? '';
    form.code.value = m.code ?? '';
    form.description.value = m.description ?? '';

    new bootstrap.Modal(document.getElementById('modalMatiere')).show();
};

    // ---------- Soumission formulaire ----------
    document.getElementById('formMatiere').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('matiereId').value;
        const estModification = !!id;

        const confirm = await Swal.fire({
            title: estModification ? 'Confirmer la modification ?' : 'Confirmer l\'ajout de la matière ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler'
        });
        if (!confirm.isConfirmed) return;

        const formData = new FormData(this);
        const url = estModification ? `${baseUrl}/${id}/update` : `${baseUrl}/store`;

        loader(true, 'Enregistrement en cours...');
        try {
            const data = await apiFetch(url, { method: 'POST', body: formData });
            loader(false);
            toast(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalMatiere')).hide();
            chargerMatieres();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    // ---------- Détail ----------
    window.voirDetail = async function (id) {
        loader(true, 'Chargement...');
        try {
            const data = await apiFetch(`${baseUrl}/${id}/detail`);
            loader(false);
            const m = data.data;

            document.getElementById('detailNom').textContent = m.nom;
            document.getElementById('detailStatutBadge').outerHTML = badgeStatut(m.statut).replace('<span', '<span id="detailStatutBadge"');
            document.getElementById('detailCode').textContent = m.code ?? '-';
            document.getElementById('detailDescription').textContent = m.description ?? '-';

            const ligneMotif = document.getElementById('ligneMotifSuspension');
            if (m.statut === 'suspendue' && m.motif_suspension) {
                ligneMotif.style.display = 'flex';
                document.getElementById('detailMotifSuspension').textContent = m.motif_suspension;
            } else {
                ligneMotif.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('modalDetailMatiere')).show();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    };

    // ---------- Suspendre / réactiver ----------
    window.toggleSuspension = async function (id, nom, statutActuel) {
        if (statutActuel === 'active') {
            const { value: motif, isConfirmed } = await Swal.fire({
                title: `Suspendre la matière "${nom}" ?`,
                input: 'text',
                inputPlaceholder: 'Motif de la suspension (optionnel)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, suspendre',
                cancelButtonText: 'Annuler'
            });
            if (!isConfirmed) return;

            await executerSuspension(id, motif);
        } else {
            const confirm = await Swal.fire({
                title: `Réactiver la matière "${nom}" ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, réactiver',
                cancelButtonText: 'Annuler'
            });
            if (!confirm.isConfirmed) return;

            await executerSuspension(id, null);
        }
    };

    async function executerSuspension(id, motif) {
        loader(true);
        try {
            const data = await apiFetch(`${baseUrl}/${id}/suspendre`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ motif: motif || null })
            });
            loader(false);
            toast(data.message, 'success');
            chargerMatieres();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    }

    // ---------- Export CSV / Excel ----------
    function getExportRows() {
        return matieresCache.map(m => ({
            'Code': m.code ?? '',
            'Nom': m.nom,
            'Niveau': m.niveau_nom ?? 'Tous niveaux',
            'Coefficient': m.coefficient,
            'Volume horaire': m.volume_horaire ?? '',
            'Statut': m.statut === 'active' ? 'Active' : 'Suspendue'
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
        link.download = `matieres_${Date.now()}.csv`;
        link.click();
        toast('Export CSV effectué', 'success');
    });

    document.getElementById('btnExportExcel').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Matières');
        XLSX.writeFile(wb, `matieres_${Date.now()}.xlsx`);
        toast('Export Excel effectué', 'success');
    });

    // ---------- Init ----------

    chargerMatieres();
})();
</script>

@endsection
