<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\admin\indexHomeController;
use App\Http\Controllers\admin\AnneeScolaireController;
use App\Http\Controllers\admin\IndexEtablissementController;
use App\Http\Controllers\admin\AddEtablissementController;
use App\Http\Controllers\admin\IndexEcoleController;
use App\Http\Controllers\admin\EditEcoleController;
use App\Http\Controllers\admin\AddEcoleController;
use App\Http\Controllers\admin\IndexContratController;
use App\Http\Controllers\admin\AddContratController;
use App\Http\Controllers\Admin\AddMenuController;
use App\Http\Controllers\Admin\IndexMenuController;
use App\Http\Controllers\Admin\IndexUsersController;
use App\Http\Controllers\Admin\AddUsersController;
use App\Http\Controllers\Admin\UserMenuController;
use App\Http\Controllers\ecole\EcoleController;
use App\Http\Controllers\ecole\EcoleDashboardController;
use App\Http\Controllers\ecole\ProfilUserController;
use App\Http\Controllers\ecole\IndexEleveController;
use App\Http\Controllers\ecole\CreatEleveController;
use App\Http\Controllers\ecole\IndexNiveauController;
use App\Http\Controllers\ecole\SectionController;
use App\Http\Controllers\ecole\ClasseController;
use App\Http\Controllers\ecole\DetailEleveController;
use App\Http\Controllers\ecole\EditEleveController;
use App\Http\Controllers\ecole\ListeInscriReinscritController;
use App\Http\Controllers\ecole\UpdateEtudiantInscritController;
use App\Http\Controllers\ecole\AttestationNiveauController;
use App\Http\Controllers\ecole\BadgeController;
use App\Http\Controllers\ecole\CertificatScolariteController;
use App\Http\Controllers\ecole\UpdateInfoInscriptionController;
use App\Http\Controllers\ecole\SettingClassesController;
use App\Http\Controllers\ecole\AttestationInscriptionReinscriptionController;
use App\Http\Controllers\ecole\SettingTrancheCntroller;
use App\Http\Controllers\ecole\PaiementScolariteController;
use App\Http\Controllers\ecole\ExonorationController;
use App\Http\Controllers\ecole\RapportPaiementPeriodiqueController;
use App\Http\Controllers\ecole\SituationGlePaiementController;
use App\Http\Controllers\ecole\CaisseController;
use App\Http\Controllers\ecole\CompteBancaireController;
use App\Http\Controllers\ecole\PressanceController;
use App\Http\Controllers\ecole\DepenseController;
use App\Http\Controllers\ecole\MatiereController;




use Illuminate\Support\Facades\DB;



Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/', [AuthController::class, 'dologin'])->name('auth.login');

