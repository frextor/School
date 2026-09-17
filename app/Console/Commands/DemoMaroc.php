<?php

namespace App\Console\Commands;

use App\Models\AbsenceEleve;
use App\Models\ActiviteIntervenant;
use App\Models\Classe;
use App\Models\Contact;
use App\Models\Cours;
use App\Models\Echeance;
use App\Models\Eleve;
use App\Models\EpreuveAdmission;
use App\Models\EpreuveAdmissionEleve;
use App\Models\Etablissement;
use App\Models\Evaluation;
use App\Models\Intervenant;
use App\Models\Niveau;
use App\Models\NiveauxOptions;
use App\Models\Note;
use App\Models\ObjetPaiement;
use App\Models\ResultatEpreuveEleve;
use App\Models\Salle;
use App\Models\SiteConstant;
use App\Models\SnTypeEvaluation;
use App\Models\Tuteur;
use Database\Seeders\ReferentielMarocSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Jeu de données de démonstration : une école marocaine complète.
 *
 *   php artisan demo:maroc          crée le jeu (sans doublon si déjà présent)
 *   php artisan demo:maroc --purge  retire tout ce que la démo a créé
 *
 * Tout ce qui est créé est reconnaissable : l'établissement porte « (démo) »
 * dans son nom et toutes les adresses e-mail sont en `demo.*@example.test`.
 * Les données sont fictives (noms tirés au sort, générateur à graine fixe :
 * deux exécutions produisent la même école).
 *
 * Reste en place après --purge, parce que ce sont des référentiels qu'une
 * vraie école réutilise tels quels : cycles et niveaux, matières et
 * coefficients, catalogue d'options, types d'évaluation.
 */
class DemoMaroc extends Command
{
    protected $signature = 'demo:maroc {--purge : Retire les données de démonstration}';
    protected $description = 'Crée (ou retire) un jeu de données de démonstration d\'école marocaine';

    private const ECOLE = 'Groupe Scolaire Al Amal (démo)';
    private const DOMAINE = '@example.test';

    private const PRENOMS_M = ['Youssef', 'Adam', 'Mohamed', 'Amine', 'Omar', 'Yassine', 'Rayan', 'Ilyas', 'Anas', 'Hamza', 'Ayoub', 'Mehdi', 'Zakaria', 'Taha', 'Ismail', 'Karim', 'Bilal', 'Sami', 'Nizar', 'Reda'];
    private const PRENOMS_F = ['Salma', 'Lina', 'Meryem', 'Aya', 'Yasmine', 'Nour', 'Ines', 'Hiba', 'Malak', 'Rim', 'Kenza', 'Sara', 'Douaa', 'Ghita', 'Imane', 'Zineb', 'Chaima', 'Hajar', 'Asmae', 'Fatima'];
    private const NOMS = ['Alaoui', 'Bennani', 'El Amrani', 'Tazi', 'Idrissi', 'Berrada', 'Chraibi', 'Fassi', 'Benjelloun', 'Lahlou', 'Kettani', 'Sefrioui', 'Bouazza', 'Ouazzani', 'Haddad', 'Mansouri', 'Zahiri', 'Benkirane', 'El Fassi', 'Tahiri', 'Rachidi', 'Sqalli', 'Cherkaoui', 'Naciri', 'Belkadi', 'Filali', 'Hajji', 'Amrani', 'Saidi', 'Bouzid'];
    private const VILLES = ['Casablanca', 'Casablanca', 'Casablanca', 'Mohammedia', 'Bouskoura', 'Dar Bouazza'];
    private const PROFESSIONS = ['Ingénieur', 'Médecin', 'Commerçant', 'Enseignant', 'Cadre bancaire', 'Pharmacien', 'Architecte', 'Fonctionnaire', 'Avocat', 'Infirmier', 'Comptable', 'Chef d\'entreprise'];

