<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\EleveAuthController;
use App\Http\Controllers\Auth\EntrepriseAuthController;
use App\Http\Controllers\Auth\IntervenantAuthController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\EcheanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TuteurController;
use App\Http\Controllers\AnnotationController;
use App\Http\Controllers\ArchivesReunionController;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ConfigPdfController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CloudController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\EntreprisePortailController;
use App\Http\Controllers\EpreuveAdmissionController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\GroupeEleveController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\HelvetiusExportController;
use App\Http\Controllers\IntervenantController;
use App\Http\Controllers\IntervenantInscriptionController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ParametrageController;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\TypePieceEntrepriseController;
use App\Http\Controllers\SpecialisationController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\NiveauOptionController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PanneauController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PeriodeFormationController;
use App\Http\Controllers\ReferentielVacanceController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RecapitulatifController;
use App\Http\Controllers\ReunionInformationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\SiteConfigController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\BulletinV2Controller;
use App\Http\Controllers\StudentSpaceController;
use App\Http\Controllers\TeacherSpaceController;
use App\Http\Controllers\TexteEmailController;
use App\Http\Controllers\TypeEvaluationController;
use App\Http\Controllers\UniteEnseignementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/demo', [DemoController::class, 'store'])->name('demo.store');

// Authentification back-office (portage du controller Admin.php CI).
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    Route::get('/', function () {
        return view('admin.dashboard');
    })->middleware('auth:admin')->name('dashboard');
});

// Widget public "réunions d'information" (site vitrine, pas d'auth requise — cf. Miracle.php CI).
Route::prefix('miracle')->name('miracle.')->group(function () {
    Route::get('reunions', [ReunionInformationController::class, 'reunions'])->name('reunions');
    Route::get('reunions-tableaux', [ReunionInformationController::class, 'reunionsTableaux'])->name('reunions-tableaux');
    Route::get('reunions-agent', [ReunionInformationController::class, 'reunionsAgent'])->name('reunions-agent');
    Route::get('planning-agent', [ReunionInformationController::class, 'planningAgent'])->name('planning-agent');
});