Route::middleware('auth')->group(function () {
    Route::get('home-admin', [indexHomeController::class, 'index_home'])->name('index.home.admin');
    Route::get('annee-scolaire', [AnneeScolaireController::class, 'annee_scolaire'])->name('index.annee.scolaire');
    Route::post('/annee-scolaire/store',[AnneeScolaireController::class, 'store'])->name('annee.scolaire.store');
    Route::delete('/annee-scolaire/delete/{id}', [AnneeScolaireController::class, 'destroy'])->name('annee.scolaire.delete');
    Route::get('etablissement', [IndexEtablissementController::class, 'index_etablissement'])->name('index.etablissement');
    Route::get('/etablissements/edit/{id}', [AddEtablissementController::class, 'edit'])->name('etablissements.edit');
    Route::delete('/etablissements/delete/{id}', [AddEtablissementController::class, 'delete'])->name('etablissements.delete');
    Route::get('add-etablissement', [AddEtablissementController::class, 'add_etablissement'])->name('add.etablissement');
    Route::post('/admin/etablissement/store', [AddEtablissementController::class, 'store'])->name('etablissements.store');
    Route::get('/ecoles', [IndexEcoleController::class, 'index'])->name('index.ecole');
    Route::get('/ecoles-ajouter', [AddEcoleController::class, 'add_ecole'])->name('add.ecole');
    Route::post('/ecoles/store', [AddEcoleController::class, 'store'])->name('ecoles.store');
    Route::get('ecoles-edite/{id_ecole}', [EditEcoleController::class, 'edit_ecole'])->name('ecoles.edite');
    Route::put('ecoles-edite/{id_ecole}', [EditEcoleController::class, 'update_ecole'])->name('ecoles.update');


    Route::get('contrats', [IndexContratController::class, 'index_contrat'])->name('index.contrat');
    Route::patch('/contrat/{id}/status',[IndexContratController::class, 'toggleStatus'])->middleware('auth')->name('contrat.status');
    Route::get('add-contrats', [AddContratController::class, 'add_contrat'])->name('add.contrat');
    Route::get('/ecoles/{idEtablissement}', [AddContratController::class, 'getEcoles'])->name('get.ecoles');
    Route::post('/contrat/store',[AddContratController::class, 'store'])->middleware('auth')->name('contrat.store');

    Route::get('menus', [IndexMenuController::class, 'index_menu'])->name('index.menus');
    Route::post('/menus/store', [IndexMenuController::class, 'store_menu'])->name('menus.store');
    Route::post('/sousmenus/store', [IndexMenuController::class, 'store_sousmenu'])->name('sousmenus.store');
    Route::get('add-menus', [AddMenuController::class, 'add_menu'])->name('add.menus');
    Route::get('utilusateurs', [IndexUsersController::class, 'index_users'])->name('index.users');
    Route::get('add-utilusateurs', [AddUsersController::class, 'add_users'])->name('add.users');
    Route::get('/get-ecoles/{id}', [AddUsersController::class, 'getEcoles']);
    Route::post('/users/store', [AddUsersController::class, 'store'])->name('users.store');
    Route::get('/setting-users', [UserMenuController::class, 'index'])->name('users.menus');
    Route::post('/setting-users', [UserMenuController::class, 'store'])->name('users.menus.store');
    Route::post('/admin/user-menu/ajax', [UserMenuController::class, 'storeAjax'])->name('user.menu.ajax');
    Route::get('/menus/{id}/sousmenus', function ($id) {
        return DB::table('tblsousmenus')
            ->where('menu_id', $id)
            ->orderBy('ordre_sousmenu')
            ->get();
    });
});
Route::get('/{slug}', [EcoleController::class, 'login'])->name('ecole.login');
Route::post('/{slug}', [EcoleController::class, 'doLogin'])->name('ecole.do.login');
Route::post('/ecole/logout', [EcoleController::class, 'logout'])->name('ecole.logout');
Route::get('/{slug}/dashboard', [EcoleDashboardController::class, 'index'])->name('ecole.dashboard')->middleware('auth');
Route::get('/{slug}/user-profil', [ProfilUserController::class, 'profil_user'])->name('user.profil')->middleware('auth');
Route::post('/{slug}/user-profil/update', [ProfilUserController::class, 'updateProfil'])->name('user.profil.update')->middleware('auth');
Route::get('/{slug}/liste-eleves', [IndexEleveController::class, 'index_eleves'])->name('index.eleves')->middleware('auth');
Route::get('/{slug}/students.create', [CreatEleveController::class, 'create'])->middleware('auth')->name('students.create');
Route::get('/{slug}/students.inscription', [CreatEleveController::class, 'create'])->middleware('auth')->name('students.inscription');

Route::get('/get-classes-by-niveau/{niveau_id}',[CreatEleveController::class, 'getClassesByNiveau'])->middleware('auth');
Route::post('/{slug}/students.create', [CreatEleveController::class, 'store_eleve'])->middleware('auth')->name('students.store');

Route::get('/{slug}/parents/search', [CreatEleveController::class, 'searchParent'])->middleware('auth')->name('parents.search');
Route::get('/{slug}/generate-matricule', function ($slug, Request $request) {
    // 1. récupérer l'école
    $ecole = DB::table('tbecole')
        ->where('v_slugecole', $slug)
        ->first();
    $ecole_id = $ecole->i_idecole;
    // 2. générer les initiales (Groupe Scolaire Diak Balde => GSDTB => mais on limite à 3)
    $words = explode('-', $slug);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    // exemple: groupe-scolaire-diak-balde => GSDB
    // si tu veux limiter à 3 lettres :
    $initials = strtoupper(substr($initials, 0, 3));
    // 3. compteur par école + année
    $year = date('Y');
    $last = DB::table('tbleleve')
        ->where('i_ecole_id', $ecole_id)
        ->whereYear('d_datecreation', $year)
        ->where('v_matricule', 'like', $initials . '%')
        ->count();
    $nextNumber = str_pad($last + 1, 2, "0", STR_PAD_LEFT);
    // 4. matricule final
    $matricule = $initials . '-' . $nextNumber;
    return response()->json([
        'matricule' => $matricule
    ]);
});

