<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Evaluation;
use App\Models\Note;
use App\Models\NoteHistorique;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Portage du sous-système de saisie des notes de `Notation.php`
 * (update_note / update_note_session_1 / update_note_session_2 / get_notes_history).
 *
 * Chaque évaluation admet 2 sessions de note (rattrapage) ; on garde cette
 * distinction telle quelle plutôt que de la généraliser.
 */
class NoteController extends Controller
{
    public function index(Evaluation $evaluation): View
    {
        $evaluation->load(['notes.eleve.contact', 'unite', 'matiere', 'campus']);

        // L'écran ne listait que les élèves ayant déjà une note : sur une
        // évaluation neuve, il n'affichait personne et aucune première note
        // ne pouvait être saisie. La liste part donc de l'effectif visé.
        $eleves = $this->effectif($evaluation);
        $notesParEleve = $evaluation->notes->groupBy('id_eleve');

        return view('notes.index', [
            'evaluation' => $evaluation,
            'eleves' => $eleves,
            'notesParEleve' => $notesParEleve,
        ]);
    }

    /** Les élèves de la classe ou du groupe visé par l'évaluation. */
    private function effectif(Evaluation $evaluation)
    {
        $requete = $evaluation->referentiel === 'groupe'
            ? Eleve::where('id_groupe', $evaluation->id_referentiel)
            : Eleve::where('id_classe', $evaluation->id_referentiel);

        return $requete
            ->with('contact')
            ->where('profil', Eleve::PROFIL_ELEVE)
            ->where('visible', true)
            ->get()
            ->sortBy(fn (Eleve $e) => mb_strtolower(($e->contact?->nom ?? '').' '.($e->contact?->prenom ?? '')))
            ->values();
    }

    /** Portage unifié de `update_note()` (sessions 1 et 2). */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_evaluation' => ['required', 'integer', 'exists:amos_sn_evaluations_existantes,id_evaluation'],
            'id_eleve' => ['required', 'integer', 'exists:amos_eleves,id_eleve'],
            'id_note' => ['nullable', 'integer', 'exists:amos_sn_base_notes,id_note'],
            'note' => ['nullable', 'string', 'max:11'],
            'old_note' => ['nullable', 'string'],
            'id_note_session_2' => ['nullable', 'integer', 'exists:amos_sn_base_notes,id_note'],
            'note_session_2' => ['nullable', 'string', 'max:11'],
            'old_note_session_2' => ['nullable', 'string'],
            'raison' => ['nullable', 'string'],
        ]);

        $idAdmin = Auth::guard('admin')->id();

        $idNoteS1 = DB::transaction(fn () => $this->enregistrerSession($data, $idAdmin, session: 1));
        $idNoteS2 = DB::transaction(fn () => $this->enregistrerSession($data, $idAdmin, session: 2));

        return response()->json([
            'code' => 200,
            'message' => 'Note bien modifiée',
            'id_note_s1' => $idNoteS1,
            'id_note_s2' => $idNoteS2,
        ]);
    }

    public function history(Note $note): JsonResponse
    {
        return response()->json(NoteHistorique::where('id_note', $note->id_note)->orderByDesc('date')->get());
    }

    private function enregistrerSession(array $data, ?int $idAdmin, int $session): ?int
    {
        $cleNote = $session === 1 ? 'note' : 'note_session_2';
        $cleIdNote = $session === 1 ? 'id_note' : 'id_note_session_2';
        $cleAncienne = $session === 1 ? 'old_note' : 'old_note_session_2';

        if (empty($data[$cleNote])) {
            return null;
        }

        if (! empty($data[$cleIdNote])) {
            $note = Note::findOrFail($data[$cleIdNote]);
            $note->update(['note' => $data[$cleNote]]);
        } else {
            $evaluation = Evaluation::findOrFail($data['id_evaluation']);

            $note = Note::create([
                'id_campus' => $evaluation->id_campus,
                'annee' => $evaluation->annee,
                'id_referentiel' => $evaluation->id_referentiel,
                'id_ue' => $evaluation->id_ue,
                'semestre' => $evaluation->semestre,
                'id_type' => $evaluation->id_type_evaluation,
                'id_matiere' => $evaluation->id_matiere,
                'id_evaluation' => $evaluation->id_evaluation,
                'note' => $data[$cleNote],
                'id_eleve' => $data['id_eleve'],
                'session' => $session,
                'publier_admin' => true,
                'publier_eleve' => true,
                'eval_session' => 0,
                'validation_sans_note' => false,
                'date_saisie' => now(),
                'referentiel' => $evaluation->referentiel,
            ]);
        }

        NoteHistorique::create([
            'id_note' => $note->id_note,
            'ancienne_note' => $data[$cleAncienne] ?? '',
            'nouvelle_note' => $data[$cleNote],
            'raison' => $data['raison'] ?? '',
            'id_admin' => $idAdmin,
            'session' => $session,
        ]);

        return $note->id_note;
    }
}
