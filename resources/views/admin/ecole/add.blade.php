@extends('admin.layout.app')

@section('container')

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
            <a href="{{ route('index.ecole') }}"
               class="btn btn-outline-primary px-24 py-10 radius-8">
                ← Retour
            </a>
        </div>

    </div>

    <form id="ecoleForm"
          action="{{ route('ecoles.store') }}"
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

                            <!-- Etablissement -->
                            <div class="col-md-6">

                                <label class="text-sm fw-semibold mb-8">
                                    Établissement <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="i_idetablissement"
                                    class="form-control @error('i_idetablissement') is-invalid @enderror">

                                    <option value="">
                                        -- Sélectionner un établissement --
                                    </option>

                                    @foreach($etablissements as $etablissement)

                                        <option
                                            value="{{ $etablissement->i_idetablissement }}"
                                            {{ old('i_idetablissement') == $etablissement->i_idetablissement ? 'selected' : '' }}>

                                            {{ $etablissement->v_nometablissement }}

                                        </option>

                                    @endforeach

                                </select>

                                @error('i_idetablissement')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Nom école -->
                            <div class="col-md-6">

                                <label class="text-sm fw-semibold mb-8">
                                    Nom de l'école <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="v_nomecole"
                                    value="{{ old('v_nomecole') }}"
                                    class="form-control @error('v_nomecole') is-invalid @enderror"
                                    placeholder="Nom de l'école">

                                @error('v_nomecole')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Téléphone 1 -->
                            <div class="col-md-3">

                                <label class="text-sm fw-semibold mb-8">
                                    Téléphone principal
                                </label>

                                <input
                                    type="text"
                                    name="v_telephone1ecole"
                                    value="{{ old('v_telephone1ecole') }}"
                                    class="form-control @error('v_telephone1ecole') is-invalid @enderror"
                                    placeholder="+224 XXX XXX XXX">

                                @error('v_telephone1ecole')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Téléphone 2 -->
                            <div class="col-md-3">

                                <label class="text-sm fw-semibold mb-8">
                                    Téléphone secondaire
                                </label>

                                <input
                                    type="text"
                                    name="v_telephone2ecole"
                                    value="{{ old('v_telephone2ecole') }}"
                                    class="form-control @error('v_telephone2ecole') is-invalid @enderror"
                                    placeholder="+224 XXX XXX XXX">

                                @error('v_telephone2ecole')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Email -->
                            <div class="col-md-3">

                                <label class="text-sm fw-semibold mb-8">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="v_adressemailv_telephone1ecole"
                                    value="{{ old('v_adressemailv_telephone1ecole') }}"
                                    class="form-control @error('v_adressemailv_telephone1ecole') is-invalid @enderror"
                                    placeholder="email@exemple.com">

                                @error('v_adressemailv_telephone1ecole')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Directeur -->
                            <div class="col-md-3">

                                <label class="text-sm fw-semibold mb-8">
                                    Nom du directeur
                                </label>

                                <input
                                    type="text"
                                    name="v_nomdirecteurecole"
                                    value="{{ old('v_nomdirecteurecole') }}"
                                    class="form-control @error('v_nomdirecteurecole') is-invalid @enderror"
                                    placeholder="Nom du directeur">

                                @error('v_nomdirecteurecole')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Code école -->
                            <div class="col-md-4">

                                <label class="text-sm fw-semibold mb-8">
                                    Code école
                                </label>

                                <input
                                    type="text"
                                    name="v_codeecole"
                                    value="{{ old('v_codeecole') }}"
                                    class="form-control @error('v_codeecole') is-invalid @enderror"
                                    placeholder="Code école">

                                @error('v_codeecole')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Logo -->
                            <div class="col-md-4">

                                <label class="text-sm fw-semibold mb-8">
                                    Logo
                                </label>

                                <input
                                    type="file"
                                    name="logo"
                                    class="form-control @error('logo') is-invalid @enderror"
                                    accept="image/*">

                                @error('logo')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Adresse -->
                            <div class="col-md-12">

                                <label class="text-sm fw-semibold mb-8">
                                    Adresse
                                </label>

                                <textarea
                                    name="t_adresseecole"
                                    class="form-control @error('t_adresseecole') is-invalid @enderror"
                                    placeholder="Adresse complète">{{ old('t_adresseecole') }}</textarea>

                                @error('t_adresseecole')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <!-- Champs cachés -->
                            <input type="hidden"
                                   name="i_userID"
                                   value="{{ auth()->id() }}">

                            <input type="hidden"
                                   name="bt_etat_ecole"
                                   value="1">

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
            text: "Voulez-vous vraiment enregistrer cette école ?",
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

@endsection
