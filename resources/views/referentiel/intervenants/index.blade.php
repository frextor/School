@extends('layouts.app')

@section('title', 'Intervenants')

@section('content')
    <h1>Intervenants</h1>
    <a class="btn" href="{{ route('referentiel.intervenants.create') }}">+ Nouvel intervenant</a>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Société</th>
                <th>Téléphone</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($intervenants as $intervenant)
                <tr>
                    <td>{{ $intervenant->civilite }} {{ $intervenant->nom }} {{ $intervenant->prenom }}</td>
                    <td>{{ $intervenant->email }}</td>
                    <td>{{ $intervenant->societe?->raison_sociale ?? '-' }}</td>
                    <td>{{ $intervenant->telephone ?: $intervenant->mobile }}</td>
                    <td>
                        <a href="{{ route('referentiel.intervenants.show', $intervenant) }}">Voir</a>
                        <form method="post" action="{{ route('referentiel.intervenants.destroy', $intervenant) }}" style="display:inline" onsubmit="return confirm('Supprimer cet intervenant ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun intervenant.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $intervenants->links() }}
@endsection
