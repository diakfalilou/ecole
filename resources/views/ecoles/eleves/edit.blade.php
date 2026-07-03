@extends('ecoles.layout.app')
@section('containte')
    <div class="dashboard-main-body">
        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div>
                <h1 class="fw-semibold mb-4 h6 text-primary-light"> Modifié les information d'un élève    </h1>
                <div>
                    <a href="{{route('ecole.dashboard',session('slug_ecole'))}}" class="text-secondary-light hover-text-primary hover-underline">
                        Acceuille
                    </a>
                    <a href="{{route('index.eleves',session('slug_ecole'))}}" class="text-secondary-light hover-text-primary hover-underline ">/ Elève</a>
                    <span class="text-secondary-light">
                        / Modifie </span>
                    </div>
            </div>

            <a  href="{{route('index.eleves',session('slug_ecole'))}}"
            class="btn btn-primary-600 d-flex align-items-center gap-6 d-none">
            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>
            Modification élève    </a>
        </div>




        <form id="studentForm"
            method="POST"
            action="{{ route('edit.eleve', [$slug, $id]) }}"
            enctype="multipart/form-data"
            class="mt-24">
            @csrf

            <div class="row gy-3">
                <div class="col-lg-12">
                    <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                        <div
                            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                            <h6 class="text-lg fw-semibold mb-0">Information de l'élève</h6>
                        </div>
                        <div class="card-body p-20">
                            <div class="row gy-3">
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_matricule" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matricule * </label>
                                        <input value="{{$eleve->v_matricule}}" required name="v_matricule" type="text" class="form-control" id="v_matricule" placeholder="Matricule">
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                const input = document.getElementById("v_matricule");
                                                fetch("/{{ $slug }}/generate-matricule")
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if (input) {
                                                            input.value = data.matricule;
                                                        }
                                                    })
                                                    .catch(err => console.error("Erreur matricule:", err));
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_nom" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nom
                                            <span class="text-danger-600">*</span>
                                        </label>
                                        <input value="{{$eleve->v_nom}}" required name="v_nom" type="text" class="form-control" id="v_nom" placeholder="Entrez le nom">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_prenom"  class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prénom
                                            <span class="text-danger-600">*</span>
                                        </label>
                                        <input value="{{$eleve->v_prenom}}" required name="v_prenom" type="text" class="form-control" id="v_prenom" placeholder="Entrez le prénom">
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_genre"
                                            class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Genre</label>
                                        <select required name="v_genre" id="v_genre" class="form-control form-select">

                                            <option value="{{$eleve->v_genre}}">{{$eleve->v_genre}}</option>
                                            <option value="Garçons">Garçons</option>
                                            <option value="Fille">Fille</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="d_date_naissance" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date de naissance

                                        </label>
                                        <input value="{{$eleve->d_date_naissance}}" name="d_date_naissance" type="date" class="form-control" id="d_date_naissance">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_telephone"
                                            class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Numéro de télèphone </label>
                                        <input value="{{$eleve->v_telephone}}" name="v_telephone" type="text" class="form-control" id="v_telephone" placeholder="Entrez le numéro de télèphone">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_email" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">E-mail </label>
                                        <input value="{{$eleve->v_email}}" name="v_email" type="text" class="form-control" id="v_email" placeholder="Entrez sont mail">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_adresse" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Adresse</label>
                                        <input value="{{$eleve->v_adresse}}" id="v_adresse" class="form-control" name="v_adresse" type="text">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div>
                                        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Photo de l'élève
                                        </label>

                                        <label
                                            class="drop-zone border border-neutral-400 border-dashed radius-8 p-3 text-center cursor-pointer w-100">

                                            <img
                                                class="image-preview mb-2 {{ empty($eleve->v_photo) ? 'd-none' : '' }}"
                                                src="{{ !empty($eleve->v_photo) ? asset($eleve->v_photo) : '' }}"
                                                style="max-width: 100%; max-height: 150px; object-fit: cover;">
                                            <span
                                                class="drop-zone__prompt d-block {{ !empty($eleve->v_photo) ? 'd-none' : '' }}">
                                                Déposez un fichier ici ou cliquez
                                            </span>
                                            <small class="file-name text-muted d-block mt-1">
                                                {{ !empty($eleve->v_photo) ? basename($eleve->v_photo) : '' }}
                                            </small>

                                            <input
                                                type="file"
                                                name="v_photo"
                                                accept="image/*"
                                                class="drop-zone__input d-none">
                                        </label>
                                    </div>
                                </div>
                                <script>
                                    document.querySelectorAll('.drop-zone__input').forEach(input => {

                                        input.addEventListener('change', function () {

                                            const dropZone = this.closest('.drop-zone');
                                            const prompt = dropZone.querySelector('.drop-zone__prompt');
                                            const fileName = dropZone.querySelector('.file-name');
                                            const preview = dropZone.querySelector('.image-preview');

                                            if (!this.files || !this.files[0]) {
                                                return;
                                            }

                                            const file = this.files[0];

                                            fileName.textContent = file.name;

                                            if (file.type.startsWith('image/')) {

                                                const reader = new FileReader();

                                                reader.onload = function (e) {
                                                    preview.src = e.target.result;
                                                    preview.classList.remove('d-none');

                                                    if (prompt) {
                                                        prompt.classList.add('d-none');
                                                    }
                                                };

                                                reader.readAsDataURL(file);
                                            }
                                        });

                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                        <div
                            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                            <h6 class="text-lg fw-semibold mb-0">Informations médicales</h6>
                        </div>

                        <div class="card-body p-20">
                            <div class="row gy-3">

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="groupesangain" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Groupe sanguin
                                        </label>

                                        <select name="v_groupe_sanguin" id="groupesangain" class="form-control form-select">

                                            <option value="{{$eleve->v_groupe_sanguin}}"> {{$eleve->v_groupe_sanguin}} </option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A−</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B−</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB−</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O−</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="taille" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Taille
                                        </label>

                                        <input value="{{$eleve->v_taille}}" type="text" name="v_taille" class="form-control" id="taille">
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="poid" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Poids
                                        </label>
                                        <input value="{{$eleve->v_poids}}" type="text" name="v_poids" class="form-control" id="poid">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xxl-6">
                    <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                        <div
                            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                            <h6 class="text-lg fw-semibold mb-0">
                                Informations sur l'établissement précédent
                            </h6>
                        </div>

                        <div class="card-body p-20">
                            <div class="row gy-3">

                                <div class="col-sm-6">
                                    <div class="">
                                        <label for="schoolNamee" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Nom de l'établissement
                                        </label>
                                        <input value="{{$eleve->v_nom_etablissement}}" name="v_nom_etablissement" type="text" class="form-control" id="schoolNamee" placeholder="Saisissez le nom de l'établissement">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="">
                                        <label for="adresseancienecole" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Adresse
                                        </label>
                                        <input value="{{$eleve->v_adresse_etablissement}}" name="v_adresse_etablissement" type="text" class="form-control" id="adresseancienecole" placeholder="Saisissez l'adresse">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-6">
                    <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                        <div
                            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                            <h6 class="text-lg fw-semibold mb-0">Détails de l'élève</h6>
                        </div>
                        <div class="card-body p-20">
                            <div class="row gy-3">
                                <div class="col-sm-12">
                                    <div class="">
                                        <label for="moreDetails"
                                            class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Détails
                                        </label>
                                        <textarea name="t_details_eleve" id="moreDetails" class="form-control" placeholder="Entrez les dètails">
                                            {{$eleve->t_details_eleve}}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-center gap-3 mt-8">

                       <button type="button"
                            id="btnResetForm"
                            class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">

                            <i class="ri-close-circle-line me-2"></i>
                            Annuler

                        </button>

                        <button type="submit"
                            class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">

                            <i class="ri-save-3-line me-2"></i>
                            Enregistrer les modifications

                        </button>

                    </div>
                </div>
            </div>
        </form>




    </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('studentForm');
    const resetBtn = document.getElementById('btnResetForm');

    // Bouton Annuler
    if (resetBtn) {

        resetBtn.addEventListener('click', function () {

            Swal.fire({
                title: 'Annuler ?',
                text: 'Toutes les modifications non enregistrées seront perdues.',
                icon: 'warning',
                width: '360px',
                customClass: {
                    popup: 'swal-small',
                    title: 'swal-title-small',
                    htmlContainer: 'swal-text-small',
                    confirmButton: 'swal-btn-small',
                    cancelButton: 'swal-btn-small'
                },
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Retour',
                reverseButtons: true

            }).then((result) => {

                if (result.isConfirmed) {

                    form.reset();

                    const preview = document.querySelector('.image-preview');
                    const prompt = document.querySelector('.drop-zone__prompt');
                    const fileName = document.querySelector('.file-name');

                    @if(!empty($eleve->v_photo))
                        preview.src = "{{ asset($eleve->v_photo) }}";
                        preview.classList.remove('d-none');
                        prompt.classList.add('d-none');
                        fileName.textContent = "{{ basename($eleve->v_photo) }}";
                    @else
                        preview.classList.add('d-none');
                        prompt.classList.remove('d-none');
                        fileName.textContent = '';
                    @endif

                    Swal.fire({
                        icon: 'success',
                        title: 'Réinitialisé',
                        text: 'Le formulaire a été remis à zéro.',
                        timer: 1500,
                        showConfirmButton: false,
                        width: '320px'
                    });
                }
            });

        });

    }

    // Bouton Enregistrer
    form.addEventListener('submit', function(e) {

        e.preventDefault();

        Swal.fire({
            title: 'Confirmer ?',
            text: 'Les modifications seront enregistrées.',
            icon: 'question',
            width: '360px',
            customClass: {
                popup: 'swal-small',
                title: 'swal-title-small',
                htmlContainer: 'swal-text-small',
                confirmButton: 'swal-btn-small',
                cancelButton: 'swal-btn-small'
            },
            showCancelButton: true,
            confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler',
            reverseButtons: true

        }).then((result) => {

            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Mise à jour...',
                    text: 'Veuillez patienter',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    width: '350px',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }

        });

    });

});
  </script>
<style>
.swal-small {
    font-size: 13px !important;
}

.swal-title-small {
    font-size: 16px !important;
    font-weight: 600 !important;
}

.swal-text-small {
    font-size: 13px !important;
}

.swal-btn-small {
    font-size: 12px !important;
    padding: 6px 16px !important;
}
</style>
@endsection