// Authentification espace intervenant (portage de User_intervenant::login()).
Route::prefix('user-intervenant')->name('intervenant.')->group(function () {
    Route::get('login', [IntervenantAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [IntervenantAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [IntervenantAuthController::class, 'logout'])
        ->middleware('auth:intervenant')
        ->name('logout');

    Route::get('/', function () {
        return view('intervenant.dashboard');
    })->middleware('auth:intervenant')->name('dashboard');
});

// Espace intervenant — récapitulatif d'heures (portage de Recapitulatif.php).
Route::middleware('auth:intervenant')->prefix('recapitulatif')->name('recapitulatif.')->group(function () {
    Route::get('/', [RecapitulatifController::class, 'index'])->name('index');
    Route::post('/', [RecapitulatifController::class, 'store'])->name('store');
    Route::get('resume', [RecapitulatifController::class, 'resume'])->name('resume');
});

// Espace intervenant — libre-service (portage partiel de User_intervenant.php : planning/classes/trombinoscope).
Route::middleware('auth:intervenant')->prefix('espace-intervenant')->name('espace-intervenant.')->group(function () {
    Route::get('mon-planning', [TeacherSpaceController::class, 'myPlanning'])->name('planning');
    Route::get('mes-classes', [TeacherSpaceController::class, 'myClasses'])->name('classes');
    Route::get('mes-classes/{classe}/eleves', [TeacherSpaceController::class, 'classRoster'])->name('roster');
});

// Authentification + espace élève (portage de User::login()/logout()).
Route::prefix('user')->name('eleve.')->group(function () {
    Route::get('login', [EleveAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [EleveAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [EleveAuthController::class, 'logout'])
        ->middleware('auth:eleve')
        ->name('logout');

    Route::get('/', function () {
        return view('eleve.dashboard');
    })->middleware('auth:eleve')->name('dashboard');
});

// Espace élève — libre-service (portage partiel de User.php : évaluations/planning/bulletins).
Route::middleware('auth:eleve')->prefix('espace-eleve')->name('espace-eleve.')->group(function () {
    Route::get('mes-evaluations', [StudentSpaceController::class, 'myEvaluations'])->name('evaluations');
    Route::get('mon-planning', [StudentSpaceController::class, 'myPlanning'])->name('planning');
    Route::get('mes-bulletins', [StudentSpaceController::class, 'myBulletins'])->name('bulletins');
    Route::get('mes-bulletins/{bulletin}/telecharger', [StudentSpaceController::class, 'downloadBulletin'])->name('bulletins.download');
});

// Authentification + espace entreprise (portage de Entreprises_portail.php).
Route::prefix('entreprises-portail')->name('entreprise.')->group(function () {
    Route::get('login', [EntrepriseAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [EntrepriseAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [EntrepriseAuthController::class, 'logout'])
        ->middleware('auth:entreprise')
        ->name('logout');

    Route::middleware('auth:entreprise')->group(function () {
        Route::get('/', [EntreprisePortailController::class, 'index'])->name('dashboard');
        Route::get('informations', [EntreprisePortailController::class, 'informations'])->name('informations');
        Route::put('informations', [EntreprisePortailController::class, 'updateInformations'])->name('informations.update');
    });
});

// Formulaires publics du site vitrine (pas d'auth requise — cf. Formulaires.php CI).
Route::prefix('formulaires')->name('formulaires.')->group(function () {
    Route::post('contact', [PublicFormController::class, 'storeContact'])->name('contact');
    Route::post('demande-brochure', [PublicFormController::class, 'storeBrochureRequest'])->name('demande-brochure');
});

// Auto-inscription intervenant, pas d'auth requise (portage de Intervenant_amos.php).
Route::prefix('intervenant-inscription')->name('intervenant-inscription.')->group(function () {
    Route::get('/', [IntervenantInscriptionController::class, 'create'])->name('create');
    Route::post('/', [IntervenantInscriptionController::class, 'store'])->name('store');
});

// Modules métier, protégés par le guard admin.
Route::middleware('auth:admin')->group(function () {
    Route::resource('eleves', EleveController::class)
        ->parameters(['eleves' => 'eleve']);

    // Échéanciers de scolarité : frais d'inscription + mensualités, suivi des impayés.
    Route::prefix('echeances')->name('echeances.')->group(function () {
        Route::get('/', [EcheanceController::class, 'index'])->name('index');
        Route::get('generer', [EcheanceController::class, 'formulaire'])->name('generer');
        Route::post('generer', [EcheanceController::class, 'generer'])->name('generer.store');
        Route::post('{echeance}/regler', [EcheanceController::class, 'regler'])->name('regler');
        Route::delete('{echeance}', [EcheanceController::class, 'destroy'])->name('destroy');
    });

    // Assiduité : appel par classe, suivi des absences/retards, justificatifs.
    Route::prefix('absences')->name('absences.')->group(function () {
        Route::get('/', [AbsenceController::class, 'index'])->name('index');
        Route::get('appel', [AbsenceController::class, 'appel'])->name('appel');
        Route::post('appel', [AbsenceController::class, 'enregistrerAppel'])->name('appel.store');
        Route::post('{absence}/justifier', [AbsenceController::class, 'justifier'])->name('justifier');
        Route::delete('{absence}', [AbsenceController::class, 'destroy'])->name('destroy');
    });

    // Parents / tuteurs : gérés depuis l'onglet « Famille » de la fiche élève.
    Route::get('tuteurs/recherche', [TuteurController::class, 'recherche'])->name('tuteurs.recherche');
    Route::prefix('eleves/{eleve}/tuteurs')->name('tuteurs.')->group(function () {
        Route::post('/', [TuteurController::class, 'store'])->name('store');
        Route::post('attacher', [TuteurController::class, 'attacher'])->name('attacher');
        Route::put('{tuteur}', [TuteurController::class, 'update'])->name('update');
        Route::delete('{tuteur}', [TuteurController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('eleves/{eleve}/paiements')->name('paiements.')->group(function () {
        Route::get('/', [PaiementController::class, 'index'])->name('index');
        Route::get('create', [PaiementController::class, 'create'])->name('create');
        Route::post('/', [PaiementController::class, 'store'])->name('store');
    });
    Route::get('paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show');
    Route::post('paiements/{paiement}/cheques', [PaiementController::class, 'storeCheque'])->name('paiements.cheques.store');
    Route::delete('paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');

    Route::resource('admins', AdminController::class)->except('show')->parameters(['admins' => 'admin']);

    Route::resource('roles', RoleController::class)->except('show');
    Route::resource('permissions', PermissionController::class)->except('show');

    Route::prefix('annotations')->name('annotations.')->group(function () {
        Route::post('/', [AnnotationController::class, 'store'])->name('store');
        Route::get('contact/{contactId}', [AnnotationController::class, 'forContact'])->name('for-contact');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('mark-one-read', [NotificationController::class, 'markOneRead'])->name('mark-one-read');
        Route::post('mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
    });

    Route::get('archives/reunions', [ArchivesReunionController::class, 'index'])->name('archives.reunions');

    Route::resource('entreprises', EntrepriseController::class)->parameters(['entreprises' => 'entreprise']);

    Route::prefix('taches')->name('taches.')->group(function () {
        Route::get('/', [TacheController::class, 'index'])->name('index');
        Route::post('/', [TacheController::class, 'store'])->name('store');
        Route::post('{tache}/close', [TacheController::class, 'close'])->name('close');
    });

    // Portage de Relances.php — page shell sans logique propre, réutilise TacheController.
    Route::redirect('relances', '/taches')->name('relances.index');

    Route::get('recherche', [GlobalSearchController::class, 'search'])->name('recherche.search');

    Route::resource('evaluations', EvaluationController::class)
        ->except('show')
        ->parameters(['evaluations' => 'evaluation']);

    Route::get('evaluations/{evaluation}/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('notes', [NoteController::class, 'update'])->name('notes.update');
    Route::get('notes/{note}/history', [NoteController::class, 'history'])->name('notes.history');

    Route::resource('types-evaluation', TypeEvaluationController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['types-evaluation' => 'type']);

    Route::resource('emails', TexteEmailController::class)->only(['index', 'edit', 'update']);

    Route::resource('planning', PlanningController::class)
        ->except('show')
        ->parameters(['planning' => 'creneau']);

    Route::resource('salles', SalleController::class)->except('show');

    Route::prefix('referentiel-vacances/{type}')->name('referentiel-vacances.')->where(['type' => 'ferie|vacance|stage|fermeture|event|sejour|partiel'])->group(function () {
        Route::get('/', [ReferentielVacanceController::class, 'index'])->name('index');
        Route::get('create', [ReferentielVacanceController::class, 'create'])->name('create');
        Route::post('/', [ReferentielVacanceController::class, 'store'])->name('store');
        Route::get('{referentielVacance}/edit', [ReferentielVacanceController::class, 'edit'])->name('edit');
        Route::put('{referentielVacance}', [ReferentielVacanceController::class, 'update'])->name('update');
        Route::delete('{referentielVacance}', [ReferentielVacanceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('bulletin-v2')->name('bulletin-v2.')->group(function () {
        Route::get('/', [BulletinV2Controller::class, 'index'])->name('index');
        Route::get('create', [BulletinV2Controller::class, 'create'])->name('create');
        Route::post('generate', [BulletinV2Controller::class, 'generate'])->name('generate');
        Route::get('classes/{classe}/eleves', [BulletinV2Controller::class, 'studentsForClass'])->name('students-for-class');
        Route::get('{bulletin}', [BulletinV2Controller::class, 'show'])->name('show');
    });

    Route::prefix('configuration/site')->name('configuration.site')->group(function () {
        Route::get('/', [SiteConfigController::class, 'index'])->name('');
        Route::put('constants/{constant}', [SiteConfigController::class, 'updateConstant'])->name('.constants.update');
        Route::post('access/{access}/toggle', [SiteConfigController::class, 'toggleAccess'])->name('.access.toggle');
    });

    Route::prefix('bulletins')->name('bulletins.')->group(function () {
        Route::get('/', [BulletinController::class, 'index'])->name('index');
        Route::post('save', [BulletinController::class, 'save'])->name('save');
        Route::post('publish', [BulletinController::class, 'publish'])->name('publish');
        Route::post('publish-all', [BulletinController::class, 'publishAll'])->name('publish-all');
    });

    Route::prefix('candidats')->name('candidats.')->group(function () {
        Route::get('/', [CandidatController::class, 'index'])->name('index');
        Route::get('{candidat}', [CandidatController::class, 'show'])->name('show');
        Route::delete('{candidat}', [CandidatController::class, 'destroy'])->name('destroy');
        Route::post('{candidat}/archive', [CandidatController::class, 'archive'])->name('archive');
        Route::post('{candidat}/archive-attente-epreuve', [CandidatController::class, 'archiveAttenteEpreuve'])->name('archive-attente-epreuve');
        Route::post('{candidat}/unarchive', [CandidatController::class, 'unarchive'])->name('unarchive');
        Route::post('{candidat}/inscrire-eleve', [CandidatController::class, 'inscrireEleve'])->name('inscrire-eleve');
    });

    Route::resource('epreuves', EpreuveAdmissionController::class)
        ->except(['show'])
        ->parameters(['epreuves' => 'epreuve']);
    Route::post('epreuves/{epreuve}/inscrire', [EpreuveAdmissionController::class, 'inscrireCandidat'])->name('epreuves.inscrire');
    Route::post('epreuves/inscriptions/{inscription}/presence', [EpreuveAdmissionController::class, 'togglePresence'])->name('epreuves.toggle-presence');
    Route::delete('epreuves/{epreuve}/candidats/{candidat}', [EpreuveAdmissionController::class, 'suppressionCandidat'])->name('epreuves.suppression-candidat');
    Route::post('epreuves/{epreuve}/resultats', [EpreuveAdmissionController::class, 'storeResultat'])->name('epreuves.resultats.store');
    Route::delete('epreuves/resultats/{resultat}', [EpreuveAdmissionController::class, 'destroyResultat'])->name('epreuves.resultats.destroy');
    Route::post('epreuves/resultats/{resultat}/archive', [EpreuveAdmissionController::class, 'archiveResultat'])->name('epreuves.resultats.archive');

    Route::get('import', [ImportController::class, 'index'])->name('import.index');
    Route::post('import', [ImportController::class, 'store'])->name('import.store');

    Route::resource('contacts', ContactController::class)->except(['create', 'store', 'show'])->parameters(['contacts' => 'contact']);
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::post('contacts/update-field', [ContactController::class, 'updateField'])->name('contacts.update-field');
    Route::post('contacts/{contact}/archive', [ContactController::class, 'archive'])->name('contacts.archive');
    Route::post('contacts/{contact}/allow-relances', [ContactController::class, 'allowRelances'])->name('contacts.allow-relances');

    Route::resource('panneaux', PanneauController::class)
        ->except('show')
        ->parameters(['panneaux' => 'panneau']);
    Route::post('panneaux/check-identifiant', [PanneauController::class, 'checkIdentifiant'])->name('panneaux.check-identifiant');

    // Export vers le format d'échange Helvetius (portage partiel de Helvetius.php).
    Route::prefix('helvetius')->name('helvetius.')->group(function () {
        Route::get('eleves', [HelvetiusExportController::class, 'eleves'])->name('eleves');
        Route::get('profs', [HelvetiusExportController::class, 'profs'])->name('profs');
    });

    Route::prefix('cloud')->name('cloud.')->group(function () {
        Route::get('/', [CloudController::class, 'index'])->name('index');
        Route::post('/', [CloudController::class, 'store'])->name('store');
        Route::delete('{document}', [CloudController::class, 'destroy'])->name('destroy');
    });

    // Référentiel pédagogique (socle UE / Cours / Matières / Niveaux, portage partiel de Referentiel.php).
    Route::prefix('referentiel')->name('referentiel.')->group(function () {
        Route::resource('niveaux', NiveauController::class)->except('show');
        Route::prefix('niveaux/{niveau}/options')->name('niveaux.options.')->group(function () {
            Route::post('/', [NiveauOptionController::class, 'store'])->name('store');
            Route::put('{option}', [NiveauOptionController::class, 'update'])->name('update');
            Route::delete('{option}', [NiveauOptionController::class, 'destroy'])->name('destroy');
        });
        Route::resource('unites', UniteEnseignementController::class)->except('show');
        Route::resource('cours', CoursController::class)
            ->except('show')
            ->parameters(['cours' => 'cours']);
        Route::resource('matieres', MatiereController::class)->except('show');
        Route::resource('etablissements', EtablissementController::class)->except(['show', 'destroy']);
        Route::resource('classes', ClasseController::class)
            ->except('show')
            ->parameters(['classes' => 'classe']);

        Route::resource('groupes', GroupeEleveController::class)
            ->except('show')
            ->parameters(['groupes' => 'groupe']);

        Route::resource('specialisations', SpecialisationController::class)
            ->except('show')
            ->parameters(['specialisations' => 'specialisation']);

        Route::resource('intervenants', IntervenantController::class)
            ->parameters(['intervenants' => 'intervenant']);

        // Paramétrage — volumes de formation (portage réduit de Parametrage.php).
        Route::prefix('parametrage')->name('parametrage.')->group(function () {
            Route::get('/', [ParametrageController::class, 'index'])->name('index');
            Route::post('/', [ParametrageController::class, 'save'])->name('save');
        });

        // Référentiel des heures d'enseignement (portage réduit de Ref.php).
        Route::prefix('ref')->name('ref.')->group(function () {
            Route::get('/', [ReferentielController::class, 'index'])->name('index');
            Route::post('niveau', [ReferentielController::class, 'storeNiveau'])->name('niveau.store');
            Route::delete('niveau/{referentielNiveau}', [ReferentielController::class, 'destroyNiveau'])->name('niveau.destroy');
            Route::post('classe', [ReferentielController::class, 'storeClasse'])->name('classe.store');
            Route::delete('classe/{referentielClasse}', [ReferentielController::class, 'destroyClasse'])->name('classe.destroy');
        });

        Route::resource('types-piece', TypePieceEntrepriseController::class)
            ->except('show')
            ->parameters(['types-piece' => 'type']);

        Route::resource('signatures', SignatureController::class)->except('show');

        Route::resource('periodes-formation', PeriodeFormationController::class)->except('show');

        Route::prefix('config-pdf/{type}')->name('config-pdf.')->where(['type' => 'facture|avoir|attestation'])->group(function () {
            Route::get('/', [ConfigPdfController::class, 'index'])->name('index');
            Route::get('create', [ConfigPdfController::class, 'create'])->name('create');
            Route::post('/', [ConfigPdfController::class, 'store'])->name('store');
            Route::get('{configPdf}/edit', [ConfigPdfController::class, 'edit'])->name('edit');
            Route::put('{configPdf}', [ConfigPdfController::class, 'update'])->name('update');
            Route::delete('{configPdf}', [ConfigPdfController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('statistiques')->name('statistiques.')->group(function () {
        Route::get('contacts-by-etablissement', [StatistiqueController::class, 'contactsByEtablissement'])->name('contacts-by-etablissement');
        Route::get('candidats-by-etablissement', [StatistiqueController::class, 'candidatsByEtablissement'])->name('candidats-by-etablissement');
        Route::get('eleves-by-etablissement', [StatistiqueController::class, 'elevesByEtablissement'])->name('eleves-by-etablissement');
        Route::get('eleves-by-niveau', [StatistiqueController::class, 'elevesByNiveau'])->name('eleves-by-niveau');
    });
});
