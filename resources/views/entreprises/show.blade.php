@extends('layouts.app')

@section('title', 'Fiche entreprise')

@section('content')
    <a href="{{ route('entreprises.index') }}">&larr; Retour</a>
    <h1>{{ $entreprise->nom_entreprise }}</h1>

    <table>
        <tr><th>Email</th><td>{{ $entreprise->email }}</td></tr>
        <tr><th>Téléphone</th><td>{{ $entreprise->telephone }}</td></tr>
        <tr><th>Adresse</th><td>{{ $entreprise->adresse }} {{ $entreprise->code_postal }} {{ $entreprise->ville }}</td></tr>
        <tr><th>Secteur</th><td>{{ $entreprise->secteur?->nom_secteur ?? '-' }}</td></tr>
        <tr><th>Établissement</th><td>{{ $entreprise->etablissement?->nom_etablissement ?? '-' }}</td></tr>
        <tr><th>Site web</th><td>{{ $entreprise->site_web ?: '-' }}</td></tr>
        <tr><th>SIRET</th><td>{{ $entreprise->siret ?: '-' }}</td></tr>
        <tr><th>Compte portail</th><td>{{ $entreprise->compteLoginPortail ? 'Oui ('.$entreprise->compteLoginPortail->username.')' : 'Non' }}</td></tr>
    </table>

    <p style="margin-top:1rem">
        <a class="btn" href="{{ route('entreprises.edit', $entreprise) }}">Modifier</a>
    </p>

    <h2>Contacts</h2>
    <ul>
        @forelse ($entreprise->contacts as $contact)
            <li>{{ $contact->civilite }} {{ $contact->nom }} {{ $contact->prenom }} — {{ $contact->email }} — {{ $contact->telephone }}</li>
        @empty
            <li>Aucun contact.</li>
        @endforelse
    </ul>

    <h2>Filiales</h2>
    <ul>
        @forelse ($entreprise->filiales as $filiale)
            <li>{{ $filiale->nom_entreprise }}</li>
        @empty
            <li>Aucune filiale.</li>
        @endforelse
    </ul>

    <form method="post" action="{{ route('entreprises.destroy', $entreprise) }}" onsubmit="return confirm('Supprimer cette entreprise ?')">
        @csrf
        @method('DELETE')
        <button type="submit">Supprimer</button>
    </form>
@endsection
