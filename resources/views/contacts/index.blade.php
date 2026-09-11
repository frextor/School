@extends('layouts.app')

@section('title', 'Contacts')

@section('content')
    <h1>Contacts</h1>

    <form method="get" class="filters">
        <input type="text" name="recherche" placeholder="Nom, prénom, email..." value="{{ $filtres['recherche'] ?? '' }}">
        <label><input type="checkbox" name="agent_de_joueur" value="1" @checked($filtres['agent_de_joueur'] ?? false)> Agents de joueur</label>
        <label><input type="checkbox" name="salon" value="1" @checked($filtres['salon'] ?? false)> Salons</label>
        <button type="submit">Filtrer</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contacts as $contact)
                <tr>
                    <td>{{ $contact->civilite }} {{ $contact->nom_complet }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->telephone }}</td>
                    <td>{{ $contact->eleve ? ucfirst($contact->eleve->profil) : 'Contact' }}</td>
                    <td><a href="{{ route('contacts.show', $contact) }}">Voir</a></td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun contact.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $contacts->links() }}
@endsection
