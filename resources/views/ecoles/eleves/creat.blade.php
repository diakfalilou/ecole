@extends('ecoles.layout.app')
@section('containte')

    <div class="dashboard-main-body">
        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div>
                <h1 class="fw-semibold mb-4 h6 text-primary-light"> Ajouter un nouvel élève    </h1>
                <div>
                    <a href="{{route('ecole.dashboard',session('slug_ecole'))}}" class="text-secondary-light hover-text-primary hover-underline">
                        Acceuille
                    </a>
                    <a href="{{route('index.eleves',session('slug_ecole'))}}" class="text-secondary-light hover-text-primary hover-underline ">/ Elève</a>
                    <span class="text-secondary-light">
                        / Ajouter </span>
                    </div>
            </div>

            <a  href="{{route('index.eleves',session('slug_ecole'))}}"
            class="btn btn-primary-600 d-flex align-items-center gap-6 d-none">
            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>
            Add Student    </a>
        </div>




        <form id="studentForm"
            method="POST"
            action="{{ route('students.store', $slug) }}"
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
                                        <label for="anneescolaire" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Academic Year
                                        <span class="text-danger-600">* </span> </label>
                                        <select required name="anneescolaire" id="anneescolaire" class="form-control form-select">
                                            <option value="{{ session('anneescolaire') }}">{{ session('anneescolaire') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
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

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
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

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_matricule" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matricule * </label>
                                        <input required name="v_matricule" type="text" class="form-control" id="v_matricule" placeholder="Matricule">
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
                                        <input required name="v_nom" type="text" class="form-control" id="v_nom" placeholder="Entrez le nom">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_prenom"  class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prénom
                                            <span class="text-danger-600">*</span>
                                        </label>
                                        <input required name="v_prenom" type="text" class="form-control" id="v_prenom" placeholder="Entrez le prénom">
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_genre"
                                            class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Genre</label>
                                        <select required name="v_genre" id="v_genre" class="form-control form-select">
                                            <option value="Sélèctionner le genre" >Sélèctionner le genre</option>
                                            <option value="Garçons">Garçons</option>
                                            <option value="Fille">Fille</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="d_date_naissance" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date de naissance

                                        </label>
                                        <input name="d_date_naissance" type="date" class="form-control" id="d_date_naissance">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_telephone"
                                            class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Numéro de télèphone </label>
                                        <input name="v_telephone" type="text" class="form-control" id="v_telephone" placeholder="Entrez le numéro de télèphone">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_email" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">E-mail </label>
                                        <input name="v_email" type="text" class="form-control" id="v_email" placeholder="Entrez sont mail">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_adresse" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Adresse</label>
                                        <input id="v_adresse" class="form-control" name="v_adresse" type="text">
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
                                                class="image-preview d-none mb-2"
                                                style="max-width: 100%; max-height: 150px; object-fit: cover;">

                                            <span class="drop-zone__prompt d-block">
                                                Déposez un fichier ici ou cliquez
                                            </span>

                                            <small class="file-name text-muted d-block mt-1"></small>

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

                                        // Afficher le nom du fichier
                                        fileName.textContent = file.name;

                                        // Afficher l'image
                                        if (file.type.startsWith('image/')) {

                                            const reader = new FileReader();

                                            reader.onload = function (e) {
                                                preview.src = e.target.result;
                                                preview.classList.remove('d-none');
                                                prompt.style.display = 'none';
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
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="text-lg fw-semibold mb-0">
                                    Informations sur les parents et tuteurs
                                </h6>
                            </div>
                            <!-- OPTIONS PARENTS -->
                            <div class="mt-12 d-flex align-items-center gap-4 flex-wrap">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="parent_option" id="selectParent" value="select">
                                    <label class="form-check-label fw-medium" for="selectParent">
                                        Sélectionner un parent
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="parent_option" id="createParent" value="create">
                                    <label class="form-check-label fw-medium" for="createParent">
                                        Enregistrer un parent
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="card-body p-20">

                            <div id="selectParentBlock" class="row gy-3 d-none">
                                <!-- SELECT AUTOCOMPLETE -->
                                <div class="col-xxl-6 col-xl-6 col-sm-12">
                                    <label class="text-sm fw-semibold text-primary-light mb-8">
                                        Sélectionner un parent
                                    </label>
                                    <input
                                        type="text"
                                        id="parentSearch"
                                        class="form-control"
                                        placeholder="Rechercher un parent (nom, téléphone...)">
                                    <input type="hidden" name="parent_id" id="parent_id">
                                    <!-- RESULTATS -->
                                    <div id="parentResults"
                                        class="list-group position-absolute w-100"
                                        style="z-index: 999;"></div>
                                </div>
                                <!-- INFOS PARENT -->
                                <div class="col-xxl-6 col-xl-6 col-sm-12">
                                    <div id="parentInfo" class="p-3 border rounded bg-light d-none">
                                        <h6 class="mb-2">Informations du parent</h6>
                                        <div><strong>Père :</strong> <span id="p_pere"></span></div>
                                        <div><strong>Mère :</strong> <span id="p_mere"></span></div>
                                        <div><strong>Tuteur :</strong> <span id="p_tuteur"></span></div>
                                        <div><strong>Téléphone :</strong> <span id="p_tel"></span></div>
                                        <div><strong>Email :</strong> <span id="p_email"></span></div>
                                        <div><strong>Adresse :</strong> <span id="p_adresse"></span></div>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {

                                        const input = document.getElementById("parentSearch");
                                        const results = document.getElementById("parentResults");
                                        const parentInfo = document.getElementById("parentInfo");

                                        let timeout = null;

                                        input.addEventListener("input", function () {

                                            clearTimeout(timeout);

                                            const query = this.value;

                                            if (query.length < 2) {
                                                results.innerHTML = "";
                                                return;
                                            }

                                            timeout = setTimeout(() => {

                                                fetch("{{ route('parents.search', $slug) }}?q=" + query)
                                                    .then(res => res.json())
                                                    .then(data => {

                                                        results.innerHTML = "";

                                                        data.forEach(parent => {

                                                            const item = document.createElement("div");
                                                            item.classList.add("list-group-item", "list-group-item-action");
                                                            item.style.cursor = "pointer";

                                                            item.innerHTML = `
                                                                <strong>${parent.v_nom_tuteur}</strong><br>
                                                                <small>${parent.v_telephone_tuteur}</small>
                                                            `;

                                                            item.addEventListener("click", function () {

                                                                // remplir input hidden
                                                                document.getElementById("parent_id").value = parent.i_parent_id;

                                                                // afficher infos à droite
                                                                document.getElementById("p_pere").textContent = parent.v_nom_pere ?? '-';
                                                                document.getElementById("p_mere").textContent = parent.v_nom_mere ?? '-';
                                                                document.getElementById("p_tuteur").textContent = parent.v_nom_tuteur ?? '-';
                                                                document.getElementById("p_tel").textContent = parent.v_telephone_tuteur ?? '-';
                                                                document.getElementById("p_email").textContent = parent.v_email_tuteur ?? '-';
                                                                document.getElementById("p_adresse").textContent = parent.v_adresse_tuteur ?? '-';

                                                                parentInfo.classList.remove("d-none");

                                                                results.innerHTML = "";
                                                                input.value = parent.v_nom_tuteur;
                                                            });

                                                            results.appendChild(item);
                                                        });

                                                    });

                                            }, 300);

                                        });

                                    });
                                    </script>
                            </div>
                            <!-- CREATE PARENT -->
                            <div id="createParentBlock" class="row gy-3 d-none">
                                <!-- 👇 TON FORMULAIRE COMPLET ICI -->
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_nom_pere" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nom du père </label>
                                        <input name="v_nom_pere" type="text" class="form-control" id="v_nom_pere" placeholder="Entrez le nom de sont Père">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_telephone_pere" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Numéro télèphone du pére </label>
                                        <input name="v_telephone_pere" type="tel" class="form-control" id="v_telephone_pere" placeholder="Entrez le numéro de télèphone du pére">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_profession_pere" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Profession du père </label>
                                        <input name="v_profession_pere" type="text" class="form-control" id="v_profession_pere" placeholder="Entrez la Profession du père">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div>
                                        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Photo du père
                                        </label>

                                        <label
                                            class="drop-zone height-44-px p-4 d-flex justify-content-center align-items-center text-center fw-medium text-md cursor-pointer border border-neutral-400 radius-8 border-dashed bg-hover-neutral-200">
                                            <span class="drop-zone__prompt">
                                                Déposez un fichier ici ou cliquez
                                            </span>
                                            <input
                                                type="file"
                                                name="v_photo_pere"
                                                class="drop-zone__input d-none">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_nom_mere"   class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nom de la mère </label>
                                        <input name="v_nom_mere" type="text" class="form-control" id="v_nom_mere" placeholder="Entrez le nom de la mère">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_telephone_mere" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Numéro de télèphone de la mère </label>
                                        <input name="v_telephone_mere" type="tel" class="form-control" id="v_telephone_mere" placeholder="Entrez le numéro de télèphone de la mère">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_profession_mere" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Profession de la mère  </label>
                                        <input name="v_profession_mere" type="text" class="form-control" id="v_profession_mere" placeholder="Entrez la profession de la mère">
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div>
                                        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Photo de la mère
                                        </label>

                                        <label
                                            class="drop-zone height-44-px p-4 d-flex justify-content-center align-items-center text-center fw-medium text-md cursor-pointer border border-neutral-400 radius-8 border-dashed bg-hover-neutral-200">
                                            <span class="drop-zone__prompt">
                                                Déposez un fichier ici ou cliquez
                                            </span>
                                            <input
                                                name="v_photo_mere"
                                                type="file"
                                                class="drop-zone__input d-none">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mt-24">
                                        <span class="text-lg fw-bold text-primary-light d-inline-block mb-8">
                                            Sélectionnez un tuteur
                                        </span>
                                        <div class="d-flex align-items-center flex-wrap gap-28">
                                            <div class="form-check checked-primary d-flex align-items-center gap-2">
                                                <input value="Père" class="form-check-input" type="radio" name="v_tuteur_type" id="v_tuteur_type">
                                                <label
                                                    class="form-check-label line-height-1 fw-medium text-secondary-light"
                                                    for="v_tuteur_type">
                                                    Père
                                                </label>
                                            </div>
                                            <div class="form-check checked-secondary d-flex align-items-center gap-2">
                                                <input value="Mère" class="form-check-input" type="radio" name="v_tuteur_type" id="selectMother">
                                                <label
                                                    class="form-check-label line-height-1 fw-medium text-secondary-light"
                                                    for="selectMother">
                                                    Mère
                                                </label>
                                            </div>
                                            <div class="form-check checked-success d-flex align-items-center gap-2">
                                                <input name="Autres" class="form-check-input" type="radio" name="v_tuteur_type" id="selectOthers">
                                                <label
                                                    class="form-check-label line-height-1 fw-medium text-secondary-light"
                                                    for="selectOthers">
                                                    Autres
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_nom_tuteur"  class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Nom du tuteur
                                        </label>
                                        <input type="text" class="form-control" name="v_nom_tuteur" id="v_nom_tuteur" placeholder="Saisissez le nom du tuteur">
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_email_tuteur" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            E-mail du tuteur
                                        </label>
                                        <input type="email" class="form-control" name="v_email_tuteur" id="v_email_tuteur" placeholder="Saisissez l'e-mail du tuteur">
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_telephone_tuteur" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Numéro de téléphone
                                        </label>
                                        <input type="tel" class="form-control" name="v_telephone_tuteur" id="v_telephone_tuteur" placeholder="Saisissez le numéro du tuteur">
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="v_profession_tuteur" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Profession du Titeur
                                        </label>
                                        <input type="text" class="form-control" name="v_profession_tuteur" id="v_profession_tuteur" placeholder="Saisissez la profession du Titeur">
                                    </div>
                                </div>

                                <div class="col-xl-9 col-sm-6">
                                    <div class="">
                                        <label for="v_adresse_tuteur" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Adresse du tuteur
                                        </label>
                                        <input type="text" class="form-control" name="v_adresse_tuteur" id="v_adresse_tuteur" placeholder="Saisissez l'adresse du tuteur">
                                    </div>
                                </div>

                                <div class="col-xl-3 col-sm-6">
                                    <div class="">
                                        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Photo du tuteur <span class="text-danger-600">*</span>
                                        </label>

                                        <label class="drop-zone height-44-px p-4 d-flex justify-content-center align-items-center text-center fw-medium text-md cursor-pointer border border-neutral-400 radius-8 border-dashed bg-hover-neutral-200">
                                            <span class="drop-zone__prompt">
                                                Glissez-déposez un fichier ici ou cliquez
                                            </span>
                                            <input type="file" name="v_photo_tuteur" class="d-none">
                                        </label>
                                    </div>
                                </div>

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
                                            <option value="" disabled selected>
                                                Sélectionnez un groupe sanguin
                                            </option>

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

                                        <input type="text" name="v_taille" class="form-control" id="taille">
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="">
                                        <label for="poid" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Poids
                                        </label>
                                        <input type="text" name="v_poids" class="form-control" id="poid">
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
                                        <label for="v_nom_etablissement" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Nom de l'établissement
                                        </label>
                                        <input name="v_nom_etablissement" type="text" class="form-control" id="v_nom_etablissement" placeholder="Saisissez le nom de l'établissement">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="">
                                        <label for="v_adresse_etablissement" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                                            Adresse
                                        </label>
                                        <input name="v_adresse_etablissement" type="text" class="form-control" id="v_adresse_etablissement" placeholder="Saisissez l'adresse">
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
                                        <textarea name="t_details_eleve" id="moreDetails" class="form-control" placeholder="Entrez les dètails"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-center gap-3 mt-8">

                        <button type="reset"
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
        document.getElementById('selectParent').addEventListener('change', function () {
            document.getElementById('selectParentBlock').classList.remove('d-none');
            document.getElementById('createParentBlock').classList.add('d-none');
        });
        document.getElementById('createParent').addEventListener('change', function () {
            document.getElementById('createParentBlock').classList.remove('d-none');
            document.getElementById('selectParentBlock').classList.add('d-none');
        });
    </script>
    <script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const resetBtn = document.querySelector('button[type="reset"]');
    const submitBtn = document.querySelector('button[type="submit"]');

    if (!form) {
        console.error("Formulaire introuvable");
        return;
    }

    // =========================
    // RESET (ANNULER)
    // =========================
    if (resetBtn) {
        resetBtn.addEventListener("click", function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Annuler la saisie ?",
                text: "Toutes les informations seront effacées.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, annuler",
                cancelButtonText: "Non",
                confirmButtonColor: "#d33"
            }).then((result) => {

                if (result.isConfirmed) {

                    form.reset();

                    Swal.fire({
                        icon: "success",
                        title: "Formulaire réinitialisé",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
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
            title: "Confirmer l'enregistrement ?",
            text: "L'élève va être enregistré.",
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
