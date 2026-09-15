<?php

namespace App\Http\Controllers;

use App\Models\Echeance;
use App\Models\Eleve;
use App\Models\SiteConstant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/**
 * Documents administratifs délivrés à un élève.
 *
 * Le legacy générait ces pièces dans `Pdf.php`, resté non porté (il dépendait
 * des paiements et de la notation). On repart ici du besoin réel des écoles
 * marocaines plutôt que du code d'origine.
 *
 * Réserve assumée : les formulations employées sont d'usage courant, pas des
 * modèles officiels certifiés par le ministère — à faire relire par une école
 * avant diffusion.
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

    /** Renvoie le PDF pour affichage dans le navigateur, prêt à imprimer. */
    private function afficher(string $vue, array $donnees, string $prefixe, Eleve $eleve): Response
    {
        $nom = str($eleve->contact?->nom_complet ?? 'eleve-'.$eleve->id_eleve)->ascii()->slug();

        // Affiché dans le navigateur plutôt que téléchargé : le secrétariat
        // l'imprime directement pour le remettre à la famille.
        return response(Pdf::loadView($vue, $donnees)->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$prefixe.'-'.$nom.'.pdf"',
        ]);
    }

    /** Certificat attestant l'inscription de l'élève pour l'année scolaire en cours. */
    public function certificatScolarite(Eleve $eleve): Response
    {
        $eleve->load(['contact', 'niveau', 'classe.etablissement']);

        return $this->afficher('documents.certificat-scolarite', [
            'eleve' => $eleve,
            'etablissement' => $eleve->etablissement,
            'anneeScolaire' => Echeance::anneeScolaireCourante(),
            'ville' => $this->villeDelivrance($eleve),
        ], 'certificat-scolarite', $eleve);
    }

    /**
     * Certificat de radiation : la pièce qu'un autre établissement réclame pour
     * inscrire l'élève.
     *
     * Le document rappelle la situation financière, car une école ne le délivre
     * en général qu'une fois la scolarité soldée. Le calcul est affiché mais
     * **ne bloque pas** la génération : la décision appartient à la direction,
     * pas au logiciel.
     */
    public function certificatRadiation(Request $request, Eleve $eleve): Response
    {
        $data = $request->validate([
            'date_radiation' => ['nullable', 'date'],
            'motif' => ['nullable', 'string', 'max:150'],
        ]);

        $eleve->load(['contact', 'niveau', 'classe.etablissement']);
        $annee = Echeance::anneeScolaireCourante();

        $echeances = $eleve->echeances()->annee($annee)->get();
        $resteDu = $echeances->sum(fn (Echeance $e) => $e->reste);

        return $this->afficher('documents.certificat-radiation', [
            'eleve' => $eleve,
            'etablissement' => $eleve->etablissement,
            'anneeScolaire' => $annee,
            'ville' => $this->villeDelivrance($eleve),
            'dateRadiation' => isset($data['date_radiation']) ? Carbon::parse($data['date_radiation']) : Carbon::today(),
            'motif' => $data['motif'] ?? null,
            'resteDu' => $resteDu,
        ], 'certificat-radiation', $eleve);
    }

    /** Reçu d'un règlement encaissé sur une échéance de scolarité. */
    public function recuPaiement(Echeance $echeance): Response|RedirectResponse
    {
        if ((float) $echeance->montant_regle <= 0) {
            return back()->withErrors([
                'echeance' => "Aucun règlement encaissé sur cette échéance : il n'y a rien à justifier par un reçu.",
            ]);
        }

        $eleve = Eleve::with(['contact', 'classe.etablissement'])->findOrFail($echeance->id_eleve);

        return $this->afficher('documents.recu-paiement', [
            'eleve' => $eleve,
            'echeance' => $echeance,
            'etablissement' => $eleve->etablissement,
            'ville' => $this->villeDelivrance($eleve),
            // Numéro stable et lisible : l'identifiant de l'échéance suffit à
            // le rendre unique, l'année le rend parlant pour la comptabilité.
            'numeroRecu' => 'REC-'.substr($echeance->annee_scolaire, 0, 4).'-'.str_pad((string) $echeance->id_echeance, 6, '0', STR_PAD_LEFT),
        ], 'recu-paiement', $eleve);
    }
}
