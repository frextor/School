@extends('layouts.app')

@section('title', 'Enseignant · '.$intervenant->nom.' '.$intervenant->prenom)

@section('content')
@php
    $initiales = mb_strtoupper(mb_substr($intervenant->nom, 0, 1).mb_substr($intervenant->prenom, 0, 1));
    $adresse = collect([$intervenant->adresse, $intervenant->code_postal, $intervenant->ville])->filter()->implode(' ');
@endphp

<div class="crumb">
    <a href="{{ route('referentiel.intervenants.index') }}">Enseignants</a>
    <span class="sep">/</span>
    <span class="current">{{ $intervenant->nom }} {{ $intervenant->prenom }}</span>
</div>

<div class="page-head">
    <div class="fiche-tete">
        @if ($intervenant->cheminPhoto())
            <img class="fiche-photo" src="{{ Storage::disk('public')->url($intervenant->cheminPhoto()) }}" alt="">
        @else
            <span class="fiche-photo fiche-initiales">{{ $initiales }}</span>
        @endif
        <div>
            <h1>{{ $intervenant->civilite === 'M' ? 'M.' : $intervenant->civilite }} {{ $intervenant->nom }} {{ $intervenant->prenom }}</h1>
            <p class="page-sub">{{ $intervenant->poste_actuel ?: $intervenant->profession ?: 'Enseignant' }}</p>
        </div>
    </div>
    <div class="page-actions">
        @if ($intervenant->cheminCv())
            <a class="btn btn-ghost" href="{{ Storage::disk('public')->url($intervenant->cheminCv()) }}" target="_blank" rel="noopener">
                @include('partials.icon', ['n' => 'download', 's' => 15, 'w' => 2])CV
            </a>
        @endif
        <a class="btn" href="{{ route('referentiel.intervenants.edit', $intervenant) }}">
            @include('partials.icon', ['n' => 'pencil', 's' => 15, 'c' => '#fff', 'w' => 2])Modifier
        </a>
    </div>
</div>

<div class="form-page is-wide">
    <section class="form-card">
        <div class="form-head"><h2>Coordonnées</h2></div>
        <div class="detail-list">
            <div class="detail-line"><span>E-mail</span><span>{{ $intervenant->email ?: '—' }}</span></div>
            <div class="detail-line"><span>Téléphone</span><span>{{ $intervenant->telephone ?: '—' }}</span></div>
            <div class="detail-line"><span>Mobile</span><span>{{ $intervenant->mobile ?: '—' }}</span></div>
            <div class="detail-line"><span>Adresse</span><span>{{ $adresse ?: '—' }}</span></div>
            <div class="detail-line"><span>Profession</span><span>{{ $intervenant->profession ?: '—' }}</span></div>
            <div class="detail-line"><span>Société</span><span>{{ $intervenant->societe?->raison_sociale ?: '—' }}</span></div>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Enseignement</h2></div>
        <div class="form-body">
            @php
                $blocs = [
                    'Matières' => $intervenant->cours->pluck('nom_cours'),
                    'Établissements' => $intervenant->etablissements->pluck('nom_etablissement'),
                    'Compétences' => $intervenant->competences->pluck('nom_competence'),
                    'Diplômes' => $intervenant->diplomes->pluck('titre_diplome'),
                    "Secteurs d'activité" => $intervenant->secteursActivite->pluck('nom_secteur_activite'),
                ];
            @endphp

            @foreach ($blocs as $titre => $valeurs)
                @continue($valeurs->isEmpty() && ! in_array($titre, ['Matières', 'Établissements'], true))
                <div class="fiche-bloc">
                    <span class="field-label">{{ $titre }}</span>
                    @if ($valeurs->isEmpty())
                        <p class="field-aide" style="margin:0">Non renseigné.</p>
                    @else
                        <div class="pick-list">
                            @foreach ($valeurs as $valeur)
                                <span class="badge badge-brand">{{ $valeur }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
</div>

<form method="post" action="{{ route('referentiel.intervenants.destroy', $intervenant) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer {{ $intervenant->nom }} {{ $intervenant->prenom }} ? Ses affectations à l\'emploi du temps ne pointeront plus vers personne.')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer cet enseignant
    </button>
</form>

<style>
    .fiche-tete { display: flex; align-items: center; gap: 15px; }
    .fiche-tete h1 { margin: 0; }
    .fiche-photo { width: 52px; height: 52px; border-radius: 14px; object-fit: cover; flex-shrink: 0; }
    .fiche-initiales {
        display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(140deg, #6366f1, var(--brand-dark));
        color: #fff; font-size: 17px; font-weight: 700;
    }
    .fiche-bloc + .fiche-bloc { margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--border-soft); }
</style>
@endsection
