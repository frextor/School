@extends('documents._base', [
    'titreDocument' => 'Certificat de scolarité',
    'sousTitre' => 'Année scolaire '.$anneeScolaire,
])

@section('document')
@php
    $contact = $eleve->contact;
    $nomEcole = $etablissement?->nom_etablissement ?: config('app.name');
    $dateNaissance = $contact?->date_naissance
        ? \Illuminate\Support\Carbon::parse($contact->date_naissance)->format('d/m/Y')
        : null;
@endphp

<div class="corps">
    <p>
        Je soussigné(e), Directeur / Directrice de {{ $nomEcole }}, certifie que l'élève désigné(e)
        ci-dessous est régulièrement inscrit(e) dans notre établissement au titre de l'année
        scolaire {{ $anneeScolaire }}.
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
        <td class="label">Niveau</td>
        <td class="value">{{ $eleve->niveau?->nom_niveau ?: '—' }}</td>
    </tr>
    @if ($eleve->classe?->classe)
        <tr>
            <td class="label">Classe</td>
            <td class="value">{{ $eleve->classe->classe }}</td>
        </tr>
    @endif
    <tr>
        <td class="label">Année scolaire</td>
        <td class="value">{{ $anneeScolaire }}</td>
    </tr>
</table>

<p class="mention">
    Le présent certificat est délivré à l'intéressé(e) pour servir et valoir ce que de droit.
</p>
@endsection
