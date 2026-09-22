@extends('layouts.app')

@section('title', 'Notes · '.$evaluation->nom_evaluation)

@section('content')
@php
    $bareme = $evaluation->type_notation ?: '20';
    $cible = $evaluation->referentiel === 'groupe'
        ? $evaluation->groupe?->nom_groupe
        : $evaluation->classe?->classe;
    // Le jeu de démonstration a écrit ses notes avec `session = 0`, là où le
    // contrôleur numérote 1 (normale) et 2 (rattrapage). On accepte les deux
    // pour la première session, sinon les notes déjà saisies restaient invisibles.
    $premiere = fn ($notes) => $notes->first(fn ($n) => (int) $n->session <= 1);
    $saisies = $notesParEleve->filter(fn ($notes) => filled($premiere($notes)?->note))->count();
@endphp

<div class="crumb">
    <a href="{{ route('evaluations.index') }}">Évaluations</a>
    <span class="sep">/</span>
    <span class="current">{{ $evaluation->nom_evaluation }}</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $evaluation->nom_evaluation }}</h1>
            <span class="badge badge-brand">/ {{ $bareme }}</span>
        </div>
        <p class="page-sub">
            {{ $evaluation->matiere?->nom_cours }}
            @if ($cible) · {{ $cible }} @endif
            · {{ $evaluation->date_evaluation->format('d/m/Y') }}
        </p>
    </div>
    <div class="page-actions">
        <a class="btn btn-ghost" href="{{ route('evaluations.edit', $evaluation) }}">
            @include('partials.icon', ['n' => 'gear', 's' => 15, 'w' => 2])Modifier l'évaluation
        </a>
    </div>
</div>

<div class="nt-barre">
    <span><strong data-compte>{{ $saisies }}</strong> / {{ $eleves->count() }} note{{ $eleves->count() > 1 ? 's' : '' }} saisie{{ $saisies > 1 ? 's' : '' }}</span>
    {{-- Chaque note part seule dès qu'on quitte le champ : c'est ainsi que
         l'historique de modification garde une trace ligne par ligne. --}}
    <span class="nt-aide">Chaque note s'enregistre en quittant le champ.</span>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Élève</th>
                    <th style="width:150px">Note</th>
                    <th style="width:170px">Rattrapage</th>
                    <th style="width:240px">Motif de correction</th>
                    <th style="width:120px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($eleves as $eleve)
                    @php
                        $notes = $notesParEleve->get($eleve->id_eleve, collect());
                        $s1 = $premiere($notes);
                        $s2 = $notes->firstWhere('session', 2);
                        $nomComplet = $eleve->contact?->nom_complet ?: 'Élève #'.$eleve->id_eleve;
                    @endphp
                    <tr data-ligne data-eleve="{{ $eleve->id_eleve }}"
                        data-note="{{ $s1?->id_note }}" data-note2="{{ $s2?->id_note }}">
                        <td class="cell-name">{{ $nomComplet }}</td>
                        <td>
                            <input type="text" class="nt-note" data-champ="note" inputmode="decimal"
                                   value="{{ $s1?->note }}" placeholder="—"
                                   aria-label="Note de {{ $nomComplet }}">
                        </td>
                        <td>
                            <input type="text" class="nt-note" data-champ="note2" inputmode="decimal"
                                   value="{{ $s2?->note }}" placeholder="—"
                                   aria-label="Note de rattrapage de {{ $nomComplet }}">
                        </td>
                        <td>
                            <input type="text" class="nt-motif" data-champ="raison"
                                   placeholder="Si vous corrigez une note"
                                   aria-label="Motif de correction pour {{ $nomComplet }}">
                        </td>
                        <td><span class="nt-etat" data-etat></span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-cell">
                            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>
                                Aucun élève dans {{ $evaluation->referentiel === 'groupe' ? 'ce groupe' : 'cette classe' }}.
                                Vérifiez le référentiel de l'évaluation.
                            </span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .nt-barre {
        display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
        background: var(--surface); border: 1px solid var(--border); border-radius: 13px;
        padding: 12px 15px; margin-bottom: 14px; font-size: 13px;
    }
    .nt-barre strong { font-variant-numeric: tabular-nums; }
    .nt-aide { margin-left: auto; font-size: 12px; color: var(--muted); }

    .nt-note, .nt-motif { max-width: none; margin: 0; padding: 7px 10px; font-size: 13px; }
    .nt-note { width: 90px; text-align: center; font-variant-numeric: tabular-nums; font-weight: 600; }
    .nt-motif { width: 100%; font-size: 12.5px; }
    .nt-etat { font-size: 11.5px; font-weight: 600; color: var(--muted); }
    .nt-etat.is-ok { color: #047857; }
    .nt-etat.is-ko { color: var(--danger); }
</style>

<script>
    (function () {
        var url = @json(route('notes.update'));
        var jeton = @json(csrf_token());
        var idEvaluation = @json($evaluation->id_evaluation);
        var compte = document.querySelector('[data-compte]');

        function recompter() {
            var n = 0;
            document.querySelectorAll('[data-ligne]').forEach(function (ligne) {
                if (ligne.querySelector('[data-champ="note"]').value.trim() !== '') n++;
            });
            if (compte) compte.textContent = n;
        }

        document.querySelectorAll('[data-ligne]').forEach(function (ligne) {
            var etat = ligne.querySelector('[data-etat]');
            var champs = {
                note: ligne.querySelector('[data-champ="note"]'),
                note2: ligne.querySelector('[data-champ="note2"]'),
                raison: ligne.querySelector('[data-champ="raison"]')
            };
            var initial = { note: champs.note.value, note2: champs.note2.value };

            function envoyer() {
                if (champs.note.value === initial.note && champs.note2.value === initial.note2) {
                    return;
                }

                etat.textContent = 'Enregistrement…';
                etat.className = 'nt-etat';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': jeton
                    },
                    body: JSON.stringify({
                        id_evaluation: idEvaluation,
                        id_eleve: ligne.dataset.eleve,
                        id_note: ligne.dataset.note || null,
                        old_note: initial.note,
                        note: champs.note.value,
                        id_note_session_2: ligne.dataset.note2 || null,
                        old_note_session_2: initial.note2,
                        note_session_2: champs.note2.value,
                        raison: champs.raison.value
                    })
                })
                    .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
                    .then(function (d) {
                        // L'identifiant revient de la réponse : sans lui, une
                        // deuxième correction créerait une seconde ligne.
                        if (d.id_note_s1) ligne.dataset.note = d.id_note_s1;
                        if (d.id_note_s2) ligne.dataset.note2 = d.id_note_s2;
                        initial = { note: champs.note.value, note2: champs.note2.value };
                        champs.raison.value = '';
                        etat.textContent = 'Enregistré';
                        etat.className = 'nt-etat is-ok';
                        recompter();
                        setTimeout(function () { etat.textContent = ''; }, 2000);
                    })
                    .catch(function () {
                        etat.textContent = 'Échec';
                        etat.className = 'nt-etat is-ko';
                    });
            }

            champs.note.addEventListener('blur', envoyer);
            champs.note2.addEventListener('blur', envoyer);
        });
    })();
</script>
@endsection
