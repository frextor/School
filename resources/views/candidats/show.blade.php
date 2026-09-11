@extends('layouts.app')

@section('title', 'Fiche candidat')

@section('content')
    <a href="{{ route('candidats.index') }}">&larr; Retour</a>
    <h1>{{ $candidat->contact?->nom_complet }}</h1>

    <table>
        <tr><th>Email</th><td>{{ $candidat->contact?->email }}</td></tr>
        <tr><th>Niveau</th><td>{{ $candidat->niveau?->nom_niveau }}</td></tr>
        <tr><th>Visible</th><td>{{ $candidat->visible ? 'Oui' : 'Non' }}</td></tr>
    </table>

    <h2>Épreuves d'admission</h2>
    <table>
        <thead><tr><th>Date</th><th>Lieu</th><th>Présent</th></tr></thead>
        <tbody>
            @forelse ($candidat->epreuvesInscriptions as $inscription)
                <tr>
                    <td>{{ $inscription->epreuve?->date_epreuve?->format('d/m/Y H:i') }}</td>
                    <td>{{ $inscription->epreuve?->lieu }}</td>
                    <td>{{ $inscription->presence ? 'Oui' : 'Non' }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Aucune épreuve programmée.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Résultats</h2>
    <table>
        <thead><tr><th>Épreuve</th><th>Anglais</th><th>Culture G.</th><th>Rédaction</th><th>Entretien</th><th>Décision</th></tr></thead>
        <tbody>
            @forelse ($candidat->resultatsEpreuves as $resultat)
                <tr>
                    <td>{{ $resultat->epreuve?->date_epreuve?->format('d/m/Y') }}</td>
                    <td>{{ $resultat->anglais }}</td>
                    <td>{{ $resultat->culture_generale }}</td>
                    <td>{{ $resultat->epreuve_redaction }}</td>
                    <td>{{ $resultat->entretien }}</td>
                    <td>{{ $resultat->decision }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun résultat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem;display:flex;gap:0.5rem">
        @if ($candidat->visible)
            <form method="post" action="{{ route('candidats.unarchive', $candidat) }}">
                @csrf
                <button type="submit">Désarchiver</button>
            </form>
        @else
            <form method="post" action="{{ route('candidats.archive', $candidat) }}">
                @csrf
                <button type="submit">Archiver</button>
            </form>
        @endif

        <form method="post" action="{{ route('candidats.destroy', $candidat) }}" onsubmit="return confirm('Supprimer ce candidat ?')">
            @csrf
            @method('DELETE')
            <button type="submit">Supprimer</button>
        </form>
    </div>
@endsection
