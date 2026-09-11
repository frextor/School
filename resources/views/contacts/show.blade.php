@extends('layouts.app')

@section('title', 'Fiche contact')

@section('content')
    <a href="{{ route('contacts.index') }}">&larr; Retour à la liste</a>
    <h1>{{ $contact->civilite }} {{ $contact->nom_complet }}</h1>

    <table>
        <tr><th>Email</th><td>{{ $contact->email }}</td></tr>
        <tr><th>Téléphone</th><td>{{ $contact->telephone }}</td></tr>
        <tr><th>Ville</th><td>{{ $contact->ville }} {{ $contact->code_postal }}</td></tr>
        <tr><th>Formation</th><td>{{ $contact->formation?->niveau ?? '-' }}</td></tr>
        <tr><th>Établissements candidatés</th><td>{{ $contact->ecoles->pluck('etablissement')->join(', ') ?: '-' }}</td></tr>
        <tr><th>Réunion d'information</th>
            <td>
                @forelse ($contact->inscriptionsReunion as $inscription)
                    {{ $inscription->reunion?->lieu }} — {{ $inscription->reunion?->date?->format('d/m/Y H:i') }}
                @empty
                    -
                @endforelse
            </td>
        </tr>
        <tr><th>Newsletter</th><td>{{ $contact->newsletter ? 'Oui' : 'Non' }}</td></tr>
        <tr><th>Élève</th><td>{{ $contact->eleve ? 'Oui ('.ucfirst($contact->eleve->profil).')' : 'Non' }}</td></tr>
        <tr><th>Stop relances</th><td>{{ $contact->stop_relances ? 'Oui' : 'Non' }}</td></tr>
        <tr><th>Annotation</th><td>{{ $contact->annotation ?: '-' }}</td></tr>
    </table>

    <p style="margin-top:1rem">
        <a class="btn" href="{{ route('contacts.edit', $contact) }}">Modifier</a>
    </p>

    <h2>Annotations</h2>
    <ul>
        @forelse ($contact->annotations as $annotation)
            <li>{{ $annotation->created_at?->format('d/m/Y H:i') }} — {{ $annotation->content }}</li>
        @empty
            <li>Aucune annotation.</li>
        @endforelse
    </ul>

    <form method="post" action="{{ route('contacts.destroy', $contact) }}" onsubmit="return confirm('Supprimer ce contact ?')">
        @csrf
        @method('DELETE')
        <button type="submit">Supprimer le contact</button>
    </form>
@endsection
