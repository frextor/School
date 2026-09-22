@extends('layouts.app')

@section('title', 'Panneau · '.$panneau->titre)

@section('content')
@php $lien = route('panneaux.affichage', $panneau->identifiant_panneaux); @endphp

<div class="crumb">
    <a href="{{ route('panneaux.index') }}">Panneaux d'affichage</a>
    <span class="sep">/</span>
    <span class="current">{{ $panneau->titre }}</span>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

<div class="page-head">
    <div>
        <h1>{{ $panneau->titre }}</h1>
        <p class="page-sub">{{ $panneau->etablissement?->nom_etablissement }} · identifiant {{ $panneau->identifiant_panneaux }}</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-ghost" href="{{ $lien }}" target="_blank" rel="noopener">
            @include('partials.icon', ['n' => 'eye', 's' => 15, 'w' => 2])Ouvrir l'affichage
        </a>
    </div>
</div>

{{-- L'adresse à saisir une fois dans le navigateur de l'écran. --}}
<section class="pn-url">
    <span class="pn-url-label">Adresse du panneau</span>
    <div class="pn-url-ligne">
        <input type="text" value="{{ $lien }}" readonly data-url aria-label="Adresse du panneau">
        <button type="button" class="btn btn-ghost" data-copier>Copier</button>
    </div>
    <p class="pn-url-aide">Aucune connexion n'est demandée sur cette page : l'écran peut l'afficher en permanence.</p>
</section>

<form method="post" action="{{ route('panneaux.update', $panneau) }}" class="pn-form">
    @csrf
    @method('PUT')

    @include('panneaux._form', ['panneau' => $panneau])

    <div class="pn-pied">
        <a href="{{ route('panneaux.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

<form method="post" action="{{ route('panneaux.destroy', $panneau) }}" class="pn-suppr-bloc"
      onsubmit="return confirm('Supprimer le panneau « {{ $panneau->titre }} » ?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost pn-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer ce panneau
    </button>
</form>

<style>
    .pn-url {
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        padding: 15px 16px; margin-bottom: 14px; max-width: 920px;
    }
    .pn-url-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); margin-bottom: 7px; }
    .pn-url-ligne { display: flex; gap: 8px; }
    .pn-url-ligne input {
        flex: 1; min-width: 0; max-width: none; margin: 0;
        background: #fafbfd; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12.5px;
    }
    .pn-url-ligne .btn { flex-shrink: 0; }
    .pn-url-aide { margin: 8px 0 0; font-size: 11.5px; color: var(--muted); }

    .pn-suppr-bloc { max-width: 920px; margin: 18px 0 0; }
    .pn-danger { color: var(--danger); border-color: #fecaca; display: inline-flex; align-items: center; gap: 7px; }
    .pn-danger:hover { background: var(--danger-bg); }
    .pn-danger svg { stroke: var(--danger); }
</style>

<script>
    (function () {
        var bouton = document.querySelector('[data-copier]');
        var champ = document.querySelector('[data-url]');
        if (!bouton || !champ) return;

        bouton.addEventListener('click', function () {
            champ.select();
            // `clipboard` n'existe pas hors HTTPS : la sélection reste copiable au clavier.
            if (navigator.clipboard) {
                navigator.clipboard.writeText(champ.value);
            } else {
                document.execCommand('copy');
            }
            bouton.textContent = 'Copié';
            setTimeout(function () { bouton.textContent = 'Copier'; }, 1200);
        });
    })();
</script>
@endsection
