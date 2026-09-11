@extends('layouts.app')

@section('title', 'Niveaux')

@section('content')
    <h1>Niveaux</h1>
    <a class="btn" href="{{ route('referentiel.niveaux.create') }}">+ Nouveau niveau</a>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Formation</th>
                <th>Campus</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($niveaux as $niveau)
                <tr>
                    <td>{{ $niveau->code_niveau }}</td>
                    <td>{{ $niveau->nom_niveau }}</td>
                    <td>{{ $niveau->formation?->niveau }}</td>
                    <td>{{ $niveau->etablissements->pluck('nom_etablissement')->join(', ') ?: '-' }}</td>
                    <td>
                        <a href="{{ route('referentiel.niveaux.edit', $niveau) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.niveaux.destroy', $niveau) }}" style="display:inline" onsubmit="return confirm('Supprimer ce niveau ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun niveau.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $niveaux->links() }}
@endsection
