@extends('layouts.app')

@section('title', 'Évaluations')

@section('content')
    <h1>Évaluations</h1>
    <a class="btn" href="{{ route('evaluations.create') }}">+ Nouvelle évaluation</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Date</th>
                <th>Campus</th>
                <th>UE</th>
                <th>Matière</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($evaluations as $evaluation)
                <tr>
                    <td>{{ $evaluation->nom_evaluation }}</td>
                    <td>{{ $evaluation->date_evaluation->format('d/m/Y') }}</td>
                    <td>{{ $evaluation->campus?->nom_etablissement }}</td>
                    <td>{{ $evaluation->unite?->nom_unite_enseignement }}</td>
                    <td>{{ $evaluation->matiere?->nom_cours }}</td>
                    <td>
                        <a href="{{ route('notes.index', $evaluation) }}">Notes</a>
                        <a href="{{ route('evaluations.edit', $evaluation) }}">Modifier</a>
                        <form method="post" action="{{ route('evaluations.destroy', $evaluation) }}" style="display:inline" onsubmit="return confirm('Supprimer cette évaluation ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucune évaluation.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $evaluations->links() }}
@endsection
