<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Recherche.php` (CodeIgniter) — recherche transverse par nom/
 * prénom/email dans les fiches contact, avec classification par type
 * (candidat, élève, agent de joueur, salon, contact simple) comme le legacy.
 */
class GlobalSearchController extends Controller
{
    public function search(Request $request): View
    {
        $terme = trim((string) $request->input('rechercher', ''));
        $resultats = collect();

        if ($terme !== '') {
            $mots = array_filter(explode(' ', $terme), fn ($m) => strlen($m) > 2);

            $resultats = Contact::query()
                ->with(['eleve', 'ecoles'])
                ->when(count($mots) > 0, function ($q) use ($mots) {
                    foreach ($mots as $mot) {
                        $q->where(function ($q) use ($mot) {
                            $q->where('nom', 'like', "%{$mot}%")
                                ->orWhere('prenom', 'like', "%{$mot}%")
                                ->orWhere('email', 'like', "%{$mot}%");
                        });
                    }
                })
                ->limit(50)
                ->get()
                ->map(function (Contact $contact) {
                    $type = match (true) {
                        (bool) $contact->eleve => $contact->eleve->profil,
                        (bool) $contact->agent_de_joueur => 'contact agent_de_joueur',
                        (bool) $contact->salon => 'contact salon',
                        default => 'contact',
                    };

                    return [
                        'contact' => $contact,
                        'type' => $type,
                        'etablissements' => $contact->ecoles->pluck('etablissement')->join(', '),
                    ];
                });
        }

        return view('recherche.index', ['resultats' => $resultats, 'terme' => $terme]);
    }
}
