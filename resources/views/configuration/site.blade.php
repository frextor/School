@extends('layouts.app')

@section('title', "Paramètres de l'école")

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Paramètres de l'école</span>
</div>

<div class="page-head">
    <div>
        <h1>Paramètres de l'école</h1>
        <p class="page-sub">Les valeurs reprises un peu partout dans l'application, et les modules ouverts.</p>
    </div>
</div>

<div class="form-page is-wide">
    <section class="form-card">
        <div class="form-head">
            <h2>Constantes</h2>
            <span class="form-sub">Nom, ville, coordonnées : ce que les documents et les en-têtes reprennent.</span>
        </div>

        @forelse ($constants as $constant)
            {{-- Une ligne = un formulaire : chaque valeur s'enregistre seule,
                 sans risquer d'écraser celles qu'on n'a pas touchées. --}}
            <form method="post" action="{{ route('configuration.site.constants.update', $constant) }}" class="cfg-ligne">
                @csrf
                @method('PUT')
                <span class="cfg-label">
                    {{ $constant->label ?: $constant->title }}
                    <span class="cfg-cle">{{ $constant->title }}</span>
                </span>
                <input type="text" name="value" value="{{ $constant->value }}" aria-label="{{ $constant->label }}">
                <button type="submit" class="btn btn-ghost">Enregistrer</button>
            </form>
        @empty
            <div class="form-body">
                <p class="field-aide" style="margin:0">Aucune constante définie.</p>
            </div>
        @endforelse
    </section>

    <section class="form-card">
        <div class="form-head">
            <h2>Sections du site</h2>
            <span class="form-sub">Une section désactivée disparaît pour tout le monde.</span>
        </div>

        @forelse ($accesses as $access)
            <form method="post" action="{{ route('configuration.site.access.toggle', $access) }}" class="cfg-ligne">
                @csrf
                <span class="cfg-label">{{ $access->libelle }}</span>
                @if ($access->status)
                    <span class="badge badge-success">Ouverte</span>
                    <button type="submit" class="btn btn-ghost is-danger">Désactiver</button>
                @else
                    <span class="badge">Fermée</span>
                    <button type="submit" class="btn btn-ghost">Activer</button>
                @endif
            </form>
        @empty
            <div class="form-body">
                <p class="field-aide" style="margin:0">Aucune section déclarée.</p>
            </div>
        @endforelse
    </section>
</div>

<style>
    /* Une ligne de réglage : intitulé, valeur, bouton — et un formulaire par ligne. */
    .cfg-ligne {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap; max-width: none; margin: 0;
        padding: 12px 16px; border-bottom: 1px solid var(--border-soft);
    }
    .cfg-ligne:last-child { border-bottom: 0; }
    .cfg-ligne:hover { background: #fcfcfe; }
    .cfg-label { flex: 1 1 260px; min-width: 0; font-size: 13.5px; font-weight: 600; }
    .cfg-cle { display: block; font-size: 11px; font-weight: 500; color: var(--faint); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
    .cfg-ligne input { flex: 1 1 260px; max-width: 420px; }
    .cfg-ligne .btn { flex-shrink: 0; }
    .cfg-ligne .badge { flex-shrink: 0; }
</style>
@endsection
