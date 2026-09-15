<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Tuteur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Parents / tuteurs légaux rattachés à un élève (orientation K-12).
 *
 * Toutes les actions sont portées par la fiche élève (onglet « Famille ») :
 * il n'y a pas d'écran de gestion des tuteurs indépendant, un tuteur n'ayant
 * pas de sens hors du ou des élèves qu'il accompagne.
 */
class TuteurController extends Controller
{
    /** Champs d'identité du tuteur, partagés par la création et la mise à jour. */
    private function reglesIdentite(): array
    {
        return [
            'civilite' => ['nullable', 'string', 'max:10'],
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'telephone_pro' => ['nullable', 'string', 'max:30'],
            'profession' => ['nullable', 'string', 'max:100'],
            'cin' => ['nullable', 'string', 'max:30'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'ville' => ['nullable', 'string', 'max:100'],
            'commentaire' => ['nullable', 'string'],
        ];
    }

    /** Champs portés par la liaison élève↔tuteur (et non par la fiche tuteur). */
    private function reglesLiaison(): array
    {
        return [
            'lien_parente' => ['required', 'in:'.implode(',', array_keys(Tuteur::LIENS))],
            'responsable_legal' => ['boolean'],
            'contact_urgence' => ['boolean'],
        ];
    }

    private function donneesLiaison(Request $request, Eleve $eleve): array
    {
        return [
            'lien_parente' => $request->string('lien_parente')->toString(),
            'responsable_legal' => $request->boolean('responsable_legal'),
            'contact_urgence' => $request->boolean('contact_urgence'),
            // Ordre d'affichage : à la suite des tuteurs déjà rattachés.
            'ordre' => $eleve->tuteurs()->count() + 1,
        ];
    }

    /** Crée une nouvelle fiche tuteur et la rattache à l'élève. */
    public function store(Request $request, Eleve $eleve): RedirectResponse
    {
        $data = $request->validate($this->reglesIdentite() + $this->reglesLiaison());

        DB::transaction(function () use ($data, $request, $eleve) {
            $tuteur = Tuteur::create($data);
            $eleve->tuteurs()->attach($tuteur->id_tuteur, $this->donneesLiaison($request, $eleve));
        });

        return back()->with('status', 'Parent / tuteur ajouté.');
    }

    /**
     * Rattache un tuteur **déjà existant** à cet élève — le cas des fratries :
     * le parent d'un élève déjà inscrit ne doit pas être resaisi.
     */
    public function attacher(Request $request, Eleve $eleve): RedirectResponse
    {
        $data = $request->validate([
            'id_tuteur' => ['required', 'integer', 'exists:tuteurs,id_tuteur'],
        ] + $this->reglesLiaison());

        if ($eleve->tuteurs()->where('tuteurs.id_tuteur', $data['id_tuteur'])->exists()) {
            return back()->withErrors(['id_tuteur' => 'Ce parent est déjà rattaché à cet élève.']);
        }

        $eleve->tuteurs()->attach($data['id_tuteur'], $this->donneesLiaison($request, $eleve));

        return back()->with('status', 'Parent / tuteur rattaché à cet élève.');
    }

    /** Met à jour l'identité du tuteur et son lien avec cet élève. */
    public function update(Request $request, Eleve $eleve, Tuteur $tuteur): RedirectResponse
    {
        $data = $request->validate($this->reglesIdentite() + $this->reglesLiaison());

        DB::transaction(function () use ($data, $request, $eleve, $tuteur) {
            $tuteur->update($data);

            $eleve->tuteurs()->updateExistingPivot($tuteur->id_tuteur, [
                'lien_parente' => $data['lien_parente'],
                'responsable_legal' => $request->boolean('responsable_legal'),
                'contact_urgence' => $request->boolean('contact_urgence'),
            ]);
        });

        $autres = $tuteur->eleves()->count() - 1;

        return back()->with('status', $autres > 0
            ? "Fiche mise à jour (également rattachée à {$autres} autre(s) élève(s))."
            : 'Fiche du parent / tuteur mise à jour.');
    }

    /**
     * Détache le tuteur de cet élève. La fiche n'est supprimée que si elle
     * n'accompagne plus aucun élève, pour ne pas casser les liens d'une fratrie.
     */
    public function destroy(Eleve $eleve, Tuteur $tuteur): RedirectResponse
    {
        $eleve->tuteurs()->detach($tuteur->id_tuteur);

        if ($tuteur->eleves()->count() === 0) {
            $tuteur->delete();

            return back()->with('status', 'Parent / tuteur supprimé.');
        }

        return back()->with('status', 'Parent / tuteur détaché de cet élève (sa fiche reste rattachée à un autre élève).');
    }

    /** Recherche de tuteurs existants, pour le rattachement d'une fratrie. */
    public function recherche(Request $request): JsonResponse
    {
        $terme = $request->string('q')->toString();

        if (mb_strlen($terme) < 2) {
            return response()->json([]);
        }

        $tuteurs = Tuteur::query()
            ->where(fn ($q) => $q->where('nom', 'like', "%{$terme}%")
                ->orWhere('prenom', 'like', "%{$terme}%")
                ->orWhere('email', 'like', "%{$terme}%")
                ->orWhere('telephone', 'like', "%{$terme}%"))
            ->withCount('eleves')
            ->orderBy('nom')
            ->limit(10)
            ->get()
            ->map(fn (Tuteur $t) => [
                'id_tuteur' => $t->id_tuteur,
                'libelle' => $t->nom_complet,
                'detail' => trim(($t->telephone ?: $t->email ?: '').' · '.$t->eleves_count.' élève(s)', ' ·'),
            ]);

        return response()->json($tuteurs);
    }
}
