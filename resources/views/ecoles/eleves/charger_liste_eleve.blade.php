@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Chargement de la liste des élèves</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Eleve / Charger-liste-eleve</span>
            </div>
        </div>
    </div>

    {{-- FILTRES + UPLOAD --}}
    <div class="row gy-3">
        <div class="col-lg-12">
            <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                    <h6 class="text-lg fw-semibold mb-0">Sélection classe / section</h6>
                </div>
                <div class="card-body p-20">
                    <div class="row gy-3 mb-24 align-items-end">
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                Année scolaire <span class="text-danger-600">*</span>
                            </label>
                            <select id="anneescolaireSelect" class="form-control form-select">
                                @foreach ($data_anneescolaire as $annee)
                                    <option value="{{ $annee->v_annee_scolaire }}" {{ $annee->v_annee_scolaire == $annee_courante ? 'selected' : '' }}>
                                        {{ $annee->v_annee_scolaire }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau <span class="text-danger-600">*</span></label>
                            <select required id="niveauSelect" class="form-control form-select">
                                <option value="">Séléctionner le niveau</option>
                                @foreach ($niveaux as $niveau)
                                    <option value="{{ $niveau->i_niveauID }}">{{ $niveau->v_niveaux }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                            <select required id="classeSelect" class="form-control form-select">
                                <option value="">Séléctionner un niveau d'abord</option>
                            </select>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section <span class="text-secondary-light">(facultatif)</span></label>
                            <select id="sectionSelect" class="form-control form-select">
                                <option value="">Aucune</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-24">

                    <div class="row gy-3 align-items-end">
                        <div class="col-xxl-4 col-xl-5 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Fichier Excel <span class="text-danger-600">*</span></label>
                            <input required type="file" id="fichierExcel" accept=".xlsx,.xls,.csv" class="form-control">
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type <span class="text-danger-600">*</span></label>
                            <select required id="typeInscriptionSelect" class="form-control form-select">
                                <option value="inscription">Inscription</option>
                                <option value="reinscription">Réinscription</option>
                            </select>
                        </div>

                        <div class="col-xxl-2 col-xl-3 col-sm-6">
                            <button type="button" id="btnValiderFichier" class="btn btn-primary-600 w-100">Valider le fichier</button>
                        </div>
                    </div>

                    <div id="uploadFeedback" class="mt-16"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLEAU EDITABLE --}}
    <div id="previewContainer" class="row gy-3 mt-8" style="display:none;">
        <div class="col-lg-12">
            <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                <div class="card-header border-bottom bg-base py-16 px-24">
                    <h6 class="text-lg fw-semibold mb-0">Aperçu — vérifiez et corrigez avant validation</h6>
                </div>
                <div class="card-body p-20">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0" id="previewTable">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Matricule</th>
                                    <th>Sexe</th>
                                    <th>Contact parent</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="previewTbody"></tbody>
                        </table>
                    </div>
                    <div class="mt-24 text-end">
                        <button type="button" id="btnEnregistrerTout" class="btn btn-success-600">Enregistrer les inscriptions</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<style>
/* ===================== LOADER OVERLAY ===================== */
#globalLoader {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(255, 255, 255, 0.7);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}
#globalLoader .spinner-border {
    width: 3rem;
    height: 3rem;
}
#globalLoader p {
    margin-top: 12px;
    font-weight: 600;
    color: #333;
}

/* ===================== TOASTS ===================== */
#toastContainer {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.custom-toast {
    min-width: 280px;
    max-width: 380px;
    padding: 14px 18px;
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    opacity: 0;
    transform: translateX(40px);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.custom-toast.show { opacity: 1; transform: translateX(0); }
.custom-toast.success { background: #2e7d32; }
.custom-toast.error   { background: #c62828; }
.custom-toast .toast-close { cursor: pointer; font-weight: bold; opacity: 0.8; }
.custom-toast .toast-close:hover { opacity: 1; }
</style>

<div id="globalLoader">
    <div class="spinner-border text-primary" role="status"></div>
    <p id="globalLoaderText">Chargement en cours...</p>
</div>

<div id="toastContainer"></div>

<script>
const SLUG = "{{ $slug ?? request()->route('slug') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

function fetchJson(url, options = {}) {
    return fetch(url, options).then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    });
}

// ===================== LOADER =====================
function showLoader(text = 'Chargement en cours...') {
    document.getElementById('globalLoaderText').textContent = text;
    document.getElementById('globalLoader').style.display = 'flex';
}
function hideLoader() {
    document.getElementById('globalLoader').style.display = 'none';
}

// ===================== TOAST =====================
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    toast.innerHTML = `<span>${message}</span><span class="toast-close">&times;</span>`;
    container.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('show'));

    const remove = () => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    };

    toast.querySelector('.toast-close').addEventListener('click', remove);
    setTimeout(remove, 5000);
}

let previewData = null; // { batch_id, rows }

// ===================== NIVEAU -> CLASSES + SECTIONS =====================
document.getElementById('niveauSelect').addEventListener('change', function () {
    let niveauId = this.value;
    let classeSelect = document.getElementById('classeSelect');
    let sectionSelect = document.getElementById('sectionSelect');

    classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
    sectionSelect.innerHTML = '<option value="">Aucune</option>';

    if (!niveauId) return;

    classeSelect.innerHTML = '<option>Chargement...</option>';

    fetchJson(`/${SLUG}/charger.liste.eleve/classes/${niveauId}`)
        .then(data => {
            classeSelect.innerHTML = '<option value="">Séléctionner une classe</option>';
            data.forEach(c => {
                classeSelect.innerHTML += `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`;
            });
        })
        .catch(() => {
            classeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            showToast('Erreur lors du chargement des classes.', 'error');
        });

    fetchJson(`/${SLUG}/charger.liste.eleve/sections/${niveauId}`)
        .then(data => {
            sectionSelect.innerHTML = '<option value="">Aucune</option>';
            data.forEach(s => {
                sectionSelect.innerHTML += `<option value="${s.id}">${s.v_sections}</option>`;
            });
        })
        .catch(() => {}); // section facultative
});

