@extends('layouts.app')

@section('title', 'Notes')

@section('content')
    <a href="{{ route('evaluations.index') }}">&larr; Retour</a>
    <h1>Notes — {{ $evaluation->nom_evaluation }}</h1>
    <p>{{ $evaluation->unite?->nom_unite_enseignement }} / {{ $evaluation->matiere?->nom_cours }} — {{ $evaluation->date_evaluation->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Élève</th>
                <th>Note (session 1)</th>
                <th>Note (session 2)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($evaluation->notes->groupBy('id_eleve') as $idEleve => $notesEleve)
                @php
                    $eleve = $notesEleve->first()->eleve;
                    $noteS1 = $notesEleve->firstWhere('session', 1);
                    $noteS2 = $notesEleve->firstWhere('session', 2);
                @endphp
                <tr>
                    <td>{{ $eleve?->contact?->nom_complet }}</td>
                    <td>
                        <form method="post" action="{{ route('notes.update') }}" style="display:flex;gap:.5rem">
                            @csrf
                            <input type="hidden" name="id_evaluation" value="{{ $evaluation->id_evaluation }}">
                            <input type="hidden" name="id_eleve" value="{{ $idEleve }}">
                            <input type="hidden" name="id_note" value="{{ $noteS1?->id_note }}">
                            <input type="hidden" name="old_note" value="{{ $noteS1?->note }}">
                            <input type="text" name="note" value="{{ $noteS1?->note }}" size="4">
                            <input type="text" name="raison" placeholder="Raison (si modif.)" size="12">
                            <button type="submit">Enregistrer</button>
                        </form>
                    </td>
                    <td>{{ $noteS2?->note ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune note saisie.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
