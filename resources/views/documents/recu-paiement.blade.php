@extends('documents._base', [
    'titreDocument' => 'Reçu de paiement',
    'sousTitre' => 'N° '.$numeroRecu,
])

@section('document')
@php
    $contact = $eleve->contact;
    $nomEcole = $etablissement?->nom_etablissement ?: config('app.name');
    $dh = fn ($v) => number_format((float) $v, 2, ',', ' ').' DH';
@endphp

<div class="corps">
    <p>
        {{ $nomEcole }} accuse réception du règlement détaillé ci-dessous, au titre de la scolarité
        de l'élève <strong>{{ $contact?->nom_complet ?? '—' }}</strong>
        @if ($eleve->classe?->classe)(classe de {{ $eleve->classe->classe }})@endif
        pour l'année scolaire {{ $echeance->annee_scolaire }}.
    </p>
</div>

<table class="recap">
    <tr>
        <td class="label">Reçu n°</td>
        <td class="value">{{ $numeroRecu }}</td>
    </tr>
    <tr>
        <td class="label">Élève</td>
        <td class="value">{{ $contact?->nom_complet ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Objet</td>
        <td class="value">{{ $echeance->libelle }}</td>
    </tr>
    <tr>
        <td class="label">Montant dû</td>
        <td class="value">{{ $dh($echeance->montant) }}</td>
    </tr>
    <tr>
        <td class="label">Montant réglé</td>
        <td class="value is-ok">{{ $dh($echeance->montant_regle) }}</td>
    </tr>
    @if ($echeance->reste > 0)
        <tr>
            <td class="label">Reste dû sur cette échéance</td>
            <td class="value is-warn">{{ $dh($echeance->reste) }}</td>
        </tr>
    @endif
    <tr>
        <td class="label">Mode de règlement</td>
        <td class="value">{{ $echeance->mode_reglement ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Date du règlement</td>
        <td class="value">{{ $echeance->date_reglement?->format('d/m/Y') ?? '—' }}</td>
    </tr>
</table>

@if ($echeance->reste > 0)
    <div class="alerte">
        Ce reçu correspond à un <strong>règlement partiel</strong> :
        {{ $dh($echeance->reste) }} restent dus sur cette échéance.
    </div>
@endif

<p class="mention">
    Le présent reçu est délivré à l'intéressé(e) pour servir et valoir ce que de droit.
</p>
@endsection
