@extends('layouts.app')

@section('title', 'Fiche intervenant')

@section('content')
    <a href="{{ route('referentiel.intervenants.index') }}">&larr; Retour à la liste</a>
    <h1>{{ $intervenant->civilite }} {{ $intervenant->nom }} {{ $intervenant->prenom }}</h1>

    <table>
        <tr><th>Email</th><td>{{ $intervenant->email }}</td></tr>
        <tr><th>Téléphone</th><td>{{ $intervenant->telephone ?: '-' }}</td></tr>
        <tr><th>Mobile</th><td>{{ $intervenant->mobile ?: '-' }}</td></tr>
        <tr><th>Adresse</th><td>{{ $intervenant->adresse }} {{ $intervenant->code_postal }} {{ $intervenant->ville }}</td></tr>
        <tr><th>Profession</th><td>{{ $intervenant->profession ?: '-' }}</td></tr>
        <tr><th>Poste actuel</th><td>{{ $intervenant->poste_actuel ?: '-' }}</td></tr>
        <tr><th>Société</th><td>{{ $intervenant->societe?->raison_sociale ?? '-' }}</td></tr>
        <tr><th>Cours enseignés</th><td>{{ $intervenant->cours->pluck('nom_cours')->join(', ') ?: '-' }}</td></tr>
        <tr><th>Établissements</th><td>{{ $intervenant->etablissements->pluck('nom_etablissement')->join(', ') ?: '-' }}</td></tr>
        <tr><th>Compétences</th><td>{{ $intervenant->competences->pluck('nom_competence')->join(', ') ?: '-' }}</td></tr>
        <tr><th>Diplômes</th><td>{{ $intervenant->diplomes->pluck('titre_diplome')->join(', ') ?: '-' }}</td></tr>
        <tr><th>Secteurs d'activité</th><td>{{ $intervenant->secteursActivite->pluck('nom_secteur_activite')->join(', ') ?: '-' }}</td></tr>
        @if ($intervenant->cheminCv())
            <tr><th>CV</th><td><a href="{{ Storage::disk('public')->url($intervenant->cheminCv()) }}" target="_blank">Télécharger</a></td></tr>
        @endif
        @if ($intervenant->cheminPhoto())
            <tr><th>Photo</th><td><img src="{{ Storage::disk('public')->url($intervenant->cheminPhoto()) }}" style="max-width:150px"></td></tr>
        @endif
    </table>

    <div style="margin-top:1rem;display:flex;gap:0.5rem">
        <a class="btn" href="{{ route('referentiel.intervenants.edit', $intervenant) }}">Modifier</a>

        <form method="post" action="{{ route('referentiel.intervenants.destroy', $intervenant) }}" onsubmit="return confirm('Supprimer cet intervenant ?')">
            @csrf
            @method('DELETE')
            <button type="submit">Supprimer</button>
        </form>
    </div>
@endsection
