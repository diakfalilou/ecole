@extends('admin.layout.app')

@section('container')
<style>
.avatar-upload {
    width: 160px;
}

/* Le cadre */
.avatar-preview {
    position: relative;
    width: 160px;
    height: 160px;

    /* FORCER le carré */
    border-radius: 0 !important;

    overflow: hidden;
    cursor: pointer;
    border: 2px dashed #d0d5dd;
    background-color: #f8f9fa;
}

/* L'image */
.avatar-preview img {
    width: 100%;
    height: 100%;

    /* FORCER le carré */
    border-radius: 0 !important;

    object-fit: cover;
}

/* Overlay */
.avatar-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

/* Hover */
.avatar-preview:hover .avatar-overlay {
    opacity: 1;
}
</style>

<div class="dashboard-main-body">

    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">

        <!-- LEFT -->
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">
                Ajout d'une école
            </h1>

            <div>
                <a href="#" class="text-secondary-light hover-text-primary hover-underline">
                    Dashboard
                </a>

                <a href="#" class="text-secondary-light hover-text-primary hover-underline">
                    / École
                </a>

                <span class="text-secondary-light">
                    / Ajouter une école
                </span>
            </div>
        </div>

        <!-- RIGHT -->
        <div>
            <a href="{{ route('index.users') }}"
               class="btn btn-outline-primary px-24 py-10 radius-8">
                ← Retour
            </a>
        </div>

    </div>

    <form id="ecoleForm"
          action="{{ route('users.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="row gy-3">

            <div class="col-lg-12">

                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">

                    <div class="card-header border-bottom bg-base py-16 px-24">
                        <h6 class="text-lg fw-semibold mb-0">
                            Informations de l'école
                        </h6>
                    </div>

                    <div class="card-body p-20">

                        <div class="row gy-3">

                            <!-- Nom et prénom -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-8">
                                    Nom et prénom <span class="text-danger">*</span>
                                </label>
                                <input value="{{ old('name') }}" name="name" class="form-control @error('name') is-invalid @enderror" type="text">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- E-mail -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-8">
                                    E-mail <span class="text-danger">*</span>
                                </label>
                                <input type="text"   name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Entrez l'adresse mail">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Téléphone 1 -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-8">
                                    Téléphone
                                </label>
                                <input type="text" name="telephone" value="{{ old('telephone') }}" class="form-control @error('telephone') is-invalid @enderror" placeholder="+224 XXX XXX XXX">
                                @error('telephone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- role -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-8">
                                    Rôle
                                </label>
                                <select name="role" class="form-control @error('role') is-invalid @enderror" id="role">
                                    <option value="">Séléctionner un rôle</option>
                                    <option value="0">Administrateur</option>
                                    <option value="1">Fondateur</option>
                                    <option value="2">Utilisateur</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- etablissement -->
                            <div class="col-md-4 d-none" id="etablissement-wrapper">
                                <label class="text-sm fw-semibold mb-8">
                                    Etablissement
                                </label>
                                <select name="etablissement_id" class="form-control" id="etablissement_id">
                                    <option value="">Séléctionner un établissement</option>
                                    @foreach ($data_etablissement as $etat)
                                        <option value="{{ $etat->i_idetablissement }}">
                                            {{ $etat->v_nometablissement }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ecole -->
                            <div class="col-md-4 d-none" id="ecole-wrapper">
                                <label class="text-sm fw-semibold mb-8">
                                    Ecole
                                </label>
                                <select name="ecole_id" class="form-control" id="ecole_id">
                                    <option value="">Séléctionner une école</option>
                                </select>
                            </div>

                            <!-- Photo de profil -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-2 d-block">
                                    Photo
                                </label>

                                <div class="avatar-upload">
                                    <label for="logoInput" class="avatar-preview">
                                        <img id="avatarImage"
                                            src="{{ asset('assets/images/avatar-default.png') }}"
                                            alt="Photo utilisateur">
                                        <div class="avatar-overlay">
                                            <i class="ri-camera-line"></i>
                                        </div>
                                    </label>

                                    <input
                                        type="file"
                                        name="logo"
                                        id="logoInput"
                                        accept="image/*"
                                        class="d-none @error('logo') is-invalid @enderror"
                                    >
                                </div>

                                @error('logo')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="col-12">

                <div class="d-flex justify-content-center gap-3 mt-8">

                    <button type="reset"
                            class="border border-danger text-danger px-40 py-10 radius-8">

                        Réinitialiser

                    </button>

                    <button type="submit"
                            class="btn btn-primary px-40 py-10 radius-8">

                        Enregistrer

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('ecoleForm');

    form.addEventListener('submit', function (e) {

        if (form.dataset.submitted) {
            return;
        }

        e.preventDefault();

        Swal.fire({
            title: 'Confirmation',
            text: "Confirmes-vous la création de l'utlisateur ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {

                form.dataset.submitted = true;

                form.submit();
            }
        });
    });

});

</script>

<script>
    document.getElementById('logoInput').addEventListener('change', function (e) {
        const file = e.target.files[0];

        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Veuillez sélectionner une image valide');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            document.getElementById('avatarImage').src = e.target.result;
        };

        reader.readAsDataURL(file);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const roleSelect = document.getElementById('role');
        const etablissementWrapper = document.getElementById('etablissement-wrapper');
        const ecoleWrapper = document.getElementById('ecole-wrapper');

        function updateVisibility() {
            const role = roleSelect.value;

            // Par défaut : tout cacher
            etablissementWrapper.classList.add('d-none');
            ecoleWrapper.classList.add('d-none');

            // Fondateur
            if (role === '1') {
                etablissementWrapper.classList.remove('d-none');
            }

            // Utilisateur
            if (role === '2') {
                etablissementWrapper.classList.remove('d-none');
                ecoleWrapper.classList.remove('d-none');
            }
        }

        // Changement de rôle
        roleSelect.addEventListener('change', updateVisibility);

        // Au chargement (edit form)
        updateVisibility();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const etablissementSelect = document.getElementById('etablissement_id');
        const ecoleSelect = document.getElementById('ecole_id');
        etablissementSelect.addEventListener('change', function () {
            const etablissementId = this.value;
            // reset + état loading
            ecoleSelect.innerHTML = '<option>Chargement des écoles...</option>';
            ecoleSelect.disabled = true;
            // si vide → reset complet
            if (!etablissementId) {
                ecoleSelect.innerHTML = '<option value="">Séléctionner une école</option>';
                ecoleSelect.disabled = false;
                return;
            }
            fetch(`/get-ecoles/${etablissementId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {

                    let options = '<option value="">Séléctionner une école</option>';

                    data.forEach(ecole => {
                        options += `
                            <option value="${ecole.i_idecole}">
                                ${ecole.v_nomecole}
                            </option>
                        `;
                    });

                    ecoleSelect.innerHTML = options;
                    ecoleSelect.disabled = false; // ✅ IMPORTANT

                })
                .catch(error => {
                    console.error('Erreur:', error);

                    ecoleSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    ecoleSelect.disabled = false;
                });
        });

    });
</script>

@endsection
