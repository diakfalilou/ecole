@extends('admin.layout.app')
@section('container')

<div class="dashboard-main-body">
        <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div>
                <h1 class="fw-semibold mb-4 h6 text-primary-light"> Ajouter un nouveau contrat    </h1>
                <div>
                    <a href="{{route('index.home.admin')}}" class="text-secondary-light hover-text-primary hover-underline">
                        Acceuille
                    </a>
                    <a href="{{route('index.contrat')}}" class="text-secondary-light hover-text-primary hover-underline ">/ Contrat</a>
                    <span class="text-secondary-light">
                        / Ajouter </span>
                    </div>
            </div>

            <a href="add-new-student.html"
            class="btn btn-primary-600 d-flex align-items-center gap-6 d-none">
            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>
                Liste des contrats    </a>
        </div>
        <form action="{{ route('contrat.store') }}" method="POST" id="contratForm">
            @csrf
            <div class="row gy-3">
                <div class="col-lg-12">
                    <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                        <div
                            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                            <h6 class="text-lg fw-semibold mb-0">Remplir le formulaire et valider</h6>
                        </div>
                        <div class="card-body p-20">
                            <div class="row gy-3">
                                <div class="col-xxl-6 col-xl-6 col-sm-6">
                                    <div class="">
                                        <label for="academicYear" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année scolaire
                                        <span class="text-danger-600">* </span> </label>
                                        <select name="annee_scolaire" id="academicYear" class="form-control form-select" required>
                                            <option label="Séléctionner l'année scolaire"></option>
                                            @foreach ($anneescolaires as $annee)
                                                <option value="{{$annee->v_annesclaire}}">{{$annee->v_annesclaire}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-xl-6 col-sm-6">
                                    <div class="">
                                        <label for="section" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Etablissement
                                        <span class="text-danger-600">* </span> </label>
                                        <select name="etablissement_id" id="etablissement_id" class="form-control form-select" required>
                                            <option value="">Séléctionner un établissement</option>

                                            @foreach ($etablissemnt as $etab)
                                                <option value="{{ $etab->i_idetablissement }}">
                                                    {{ $etab->v_nometablissement }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-xl-6 col-sm-6">
                                    <div class="">
                                        <label for="ecole_id" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Ecole
                                            <span class="text-danger-600">* </span>
                                        </label>
                                        <select name="ecole_id" id="ecole_id" class="form-control form-select" required>
                                            <option value="">Sélectionner une école</option>
                                        </select>

                                        <div id="loaderEcole" style="display:none;">
                                            <small class="text-primary">
                                                <i class="fas fa-spinner fa-spin"></i>
                                                Chargement des écoles...
                                            </small>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xxl-6 col-xl-6 col-sm-6">
                                    <div class="">
                                        <label for="datedebut" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date début </label>
                                        <input type="date" name="datedebut" class="form-control" id="datedebut" required>
                                    </div>
                                </div>
                                <div class="col-xxl-6 col-xl-6 col-sm-6">
                                    <div class="">
                                        <label for="datefin" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date fin
                                            <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="date" name="datefin" class="form-control" id="datefin" required>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-xl-6 col-sm-6">
                                    <div class="">
                                        <label for="montant"  class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant
                                            <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="number" name="montant" class="form-control" id="montant" required>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-center gap-3 mt-8">

                       <button type="reset"
                        id="cancelContratBtn"
                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">

                            <i class="ri-close-circle-line me-2"></i>
                            Annuler

                        </button>

                        <button type="submit"
                            class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">

                            <i class="ri-save-3-line me-2"></i>
                            Enregistrer le contrat

                        </button>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $('#etablissement_id').on('change', function () {
                let id = $(this).val();
                // console.log('Etablissement sélectionné :', id);
                $('#ecole_id').html(`
                    <option value="">
                        ⏳ Chargement des écoles...
                    </option>
                `);
                $.ajax({
                    url: "{{ url('ecoles') }}/" + id,
                    type: "GET",
                    success: function (data) {
                        console.log(data);

                        let options =
                            '<option value="">Sélectionner une école</option>';

                        if (data.length === 0) {
                            options =
                                '<option value="">Aucune école trouvée</option>';
                        }

                        $.each(data, function (index, ecole) {

                            options += `
                                <option value="${ecole.i_idecole}">
                                    ${ecole.v_nomecole}
                                </option>
                            `;
                        });

                        $('#ecole_id').hide().html(options).fadeIn(500);
                    },

                    error: function (xhr) {

                        console.log(xhr);

                        $('#ecole_id').html(`
                            <option value="">
                                ❌ Erreur de chargement
                            </option>
                        `);
                    }
                });

            });

        });
    </script>
    <script>
        document.getElementById('contratForm').addEventListener('submit', function(e){

            e.preventDefault();

            Swal.fire({
                title: 'Confirmation',
                text: 'Confirmez-vous l\'enregistrement du contrat ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result)=>{

                if(result.isConfirmed){

                    Swal.fire({
                        title: 'Enregistrement en cours...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    this.submit();
                }

            });

        });


        document.getElementById('cancelContratBtn').addEventListener('click', function(e){

            e.preventDefault();

            Swal.fire({
                title: 'Confirmation',
                text: 'Voulez-vous réellement annuler la saisie ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Non'
            }).then((result)=>{

                if(result.isConfirmed){

                    Swal.fire({
                        title: 'Annulation...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 1000,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        document.getElementById('contratForm').reset();
                    }, 1000);

                }

            });

        });

    </script>
@endsection