    /** Matières et coefficients par cycle (le libellé du cycle est celui de `amos_formations`). */
    private const MATIERES = [
        'Maternelle' => ['Langage et communication' => 1, 'Éveil scientifique' => 1, 'Graphisme et écriture' => 1, 'Activités artistiques' => 1, 'Motricité' => 1],
        'Primaire' => ['Arabe' => 3, 'Français' => 3, 'Mathématiques' => 3, 'Éducation islamique' => 1, 'Éveil scientifique' => 1, 'Anglais' => 1, 'Éducation artistique' => 1, 'Éducation physique' => 1],
        'Collège' => ['Arabe' => 3, 'Français' => 3, 'Mathématiques' => 3, 'Anglais' => 2, 'Sciences de la vie et de la terre' => 2, 'Physique-Chimie' => 2, 'Histoire-Géographie' => 2, 'Éducation islamique' => 1, 'Informatique' => 1, 'Éducation physique' => 1],
        'Lycée' => ['Mathématiques' => 7, 'Physique-Chimie' => 7, 'Sciences de la vie et de la terre' => 5, 'Français' => 4, 'Arabe' => 2, 'Anglais' => 2, 'Philosophie' => 2, 'Histoire-Géographie' => 2, 'Éducation islamique' => 2, 'Éducation physique' => 1],
    ];

    /** Mensualité par cycle, en dirhams. */
    private const MENSUALITE = ['Maternelle' => 1200, 'Primaire' => 1500, 'Collège' => 1800, 'Lycée' => 2200];

    /** Âge à la rentrée par niveau (année de naissance = année de rentrée − âge). */
    private const AGE = ['PS' => 3, 'MS' => 4, 'GS' => 5, '1AEP' => 6, '2AEP' => 7, '3AEP' => 8, '4AEP' => 9, '5AEP' => 10, '6AEP' => 11, '1AC' => 12, '2AC' => 13, '3AC' => 14, 'TC' => 15, '1BAC' => 16, '2BAC' => 17];

    private string $anneeScolaire;
    private int $anneeDebut;
    private Carbon $rentree;
    private int $compteurEmail = 0;

    public function handle(): int
    {
        // Le schéma legacy a beaucoup de colonnes NOT NULL sans défaut : le mode
        // permissif leur donne une valeur vide, comme le faisait l'application d'origine.
        DB::statement("SET SESSION sql_mode = ''");

        if ($this->option('purge')) {
            return $this->purger();
        }

        if (Etablissement::where('nom_etablissement', self::ECOLE)->exists()) {
            $this->warn('Le jeu de démonstration existe déjà. Lancez --purge avant de le recréer.');

            return self::SUCCESS;
        }

        mt_srand(2026);
        $this->anneeScolaire = Echeance::anneeScolaireCourante();
        $this->anneeDebut = (int) substr($this->anneeScolaire, 0, 4);
        $this->rentree = Carbon::create($this->anneeDebut, 9, 7);

        $this->call(ReferentielMarocSeeder::class);

        DB::transaction(function () {
            $ecole = $this->ecole();
            $this->matieres();
            $this->catalogueOptions();
            $salles = $this->salles($ecole);
            $enseignants = $this->enseignants();
            $classes = $this->classes($ecole);
            $eleves = $this->eleves($classes);
            $this->familles($eleves);
            $this->echeanciers($eleves);
            $this->assiduite($eleves);
            $this->notes($ecole, $classes, $eleves);
            $this->emploiDuTemps($ecole, $classes, $enseignants, $salles);
            $this->admissions($classes);
            $this->prospects();

            SiteConstant::firstOrCreate(['title' => 'ville_etablissement'], ['value' => 'Casablanca', 'label' => 'Ville de l\'établissement']);
        });

        $this->info('Jeu de démonstration créé : '.self::ECOLE.'.');
        $this->line('  Élèves : '.Eleve::whereHas('contact', fn ($q) => $q->where('email', 'like', 'demo.%'.self::DOMAINE))->count());
        $this->line('  Échéances : '.Echeance::count().' · Absences : '.AbsenceEleve::count().' · Notes : '.Note::count());
        $this->line('Connexion admin inchangée. Retrait : php artisan demo:maroc --purge');

        return self::SUCCESS;
    }

    // ------------------------------------------------------------------ Création

    private function ecole(): Etablissement
    {
        $ecole = Etablissement::create([
            'nom_etablissement' => self::ECOLE,
            'code_ville' => 'CA',
            'adresse' => '24 boulevard Zerktouni, Maârif — Casablanca',
            'visible' => 1,
        ]);

        // Tous les niveaux du référentiel sont enseignés dans cette école.
        DB::table('amos_etablissements_niveaux')->insert(
            $this->niveauxMaroc()->pluck('id_niveau')->map(fn ($id) => ['id_niveau' => $id, 'id_etablissement' => $ecole->id_etablissement])->all()
        );

        return $ecole;
    }

