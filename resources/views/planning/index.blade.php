@extends('layouts.app')

@section('title', 'Planning')

@section('content')
    <h1>Créneaux de planning</h1>
    <a class="btn" href="{{ route('planning.create') }}">+ Nouveau créneau</a>
    <p style="color:#666;font-size:0.85rem">
        Version simplifiée : pas de vérification de conflit de disponibilité ni de créneaux récurrents.
    </p>

    <table>
        <thead>
            <tr>
                <th>Intervenant</th>
                <th>Cours</th>
                <th>Établissement</th>
                <th>Début</th>
                <th>Fin</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($creneaux as $creneau)
                <tr>
                    <td>{{ $creneau->intervenant?->nom }} {{ $creneau->intervenant?->prenom }}</td>
                    <td>{{ $creneau->cours?->nom_cours }}</td>
                    <td>{{ $creneau->etablissement?->nom_etablissement }}</td>
                    <td>{{ $creneau->date_debut->format('d/m/Y H:i') }}</td>
                    <td>{{ $creneau->date_fin->format('H:i') }}</td>
                    <td>
                        <a href="{{ route('planning.edit', $creneau) }}">Modifier</a>
                        <form method="post" action="{{ route('planning.destroy', $creneau) }}" style="display:inline" onsubmit="return confirm('Supprimer ce créneau ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun créneau.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $creneaux->links() }}
@endsection
