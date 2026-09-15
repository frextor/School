<?php

namespace App\Http\Controllers;

use App\Models\Echeance;
use App\Models\Eleve;
use App\Models\SiteConstant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

/**
 * Documents administratifs délivrés à un élève.
 *
 * Le legacy générait ces pièces dans `Pdf.php`, resté non porté (il dépendait
 * des paiements et de la notation). On repart ici du besoin réel des écoles
 * marocaines plutôt que du code d'origine : le certificat de scolarité est
 * demandé en permanence (bourses, CAF, transport, employeur des parents).
 */
class DocumentEleveController extends Controller
{
    /** Constante de configuration portant la ville de l'établissement. */
    private const CONSTANTE_VILLE = 'ville_etablissement';

    /**
     * Ville portée par la mention « Fait à … », c'est-à-dire celle de
     * l'établissement qui délivre le document.
     *
     * `amos_etablissement.code_ville` ne convient pas : c'est un `char(2)`,
     * un code (« CA »), pas un nom de ville. On lit donc une constante de
     * configuration, que l'école renseigne depuis Configuration du site, avec
     * repli sur la ville de l'élève. À défaut, le champ reste à compléter à la
     * main plutôt que d'imprimer une valeur fausse.
     */
    private function villeDelivrance(Eleve $eleve): ?string
    {
        $constante = SiteConstant::where('title', self::CONSTANTE_VILLE)->value('value');

        return $constante ?: $eleve->contact?->ville;
    }

    /** Certificat attestant l'inscription de l'élève pour l'année scolaire en cours. */
    public function certificatScolarite(Eleve $eleve): Response
    {
        $eleve->load(['contact', 'niveau', 'classe.etablissement']);

        $pdf = Pdf::loadView('documents.certificat-scolarite', [
            'eleve' => $eleve,
            'etablissement' => $eleve->etablissement,
            'anneeScolaire' => Echeance::anneeScolaireCourante(),
            'ville' => $this->villeDelivrance($eleve),
        ]);

        $nom = str($eleve->contact?->nom_complet ?? 'eleve-'.$eleve->id_eleve)
            ->ascii()
            ->slug();

        // Affiché dans le navigateur plutôt que téléchargé : le secrétariat
        // l'imprime directement pour le remettre à la famille.
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificat-scolarite-'.$nom.'.pdf"',
        ]);
    }
}