    private function matieres(): void
    {
        foreach (self::MATIERES as $cycle => $matieres) {
            $niveaux = $this->niveauxMaroc()->filter(fn ($n) => $n->formation?->niveau === $cycle);

            foreach ($matieres as $nom => $coef) {
                $cours = Cours::firstOrCreate(['nom_cours' => $nom], ['code_cours' => str($nom)->ascii()->slug()->upper()->substr(0, 20), 'id_unite_enseignement' => 0]);

                foreach ($niveaux as $i => $niveau) {
                    $niveau->matieres()->syncWithoutDetaching([$cours->id_cours => ['coefficient' => $coef, 'ordre' => array_search($nom, array_keys($matieres)) + 1]]);
                }
            }
        }
    }

    private function catalogueOptions(): void
    {
        $objet = ObjetPaiement::firstOrCreate(['reference' => 'OPTION'], ['objet_paiement' => 'Option facturable', 'type' => 'option']);

        $catalogue = [
            ['Transport scolaire', 350, 'mensuelle', 1],
            ['Cantine', 400, 'mensuelle', 2],
            ['Assurance scolaire', 150, 'annuelle', 3],
            ['Uniforme', 300, 'annuelle', 4],
        ];

        foreach ($this->niveauxMaroc() as $niveau) {
            foreach ($catalogue as [$titre, $montant, $periodicite, $ordre]) {
                NiveauxOptions::firstOrCreate(
                    ['id_niveau' => $niveau->id_niveau, 'titre' => $titre, 'annee' => $this->anneeDebut],
                    ['id_objet_paiement' => $objet->id_objet_paiement, 'montant' => $montant, 'periodicite' => $periodicite, 'ordre' => $ordre]
                );
            }
        }
    }

    private function salles(Etablissement $ecole): array
    {
        $salles = [];
        foreach (['A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2', 'Labo sciences', 'Salle informatique', 'Bibliothèque', 'Gymnase'] as $i => $nom) {
            $salles[] = Salle::create([
                'code_salle' => 'DEMO-'.($i + 1), 'nom_salle' => $nom, 'nombre_place' => mt_rand(24, 36),
                'id_etablissement' => $ecole->id_etablissement, 'date_creation' => now(), 'ip' => '',
            ]);
        }

        return $salles;
    }

    private function enseignants(): array
    {
        $profs = [];
        $disciplines = ['Arabe', 'Français', 'Mathématiques', 'Anglais', 'Sciences', 'Physique-Chimie', 'Histoire-Géographie', 'Éducation islamique', 'Éducation physique', 'Informatique', 'Maternelle', 'Primaire', 'Primaire', 'Philosophie'];

        foreach ($disciplines as $i => $discipline) {
            [$civ, $prenom] = $this->personne();
            $nom = self::NOMS[$i % count(self::NOMS)];
            $profs[] = Intervenant::create([
                'civilite' => $civ, 'nom' => $nom, 'prenom' => $prenom,
                'email' => $this->email('prof', $prenom, $nom),
                'date_naissance' => Carbon::create(mt_rand(1970, 1995), mt_rand(1, 12), mt_rand(1, 28)),
                'telephone' => $this->telephone(), 'mobile' => $this->telephone(),
                'ville' => 'Casablanca', 'profession' => 'Enseignant(e) de '.$discipline,
                'poste_actuel' => 'Enseignant(e)', 'id_nationalite' => 0, 'id_langue' => 0, 'id_pays' => 0, 'id_societe' => 0,
            ]);
        }

        return $profs;
    }

