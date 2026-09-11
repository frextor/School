<?php

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Portage de `Parent_script.php` (CodeIgniter) — pour chaque email partagé
 * par plusieurs fiches contact, rattache toutes ces fiches au plus ancien
 * contact (le plus petit id_contact) via `id_contact_parent`.
 *
 * Usage : php artisan legacy:link-contact-parents
 */
class LinkContactParents extends Command
{
    protected $signature = 'legacy:link-contact-parents';

    protected $description = "Rattache les fiches contact partageant un même email à la fiche la plus ancienne (id_contact_parent)";

    public function handle(): int
    {
        $emails = Contact::query()
            ->select('email')
            ->distinct()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email');

        $count = 0;

        $this->withProgressBar($emails, function (string $email) use (&$count) {
            $premierContact = Contact::where('email', $email)
                ->orderBy('id_contact')
                ->first();

            if ($premierContact) {
                Contact::where('email', $email)->update(['id_contact_parent' => $premierContact->id_contact]);
                $count++;
            }
        });

        $this->newLine(2);
        $this->info("{$count} email(s) traités.");

        return self::SUCCESS;
    }
}
