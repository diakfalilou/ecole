@extends('ecoles.layout.app')
@section('containte')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>

<style>
    .grille-wrapper { overflow-x: auto; }
    table.grille-emploi { border-collapse: collapse; width: 100%; min-width: 1100px; }
    table.grille-emploi th, table.grille-emploi td { border: 1px solid #e5e7eb; text-align: center; padding: 0; }
    table.grille-emploi th { background: #f8fafc; font-size: 12px; padding: 8px 4px; white-space: nowrap; }
    table.grille-emploi td.jour-label { background: #f8fafc; font-weight: 600; font-size: 13px; padding: 8px; white-space: nowrap; }

    .creneau-cell { width: 100%; height: 46px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 10px; line-height: 1.1; transition: background .15s; }
    .creneau-cell.libre:hover { background: #f1f5f9; }
    .creneau-cell.mine { background: #22c55e; color: #fff; font-weight: 600; cursor: pointer; }
    .creneau-cell.mine:hover { background: #16a34a; }
    .creneau-cell.locked-prof { background: #cbd5e1; color: #334155; cursor: not-allowed; }
    .creneau-cell.locked-classe { background: #fdba74; color: #7c2d12; cursor: not-allowed; }

    .legende { display: flex; gap: 16px; flex-wrap: wrap; font-size: 12px; margin-bottom: 14px; }
    .legende-item { display: flex; align-items: center; gap: 6px; }
    .legende-swatch { width: 16px; height: 16px; border-radius: 4px; display: inline-block; }

    fieldset[disabled] { opacity: .6; }
</style>

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Emploi du temps des professeurs</h1>
            <div>
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
                <span class="text-secondary-light"> / Pédagogie / Emploi du temps</span>
            </div>
        </div>
    </div>

    <!-- ÉTAPE 1 : Sélection -->
    <div class="card mb-24">
        <div class="card-header">
            <h6 class="mb-0">1. Sélection</h6>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1">Année scolaire *</label>
                    <select class="form-control form-control-sm" id="selAnnee">
                        <option value="">-- Choisir --</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Professeur *</label>
                    <select class="form-control form-control-sm" id="selProf">
                        <option value="">-- Choisir --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Niveau *</label>
                    <select class="form-control form-control-sm" id="selNiveau">
                        <option value="">-- Choisir --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Classe *</label>
                    <select class="form-control form-control-sm" id="selClasse" disabled>
                        <option value="">-- Choisir un niveau --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Matière *</label>
                    <select class="form-control form-control-sm" id="selMatiere" disabled>
                        <option value="">-- Choisir une classe --</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary-600" id="btnValider">
                    <iconify-icon icon="mdi:check-circle-outline"></iconify-icon> Valider
                </button>
            </div>
        </div>
    </div>

    <!-- ÉTAPE 2 : Grille -->
    <div class="card" id="cardGrille" style="display:none;">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h6 class="mb-0">2. Grille horaire — <span id="recapSelection" class="text-primary-light"></span></h6>
            <button class="btn btn-sm btn-outline-primary" id="btnImprimer">
                <iconify-icon icon="mdi:printer-outline"></iconify-icon> Emplois du temps
            </button>
        </div>
        <div class="card-body">
            <div class="legende">
                <div class="legende-item"><span class="legende-swatch" style="background:#fff;border:1px solid #cbd5e1;"></span> Libre</div>
                <div class="legende-item"><span class="legende-swatch" style="background:#22c55e;"></span> Affecté (classe/matière sélectionnée)</div>
                <div class="legende-item"><span class="legende-swatch" style="background:#cbd5e1;"></span> Professeur déjà occupé ailleurs</div>
                <div class="legende-item"><span class="legende-swatch" style="background:#fdba74;"></span> Classe déjà prise par un autre professeur</div>
            </div>
            <div class="grille-wrapper">
                <table class="grille-emploi">
                    <thead>
                        <tr>
                            <th style="width:110px;">Jour</th>
                            <!-- Colonnes horaires générées en JS -->
                        </tr>
                    </thead>
                    <tbody id="corpsGrille"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const slug = @json($slug);
    const baseUrl = `/${slug}/emploi-du-temps`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const JOURS = [
        { key: 'lundi', label: 'Lundi' },
        { key: 'mardi', label: 'Mardi' },
        { key: 'mercredi', label: 'Mercredi' },
        { key: 'jeudi', label: 'Jeudi' },
        { key: 'vendredi', label: 'Vendredi' },
        { key: 'samedi', label: 'Samedi' },
    ];

    // Génère les créneaux horaires de 8h à 18h (1h par créneau)
    const HEURES = [];
    for (let h = 8; h < 18; h++) {
        HEURES.push({
            debut: String(h).padStart(2, '0') + ':00',
            fin: String(h + 1).padStart(2, '0') + ':00',
        });
    }

    let selection = { annee: null, professeurId: null, classeId: null, matiereId: null, classeNom: '', matiereNom: '' };
    let mapCreneaux = {}; // clé "jour_heureDebut" -> { type, label }
    let planningProfComplet = []; // tout le planning du prof (toutes classes/matières), pour l'impression

    function toast(message, type = 'success') {
        Toastify({
            text: message, duration: 3500, gravity: 'top', position: 'right',
            style: { background: type === 'success' ? '#2e7d32' : (type === 'error' ? '#c62828' : '#1565c0') }
        }).showToast();
    }

    async function apiFetch(url, options = {}) {
        options.headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }, options.headers || {});
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Une erreur est survenue');
        return data;
    }

    // ---------- Chargement des selects ----------
    async function chargerAnnees() {
        const data = await apiFetch(`${baseUrl}/annees`);
        const sel = document.getElementById('selAnnee');
        sel.innerHTML = '<option value="">-- Choisir --</option>' +
            data.data.map(a => `<option value="${a}">${a}</option>`).join('');
    }

    async function chargerProfesseurs() {
        const data = await apiFetch(`${baseUrl}/professeurs`);
        const sel = document.getElementById('selProf');
        sel.innerHTML = '<option value="">-- Choisir --</option>' +
            data.data.map(p => `<option value="${p.id}">${p.nom} ${p.prenom}${p.matricule ? ' (' + p.matricule + ')' : ''}</option>`).join('');
    }

    async function chargerNiveaux() {
        const data = await apiFetch(`${baseUrl}/niveaux`);
        const sel = document.getElementById('selNiveau');
        sel.innerHTML = '<option value="">-- Choisir --</option>' +
            data.data.map(n => `<option value="${n.i_niveauID}">${n.v_niveaux}</option>`).join('');
    }

    document.getElementById('selNiveau').addEventListener('change', async function () {
        const selClasse = document.getElementById('selClasse');
        const selMatiere = document.getElementById('selMatiere');
        selMatiere.disabled = true;
        selMatiere.innerHTML = '<option value="">-- Choisir une classe --</option>';

        if (!this.value) {
            selClasse.disabled = true;
            selClasse.innerHTML = '<option value="">-- Choisir un niveau --</option>';
            return;
        }
        selClasse.innerHTML = '<option value="">Chargement...</option>';
        try {
            const data = await apiFetch(`${baseUrl}/classes/${this.value}`);
            selClasse.disabled = false;
            selClasse.innerHTML = '<option value="">-- Choisir --</option>' +
                data.data.map(c => `<option value="${c.i_classe_id}">${c.v_nom_classe}</option>`).join('');
        } catch (e) {
            toast(e.message, 'error');
        }
    });

    document.getElementById('selClasse').addEventListener('change', async function () {
        const selMatiere = document.getElementById('selMatiere');
        if (!this.value) {
            selMatiere.disabled = true;
            selMatiere.innerHTML = '<option value="">-- Choisir une classe --</option>';
            return;
        }
        selMatiere.innerHTML = '<option value="">Chargement...</option>';
        try {
            const data = await apiFetch(`${baseUrl}/matieres/${this.value}`);
            selMatiere.disabled = false;
            if (!data.data.length) {
                selMatiere.innerHTML = '<option value="">Aucune matière affectée à cette classe</option>';
                return;
            }
            selMatiere.innerHTML = '<option value="">-- Choisir --</option>' +
                data.data.map(m => `<option value="${m.id}">${m.nom}</option>`).join('');
        } catch (e) {
            toast(e.message, 'error');
        }
    });

    // ---------- Validation ----------
    document.getElementById('btnValider').addEventListener('click', async function () {
        const annee = document.getElementById('selAnnee').value;
        const profId = document.getElementById('selProf').value;
        const classeId = document.getElementById('selClasse').value;
        const matiereId = document.getElementById('selMatiere').value;

        if (!annee || !profId || !classeId || !matiereId) {
            toast('Veuillez remplir tous les champs avant de valider.', 'error');
            return;
        }

        selection.annee = annee;
        selection.professeurId = profId;
        selection.classeId = classeId;
        selection.matiereId = matiereId;
        selection.classeNom = document.getElementById('selClasse').selectedOptions[0].textContent;
        selection.matiereNom = document.getElementById('selMatiere').selectedOptions[0].textContent;
        selection.profNom = document.getElementById('selProf').selectedOptions[0].textContent;

        document.getElementById('recapSelection').textContent =
            `${selection.profNom} — ${selection.classeNom} — ${selection.matiereNom} — ${selection.annee}`;

        construireGrille();
        document.getElementById('cardGrille').style.display = 'block';
        await chargerGrille();
    });

    // ---------- Construction du squelette de la grille ----------
    function construireGrille() {
        const thead = document.querySelector('.grille-emploi thead tr');
        thead.innerHTML = '<th style="width:110px;">Jour</th>' +
            HEURES.map(h => `<th>${h.debut} - ${h.fin}</th>`).join('');

        const tbody = document.getElementById('corpsGrille');
        tbody.innerHTML = JOURS.map(j => `
            <tr data-jour="${j.key}">
                <td class="jour-label">${j.label}</td>
                ${HEURES.map(h => `
                    <td>
                        <div class="creneau-cell libre"
                             data-jour="${j.key}"
                             data-debut="${h.debut}"
                             data-fin="${h.fin}"></div>
                    </td>
                `).join('')}
            </tr>
        `).join('');

        // Un seul écouteur délégué sur le corps de la grille
        tbody.addEventListener('click', onClickCase);
    }

    // ---------- Chargement des données et coloration des cases ----------
    async function chargerGrille() {
        try {
            const params = new URLSearchParams({
                professeur_id: selection.professeurId,
                annee_scolaire: selection.annee,
                classe_id: selection.classeId,
            });
            const data = await apiFetch(`${baseUrl}/grille?${params.toString()}`);
            mapCreneaux = {};
            planningProfComplet = data.planning_prof;

            data.planning_prof.forEach(row => {
                const cle = `${row.jour}_${row.heure_debut.substring(0, 5)}`;
                const estMoi = String(row.classe_id) === String(selection.classeId) && String(row.matiere_id) === String(selection.matiereId);
                mapCreneaux[cle] = estMoi
                    ? { type: 'mine', label: `${row.v_nom_classe} - ${row.matiere_nom}` }
                    : { type: 'locked-prof', label: `Occupé : ${row.v_nom_classe} - ${row.matiere_nom}` };
            });

            data.planning_classe_autre.forEach(row => {
                const cle = `${row.jour}_${row.heure_debut.substring(0, 5)}`;
                // Ne pas écraser une case qui appartient déjà au prof lui-même
                if (!mapCreneaux[cle]) {
                    mapCreneaux[cle] = { type: 'locked-classe', label: `Occupé par ${row.prof_nom} - ${row.matiere_nom}` };
                }
            });

            appliquerEtatsCases();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    function appliquerEtatsCases() {
        document.querySelectorAll('.creneau-cell').forEach(cell => {
            const cle = `${cell.dataset.jour}_${cell.dataset.debut}`;
            const info = mapCreneaux[cle];

            cell.className = 'creneau-cell';
            cell.title = '';
            cell.textContent = '';

            if (!info) {
                cell.classList.add('libre');
            } else {
                cell.classList.add(info.type);
                cell.title = info.label;
                if (info.type !== 'mine') {
                    cell.textContent = info.label.length > 14 ? info.label.substring(0, 13) + '…' : info.label;
                }
            }
        });
    }

    // ---------- Clic sur une case ----------
    async function onClickCase(e) {
        const cell = e.target.closest('.creneau-cell');
        if (!cell) return;

        if (cell.classList.contains('locked-prof') || cell.classList.contains('locked-classe')) {
            toast(cell.title, 'info');
            return;
        }

        const jour = cell.dataset.jour;
        const debut = cell.dataset.debut;
        const fin = cell.dataset.fin;

        try {
            const data = await apiFetch(`${baseUrl}/toggle`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    professeur_id: selection.professeurId,
                    classe_id: selection.classeId,
                    matiere_id: selection.matiereId,
                    annee_scolaire: selection.annee,
                    jour: jour,
                    heure_debut: debut,
                    heure_fin: fin,
                })
            });

            const cle = `${jour}_${debut}`;
            if (data.action === 'added') {
                mapCreneaux[cle] = { type: 'mine', label: `${selection.classeNom} - ${selection.matiereNom}` };
                planningProfComplet.push({
                    jour: jour,
                    heure_debut: debut,
                    heure_fin: fin,
                    classe_id: selection.classeId,
                    matiere_id: selection.matiereId,
                    v_nom_classe: selection.classeNom,
                    matiere_nom: selection.matiereNom,
                });
                toast('Créneau ajouté', 'success');
            } else if (data.action === 'removed') {
                delete mapCreneaux[cle];
                planningProfComplet = planningProfComplet.filter(row =>
                    !(row.jour === jour && row.heure_debut.substring(0, 5) === debut &&
                      String(row.classe_id) === String(selection.classeId) && String(row.matiere_id) === String(selection.matiereId))
                );
                toast('Créneau retiré', 'success');
            }
            appliquerEtatsCases();
        } catch (e) {
            toast(e.message, 'error');
        }
    }

    // ---------- Impression PDF de tout l'emploi du temps du prof pour l'année ----------
    document.getElementById('btnImprimer').addEventListener('click', function () {
        if (!selection.professeurId || !selection.annee) {
            toast('Veuillez valider une sélection avant d\'imprimer.', 'error');
            return;
        }
        const params = new URLSearchParams({
            professeur_id: selection.professeurId,
            annee_scolaire: selection.annee,
        });
        window.open(`${baseUrl}/pdf?${params.toString()}`, '_blank');
    });

    // ---------- Si on change de matière après validation, on recolore sans re-fetch ----------
    document.getElementById('selMatiere').addEventListener('change', function () {
        if (document.getElementById('cardGrille').style.display === 'none') return;
        // Optionnel : rien à faire ici tant que l'utilisateur n'a pas re-cliqué sur Valider
    });

    // ---------- Init ----------
    chargerAnnees();
    chargerProfesseurs();
    chargerNiveaux();
})();
</script>
@endsection