Route::get('/{slug}/niveau', [IndexNiveauController::class, 'index_niveau'])->middleware('auth')->name('index.nivaux');
Route::post('/{slug}/niveau',[IndexNiveauController::class, 'store'])->middleware('auth')->name('niveau.store');
Route::put('/{slug}/niveau/{id}',[IndexNiveauController::class, 'update'])->middleware('auth')->name('niveau.update');
Route::patch('/{slug}/niveau/{id}/status',[IndexNiveauController::class, 'toggleStatus'])->middleware('auth')->name('niveau.status');
Route::get('/{slug}/section',[SectionController::class, 'index'])->middleware('auth')->name('section.index');
Route::post('/{slug}/section',[SectionController::class, 'store'])->middleware('auth')->name('section.store');
Route::put('/{slug}/section/{id}',[SectionController::class, 'update'])->middleware('auth')->name('section.update');
Route::patch('/{slug}/section/{id}/status',[SectionController::class, 'toggleStatus'])->middleware('auth')->name('section.status');
Route::get('/{slug}/classe', [ClasseController::class, 'classes'])->middleware('auth')->name('index.classe');
Route::post('/{slug}/classe', [ClasseController::class, 'store'])->middleware('auth')->name('classe.store');
Route::put('/{slug}/classe/{id}', [ClasseController::class, 'update'])->middleware('auth')->name('classe.update');
Route::patch('/{slug}/classe/{id}/status', [ClasseController::class, 'toggleStatus'])->middleware('auth')->name('classe.status');

Route::get('/{slug}/liste.inscription.reinscription', [ListeInscriReinscritController::class, 'inscription_reinscription'])
    ->middleware('auth')
    ->name('liste.inscription.reinscription');
Route::get('/{slug}/detail-eleve/{id}', [DetailEleveController::class, 'detail_eleve'])->middleware('auth')->name('detail.eleve');
Route::get('/{slug}/edit-eleve/{id}', [EditEleveController::class, 'edit_eleve'])->middleware('auth')->name('edit.eleve');
Route::post('/{slug}/edit-eleve/{id}', [EditEleveController::class, 'update_eleve'])->middleware('auth')->name('edit.eleve');

Route::get('/{slug}/update-eleve/{id}', [UpdateEtudiantInscritController::class, 'update_eleve_inscrit'])->middleware('auth')->name('update.eleve');
Route::post('/{slug}/update-eleve/{id}', [UpdateEtudiantInscritController::class, 'save_update_eleve_inscrit'])->middleware('auth')->name('update.eleve');


Route::get('/{slug}/Attestation-niveau/{id}', [AttestationNiveauController::class, 'generate_attestation_niveau'])->middleware('auth')->name('attestation.niveau');
Route::get('/{slug}/badge/{id}', [BadgeController::class, 'generate_badge'])->middleware('auth')->name('badge');
Route::get('/{slug}/cetificat-scolarite/{id}', [CertificatScolariteController::class, 'generate_certificat_scolarite'])->middleware('auth')->name('cetificat.scolarite');

Route::get('/{slug}/update-inscription/{id}', [UpdateInfoInscriptionController::class, 'update_inscription'])->middleware('auth')->name('update.inscription');
Route::post('/{slug}/update-inscription/{id}', [UpdateInfoInscriptionController::class, 'store_update'])->middleware('auth')->name('update.inscription');


Route::get('/{slug}/attestation-inscription-reinscription/{id}', [AttestationInscriptionReinscriptionController::class, 'genererAttestation'])->middleware('auth')->name('attestation.inscription.reinscription');

Route::get('/{slug}/setting.classes', [SettingClassesController::class, 'setting_classes'])->middleware('auth')->name('setting.classes');
Route::get('/{slug}/modalites', [SettingClassesController::class, 'getModalitesByAnnee'])->middleware('auth')->name('modalites.byannee');
Route::post('/{slug}/modalites/update', [SettingClassesController::class, 'updateModalite'])->middleware('auth')->name('modalites.update');

Route::get('/{slug}/setting.tranche', [SettingTrancheCntroller::class, 'setting_tranche'])->middleware('auth')->name('setting.tranche');
Route::get('/{slug}/tranches', [SettingTrancheCntroller::class, 'getTranches'])->middleware('auth')->name('tranches.byannee');
Route::post('/{slug}/tranches/update', [SettingTrancheCntroller::class, 'updateTranche'])->middleware('auth')->name('tranches.update');


Route::get('/{slug}/paiement.scolarite', [PaiementScolariteController::class, 'paiement_scolarite'])->middleware('auth')->name('paiement.scolarite');
Route::get('/get-eleves-by-classe/{classeId}', [PaiementScolariteController::class, 'getElevesByClasse'])->middleware('auth');
Route::get('/get-eleve-info/{eleveId}', [PaiementScolariteController::class, 'getEleveInfo'])->middleware('auth');
Route::get('/{slug}/get-montant-tranche', [PaiementScolariteController::class, 'getMontantTranche'])->middleware('auth');
Route::post('/{slug}/enregistrer-paiement', [PaiementScolariteController::class, 'enregistrerPaiement'])->middleware('auth');
Route::get('/{slug}/get-statut-paiement/{eleveId}', [PaiementScolariteController::class, 'getStatutPaiement'])->middleware('auth');
Route::get('/{slug}/get-historique-paiement/{eleveId}', [PaiementScolariteController::class, 'getHistoriquePaiement'])->middleware('auth');


