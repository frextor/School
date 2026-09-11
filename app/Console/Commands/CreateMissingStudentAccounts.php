<?php

namespace App\Console\Commands;

use App\Models\Eleve;
use App\Models\UserEleve;
use Illuminate\Console\Command;

/**
 * Portage de `Cron.php::missing_users()` — crée un compte "espace élève"
 * (table `amos_users`) pour chaque élève qui n'en a pas encore, à partir de
 * l'email de son contact. Mot de passe temporaire = md5(email), comme le
 * legacy (voir `UserEleve` pour la note sur ce mécanisme).
 *
 * Usage : php artisan legacy:create-missing-student-accounts
 */
class CreateMissingStudentAccounts extends Command
{
    protected $signature = 'legacy:create-missing-student-accounts';

    protected $description = "Crée un compte espace-élève pour chaque élève sans compte, à partir de son email";

    public function handle(): int
    {
        $orphelins = Eleve::query()
            ->with('contact')
            ->whereDoesntHave('compteUtilisateur')
            ->whereHas('contact', fn ($q) => $q->where('email', '!=', ''))
            ->get();

        $crees = 0;

        foreach ($orphelins as $eleve) {
            $email = $eleve->contact?->email;

            if (! $email) {
                continue;
            }

            UserEleve::create([
                'id_eleve' => $eleve->id_eleve,
                'username' => $email,
                'password' => md5($email),
                'connexion' => now(),
                'token' => '',
                'valide' => true,
                'email_etudiant' => '',
            ]);

            $crees++;
        }

        $this->info("{$crees} compte(s) espace-élève créé(s).");

        return self::SUCCESS;
    }
}
