@extends('layouts.app')

@section('title', 'Famille · '.$contact->nom_complet)

@section('content')
@php
    $initiales = mb_strtoupper(mb_substr($contact->nom ?: '?', 0, 1).mb_substr($contact->prenom ?: '', 0, 1));
    $ville = collect([$contact->code_postal, $contact->ville])->filter()->implode(' ');
    $ecoles = $contact->ecoles->pluck('etablissement')->filter();
@endphp

<div class="crumb">
    <a href="{{ route('contacts.index') }}">Familles &amp; prospects</a>
    <span class="sep">/</span>
    <span class="current">{{ $contact->nom_complet }}</span>
</div>

<div class="page-head">
    <div class="fiche-tete">
        <span class="fiche-photo fiche-initiales">{{ $initiales }}</span>
        <div>
            <div class="title-row">
                <h1>{{ $contact->civilite }} {{ $contact->nom_complet }}</h1>
                @if ($contact->eleve)
                    <span class="badge badge-brand">{{ ucfirst($contact->eleve->profil) }}</span>
                @else
                    <span class="badge">Prospect</span>
                @endif
                @if ($contact->stop_relances)
                    <span class="badge badge-danger">Ne pas relancer</span>
                @endif
            </div>
            <p class="page-sub">{{ $contact->email ?: 'Aucune adresse e-mail' }}</p>
        </div>
    </div>
    <div class="page-actions">
        @if ($contact->eleve)
            <a class="btn btn-ghost" href="{{ route('eleves.show', $contact->eleve) }}">
                @include('partials.icon', ['n' => 'users', 's' => 15, 'w' => 2])Fiche élève
            </a>
        @endif
        <a class="btn" href="{{ route('contacts.edit', $contact) }}">
            @include('partials.icon', ['n' => 'pencil', 's' => 15, 'c' => '#fff', 'w' => 2])Modifier
        </a>
    </div>
</div>

<div class="form-page is-wide">
    <section class="form-card">
        <div class="form-head"><h2>Coordonnées</h2></div>
        <div class="detail-list">
            <div class="detail-line"><span>E-mail</span><span>{{ $contact->email ?: '—' }}</span></div>
            <div class="detail-line"><span>Téléphone</span><span>{{ $contact->telephone ?: '—' }}</span></div>
            <div class="detail-line"><span>Ville</span><span>{{ $ville ?: '—' }}</span></div>
            <div class="detail-line"><span>Cycle visé</span><span>{{ $contact->formation?->niveau ?? '—' }}</span></div>
            <div class="detail-line"><span>Newsletter</span><span>{{ $contact->newsletter ? 'Inscrit' : 'Non inscrit' }}</span></div>
            <div class="detail-line"><span>Relances</span><span>{{ $contact->stop_relances ? 'Arrêtées' : 'Actives' }}</span></div>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head"><h2>Candidature</h2></div>
        <div class="form-body">
            <span class="field-label">Établissements visés</span>
            @if ($ecoles->isEmpty())
                <p class="field-aide" style="margin:0">Aucun établissement renseigné.</p>
            @else
                <div class="pick-list">
                    @foreach ($ecoles as $ecole)
                        <span class="badge badge-brand">{{ $ecole }}</span>
                    @endforeach
                </div>
            @endif

            <span class="field-label" style="margin-top:18px">Réunions d'information</span>
            @forelse ($contact->inscriptionsReunion as $inscription)
                <p class="ct-reunion">
                    {{ $inscription->reunion?->lieu ?: 'Lieu non précisé' }}
                    @if ($inscription->reunion?->date)
                        · {{ $inscription->reunion->date->format('d/m/Y H:i') }}
                    @endif
                </p>
            @empty
                <p class="field-aide" style="margin:0">Aucune inscription.</p>
            @endforelse
        </div>
    </section>

    <section class="form-card">
        <div class="form-head">
            <h2>Annotations</h2>
            <span class="form-sub">Le fil de suivi de la famille.</span>
        </div>
        <div class="form-body">
            @if ($contact->annotation)
                <p class="ct-annotation-fixe">{{ $contact->annotation }}</p>
            @endif

            @forelse ($contact->annotations as $annotation)
                <div class="ct-note">
                    <span class="ct-note-date">{{ $annotation->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    <p>{{ $annotation->content }}</p>
                </div>
            @empty
                @unless ($contact->annotation)
                    <p class="field-aide" style="margin:0">Aucune annotation.</p>
                @endunless
            @endforelse
        </div>
    </section>
</div>

<form method="post" action="{{ route('contacts.destroy', $contact) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer {{ $contact->nom_complet }} ? Son historique de suivi sera perdu.')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce contact
    </button>
</form>

<style>
    .fiche-tete { display: flex; align-items: center; gap: 15px; }
    .fiche-tete h1 { margin: 0; }
    .fiche-photo { width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0; }
    .fiche-initiales {
        display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(140deg, #6366f1, var(--brand-dark));
        color: #fff; font-size: 17px; font-weight: 700;
    }
    .ct-reunion { margin: 0 0 4px; font-size: 13px; }
    .ct-annotation-fixe { margin: 0 0 14px; font-size: 13px; padding: 11px 13px; background: #fafbfd; border-radius: 10px; }
    .ct-note { padding: 11px 0; border-bottom: 1px solid var(--border-soft); }
    .ct-note:last-child { border-bottom: 0; padding-bottom: 0; }
    .ct-note-date { display: block; font-size: 11.5px; color: var(--faint); }
    .ct-note p { margin: 3px 0 0; font-size: 13px; }
</style>
@endsection
