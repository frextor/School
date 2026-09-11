@extends('layouts.app')

@section('title', 'Types de documents')

@section('content')
    <h1>Types de documents (entreprises)</h1>
    <a class="btn" href="{{ route('referentiel.types-piece.create') }}">+ Nouveau type</a>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Période associée</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($types as $type)
                <tr>
                    <td>{{ $type->type }}</td>
                    <td>{{ $type->periode_associee ? 'Oui' : 'Non' }}</td>
                    <td>
                        <a href="{{ route('referentiel.types-piece.edit', $type) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.types-piece.destroy', $type) }}" style="display:inline" onsubmit="return confirm('Supprimer ce type ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Aucun type de document.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $types->links() }}
@endsection