Route::get('/{slug}/exoneration', [ExonorationController::class, 'exoneration'])->middleware('auth')->name('exoneration');
Route::get('/{slug}/get-modalite-exoneration', [ExonorationController::class, 'getModaliteExoneration'])->middleware('auth');
Route::get('/{slug}/get-historique-exoneration/{eleveId}', [ExonorationController::class, 'getHistoriqueExoneration'])->middleware('auth');
Route::post('/{slug}/enregistrer-exoneration', [ExonorationController::class, 'enregistrerExoneration'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/{slug}/caisse', [CaisseController::class, 'caisse'])->name('caisse');
    Route::get('/{slug}/caisse/list', [CaisseController::class, 'getCaisses'])->name('caisse.list');
    Route::post('/{slug}/caisse/store', [CaisseController::class, 'store'])->name('caisse.store');
    Route::post('/{slug}/caisse/alimenter', [CaisseController::class, 'alimenter'])->name('caisse.alimenter');
    Route::post('/{slug}/caisse/transfert', [CaisseController::class, 'transfert'])->name('caisse.transfert');
    Route::post('/{slug}/caisse/{id}/suspendre', [CaisseController::class, 'suspendre'])->name('caisse.suspendre');
    Route::get('/{slug}/caisse/{id}/mouvements', [CaisseController::class, 'mouvements'])->name('caisse.mouvements');

    Route::get('/{slug}/compte-bancaire', [CompteBancaireController::class, 'compteBancaire'])->name('compte-bancaire');
    Route::get('/{slug}/compte-bancaire/list', [CompteBancaireController::class, 'getComptes'])->name('compte-bancaire.list');
    Route::post('/{slug}/compte-bancaire/store', [CompteBancaireController::class, 'store'])->name('compte-bancaire.store');
    Route::post('/{slug}/compte-bancaire/alimenter', [CompteBancaireController::class, 'alimenter'])->name('compte-bancaire.alimenter');
    Route::post('/{slug}/compte-bancaire/transfert', [CompteBancaireController::class, 'transfert'])->name('compte-bancaire.transfert');
    Route::post('/{slug}/compte-bancaire/{id}/suspendre', [CompteBancaireController::class, 'suspendre'])->name('compte-bancaire.suspendre');
    Route::get('/{slug}/compte-bancaire/{id}/mouvements', [CompteBancaireController::class, 'mouvements'])->name('compte-bancaire.mouvements');


    Route::get('/{slug}/pressance', [PressanceController::class, 'pressance'])->name('pressance');
    Route::get('/{slug}/pressance/classes', [PressanceController::class, 'getClasses'])->name('pressance.classes');
    Route::get('/{slug}/pressance/annees', [PressanceController::class, 'getAnnees'])->name('pressance.annees');
    Route::get('/{slug}/pressance/eleves', [PressanceController::class, 'getElevesAppel'])->name('pressance.eleves');
    Route::post('/{slug}/pressance/save', [PressanceController::class, 'saveAppel'])->name('pressance.save');
    Route::get('/{slug}/pressance/historique', [PressanceController::class, 'historique'])->name('pressance.historique');
    Route::get('/{slug}/pressance/stats', [PressanceController::class, 'statsEleve'])->name('pressance.stats');

    // Route::get('/{slug}/rapport.periodique.paiement', [RapportPaiementPeriodiqueController::class, 'rapport_periodique_paiement'])->name('rapport.periodique.paiement');
    Route::get('/{slug}/rapport.paiement.periodique', [RapportPaiementPeriodiqueController::class, 'rapport_periodique_paiement'])->name('rapport.paiement.periodique');
    Route::get('/{slug}/situation.general.classe', [SituationGlePaiementController::class, 'situation_gen_paiement'])->name('situation.general.classe');

    Route::get('/{slug}/depense', [DepenseController::class, 'depense'])->name('depense');
    Route::get('/{slug}/depense/list', [DepenseController::class, 'getDepenses'])->name('depense.list');
    Route::get('/{slug}/depense/sources', [DepenseController::class, 'getSources'])->name('depense.sources');
    Route::post('/{slug}/depense/store', [DepenseController::class, 'store'])->name('depense.store');
    Route::post('/{slug}/depense/{id}/annuler', [DepenseController::class, 'annuler'])->name('depense.annuler');


    Route::get('/{slug}/matiere', [MatiereController::class, 'matiere'])->name('matiere');
    Route::post('/{slug}/matiere/store', [MatiereController::class, 'store'])->name('matiere.store');



});


