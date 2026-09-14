@extends('layouts.app')

@section('title', 'Règlements')

@section('content')
    <a href="{{ route('eleves.show', $eleve) }}">&larr; Retour à la fiche élève</a>
    <h1>Règlements — {{ $eleve->contact?->nom_complet }}</h1>
    <a class="btn" href="{{ route('paiements.create', $eleve) }}">+ Nouveau règlement</a>

    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Date</th>
                <th>Établissement</th>
                <th>Montant réglé</th>
                <th>Options facturées</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($paiements as $paiement)
                <tr>
                    <td>{{ $paiement->titre }}</td>
                    <td>{{ $paiement->date?->format('d/m/Y') }}</td>
                    <td>{{ $paiement->etablissement?->nom_etablissement }}</td>
                    <td>{{ number_format($paiement->montantTotal(), 2) }} €</td>
                    <td>{{ $paiement->options->isNotEmpty() ? number_format($paiement->options->sum('montant'), 2).' €' : '—' }}</td>
                    <td><a href="{{ route('paiements.show', $paiement) }}">Détail</a></td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun règlement.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
