@extends('layouts.app')

@section('title', 'Spécialisations')

@section('content')
    <h1>Spécialisations</h1>
    <a class="btn" href="{{ route('referentiel.specialisations.create') }}">+ Nouvelle spécialisation</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Créée le</th>
                <th>Modifiée le</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($specialisations as $specialisation)
                <tr>
                    <td>{{ $specialisation->nom_specialisation }}</td>
                    <td>{{ $specialisation->date_creation?->format('d/m/Y H:i') }}</td>
                    <td>{{ $specialisation->date_modification?->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('referentiel.specialisations.edit', $specialisation) }}">Modifier</a>
                        <form method="post" action="{{ route('referentiel.specialisations.destroy', $specialisation) }}" style="display:inline" onsubmit="return confirm('Supprimer cette spécialisation ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Aucune spécialisation.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $specialisations->links() }}
@endsection
