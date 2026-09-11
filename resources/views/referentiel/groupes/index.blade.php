@extends('layouts.app')

@section('title', 'Groupes')

@section('content')
    <h1>Groupes</h1>
    <a class="btn" href="{{ route('referentiel.groupes.create') }}">+ Nouveau groupe</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Créé le</th>
                <th>Modifié le</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groupes as $groupe)
                <tr>
                    <td>{{ $groupe->nom_groupe }}</td>
                    <td>{{ $groupe->date_creation?->format('d/m/Y H:i') }}</td>
                    <td>{{ $groupe->date_modification?->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('referentiel.groupes.edit', $groupe) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.groupes.destroy', $groupe) }}" style="display:inline" onsubmit="return confirm('Supprimer ce groupe ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Aucun groupe.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $groupes->links() }}
@endsection
