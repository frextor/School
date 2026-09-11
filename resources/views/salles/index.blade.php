@extends('layouts.app')

@section('title', 'Salles')

@section('content')
    <h1>Salles</h1>
    <a class="btn" href="{{ route('salles.create') }}">+ Nouvelle salle</a>

    <table>
        <thead><tr><th>Code</th><th>Nom</th><th>Places</th><th>Établissement</th><th></th></tr></thead>
        <tbody>
            @forelse ($salles as $salle)
                <tr>
                    <td>{{ $salle->code_salle }}</td>
                    <td>{{ $salle->nom_salle }}</td>
                    <td>{{ $salle->nombre_place }}</td>
                    <td>{{ $salle->etablissement?->nom_etablissement }}</td>
                    <td>
                        <a href="{{ route('salles.edit', $salle) }}">Modifier</a>
                        <form method="post" action="{{ route('salles.destroy', $salle) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune salle.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $salles->links() }}
@endsection