// ===================== VALIDATION DU FICHIER (UPLOAD -> STAGING) =====================
document.getElementById('btnValiderFichier').addEventListener('click', function () {
    const niveauId = document.getElementById('niveauSelect').value;
    const classeId = document.getElementById('classeSelect').value;
    const sectionId = document.getElementById('sectionSelect').value;
    const annee = document.getElementById('anneescolaireSelect').value;
    const type = document.getElementById('typeInscriptionSelect').value;
    const fichier = document.getElementById('fichierExcel').files[0];

    if (!niveauId || !classeId || !fichier) {
        showToast('Niveau, classe et fichier sont obligatoires.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('niveau_id', niveauId);
    formData.append('classe_id', classeId);
    if (sectionId) formData.append('section_id', sectionId);
    formData.append('annee_scolaire', annee);
    formData.append('type_inscription', type);
    formData.append('fichier_excel', fichier);
    formData.append('_token', CSRF_TOKEN);

    showLoader('Lecture du fichier en cours...');

    fetch(`/${SLUG}/charger.liste.eleve/preview`, {
        method: 'POST',
        body: formData,
    })
    .then(r => r.json().then(data => ({ status: r.status, data })))
    .then(({ status, data }) => {
        hideLoader();
        if (status !== 200) {
            showToast(data.message || 'Erreur lors de la lecture du fichier.', 'error');
            return;
        }
        previewData = data;
        renderPreviewTable(data.rows);
        showToast(`${data.rows.length} élève(s) trouvé(s). Vérifiez le tableau ci-dessous.`, 'success');
        document.getElementById('previewContainer').style.display = '';
    })
    .catch(() => {
        hideLoader();
        showToast('Erreur réseau ou serveur.', 'error');
    });
});

// ===================== AFFICHAGE DU TABLEAU EDITABLE =====================
function renderPreviewTable(rows) {
    const tbody = document.getElementById('previewTbody');
    tbody.innerHTML = '';

    rows.forEach((row) => {
        const tr = document.createElement('tr');
        tr.dataset.importId = row.import_id;
        tr.dataset.eleveIdExistant = row.eleve_id_existant ?? '';

        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" data-field="nom" value="${row.nom ?? ''}"></td>
            <td><input type="text" class="form-control form-control-sm" data-field="prenom" value="${row.prenom ?? ''}"></td>
            <td><input type="text" class="form-control form-control-sm" data-field="matricule" value="${row.matricule ?? ''}"></td>
            <td><input type="text" class="form-control form-control-sm" data-field="sexe" value="${row.sexe ?? ''}"></td>
            <td><input type="text" class="form-control form-control-sm" data-field="contact_parent" value="${row.contact_parent ?? ''}"></td>
            <td>${row.doublon ? '<span class="badge bg-warning-focus text-warning-main">Doublon possible</span>' : '<span class="badge bg-success-focus text-success-main">Nouveau</span>'}</td>
            <td>
                <select class="form-select form-select-sm" data-field="action">
                    <option value="nouveau" ${row.action === 'nouveau' ? 'selected' : ''}>Créer nouvel élève</option>
                    <option value="existant" ${row.action === 'existant' ? 'selected' : ''} ${!row.eleve_id_existant ? 'disabled' : ''}>Réutiliser existant (#${row.eleve_id_existant ?? '—'})</option>
                    <option value="ignorer">Ignorer cette ligne</option>
                </select>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ===================== ENREGISTREMENT FINAL =====================
document.getElementById('btnEnregistrerTout').addEventListener('click', function () {
    if (!previewData) return;

    const tbody = document.getElementById('previewTbody');
    const rows = [];

    tbody.querySelectorAll('tr').forEach(tr => {
        rows.push({
            import_id: parseInt(tr.dataset.importId, 10),
            nom: tr.querySelector('[data-field="nom"]').value.trim(),
            prenom: tr.querySelector('[data-field="prenom"]').value.trim(),
            matricule: tr.querySelector('[data-field="matricule"]').value.trim(),
            sexe: tr.querySelector('[data-field="sexe"]').value.trim(),
            contact_parent: tr.querySelector('[data-field="contact_parent"]').value.trim(),
            action: tr.querySelector('[data-field="action"]').value,
            eleve_id_existant: tr.dataset.eleveIdExistant || null,
        });
    });

    const payload = {
        batch_id: previewData.batch_id,
        rows: rows,
    };

    showLoader('Enregistrement des inscriptions...');

    fetch(`/${SLUG}/charger.liste.eleve/save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        body: JSON.stringify(payload),
    })
    .then(r => r.json().then(data => ({ status: r.status, data })))
    .then(({ status, data }) => {
        hideLoader();
        if (status !== 200) {
            showToast(data.message || 'Erreur lors de l\'enregistrement.', 'error');
            return;
        }
        showToast(data.message, 'success');
        setTimeout(() => window.location.reload(), 1500);
    })
    .catch(() => {
        hideLoader();
        showToast('Erreur réseau ou serveur.', 'error');
    });
});
</script>
@endsection