    private function classes(Etablissement $ecole): array
    {
        $classes = [];
        $couleurs = ['#4f46e5', '#0f766e', '#b45309', '#7c3aed', '#be123c', '#0369a1'];

        foreach ($this->niveauxMaroc()->values() as $i => $niveau) {
            // Deux classes en 1AEP et 6AEP (niveaux d'entrée et de sortie du primaire), une ailleurs.
            $sections = in_array($niveau->code_niveau, ['1AEP', '6AEP']) ? ['A', 'B'] : ['A'];

            foreach ($sections as $section) {
                $classes[] = Classe::create([
                    'code_classe' => $niveau->code_niveau.'-'.$section,
                    'classe' => $niveau->code_niveau.' '.$section,
                    'id_niveau' => $niveau->id_niveau,
                    'id_etablissement' => $ecole->id_etablissement,
                    'couleur' => $couleurs[$i % count($couleurs)],
                ])->setRelation('niveau', $niveau);
            }
        }

        return $classes;
    }

    private function eleves(array $classes): array
    {
        $eleves = [];

        foreach ($classes as $classe) {
            $effectif = mt_rand(18, 26);
            $age = self::AGE[$classe->niveau->code_niveau];

            for ($n = 0; $n < $effectif; $n++) {
                [$civ, $prenom, $sexe] = $this->personne(true);
                $nom = self::NOMS[mt_rand(0, count(self::NOMS) - 1)];
                $naissance = Carbon::create($this->anneeDebut - $age, mt_rand(1, 12), mt_rand(1, 28));

                $contact = Contact::create([
                    'id_contact_parent' => 0, 'civilite' => $civ, 'nom' => $nom, 'prenom' => $prenom, 'sexe' => $sexe,
                    'email' => $this->email('eleve', $prenom, $nom),
                    'date_naissance' => $naissance, 'lieu_naissance' => self::VILLES[mt_rand(0, 5)],
                    'ville' => self::VILLES[mt_rand(0, 5)], 'visible' => 1,
                    'date_inscription' => $this->rentree->copy()->subDays(mt_rand(10, 60)),
                    'annee_rentree' => $this->anneeDebut, 'candidat' => 0,
                ]);

                $eleves[] = Eleve::create([
                    'id_eleve_parent' => 0, 'id_contact' => $contact->id_contact, 'profil' => Eleve::PROFIL_ELEVE,
                    'id_niveau' => $classe->id_niveau, 'id_classe' => $classe->id_classe, 'visible' => 1,
                    'date_inscription' => $contact->date_inscription,
                ])->setRelation('contact', $contact)->setRelation('classe', $classe);
            }
        }

        return $eleves;
    }

    /**
     * Un père et une mère par élève (le père responsable légal, la mère contact
     * d'urgence). Environ un élève sur huit a un frère ou une sœur dans l'école :
     * les deux partagent les mêmes fiches tuteur, c'est ce qui démontre la
     * gestion des fratries.
     */
    private function familles(array $eleves): void
    {
        $fratries = [];

        foreach ($eleves as $eleve) {
            $nom = $eleve->contact->nom;

            if (isset($fratries[$nom]) && mt_rand(1, 100) <= 40) {
                [$pere, $mere] = $fratries[$nom];
            } else {
                $ville = $eleve->contact->ville;
                $adresse = mt_rand(2, 180).' '.['rue', 'avenue', 'boulevard', 'lotissement'][mt_rand(0, 3)].' '.['Al Massira', 'Ghandi', 'Anfa', 'Zerktouni', 'Al Qods', 'Ibn Sina'][mt_rand(0, 5)];

                $pere = Tuteur::create([
                    'civilite' => 'M.', 'nom' => $nom, 'prenom' => self::PRENOMS_M[mt_rand(0, 19)],
                    'email' => $this->email('parent', 'pere', $nom), 'telephone' => $this->telephone(),
                    'profession' => self::PROFESSIONS[mt_rand(0, 11)], 'cin' => chr(mt_rand(65, 90)).chr(mt_rand(65, 90)).mt_rand(100000, 999999),
                    'adresse' => $adresse, 'ville' => $ville,
                ]);
                $mere = Tuteur::create([
                    'civilite' => 'Mme', 'nom' => self::NOMS[mt_rand(0, 29)], 'prenom' => self::PRENOMS_F[mt_rand(0, 19)],
                    'email' => $this->email('parent', 'mere', $nom), 'telephone' => $this->telephone(),
                    'profession' => self::PROFESSIONS[mt_rand(0, 11)], 'cin' => chr(mt_rand(65, 90)).chr(mt_rand(65, 90)).mt_rand(100000, 999999),
                    'adresse' => $adresse, 'ville' => $ville,
                ]);
                $fratries[$nom] = [$pere, $mere];
            }

            $eleve->tuteurs()->attach($pere->id_tuteur, ['lien_parente' => 'pere', 'responsable_legal' => true, 'contact_urgence' => false, 'ordre' => 1]);
            $eleve->tuteurs()->attach($mere->id_tuteur, ['lien_parente' => 'mere', 'responsable_legal' => false, 'contact_urgence' => true, 'ordre' => 2]);
        }
    }

