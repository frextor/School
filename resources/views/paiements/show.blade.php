@extends('layouts.app')

@section('title', 'Détail du règlement')

@section('content')
    <a href="{{ route('paiements.index', $paiement->id_eleve) }}">&larr; Retour</a>
    <h1>{{ $paiement->titre }}</h1>
    <p>{{ $paiement->eleve?->contact?->nom_complet }} — {{ $paiement->date?->format('d/m/Y') }}</p>

    <h2>Versements / chèques</h2>
    <table>
        <thead><tr><th>Montant</th><th>Mode</th><th>N° chèque</th><th>Banque</th><th>Date encaissement</th></tr></thead>
        <tbody>
            @forelse ($paiement->cheques as $cheque)
                <tr>
                    <td>{{ number_format($cheque->montant_paiement, 2) }} €</td>
                    <td>{{ $cheque->mode_paiement ?: '-' }}</td>
                    <td>{{ $cheque->numero_cheque ?: '-' }}</td>
                    <td>{{ $cheque->nom_banque ?: '-' }}</td>
                    <td>{{ $cheque->date_encaissement?->format('d/m/Y') ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun versement.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Ajouter un versement</h3>
    <form method="post" action="{{ route('paiements.cheques.store', $paiement) }}" enctype="multipart/form-data">
        @csrf

        <label for="montant_paiement">Montant</label>
        <input type="number" step="0.01" name="montant_paiement" id="montant_paiement" required>

        <label for="mode_paiement">Mode</label>
        <input type="text" name="mode_paiement" id="mode_paiement" placeholder="CB, chèque, virement...">

        <label for="numero_cheque">N° chèque (si applicable)</label>
        <input type="text" name="numero_cheque" id="numero_cheque">

        <label for="nom_banque">Banque</label>
        <input type="text" name="nom_banque" id="nom_banque">

        <label for="date_encaissement">Date d'encaissement</label>
        <input type="date" name="date_encaissement" id="date_encaissement">

        <label for="photo_cheque">Photo du chèque</label>
        <input type="file" name="photo_cheque" id="photo_cheque">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Ajouter</button>
        </p>
    </form>

    <form method="post" action="{{ route('paiements.destroy', $paiement) }}" style="margin-top:2rem" onsubmit="return confirm('Supprimer ce règlement ?')">
        @csrf
        @method('DELETE')
        <button type="submit">Supprimer ce règlement</button>
    </form>
@endsection
