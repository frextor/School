@extends('documents._base', [
    'titreDocument' => 'Certificat de radiation',
    'sousTitre' => 'Année scolaire '.$anneeScolaire,
])

@section('document')
@php
    $contact = $eleve->contact;
    $nomEcole = $etablissement?->nom_etablissement ?: config('app.name');
    $dateNaissance = $contact?->date_naissance
        ? \Illuminate\Support\Carbon::parse($contact->date_naissance)->format('d/m/Y')
        : null;
    $dh = fn ($v) => number_format((float) $v, 2, ',', ' ').' DH';
@endphp

<div class="corps">
    <p>
        Je soussigné(e), Directeur / Directrice de {{ $nomEcole }}, certifie que l'élève désigné(e)
        ci-dessous a été radié(e) des registres de notre établissement à la date du
        <strong>{{ $dateRadiation->format('d/m/Y') }}</strong>, et qu'il / elle est libre de tout
        engagement vis-à-vis de l'établissement sur le plan scolaire.
    </p>
</div>

<table class="recap">
    <tr>
        <td class="label">Nom et prénom</td>
        <td class="value">{{ $contact?->nom_complet ?? '—' }}</td>
    </tr>
    @if ($dateNaissance)
        <tr>
            <td class="label">Né(e) le</td>
            <td class="value">
                {{ $dateNaissance }}@if ($contact?->lieu_naissance) à {{ $contact->lieu_naissance }}@endif
            </td>
        </tr>
    @endif
    <tr>
        <td class="label">Dernier niveau suivi</td>
        <td class="value">{{ $eleve->niveau?->nom_niveau ?: '—' }}</td>
    </tr>
    @if ($eleve->classe?->classe)
        <tr>
            <td class="label">Dernière classe</td>
            <td class="value">{{ $eleve->classe->classe }}</td>
        </tr>
    @endif
    <tr>
        <td class="label">Date de radiation</td>
        <td class="value">{{ $dateRadiation->format('d/m/Y') }}</td>
    </tr>
    @if ($motif)
        <tr>
            <td class="label">Motif</td>
            <td class="value">{{ $motif }}</td>
        </tr>
    @endif
    <tr>
        <td class="label">Situation financière</td>
        <td class="value {{ $resteDu > 0 ? 'is-warn' : 'is-ok' }}">
            {{ $resteDu > 0 ? 'Reste dû : '.$dh($resteDu) : 'Scolarité soldée' }}
        </td>
    </tr>
</table>

@if ($resteDu > 0)
    <div class="alerte">
        <strong>Attention :</strong> la scolarité de cet élève n'est pas soldée
        ({{ $dh($resteDu) }} restant dû au titre de l'année {{ $anneeScolaire }}).
        Vérifiez la politique de l'établissement avant de remettre ce certificat à la famille.
    </div>
@endif

<p class="mention">
    Le présent certificat est délivré à l'intéressé(e) pour servir et valoir ce que de droit,
    notamment en vue d'une inscription dans un autre établissement.
</p>
@endsection