    /**
     * Inscription + 10 mensualités (septembre → juin), options selon l'élève,
     * puis des règlements réalistes : l'inscription est presque toujours payée,
     * septembre l'est aux trois quarts, le reste est à venir — de quoi remplir
     * le suivi des impayés sans le rendre absurde.
     */
    private function echeanciers(array $eleves): void
    {
        $premiere = Carbon::create($this->anneeDebut, 9, 5);
        $modes = ['Espèces', 'Chèque', 'Virement', 'Carte'];

        foreach ($eleves as $eleve) {
            $cycle = $eleve->classe->niveau->formation->niveau;
            $mensualite = self::MENSUALITE[$cycle];
            $lignes = [];

            $lignes[] = ['type' => Echeance::TYPE_INSCRIPTION, 'libelle' => "Frais d'inscription", 'montant' => 2000, 'date' => $premiere];
            $lignes[] = ['type' => Echeance::TYPE_OPTION, 'libelle' => 'Assurance scolaire', 'montant' => 150, 'date' => $premiere];

            $transport = mt_rand(1, 100) <= 40;
            $cantine = mt_rand(1, 100) <= 30;

            for ($i = 0; $i < 10; $i++) {
                $date = $premiere->copy()->addMonthsNoOverflow($i);
                $mois = $date->locale('fr_FR')->isoFormat('MMMM YYYY');
                $lignes[] = ['type' => Echeance::TYPE_MENSUALITE, 'libelle' => 'Mensualité '.$mois, 'montant' => $mensualite, 'date' => $date];
                if ($transport) {
                    $lignes[] = ['type' => Echeance::TYPE_OPTION, 'libelle' => 'Transport scolaire — '.$mois, 'montant' => 350, 'date' => $date];
                }
                if ($cantine) {
                    $lignes[] = ['type' => Echeance::TYPE_OPTION, 'libelle' => 'Cantine — '.$mois, 'montant' => 400, 'date' => $date];
                }
            }

            foreach ($lignes as $l) {
                $regle = 0;
                $tirage = mt_rand(1, 100);

                if ($l['date']->lte(Carbon::today())) {
                    // Échéance passée : payée, partielle ou en retard.
                    $regle = match (true) {
                        $l['type'] === Echeance::TYPE_INSCRIPTION && $tirage <= 92 => $l['montant'],
                        $tirage <= 72 => $l['montant'],
                        $tirage <= 82 => round($l['montant'] / 2),
                        default => 0,
                    };
                }

                Echeance::create([
                    'id_eleve' => $eleve->id_eleve, 'annee_scolaire' => $this->anneeScolaire,
                    'type' => $l['type'], 'libelle' => $l['libelle'], 'montant' => $l['montant'], 'date_echeance' => $l['date'],
                    'montant_regle' => $regle,
                    'date_reglement' => $regle > 0 ? $l['date']->copy()->subDays(mt_rand(0, 6)) : null,
                    'mode_reglement' => $regle > 0 ? $modes[mt_rand(0, 3)] : null,
                ]);
            }
        }
    }

