@extends('layouts.app')

@section('title', 'Mes évaluations')

@section('content')
    <h1>Mes évaluations</h1>

    @forelse ($notesParUe as $nomUe => $notes)
        <h2>{{ $nomUe }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Type</th>
                    <th>Note</th>
                    <th>Session</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notes as $note)
                    <tr>
                        <td>{{ $note->evaluation?->matiere?->nom_cours }}</td>
                        <td>{{ $note->evaluation?->typeEvaluation?->type?->type }}</td>
                        <td>{{ $note->note }}</td>
                        <td>{{ $note->session }}</td>
                        <td>{{ $note->date_saisie?->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p>Aucune note publiée pour le moment.</p>
    @endforelse
@endsection
