@extends('ecoles.layout.app')
@section('containte')
<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div class="">
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Parametrage des tranches</h1>
            <div class="">
                <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueille </a>
                <span class="text-secondary-light">/ Classe</span>
                <span class="text-secondary-light">/ Parametrages tranches</span>
            </div>
        </div>
    </div>

    <form id="studentForm" method="POST" action="" enctype="multipart/form-data" class="mt-24">
        @csrf

        <div class="row gy-3">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                        <h6 class="text-lg fw-semibold mb-0">Paramétrage des tranches de paiement</h6>
                    </div>
                    <div class="card-body p-20">
                        <div class="row gy-3 mb-24">
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                    Année scolaire <span class="text-danger-600">*</span>
                                </label>
                                <select id="anneescolaireSelect" class="form-control form-select">
                                    @foreach ($data_anneescolaire as $annee)
                                        <option value="{{ $annee->v_annesclaire }}"
                                            {{ $annee->v_annesclaire == $annee_courante ? 'selected' : '' }}>
                                            {{ $annee->v_annesclaire }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- CONTENEUR DES TRANCHES --}}
                        <div id="conteneurTranches">
                            <div class="text-center py-20">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
document.addEventListener('DOMContentLoaded', function() {

    const routeGetTranches = "{{ route('tranches.byannee', ['slug' => $slug]) }}";
    const routeUpdateTranche = "{{ route('tranches.update', ['slug' => $slug]) }}";
    const csrfToken = "{{ csrf_token() }}";

    function afficherToast(message, type) {
        var toast = document.createElement('div');
        toast.style.cssText = 'position:fixed; top:20px; right:20px; z-index:9999; padding:12px 20px; border-radius:8px; color:#fff; font-size:14px; font-weight:500; display:flex; align-items:center; gap:8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: opacity 0.3s ease;';
        toast.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
        toast.innerHTML = type === 'success'
            ? '<i class="ri-check-circle-line" style="font-size:18px;"></i> ' + message
            : '<i class="ri-close-circle-line" style="font-size:18px;"></i> ' + message;

        document.body.appendChild(toast);

        setTimeout(function() {
            toast.style.opacity = '0';
            setTimeout(function() { toast.remove(); }, 300);
        }, 2500);
    }

    const moisLabels = {
        'janvier':'Janvier','fevrier':'Février','mars':'Mars','avril':'Avril',
        'mai':'Mai','juin':'Juin','juillet':'Juillet','aout':'Août',
        'septembre':'Septembre','octobre':'Octobre','novembre':'Novembre','decembre':'Décembre'
    };

    const tranchesLabels = {
        '1ere'     : '1ère Tranche',
        '2eme'     : '2ème Tranche',
        '3eme'     : '3ème Tranche',
        'annuelle' : 'Annuelle'
    };

    const tranchesCouleurs = {
        '1ere'     : 'primary',
        '2eme'     : 'success',
        '3eme'     : 'warning',
        'annuelle' : 'info'
    };

    function chargerTranches(annee) {
        document.getElementById('conteneurTranches').innerHTML =
            '<div class="text-center py-20"><div class="spinner-border text-primary" role="status"></div></div>';

        fetch(routeGetTranches + '?annee=' + annee)
            .then(function(r) { return r.json(); })
            .then(function(data) { afficherTranches(data); })
            .catch(function() {
                document.getElementById('conteneurTranches').innerHTML =
                    '<p class="text-danger text-center">Erreur de chargement</p>';
            });
    }

    function afficherTranches(data) {
        const conteneur = document.getElementById('conteneurTranches');
        const moisOrdre = Object.keys(moisLabels);
        const tranchesOrdre = Object.keys(tranchesLabels);

        let html = '<div class="row gy-4">';

        tranchesOrdre.forEach(function(tranche) {
            const couleur = tranchesCouleurs[tranche];
            html += '<div class="col-lg-6 col-xl-3">';
            html += '<div class="border radius-12 overflow-hidden h-100">';

            // Header tranche
            html += '<div class="px-16 py-12 bg-' + couleur + '-100 border-bottom d-flex align-items-center gap-8">';
            html += '<span class="w-8-px h-8-px bg-' + couleur + '-600 rounded-circle d-inline-block"></span>';
            html += '<h6 class="text-sm fw-semibold mb-0 text-' + couleur + '-600">' + tranchesLabels[tranche] + '</h6>';
            html += '</div>';

            // Liste des mois
            html += '<div class="px-16 py-12">';
            moisOrdre.forEach(function(mois) {
                var row = data.find(function(d) { return d.v_tranche === tranche && d.v_mois === mois; });
                var checked = row && row.b_actif == 1 ? 'checked' : '';
                var id = row ? row.i_tranche_id : '';

                html += '<div class="d-flex align-items-center justify-content-between py-6 border-bottom border-neutral-100">';
                html += '<label class="text-sm text-secondary-light mb-0" for="chk_' + tranche + '_' + mois + '">';
                html += moisLabels[mois];
                html += '</label>';
                html += '<div class="form-check">';
                html += '<input class="form-check-input tranche-checkbox" type="checkbox"';
                html += ' id="chk_' + tranche + '_' + mois + '"';
                html += ' data-id="' + id + '"';
                html += ' ' + checked + '>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';

            html += '</div></div>';
        });

        html += '</div>';
        conteneur.innerHTML = html;

        // Écouter les checkboxes
        document.querySelectorAll('.tranche-checkbox').forEach(function(chk) {
            chk.addEventListener('change', function() {
                var id    = this.dataset.id;
                var actif = this.checked ? 1 : 0;
                var chkEl = this;

                fetch(routeUpdateTranche, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ id: id, actif: actif })
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) {
                        var label = document.querySelector('label[for="' + chkEl.id + '"]');
                        if (label) {
                            label.style.color = '#28a745';
                            setTimeout(function() { label.style.color = ''; }, 1500);
                        }
                        afficherToast(actif ? 'Mois activé avec succès' : 'Mois désactivé avec succès', 'success');
                    }
                })
                .catch(function() {
                    chkEl.checked = !chkEl.checked;
                    afficherToast('Erreur lors de la mise à jour', 'error');
                });
            });
        });
    }

    var selectAnnee = document.getElementById('anneescolaireSelect');
    if (selectAnnee) {
        selectAnnee.addEventListener('change', function() {
            chargerTranches(this.value);
        });
        chargerTranches(selectAnnee.value);
    }

});
</script>
</div>


@endsection