    /** Un élève sur quatre a une à trois absences ou retards depuis la rentrée. */
    private function assiduite(array $eleves): void
    {
        $jours = max(1, $this->rentree->diffInDays(Carbon::today()));

        foreach ($eleves as $eleve) {
            if (mt_rand(1, 100) > 25) {
                continue;
            }

            for ($n = mt_rand(1, 3); $n > 0; $n--) {
                $date = $this->rentree->copy()->addDays(mt_rand(0, $jours));
                if ($date->isWeekend()) {
                    continue;
                }
                $retard = mt_rand(1, 100) <= 30;
                $justifie = mt_rand(1, 100) <= 55;

                AbsenceEleve::firstOrCreate(
                    ['id_eleve' => $eleve->id_eleve, 'date_absence' => $date->format('Y-m-d'), 'heure_absence' => ['08:00:00', '10:00:00', '14:00:00'][mt_rand(0, 2)]],
                    AbsenceEleve::drapeaux($retard ? AbsenceEleve::NATURE_RETARD : AbsenceEleve::NATURE_ABSENCE, $justifie) + [
                        'id_cours' => 0, 'id_unite_enseignement' => 0, 'semestre' => AbsenceEleve::semestrePour($date), 'valide' => 1,
                        'annotation' => $justifie ? ['Certificat médical', 'Raison familiale', 'Rendez-vous médical'][mt_rand(0, 2)] : '',
                        'justificatif' => $justifie, 'date_justificatif' => $justifie ? $date->copy()->addDays(2) : null,
                        'modification_justificatif' => 0,
                    ]
                );
            }
        }
    }

    /**
     * Un premier contrôle par matière dans chaque classe (hors maternelle, qui
     * n'a pas de notes chiffrées), avec une note pour chaque élève.
     */
    private function notes(Etablissement $ecole, array $classes, array $eleves): void
    {
        $idType = DB::table('amos_type_evaluation')->where('type', 'Contrôle continu')->value('id_type');
        $parClasse = collect($eleves)->groupBy('id_classe');

        foreach ($classes as $classe) {
            if ($classe->niveau->formation->niveau === 'Maternelle') {
                continue;
            }

            $type = SnTypeEvaluation::create([
                'id_etablissement' => $ecole->id_etablissement, 'coef' => 1, 'id_unite_enseignement' => 0, 'id_cours' => 0,
                'id_niveau' => $classe->id_niveau, 'id_referentiel' => $classe->id_classe, 'id_type' => $idType,
                'annee' => $this->anneeDebut, 'semestre' => 1, 'referentiel' => 'classe',
            ]);

            foreach ($classe->niveau->matieres as $matiere) {
                if (in_array($matiere->nom_cours, ['Éducation physique', 'Éducation artistique'])) {
                    continue;
                }

                $evaluation = Evaluation::create([
                    'id_campus' => $ecole->id_etablissement, 'annee' => $this->anneeDebut, 'semestre' => 1,
                    'id_referentiel' => $classe->id_classe, 'id_ue' => 0, 'id_matiere' => $matiere->id_cours,
                    'id_type_evaluation' => $type->id_type_evaluation, 'nom_evaluation' => 'Contrôle n°1',
                    'date_evaluation' => $this->rentree->copy()->addDays(mt_rand(5, 9)), 'heure_debut' => '08:00', 'heure_fin' => '09:00',
                    'type_notation' => '20', 'boolean_facultatif' => 0, 'referentiel' => 'classe', 'id_evaluation_parent' => 0,
                ]);

                foreach ($parClasse[$classe->id_classe] ?? [] as $eleve) {
                    // Notes centrées autour de 12, avec quelques très bonnes et quelques faibles.
                    $note = max(2, min(20, round(12 + (mt_rand(-40, 40) + mt_rand(-40, 40)) / 20, 1)));
                    Note::create([
                        'id_campus' => $ecole->id_etablissement, 'annee' => $this->anneeDebut, 'semestre' => 1,
                        'id_referentiel' => $classe->id_classe, 'id_ue' => 0, 'id_matiere' => $matiere->id_cours, 'id_type' => 0,
                        'id_evaluation' => $evaluation->id_evaluation, 'eval_session' => 0, 'id_eleve' => $eleve->id_eleve,
                        'validation_sans_note' => 0, 'note' => (string) $note, 'publier_admin' => 1, 'publier_eleve' => 1,
                        'date_saisie' => now(), 'session' => 0, 'referentiel' => 'classe',
                    ]);
                }
            }
        }
    }

