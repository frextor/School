@extends('layouts.app')

@section('title', 'Matières')

@section('content')
    <h1>Matières</h1>
    <a class="btn" href="{{ route('referentiel.matieres.create') }}">+ Nouvelle matière</a>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>UE</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($matieres as $matiere)
                <tr>
                    <td>{{ $matiere->code_matiere }}</td>
                    <td>{{ $matiere->nom_matiere }}</td>
                    <td>{{ $matiere->unite?->nom_unite_enseignement }}</td>
                    <td>
                        <a href="{{ route('referentiel.matieres.edit', $matiere) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.matieres.destroy', $matiere) }}" style="display:inline" onsubmit="return confirm('Supprimer cette matière ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Aucune matière.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $matieres->links() }}
@endsection
