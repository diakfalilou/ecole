@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    .prof-photo { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #f1f5f9; }
    .prof-photo-lg { width: 100px; height: 100px; border-radius: 16px; object-fit: cover; background: #f1f5f9; }
    .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #e5e7eb; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-size: 13px; }
    .info-value { font-weight: 600; font-size: 13px; }
</style>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des professeurs</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Pédagogie / Professeurs</span>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-24">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label mb-1">Recherche</label>
                    <input type="text" class="form-control form-control-sm" id="filtreRecherche" placeholder="Nom, prénom, matricule, téléphone...">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Spécialité</label>
                    <select class="form-control form-control-sm" id="filtreSpecialite">
                        <option value="">Toutes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Statut</label>
                    <select class="form-control form-control-sm" id="filtreStatut">
                        <option value="">Tous</option>
                        <option value="active">Actif</option>
                        <option value="suspendu">Suspendu</option>
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
            <h6 class="mb-0">Liste des professeurs</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-success" id="btnExportCsv"><iconify-icon icon="mdi:file-delimited-outline"></iconify-icon> CSV</button>
                <button class="btn btn-sm btn-outline-success" id="btnExportExcel"><iconify-icon icon="mdi:file-excel-outline"></iconify-icon> Excel</button>
                <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="ouvrirModalAjout()">
                    <iconify-icon icon="ic:baseline-plus"></iconify-icon> Nouveau professeur
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="tableProfesseurs">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Matricule</th>
                            <th>Nom & Prénom</th>
                            <th>Spécialité</th>
                            <th>Téléphone</th>
                            <th>Contrat</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyProfesseurs">
                        <tr><td colspan="8" class="text-center py-4">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Ajout / Modification -->
<div class="modal fade" id="modalProfesseur" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formProfesseur" enctype="multipart/form-data">
                <input type="hidden" name="professeur_id" id="professeurId">
                <div class="modal-header">
                    <h6 class="modal-title" id="modalProfesseurTitre">Nouveau professeur</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom *</label>
                            <input type="text" class="form-control" name="prenom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Genre</label>
                            <select class="form-control" name="genre">
                                <option value="">-- Choisir --</option>
                                <option value="Masculin">Masculin</option>
                                <option value="Féminin">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" name="date_naissance">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lieu de naissance</label>
                            <input type="text" class="form-control" name="lieu_naissance">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" class="form-control" name="telephone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" name="adresse" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Spécialité / Matière enseignée</label>
                            <input type="text" class="form-control" name="specialite" list="listeSpecialites" placeholder="Ex: Mathématiques">
                            <datalist id="listeSpecialites">
                                <option value="Mathématiques">
                                <option value="Français">
                                <option value="Anglais">
                                <option value="Physique-Chimie">
                                <option value="SVT">
                                <option value="Histoire-Géographie">
                                <option value="Éducation physique">
                                <option value="Informatique">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diplôme</label>
                            <input type="text" class="form-control" name="diplome" placeholder="Ex: Licence, Master...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date d'embauche</label>
                            <input type="date" class="form-control" name="date_embauche">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type de contrat *</label>
                            <select class="form-control" name="type_contrat" required>
                                <option value="permanent">Permanent</option>
                                <option value="vacataire">Vacataire</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salaire de base</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="salaire_base">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Photo</label>
                            <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary-600" id="btnSubmitProfesseur">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Détail -->
<div class="modal fade" id="modalDetailProfesseur" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Détail du professeur</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img id="detailPhoto" src="" class="prof-photo-lg" alt="">
                    <div>
                        <h5 class="mb-1" id="detailNomComplet"></h5>
                        <span class="badge" id="detailStatutBadge"></span>
                        <div class="text-secondary-light small mt-1" id="detailMatricule"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row"><span class="info-label">Genre</span><span class="info-value" id="detailGenre"></span></div>
                        <div class="info-row"><span class="info-label">Date de naissance</span><span class="info-value" id="detailDateNaissance"></span></div>
                        <div class="info-row"><span class="info-label">Lieu de naissance</span><span class="info-value" id="detailLieuNaissance"></span></div>
                        <div class="info-row"><span class="info-label">Téléphone</span><span class="info-value" id="detailTelephone"></span></div>
                        <div class="info-row"><span class="info-label">Email</span><span class="info-value" id="detailEmail"></span></div>
                        <div class="info-row"><span class="info-label">Adresse</span><span class="info-value" id="detailAdresse"></span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row"><span class="info-label">Spécialité</span><span class="info-value" id="detailSpecialite"></span></div>
                        <div class="info-row"><span class="info-label">Diplôme</span><span class="info-value" id="detailDiplome"></span></div>
                        <div class="info-row"><span class="info-label">Date d'embauche</span><span class="info-value" id="detailDateEmbauche"></span></div>
                        <div class="info-row"><span class="info-label">Type de contrat</span><span class="info-value" id="detailTypeContrat"></span></div>
                        <div class="info-row"><span class="info-label">Salaire de base</span><span class="info-value" id="detailSalaire"></span></div>
                        <div class="info-row" id="ligneMotifSuspension" style="display:none;"><span class="info-label">Motif suspension</span><span class="info-value text-danger" id="detailMotifSuspension"></span></div>
                    </div>
                </div>
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
    const baseUrl = `/${slug}/professeur`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const assetBaseUrl = @json(asset('/'));

    let professeursCache = [];

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

    function photoUrl(photo, nom, prenom) {
        return photo ? assetBaseUrl + 'storage/' + photo : `https://ui-avatars.com/api/?name=${encodeURIComponent(nom + ' ' + prenom)}&background=e2e8f0&color=475569`;
    }

    function badgeStatut(statut) {
        return statut === 'active'
            ? '<span class="badge bg-success-focus text-success-600">Actif</span>'
            : '<span class="badge bg-danger-focus text-danger-600">Suspendu</span>';
    }

    function formatMontant(v) {
        if (!v) return '-';
        return parseFloat(v).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(d) {
        return d ? new Date(d).toLocaleDateString('fr-FR') : '-';
    }

    async function apiFetch(url, options = {}) {
        options.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, options.headers || {});
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Une erreur est survenue');
        return data;
    }

    // ---------- Chargement liste ----------
    async function chargerProfesseurs() {
        const tbody = document.getElementById('tbodyProfesseurs');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Chargement...</td></tr>';

        const params = new URLSearchParams();
        const recherche = document.getElementById('filtreRecherche').value;
        const specialite = document.getElementById('filtreSpecialite').value;
        const statut = document.getElementById('filtreStatut').value;
        if (recherche) params.append('recherche', recherche);
        if (specialite) params.append('specialite', specialite);
        if (statut) params.append('statut', statut);

        try {
            const data = await apiFetch(`${baseUrl}/list?${params.toString()}`);
            professeursCache = data.data;
            renderProfesseurs();
            remplirFiltreSpecialites();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    function remplirFiltreSpecialites() {
        const select = document.getElementById('filtreSpecialite');
        const valeurActuelle = select.value;
        const specialites = [...new Set(professeursCache.map(p => p.specialite).filter(Boolean))];
        select.innerHTML = '<option value="">Toutes</option>' + specialites.map(s => `<option value="${s}">${s}</option>`).join('');
        select.value = valeurActuelle;
    }

    function renderProfesseurs() {
        const tbody = document.getElementById('tbodyProfesseurs');
        if (!professeursCache.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Aucun professeur enregistré</td></tr>';
            return;
        }
        tbody.innerHTML = professeursCache.map(p => `
            <tr>
                <td><img src="${photoUrl(p.photo, p.nom, p.prenom)}" class="prof-photo" alt=""></td>
                <td>${p.matricule ?? '-'}</td>
                <td class="fw-semibold">${p.nom} ${p.prenom}</td>
                <td>${p.specialite ?? '-'}</td>
                <td>${p.telephone ?? '-'}</td>
                <td>${p.type_contrat === 'permanent' ? 'Permanent' : 'Vacataire'}</td>
                <td>${badgeStatut(p.statut)}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-outline-primary" onclick="voirDetail(${p.id})" title="Détail"><iconify-icon icon="mdi:eye-outline"></iconify-icon></button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="ouvrirModalModification(${p.id})" title="Modifier"><iconify-icon icon="mdi:pencil-outline"></iconify-icon></button>
                        <button class="btn btn-sm btn-outline-${p.statut === 'active' ? 'danger' : 'success'}" onclick="toggleSuspension(${p.id}, '${p.nom} ${p.prenom}', '${p.statut}')" title="${p.statut === 'active' ? 'Suspendre' : 'Réactiver'}">
                            <iconify-icon icon="${p.statut === 'active' ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'}"></iconify-icon>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    document.getElementById('btnFiltrer').addEventListener('click', chargerProfesseurs);
    document.getElementById('filtreRecherche').addEventListener('keyup', function (e) {
        if (e.key === 'Enter') chargerProfesseurs();
    });
    document.getElementById('btnResetFiltre').addEventListener('click', function () {
        document.getElementById('filtreRecherche').value = '';
        document.getElementById('filtreSpecialite').value = '';
        document.getElementById('filtreStatut').value = '';
        chargerProfesseurs();
    });

    // ---------- Ouvrir modal Ajout ----------
    window.ouvrirModalAjout = function () {
        document.getElementById('formProfesseur').reset();
        document.getElementById('professeurId').value = '';
        document.getElementById('modalProfesseurTitre').textContent = 'Nouveau professeur';
        new bootstrap.Modal(document.getElementById('modalProfesseur')).show();
    };

    // ---------- Ouvrir modal Modification ----------
    window.ouvrirModalModification = function (id) {
        const p = professeursCache.find(x => x.id === id);
        if (!p) return;

        document.getElementById('formProfesseur').reset();
        document.getElementById('professeurId').value = p.id;
        document.getElementById('modalProfesseurTitre').textContent = 'Modifier : ' + p.nom + ' ' + p.prenom;

        const form = document.getElementById('formProfesseur');
        form.nom.value = p.nom ?? '';
        form.prenom.value = p.prenom ?? '';
        form.genre.value = p.genre ?? '';
        form.date_naissance.value = p.date_naissance ?? '';
        form.lieu_naissance.value = p.lieu_naissance ?? '';
        form.telephone.value = p.telephone ?? '';
        form.email.value = p.email ?? '';
        form.adresse.value = p.adresse ?? '';
        form.specialite.value = p.specialite ?? '';
        form.diplome.value = p.diplome ?? '';
        form.date_embauche.value = p.date_embauche ?? '';
        form.type_contrat.value = p.type_contrat ?? 'permanent';
        form.salaire_base.value = p.salaire_base ?? '';

        new bootstrap.Modal(document.getElementById('modalProfesseur')).show();
    };

    // ---------- Soumission formulaire (ajout ou modification) ----------
    document.getElementById('formProfesseur').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('professeurId').value;
        const estModification = !!id;

        const confirm = await Swal.fire({
            title: estModification ? 'Confirmer la modification ?' : 'Confirmer l\'ajout du professeur ?',
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
            bootstrap.Modal.getInstance(document.getElementById('modalProfesseur')).hide();
            chargerProfesseurs();
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
            const p = data.data;

            document.getElementById('detailPhoto').src = photoUrl(p.photo, p.nom, p.prenom);
            document.getElementById('detailNomComplet').textContent = p.nom + ' ' + p.prenom;
            document.getElementById('detailStatutBadge').outerHTML = badgeStatut(p.statut).replace('<span', '<span id="detailStatutBadge"');
            document.getElementById('detailMatricule').textContent = 'Matricule : ' + (p.matricule ?? '-');
            document.getElementById('detailGenre').textContent = p.genre ?? '-';
            document.getElementById('detailDateNaissance').textContent = formatDate(p.date_naissance);
            document.getElementById('detailLieuNaissance').textContent = p.lieu_naissance ?? '-';
            document.getElementById('detailTelephone').textContent = p.telephone ?? '-';
            document.getElementById('detailEmail').textContent = p.email ?? '-';
            document.getElementById('detailAdresse').textContent = p.adresse ?? '-';
            document.getElementById('detailSpecialite').textContent = p.specialite ?? '-';
            document.getElementById('detailDiplome').textContent = p.diplome ?? '-';
            document.getElementById('detailDateEmbauche').textContent = formatDate(p.date_embauche);
            document.getElementById('detailTypeContrat').textContent = p.type_contrat === 'permanent' ? 'Permanent' : 'Vacataire';
            document.getElementById('detailSalaire').textContent = formatMontant(p.salaire_base);

            const ligneMotif = document.getElementById('ligneMotifSuspension');
            if (p.statut === 'suspendu' && p.motif_suspension) {
                ligneMotif.style.display = 'flex';
                document.getElementById('detailMotifSuspension').textContent = p.motif_suspension;
            } else {
                ligneMotif.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('modalDetailProfesseur')).show();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    };

    // ---------- Suspendre / réactiver ----------
    window.toggleSuspension = async function (id, nomComplet, statutActuel) {
        if (statutActuel === 'active') {
            const { value: motif, isConfirmed } = await Swal.fire({
                title: `Suspendre ${nomComplet} ?`,
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
                title: `Réactiver ${nomComplet} ?`,
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
            chargerProfesseurs();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    }

    // ---------- Export CSV / Excel ----------
    function getExportRows() {
        return professeursCache.map(p => ({
            'Matricule': p.matricule ?? '',
            'Nom': p.nom,
            'Prénom': p.prenom,
            'Genre': p.genre ?? '',
            'Spécialité': p.specialite ?? '',
            'Téléphone': p.telephone ?? '',
            'Email': p.email ?? '',
            'Contrat': p.type_contrat === 'permanent' ? 'Permanent' : 'Vacataire',
            'Date embauche': formatDate(p.date_embauche),
            'Statut': p.statut === 'active' ? 'Actif' : 'Suspendu'
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
        link.download = `professeurs_${Date.now()}.csv`;
        link.click();
        toast('Export CSV effectué', 'success');
    });

    document.getElementById('btnExportExcel').addEventListener('click', function () {
        const rows = getExportRows();
        if (!rows.length) { toast('Aucune donnée à exporter', 'error'); return; }
        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Professeurs');
        XLSX.writeFile(wb, `professeurs_${Date.now()}.xlsx`);
        toast('Export Excel effectué', 'success');
    });

    // ---------- Init ----------
    chargerProfesseurs();
})();
</script>

@endsection