    /** Emploi du temps de la semaine en cours : quatre séances par jour et par classe. */
    private function emploiDuTemps(Etablissement $ecole, array $classes, array $enseignants, array $salles): void
    {
        $lundi = Carbon::today()->startOfWeek();
        $creneaux = [['08:30', '10:00'], ['10:15', '11:45'], ['14:00', '15:30'], ['15:45', '17:15']];

        foreach ($classes as $ci => $classe) {
            $matieres = $classe->niveau->matieres->values();
            if ($matieres->isEmpty()) {
                continue;
            }

            for ($jour = 0; $jour < 5; $jour++) {
                foreach ($creneaux as $k => [$debut, $fin]) {
                    $matiere = $matieres[($jour * 4 + $k) % $matieres->count()];
                    $date = $lundi->copy()->addDays($jour);

                    ActiviteIntervenant::create([
                        'id_intervenant' => $enseignants[($ci + $k) % count($enseignants)]->id_intervenant,
                        'id_etablissement' => $ecole->id_etablissement, 'id_cours' => $matiere->id_cours,
                        'id_classe' => $classe->id_classe, 'id_salle' => $salles[$ci % count($salles)]->id_salle, 'id_groupe' => 0,
                        'date_debut' => $date->copy()->setTimeFromTimeString($debut), 'date_fin' => $date->copy()->setTimeFromTimeString($fin),
                        'annotation' => '', 'annotation_etudiant' => '', 'annotation_intervenant' => '', 'groupe' => 0,
                        'semestre' => 1, 'annee' => $this->anneeDebut, 'is_recurrence' => 0, 'all_day' => 0,
                    ]);
                }
            }
        }
    }

    /** Quelques candidats et une épreuve d'admission à venir, avec des résultats déjà saisis. */
    private function admissions(array $classes): void
    {
        $epreuve = EpreuveAdmission::create([
            'date_epreuve' => Carbon::today()->addDays(10)->setTime(9, 0), 'lieu' => 'Salle B1',
            'effectif' => 20, 'distanciel' => false, 'url_distanciel' => '',
        ]);
        $niveaux = [Niveau::where('code_niveau', '1AC')->first(), Niveau::where('code_niveau', 'TC')->first()];
        $decisions = ['accepte', 'accepte', 'refuse', 'en_attente', 'accepte', 'accepter_niveau_inferieur'];

        for ($i = 0; $i < 8; $i++) {
            [$civ, $prenom, $sexe] = $this->personne(true);
            $nom = self::NOMS[mt_rand(0, 29)];
            $niveau = $niveaux[$i % 2];

            $contact = Contact::create([
                'id_contact_parent' => 0, 'civilite' => $civ, 'nom' => $nom, 'prenom' => $prenom, 'sexe' => $sexe,
                'email' => $this->email('candidat', $prenom, $nom), 'visible' => 1,
                'date_naissance' => Carbon::create($this->anneeDebut - self::AGE[$niveau->code_niveau], mt_rand(1, 12), mt_rand(1, 28)),
                'ville' => 'Casablanca', 'date_inscription' => now()->subDays(mt_rand(3, 20)), 'annee_rentree' => $this->anneeDebut, 'candidat' => 1,
            ]);
            $candidat = Eleve::create([
                'id_eleve_parent' => 0, 'id_contact' => $contact->id_contact, 'profil' => Eleve::PROFIL_CANDIDAT,
                'id_niveau' => $niveau->id_niveau, 'id_classe' => 0, 'visible' => 0,
            ]);

            if ($i < 6) {
                EpreuveAdmissionEleve::create(['id_epreuve_admission' => $epreuve->id_epreuve_admission, 'id_eleve' => $candidat->id_eleve, 'presence' => $i < 4]);
            }
            if ($i < 4) {
                ResultatEpreuveEleve::create([
                    'id_eleve' => $candidat->id_eleve, 'id_epreuve_admission' => $epreuve->id_epreuve_admission,
                    'anglais' => mt_rand(8, 18), 'culture_generale' => mt_rand(8, 18), 'epreuve_redaction' => mt_rand(8, 18), 'entretien' => mt_rand(8, 18),
                    'decision' => $decisions[$i], 'date_operation' => now(),
                ]);
            }
        }
    }

