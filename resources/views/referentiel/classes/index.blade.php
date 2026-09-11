@extends('layouts.app')

@section('title', 'Classes')

@section('content')
    <h1>Classes</h1>
    <a class="btn" href="{{ route('referentiel.classes.create') }}">+ Nouvelle classe</a>

    <table>
        <thead>
            <tr>
                <th>Classe</th>
                <th>Niveau</th>
                <th>Établissement</th>
                <th>Couleur</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($classes as $classe)
                <tr>
                    <td>{{ $classe->classe }}</td>
                    <td>{{ $classe->niveau?->nom_niveau }}</td>
                    <td>{{ $classe->etablissement?->nom_etablissement }}</td>
                    <td><span style="display:inline-block;width:16px;height:16px;background:{{ $classe->couleur }}"></span></td>
                    <td>
                        <a href="{{ route('referentiel.classes.edit', $classe) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.classes.destroy', $classe) }}" style="display:inline" onsubmit="return confirm('Supprimer cette classe ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune classe.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $classes->links() }}
@endsection
