@extends('layouts.app')

@section('title', 'Recherche')

@section('content')
    <h1>Recherche</h1>

    <form method="get" class="filters">
        <input type="text" name="rechercher" placeholder="Nom, prénom, email..." value="{{ $terme }}" autofocus>
        <button type="submit">Rechercher</button>
    </form>

    @if ($terme !== '')
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Établissement(s)</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resultats as $resultat)
                    @php $contact = $resultat['contact']; @endphp
                    <tr>
                        <td>{{ $contact->nom_complet }}</td>
                        <td>{{ $contact->telephone }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $resultat['etablissements'] ?: '-' }}</td>
                        <td>{{ ucfirst($resultat['type']) }}</td>
                        <td>
                            @if ($contact->eleve)
                                <a href="{{ route('eleves.show', $contact->eleve) }}">Voir</a>
                            @else
                                <a href="{{ route('contacts.show', $contact) }}">Voir</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Aucun résultat.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
@endsection
