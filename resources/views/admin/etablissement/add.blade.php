@extends('admin.layout.app')
@section('container')
<div class="dashboard-main-body">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <!-- LEFT -->
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">
                Ajout d'un établissement
            </h1>
            <div>
                <a href="index-2.html" class="text-secondary-light hover-text-primary hover-underline">
                    Dashboard
                </a>
                <a href="teacher-list.html" class="text-secondary-light hover-text-primary hover-underline">
                    / Etablissement
                </a>
                <span class="text-secondary-light">
                    / Ajouter un établissement
                </span>
            </div>
        </div>
        <!-- RIGHT (BUTTON BACK) -->
        <div>
            <a href="{{ route('index.etablissement') }}"
            class="btn btn-outline-primary px-24 py-10 radius-8">
                ← Retour
            </a>
        </div>
    </div>

    <form id="etablissementForm" action="{{ route('etablissements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row gy-3">
            <div class="col-lg-12">
                <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                    <div class="card-header border-bottom bg-base py-16 px-24">
                        <h6 class="text-lg fw-semibold mb-0">Informations de l’établissement</h6>
                    </div>

                    <div class="card-body p-20">
                        <div class="row gy-3">

                            <!-- Nom établissement -->
                            <div class="col-md-6">
                                <label class="text-sm fw-semibold mb-8">
                                    Nom de l’établissement <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="v_nometablissement"
                                    value="{{ old('v_nometablissement') }}"
                                    class="form-control @error('v_nometablissement') is-invalid @enderror"
                                    placeholder="Nom de l’établissement">
                                @error('v_nometablissement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone 1 -->
                            <div class="col-md-3">
                                <label class="text-sm fw-semibold mb-8">Téléphone principal</label>
                                <input type="text"
                                    name="v_telephone1etablissement"
                                    value="{{ old('v_telephone1etablissement') }}"
                                    class="form-control @error('v_telephone1etablissement') is-invalid @enderror"
                                    placeholder="+224 XXX XXX XXX">
                                @error('v_telephone1etablissement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone 2 -->
                            <div class="col-md-3">
                                <label class="text-sm fw-semibold mb-8">Téléphone secondaire</label>
                                <input type="text"
                                    name="v_telephone2etablissement"
                                    value="{{ old('v_telephone2etablissement') }}"
                                    class="form-control @error('v_telephone2etablissement') is-invalid @enderror" placeholder="+224 XXX XXX XXX">
                                @error('v_telephone2etablissement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-8">Email</label>
                                <input type="email"
                                    name="v_adressemailv_telephone1etablissement"
                                    value="{{ old('v_adressemailv_telephone1etablissement') }}"
                                    class="form-control @error('v_adressemailv_telephone1etablissement') is-invalid @enderror"
                                    placeholder="email@exemple.com">
                                @error('v_adressemailv_telephone1etablissement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fondateur -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-8">Nom du fondateur</label>
                                <input type="text"
                                    name="v_nomfondateurv_telephone1etablissement"
                                    value="{{ old('v_nomfondateurv_telephone1etablissement') }}"
                                    class="form-control @error('v_nomfondateurv_telephone1etablissement') is-invalid @enderror"
                                    placeholder="Nom du fondateur">
                                @error('v_nomfondateurv_telephone1etablissement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fondateur -->
                            <div class="col-md-4">
                                <label class="text-sm fw-semibold mb-8">Logo</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>

                            <!-- Adresse -->
                            <div class="col-md-12">
                                <label class="text-sm fw-semibold mb-8">Adresse</label>
                                <textarea name="t_adresseetablissement"
                                        class="form-control @error('t_adresseetablissement') is-invalid @enderror"
                                        placeholder="Adresse complète">{{ old('t_adresseetablissement') }}</textarea>
                                @error('t_adresseetablissement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Champs cachés -->
                            <input type="hidden" name="i_userID" value="{{ auth()->id() }}">
                            <input type="hidden" name="bt_etat_etablissement" value="1">

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

        const form = document.getElementById('etablissementForm');

        form.addEventListener('submit', function (e) {

            if (form.dataset.submitted) {
                return;
            }

            e.preventDefault();

            Swal.fire({
                title: 'Confirmation',
                text: "Voulez-vous vraiment enregistrer cet établissement ?",
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
