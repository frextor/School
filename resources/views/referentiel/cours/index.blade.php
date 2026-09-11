@extends('layouts.app')

@section('title', 'Cours')

@section('content')
    <h1>Cours</h1>
    <a class="btn" href="{{ route('referentiel.cours.create') }}">+ Nouveau cours</a>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>UE</th>
                <th>Années</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cours as $cour)
                <tr>
                    <td>{{ $cour->code_cours }}</td>
                    <td>{{ $cour->nom_cours }}</td>
                    <td>{{ $cour->unite?->nom_unite_enseignement }}</td>
                    <td>{{ $cour->annees->pluck('annee')->join(', ') ?: '-' }}</td>
                    <td>
                        <a href="{{ route('referentiel.cours.edit', $cour) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.cours.destroy', $cour) }}" style="display:inline" onsubmit="return confirm('Supprimer ce cours ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun cours.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $cours->links() }}
@endsection
