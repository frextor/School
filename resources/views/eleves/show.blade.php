@extends('layouts.app')

@section('title', 'Fiche élève')

@section('content')
    <a href="{{ route('eleves.index') }}">&larr; Retour à la liste</a>
    <h1>{{ $eleve->contact?->nom_complet ?? 'Élève #' . $eleve->id_eleve }}</h1>

    <table>
        <tr><th>Profil</th><td>{{ $eleve->profil }}</td></tr>
        <tr><th>Niveau</th><td>{{ $eleve->niveau?->nom_niveau ?? '—' }}</td></tr>
        <tr><th>Niveau futur</th><td>{{ $eleve->niveauFuture?->nom_niveau ?? '—' }}</td></tr>
        <tr><th>Classe</th><td>{{ $eleve->classe?->classe ?? '—' }}</td></tr>
        <tr><th>Email</th><td>{{ $eleve->contact?->email ?? '—' }}</td></tr>
        <tr><th>Téléphone</th><td>{{ $eleve->contact?->telephone ?? '—' }}</td></tr>
        <tr><th>Date d'inscription</th><td>{{ $eleve->date_inscription?->format('d/m/Y') ?? '—' }}</td></tr>
        <tr><th>Montant formation</th><td>{{ $eleve->montant_formation ?: '—' }}</td></tr>
        <tr>
            <th>Statut paiement</th>
            <td>
                @php
                    $statutPaiement = $eleve->paiement_formation ?: 'Non payé';
                    $badgePaiement = match (true) {
                        str_contains(strtolower($statutPaiement), 'payé') && ! str_contains(strtolower($statutPaiement), 'non') => 'badge-success',
                        str_contains(strtolower($statutPaiement), 'opco') => 'badge-brand',
                        default => 'badge-danger',
                    };
                @endphp
                <span class="badge {{ $badgePaiement }}">{{ $statutPaiement }}</span>
            </td>
        </tr>
        <tr><th>Visible</th><td>{{ $eleve->visible ? 'Oui' : 'Non' }}</td></tr>
    </table>

    <p style="margin-top:1rem;display:flex;gap:0.5rem">
        <a class="btn" href="{{ route('eleves.edit', $eleve) }}">Modifier</a>
        <a class="btn btn-ghost" href="{{ route('paiements.index', $eleve) }}">Voir les règlements</a>
    </p>

    <form method="post" action="{{ route('eleves.destroy', $eleve) }}" onsubmit="return confirm('Masquer cet élève ?')">
        @csrf
        @method('DELETE')
        <button type="submit">Masquer l'élève</button>
    </form>
@endsection
