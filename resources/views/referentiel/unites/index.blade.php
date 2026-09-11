@extends('layouts.app')

@section('title', "Unités d'enseignement")

@section('content')
    <h1>Unités d'enseignement</h1>
    <a class="btn" href="{{ route('referentiel.unites.create') }}">+ Nouvelle UE</a>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Couleur</th>
                <th>Niveau</th>
                <th>Années</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($unites as $unite)
                <tr>
                    <td>{{ $unite->code_unite }}</td>
                    <td>{{ $unite->nom_unite_enseignement }}</td>
                    <td><span style="display:inline-block;width:16px;height:16px;background:{{ $unite->couleur }}"></span></td>
                    <td>{{ $unite->niveau?->nom_niveau }}</td>
                    <td>{{ $unite->annees->pluck('annee')->join(', ') ?: '-' }}</td>
                    <td>
                        <a href="{{ route('referentiel.unites.edit', $unite) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.unites.destroy', $unite) }}" style="display:inline" onsubmit="return confirm('Supprimer cette UE ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucune unité d'enseignement.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $unites->links() }}
@endsection
