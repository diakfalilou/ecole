@extends('ecoles.layout.app')
@section('containte')

<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div class="">
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Parametrage des classes</h1>
            <div class="">
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueille </a>
                <span class="text-secondary-light">/ Classe</span>
                <span class="text-secondary-light">/ Parametrages classes</span>
            </div>
        </div>
    </div>

    <div class="mt-24">
        <div class="card h-100">
            <div class="card-body p-0 dataTable-wrapper">

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                    <div class="d-flex flex-wrap align-items-center gap-16">

                        {{-- SELECT ANNÉE SCOLAIRE --}}

                        <div class="d-flex align-items-center gap-8">
                            <label for="selectAnnee" class="text-secondary-light text-sm fw-medium mb-0">
                                <i class="ri-calendar-line me-1"></i> Année scolaire :
                            </label>
                            <select id="selectAnnee"
                                class="form-select form-select-sm border border-neutral-300 radius-8 text-secondary-light"
                                style="min-width: 140px;">
                                @foreach ($data_anneescolaire as $annee)
                                    <option value="{{ $annee->v_annesclaire }}"
                                        {{ $annee->v_annesclaire == $annee_courante ? 'selected' : '' }}>
                                        {{ $annee->v_annesclaire }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- EXPORT --}}
                        <div class="dropdown">
                            <button type="button"
                                class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20"
                                data-bs-toggle="dropdown">
                                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                                    <i class="ri-file-upload-line text-md line-height-1"></i> Export
                                </span>
                                <span><i class="ri-arrow-down-s-line"></i></span>
                            </button>
                            <ul class="dropdown-menu p-12 border bg-base shadow">
                                <li>
                                    <button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                                        <i class="ri-file-3-line"></i> PDF
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                                        <i class="ri-file-excel-line"></i> Excel
                                    </button>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                {{-- TABLEAU --}}
                <div class="p-0" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="table bordered-table mb-0" id="tableModalite" style="min-width: 900px;">
                        <thead>
                            <tr>
                                <th class="text-center" style="white-space: nowrap;">Niveau</th>
                                <th class="text-center" style="white-space: nowrap;">Classe</th>
                                <th class="text-center" style="white-space: nowrap;">Prix Inscription</th>
                                <th class="text-center" style="white-space: nowrap;">Prix Réinscription</th>
                                <th class="text-center" style="white-space: nowrap;">Mensualité</th>
                                <th class="text-center" style="white-space: nowrap;">1ère Tranche</th>
                                <th class="text-center" style="white-space: nowrap;">2ème Tranche</th>
                                <th class="text-center" style="white-space: nowrap;">3ème Tranche</th>
                                <th class="text-center" style="white-space: nowrap;">Annuelle</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyModalite">
                            <tr><td colspan="9" class="text-center">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- REMPLACE @push('scripts') par ceci --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const routeGetModalites = "{{ route('modalites.byannee', ['slug' => $slug]) }}";
        const routeUpdate       = "{{ route('modalites.update', ['slug' => $slug]) }}";
        const csrfToken         = "{{ csrf_token() }}";

        function chargerModalites(annee) {
            console.log('Chargement pour année:', annee);
            console.log('URL:', routeGetModalites + '?annee=' + annee);

            fetch(routeGetModalites + '?annee=' + annee)
                .then(function(r) {
                    console.log('Status:', r.status);
                    if (!r.ok) {
                        return r.text().then(function(text) { throw new Error(text); });
                    }
                    return r.json();
                })
                .then(function(data) {
                    console.log('Données reçues:', data);
                    afficherTableau(data);
                })
                .catch(function(err) {
                    console.error('Erreur complète:', err);
                    document.getElementById('tbodyModalite').innerHTML =
                        '<tr><td colspan="9" class="text-center text-danger">Erreur: ' + err.message + '</td></tr>';
                });
        }

        function afficherTableau(data) {
            const tbody = document.getElementById('tbodyModalite');

            if (!tbody) {
                console.error('tbodyModalite introuvable !');
                return;
            }

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Aucune donnée</td></tr>';
                return;
            }

            const champsEditables = [
                'd_pirx_inscription',
                'd_prix_reinscription',
                'd_prix_mensuelle',   // ← ajouté
                'd_tranche1',
                'd_tranche2',
                'd_tranche3',
                'd_tranche_annuelle'
            ];

            let html = '';
            data.forEach(function(row, index) {
                html += '<tr>';

                html += '<td>' + row.niveau + '</td>';
                html += '<td>' + row.classe + '</td>';
                champsEditables.forEach(function(champ) {
                    html += '<td><input type="number"';
                    html += ' class="form-control form-control-sm editable-field"';
                    html += ' value="' + row[champ] + '"';
                    html += ' data-id="' + row.i_modalite_classe + '"';
                    html += ' data-champ="' + champ + '"';
                    html += ' min="0" style="width:70%; text-align:right; border:none; font-weight:bold;"></td>';
                });
                html += '</tr>';
            });

            tbody.innerHTML = html;

            document.querySelectorAll('.editable-field').forEach(function(input) {
                input.addEventListener('change', function() {
                    fetch(routeUpdate, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            id: this.dataset.id,
                            champ: this.dataset.champ,
                            valeur: this.value
                        })
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.success) {
                            input.classList.add('is-valid');
                            setTimeout(function() { input.classList.remove('is-valid'); }, 1500);
                        }
                    })
                    .catch(function() { input.classList.add('is-invalid'); });
                });
            });
        }

        var selectAnnee = document.getElementById('selectAnnee');
        if (selectAnnee) {
            selectAnnee.addEventListener('change', function() {
                chargerModalites(this.value);
            });
            chargerModalites(selectAnnee.value);
        } else {
            console.error('selectAnnee introuvable !');
        }

    });
</script>






</div>
@endsection
