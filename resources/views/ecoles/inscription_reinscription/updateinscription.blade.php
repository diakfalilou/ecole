@extends('ecoles.layout.app')
@section('containte')
<div class="dashboard-main-body">

       <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div class="">
                <h1 class="fw-semibold mb-4 h6 text-primary-light">Modifier l'inscription de l'élève</h1>
                <div class="">
                    <a href="{{route('ecole.dashboard',session('slug_ecole'))}}" class="text-secondary-light hover-text-primary hover-underline">Accueille </a>
                    <a href="{{route('liste.inscription.reinscription',session('slug_ecole'))}}" class="text-secondary-light hover-text-primary hover-underline"> /
                        Inscription/Reinscription</a>
                    <span class="text-secondary-light">/  Modifier l'inscription de l'élève</span>
                </div>
            </div>
        </div>

        <div class="mt-24">
           <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex gap-32 flex-md-row flex-column">
                        <div class="max-w-300-px w-100 text-center">
                            <figure class="mb-24 w-120-px h-120-px mx-auto rounded-circle overflow-hidden">
                                <img src="{{ !empty($eleve->v_photo) ? asset($eleve->v_photo) : asset('assets/images/thumbs/student-details-img.png') }}" alt="Student Image" class="w-100 h-100 object-fit-cover">
                            </figure>
                            <h2 class="h6 text-primary-light mb-16 fw-semibold"> {{$eleve->v_nom.' '.$eleve->v_prenom}} </h2>
                            <p style="text-align: left!important"  class="mb-0">Matricule: <span class="text-primary-600 fw-semibold">{{$eleve->v_matricule}}</span></p>
                            <p style="text-align: left!important" class="mb-0">Niveau : <span class="text-primary-light fw-semibold"> {{$eleve->v_niveaux}} </span> </p>
                            <p style="text-align: left!important" class="mb-0">Classe : <span class="text-primary-light fw-semibold"> {{$eleve->v_nom_classe}} </span> </p>
                        </div>
                        <div class="">
                            <span class="h-100 w-1-px bg-neutral-200"></span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="pb-16 border-bottom d-flex align-items-center justify-content-between gap-20">
                                <h3 class="h6 text-primary-light text-lg mb-0 fw-semibold">Changer les valeur de l'inscription</h3>
                            </div>

                            <form  method="POST" action="">
                                @csrf
                                <div class="mt-16 d-flex flex-column gap-8">
                                    <div class="row gy-3">
                                        <div class="col-xxl-4 col-xl-4 col-sm-6">
                                            <div class="">
                                                <label for="anneescolaire" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Academic Year
                                                <span class="text-danger-600">* </span> </label>
                                                <select required name="anneescolaire" id="anneescolaire" class="form-control form-select">
                                                    <option value="{{ session('anneescolaire') }}">{{ session('anneescolaire') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-xl-4 col-sm-6">
                                            <div class="">
                                                <label for="section" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Niveau
                                                <span class="text-danger-600">* </span> </label>
                                                <select required name="niveau_id" id="niveauSelect" class="form-control form-select">
                                                    <option label="Séléctionner le niveau">Séléctionner le niveau</option>
                                                    @foreach ($niveaux as $niveau )
                                                        <option value="{{ $niveau->i_niveauID }}"> {{$niveau->v_niveaux}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-xl-4 col-sm-6">
                                            <div class="">
                                                <label for="classSelection" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe
                                                    <span class="text-danger-600">* </span>
                                                </label>
                                                <select required id="classSelection" name="classe_id" class="form-control form-select">
                                                    <option value="">Séléctionner une classe</option>
                                                    <small class="text-primary">
                                                        <i class="fas fa-spinner fa-spin"></i>
                                                        Chargement des écoles...
                                                    </small>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-xl-12 col-sm-12">

                                            <div class="">
                                                    <input name="eleveId" value="{{$eleveId}}" type="hidden">
                                                    <button type="submit"
                                                        class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">

                                                        <i class="ri-save-3-line me-2"></i>
                                                        Enregistrer les modifications

                                                    </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('niveauSelect').addEventListener('change', function () {
            let niveauId = this.value;
            let classeSelect = document.getElementById('classSelection');
            classeSelect.innerHTML = `<option>Chargement...</option>`;
            if (!niveauId) {
                classeSelect.innerHTML = `<option value="">Séléctionner une classe</option>`;
                return;
            }

            fetch(`/get-classes-by-niveau/${niveauId}`)
                .then(res => res.json())
                .then(data => {
                    classeSelect.innerHTML = `<option value="">Séléctionner une classe</option>`;
                    data.forEach(classe => {
                        classeSelect.innerHTML += `
                            <option value="${classe.i_classe_id}">
                                ${classe.v_nom_classe}
                            </option>
                        `;
                    });
                })
                .catch(err => {
                    console.error(err);
                    classeSelect.innerHTML = `<option>Erreur chargement</option>`;
                });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const form = document.querySelector("form");
            const submitBtn = document.querySelector('button[type="submit"]');

            if (!form) {
                console.error("Formulaire introuvable");
                return;
            }

            // =========================
            // SUBMIT (ENREGISTREMENT)
            // =========================
            form.addEventListener("submit", function (e) {

                e.preventDefault();

                let valid = true;

                const requiredFields = form.querySelectorAll("[required]");

                requiredFields.forEach(field => {

                    if (
                        field.value === "" ||
                        field.value === null ||
                        field.value.trim() === ""
                    ) {
                        valid = false;
                    }
                });

                if (!valid) {
                    Swal.fire({
                        icon: "error",
                        title: "Champs obligatoires",
                        text: "Veuillez remplir tous les champs requis."
                    });

                    return;
                }

                Swal.fire({
                    title: "Confirmer le changement de l'inscription ?",
                    text: "L'élève va être mutter.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Oui, enregistrer",
                    cancelButtonText: "Annuler",
                    allowOutsideClick: false
                }).then((result) => {

                    if (result.isConfirmed) {

                        submitBtn.disabled = true;

                        submitBtn.innerHTML = `
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Enregistrement...
                        `;
                        // Soumission réelle du formulaire
                        form.submit();
                    }
                });
            });

        });
</script>
@endsection
