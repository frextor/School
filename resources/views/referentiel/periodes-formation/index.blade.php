@extends('layouts.app')

@section('title', 'Périodes de formation')

@section('content')
    <h1>Périodes de formation</h1>
    <a class="btn" href="{{ route('referentiel.periodes-formation.create') }}">+ Nouvelle période</a>

    <table>
        <thead>
            <tr>
                <th>Année scolaire</th>
                <th>Période</th>
                <th>Niveaux</th>
                <th>Classes</th>
                <th>Heures/an</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($periodes as $periode)
                <tr>
                    <td>{{ $periode->annee_scolaire }}</td>
                    <td>{{ $periode->periode }}</td>
                    <td>{{ $periode->niveaux->pluck('nom_niveau')->join(', ') ?: '-' }}</td>
                    <td>{{ $periode->classes->pluck('classe')->join(', ') ?: '-' }}</td>
                    <td>{{ $periode->nb_heure_annuel }}</td>
                    <td>
                        <a href="{{ route('referentiel.periodes-formation.edit', $periode) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.periodes-formation.destroy', $periode) }}" style="display:inline" onsubmit="return confirm('Supprimer cette période ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucune période de formation.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $periodes->links() }}
@endsection
