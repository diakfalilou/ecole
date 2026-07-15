@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>

<style>
    .matiere-ligne { display: flex; align-items: center; gap: 14px; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 10px; transition: all .15s ease; background: #fff; }
    .matiere-ligne.cochee { border-color: #93c5fd; background: #f0f7ff; }
    .matiere-ligne .form-check-input { width: 20px; height: 20px; cursor: pointer; }
    .matiere-nom { font-weight: 600; min-width: 220px; }
    .matiere-code { color: #64748b; font-size: 12px; }
    .coef-input { width: 90px; }
    .vh-input { width: 100px; }
    .recap-badge { border-radius: 10px; padding: 10px 16px; text-align: center; flex: 1; }
</style>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Affectation des matières aux classes</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Pédagogie / Affectation matières</span>
            </div>
        </div>
    </div>

    <div class="card mb-24">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label mb-1">Choisir une classe *</label>
                    <select class="form-control" id="selectClasse"></select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary-600 w-100" id="btnChargerMatieres">
                        <iconify-icon icon="mdi:book-cog-outline"></iconify-icon> Charger les matières
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="zoneAffectation" class="d-none">
        <div class="d-flex gap-3 mb-24 flex-wrap">
            <div class="recap-badge" style="background:#eff6ff;">
                <div class="fs-5 fw-bold text-primary" id="cntMatieresCochees">0</div>
                <div class="text-secondary-light small">Matières affectées</div>
            </div>
            <div class="recap-badge" style="background:#f0fdf4;">
                <div class="fs-5 fw-bold text-success" id="totalCoefficient">0</div>
                <div class="text-secondary-light small">Total coefficients</div>
            </div>
            <div class="recap-badge" style="background:#f8fafc;">
                <div class="fs-5 fw-bold text-dark" id="totalVolumeHoraire">0</div>
                <div class="text-secondary-light small">Volume horaire total (h/sem)</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="mb-0">Matières — <span id="classeNomAffiche" class="text-primary-600"></span></h6>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" id="rechercheMatiere" placeholder="Rechercher une matière..." style="width:220px;">
                    <button class="btn btn-primary-600" id="btnEnregistrerAffectation">
                        <iconify-icon icon="mdi:content-save-check-outline"></iconify-icon> Enregistrer
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex fw-semibold text-secondary-light small mb-2 px-2" style="padding-left:44px;">
                    <div class="matiere-nom">Matière</div>
                    <div class="coef-input">Coefficient</div>
                    <div class="vh-input ms-2">Vol. horaire</div>
                </div>
                <div id="listeMatieres" class="d-flex flex-column gap-2"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const slug = @json($slug);
    const baseUrl = `/${slug}/classe-matiere`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let classesCache = [];
    let matieresCache = [];
    let classeSelectionnee = null;

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

    // ---------- Charger les classes ----------
    async function chargerClasses() {
        try {
            const data = await apiFetch(`${baseUrl}/classes`);
            classesCache = data.data;
            document.getElementById('selectClasse').innerHTML = '<option value="">-- Choisir une classe --</option>' +
                classesCache.map(c => `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`).join('');
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    // ---------- Charger les matières pour la classe choisie ----------
    document.getElementById('btnChargerMatieres').addEventListener('click', async function () {
        const select = document.getElementById('selectClasse');
        const classeId = select.value;

        if (!classeId) {
            toast('Veuillez sélectionner une classe.', 'error');
            return;
        }

        classeSelectionnee = classeId;

        loader(true, 'Chargement des matières...');
        try {
            const data = await apiFetch(`${baseUrl}/matieres?classe_id=${classeId}`);
            matieresCache = data.data;
            loader(false);

            document.getElementById('classeNomAffiche').textContent = select.selectedOptions[0].textContent;
            document.getElementById('zoneAffectation').classList.remove('d-none');
            renderMatieres();
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    function renderMatieres(filtre = '') {
        const container = document.getElementById('listeMatieres');
        let liste = matieresCache;

        if (filtre) {
            const f = filtre.toLowerCase();
            liste = liste.filter(m => m.nom.toLowerCase().includes(f) || (m.code || '').toLowerCase().includes(f));
        }

        if (!liste.length) {
            container.innerHTML = '<div class="text-center py-4 text-secondary-light">Aucune matière trouvée</div>';
            updateRecap();
            return;
        }

        container.innerHTML = liste.map(m => {
            const estAffectee = !!m.affectation_id;
            const coefficient = m.coefficient_affecte ?? m.coefficient_defaut;
            const volumeHoraire = m.volume_horaire_affecte ?? m.volume_horaire_defaut ?? '';

            return `
                <div class="matiere-ligne ${estAffectee ? 'cochee' : ''}" data-matiere-id="${m.matiere_id}">
                    <input type="checkbox" class="form-check-input chk-matiere" ${estAffectee ? 'checked' : ''}>
                    <div class="matiere-nom">
                        ${m.nom}
                        <div class="matiere-code">${m.code ?? ''}</div>
                    </div>
                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm coef-input input-coefficient" value="${coefficient}" ${estAffectee ? '' : 'disabled'}>
                    <input type="number" min="0" class="form-control form-control-sm vh-input ms-2 input-volume-horaire" value="${volumeHoraire}" placeholder="h/sem" ${estAffectee ? '' : 'disabled'}>
                </div>
            `;
        }).join('');

        // Écouteurs checkbox
        container.querySelectorAll('.matiere-ligne').forEach(ligne => {
            const checkbox = ligne.querySelector('.chk-matiere');
            const inputCoef = ligne.querySelector('.input-coefficient');
            const inputVh = ligne.querySelector('.input-volume-horaire');

            checkbox.addEventListener('change', function () {
                ligne.classList.toggle('cochee', this.checked);
                inputCoef.disabled = !this.checked;
                inputVh.disabled = !this.checked;
                updateRecap();
            });

            inputCoef.addEventListener('input', updateRecap);
        });

        updateRecap();
    }

    function updateRecap() {
        const lignes = document.querySelectorAll('#listeMatieres .matiere-ligne');
        let nb = 0, totalCoef = 0, totalVh = 0;

        lignes.forEach(ligne => {
            const checkbox = ligne.querySelector('.chk-matiere');
            if (checkbox.checked) {
                nb++;
                totalCoef += parseFloat(ligne.querySelector('.input-coefficient').value || 0);
                totalVh += parseInt(ligne.querySelector('.input-volume-horaire').value || 0);
            }
        });

        document.getElementById('cntMatieresCochees').textContent = nb;
        document.getElementById('totalCoefficient').textContent = totalCoef.toFixed(2);
        document.getElementById('totalVolumeHoraire').textContent = totalVh;
    }

    document.getElementById('rechercheMatiere').addEventListener('input', function () {
        renderMatieres(this.value);
    });

    // ---------- Enregistrer les affectations ----------
    document.getElementById('btnEnregistrerAffectation').addEventListener('click', async function () {
        const lignes = document.querySelectorAll('#listeMatieres .matiere-ligne');
        const affectations = [];

        lignes.forEach(ligne => {
            const checkbox = ligne.querySelector('.chk-matiere');
            if (checkbox.checked) {
                affectations.push({
                    matiere_id: ligne.dataset.matiereId,
                    coefficient: parseFloat(ligne.querySelector('.input-coefficient').value || 1),
                    volume_horaire: ligne.querySelector('.input-volume-horaire').value || null,
                });
            }
        });

        if (!affectations.length) {
            const confirmVide = await Swal.fire({
                title: 'Aucune matière cochée',
                text: 'Cela retirera toutes les matières actuellement affectées à cette classe. Continuer ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, tout retirer',
                cancelButtonText: 'Annuler'
            });
            if (!confirmVide.isConfirmed) return;
        } else {
            const confirm = await Swal.fire({
                title: 'Confirmer l\'affectation ?',
                html: `<b>${affectations.length}</b> matière(s) seront affectées à cette classe avec leurs coefficients.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, enregistrer',
                cancelButtonText: 'Annuler'
            });
            if (!confirm.isConfirmed) return;
        }

        loader(true, 'Enregistrement de l\'affectation...');
        try {
            const data = await apiFetch(`${baseUrl}/save`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    classe_id: classeSelectionnee,
                    affectations: affectations
                })
            });
            loader(false);
            toast(data.message, 'success');
        } catch (e) {
            loader(false);
            toast(e.message, 'error');
        }
    });

    // ---------- Init ----------
    chargerClasses();
})();
</script>

@endsection
