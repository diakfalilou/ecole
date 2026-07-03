@extends('ecoles.layout.app')
@section('containte')
<div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des matières</h1>
        <div>
            <a href="#!" class="text-secondary-light hover-text-primary hover-underline">Accueil</a>
            <span class="text-secondary-light"> / Matière / ajouter</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="formMatiere">
            @csrf
            <div class="row gy-3">

                <div class="col-md-4">
                    <label class="form-label">Code matière</label>
                    <input type="text" name="v_code" id="v_code" class="form-control" placeholder="Ex: MATH">
                </div>

                <div class="col-md-8">
                    <label class="form-label">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="v_libelle" id="v_libelle" class="form-control" placeholder="Ex: Mathématiques" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Coefficient <span class="text-danger">*</span></label>
                    <input type="number" name="i_coefficient" id="i_coefficient" class="form-control" value="1" min="1" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Couleur</label>
                    <input type="color" name="v_couleur" id="v_couleur" class="form-control form-control-color" value="#0d6efd">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Détails</label>
                    <textarea name="t_details_matiere" id="t_details_matiere" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">Annuler</button>
                    <button type="button" id="btnEnregistrer" class="btn btn-primary">
                        <i class="ri-save-line"></i> Enregistrer
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('btnEnregistrer').addEventListener('click', function () {

    const libelle = document.getElementById('v_libelle').value.trim();
    const coefficient = document.getElementById('i_coefficient').value.trim();

    if (!libelle || !coefficient) {
        Swal.fire({
            icon: 'warning',
            title: 'Champs incomplets',
            text: 'Veuillez remplir les champs obligatoires (*).'
        });
        return;
    }

    Swal.fire({
        title: 'Confirmer l\'ajout',
        text: 'Voulez-vous enregistrer cette matière ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui, enregistrer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#0d6efd',
    }).then((result) => {
        if (result.isConfirmed) {
            enregistrerMatiere();
        }
    });

});

function enregistrerMatiere() {

    // Affichage du loader pendant l'insertion
    Swal.fire({
        title: 'Enregistrement en cours...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const form = document.getElementById('formMatiere');
    const formData = new FormData(form);

    fetch("{{ route('matiere.store', $slug ?? request()->route('slug')) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                document.getElementById('formMatiere').reset();
                // location.reload(); // décommente si tu veux recharger la liste
            });
        } else if (data.status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: data.message
            });
        } else if (data.status === 'validation') {
            let messages = Object.values(data.errors).flat().join('\n');
            Swal.fire({
                icon: 'warning',
                title: 'Erreur de validation',
                text: messages
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de l\'enregistrement.'
        });
        console.error(error);
    });
}
</script>
@endpush
