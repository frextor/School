<?php

namespace App\Console\Commands;

use App\Models\ReunionInformation;
use Illuminate\Console\Command;

/**
 * Portage de `Cron.php::suppression_contacts_reunion_cron()` /
 * `Functions::suppression_contacts_reunion_cron()` — pour chaque réunion
 * d'information passée depuis 3 jours ou plus, incrémente le compteur de
 * "non présentation" des contacts inscrits mais non pointés présents.
 *
 * Usage : php artisan legacy:mark-no-show-reunion-contacts
 */
class MarkNoShowReunionContacts extends Command
{
    protected $signature = 'legacy:mark-no-show-reunion-contacts';

    protected $description = "Incrémente le compteur de non-présentation des contacts inscrits à une réunion passée depuis 3 jours et non pointés présents";

    public function handle(): int
    {
        $reunions = ReunionInformation::query()
            ->where('date', '<=', now()->subDays(3))
            ->with('inscriptions.contact')
            ->get();

        $marques = 0;

        foreach ($reunions as $reunion) {
            foreach ($reunion->inscriptions as $inscription) {
                if ($inscription->presence || ! $inscription->contact) {
                    continue;
                }

                $inscription->contact->update([
                    'derniere_reunion' => $reunion->id_reunion_information,
                    'compteur_reunion' => $inscription->contact->compteur_reunion + 1,
                ]);

                $marques++;
            }
        }

        $this->info("{$marques} contact(s) marqué(s) non-présent(s).");

        return self::SUCCESS;
    }
}