    /** Familles ayant demandé des renseignements, sans dossier ouvert. */
    private function prospects(): void
    {
        for ($i = 0; $i < 15; $i++) {
            [$civ, $prenom] = $this->personne();
            $nom = self::NOMS[mt_rand(0, 29)];
            Contact::create([
                'id_contact_parent' => 0, 'civilite' => $civ, 'nom' => $nom, 'prenom' => $prenom, 'sexe' => $civ === 'M' ? 'm' : 'f',
                'email' => $this->email('prospect', $prenom, $nom), 'telephone' => $this->telephone(), 'ville' => self::VILLES[mt_rand(0, 5)],
                'visible' => 1, 'date_inscription' => now()->subDays(mt_rand(1, 45)), 'annee_rentree' => $this->anneeDebut, 'candidat' => 0,
                'source' => 0, 'comment_connaitre_amos' => ['Bouche à oreille', 'Réseaux sociaux', 'Site web', 'Portes ouvertes'][mt_rand(0, 3)],
            ]);
        }
    }

    // ------------------------------------------------------------------ Purge

    private function purger(): int
    {
        $ecole = Etablissement::where('nom_etablissement', self::ECOLE)->first();

        if (! $ecole) {
            $this->warn('Aucun jeu de démonstration à retirer.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($ecole) {
            $contacts = Contact::where('email', 'like', 'demo.%'.self::DOMAINE)->pluck('id_contact');
            $eleves = Eleve::whereIn('id_contact', $contacts)->pluck('id_eleve');

            Echeance::whereIn('id_eleve', $eleves)->delete();
            AbsenceEleve::whereIn('id_eleve', $eleves)->delete();
            Note::whereIn('id_eleve', $eleves)->delete();
            ResultatEpreuveEleve::whereIn('id_eleve', $eleves)->delete();
            EpreuveAdmissionEleve::whereIn('id_eleve', $eleves)->delete();
            EpreuveAdmission::where('lieu', 'Salle B1')->where('effectif', 20)->delete();

            DB::table('eleve_tuteur')->whereIn('id_eleve', $eleves)->delete();
            Tuteur::where('email', 'like', 'demo.parent%'.self::DOMAINE)->delete();

            Evaluation::where('id_campus', $ecole->id_etablissement)->delete();
            SnTypeEvaluation::where('id_etablissement', $ecole->id_etablissement)->delete();
            ActiviteIntervenant::where('id_etablissement', $ecole->id_etablissement)->delete();
            Salle::where('id_etablissement', $ecole->id_etablissement)->delete();
            Intervenant::where('email', 'like', 'demo.prof%'.self::DOMAINE)->delete();

            Eleve::whereIn('id_eleve', $eleves)->delete();
            Contact::whereIn('id_contact', $contacts)->delete();
            Classe::where('id_etablissement', $ecole->id_etablissement)->delete();
            DB::table('amos_etablissements_niveaux')->where('id_etablissement', $ecole->id_etablissement)->delete();
            $ecole->delete();
        });

        $this->info('Jeu de démonstration retiré. Conservés : cycles, niveaux, matières, coefficients, catalogue d\'options, types d\'évaluation.');

        return self::SUCCESS;
    }

    // ------------------------------------------------------------------ Outils

    /** Les 15 niveaux du référentiel marocain, dans l'ordre de progression — jamais les niveaux hérités du supérieur. */
    private function niveauxMaroc()
    {
        return Niveau::with('formation')->whereIn('code_niveau', array_keys(self::AGE))->get()
            ->sortBy(fn ($n) => array_search($n->code_niveau, array_keys(self::AGE)))->values();
    }

    /** [civilité, prénom] ou [civilité, prénom, sexe] tirés au sort. */
    private function personne(bool $avecSexe = false): array
    {
        $f = mt_rand(0, 1) === 1;
        $prenom = $f ? self::PRENOMS_F[mt_rand(0, 19)] : self::PRENOMS_M[mt_rand(0, 19)];

        return $avecSexe ? [$f ? 'Mme' : 'M', $prenom, $f ? 'f' : 'm'] : [$f ? 'Mme' : 'M', $prenom];
    }

    /** Adresse unique, toujours en example.test : impossible d'atteindre une vraie boîte. */
    private function email(string $role, string $prenom, string $nom): string
    {
        return 'demo.'.$role.'.'.str($prenom.'.'.$nom)->ascii()->slug('.').'.'.(++$this->compteurEmail).self::DOMAINE;
    }

    private function telephone(): string
    {
        return '06'.mt_rand(10, 99).' '.mt_rand(10, 99).' '.mt_rand(10, 99).' '.mt_rand(10, 99);
    }
}
