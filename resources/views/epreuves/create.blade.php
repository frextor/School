@extends('layouts.app')

@section('title', 'Nouvelle épreuve')

@section('content')
@php $cyclesChoisis = collect(old('id_formation', []))->map(fn ($v) => (int) $v); @endphp

<div class="crumb">
    <a href="{{ route('epreuves.index') }}">Épreuves d'admission</a>
    <span class="sep">/</span>
    <span class="current">Nouvelle épreuve</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouvelle épreuve d'admission</h1>
        <p class="page-sub">Une session de test, ouverte aux candidats des cycles choisis.</p>
    </div>
</div>

<form method="post" action="{{ route('epreuves.store') }}" class="form-page">
    @csrf

    <section class="form-card">
        <div class="form-head"><h2>Quand et où</h2></div>
        <div class="form-grid">
            <label class="field">
                <span>Date</span>
                <input type="date" name="date" required value="{{ old('date') }}">
            </label>

            <label class="field">
                <span>Heure</span>
                <input type="time" name="heure" required value="{{ old('heure') }}">
            </label>

            <label class="field">
                <span>Lieu</span>
                <input type="text" name="lieu" maxlength="16" required placeholder="Casablanca"
                       value="{{ old('lieu') }}">
                <small class="field-aide">16 caractères au plus.</small>
            </label>

            <label class="field">
                <span>Places</span>
                <input type="number" name="effectif" min="1" required value="{{ old('effectif', 20) }}">
                <small class="field-aide">Au-delà, les inscriptions sont refusées.</small>
            </label>
        </div>

        <div class="form-body" style="padding-top:0">
            <label class="check-card">
                <input type="checkbox" name="distanciel" value="1" @checked(old('distanciel')) data-distanciel>
                <span>
                    <strong>Épreuve à distance</strong>
                    Les candidats reçoivent un lien de connexion au lieu d'une adresse.
                </span>
            </label>

            <label class="field" style="margin-top:14px" data-url-distanciel @unless(old('distanciel')) hidden @endunless>
                <span>Lien de connexion</span>
                <input type="url" name="url_distanciel" placeholder="https://…" value="{{ old('url_distanciel') }}">
            </label>
        </div>
    </section>

    <section class="form-card">
        <div class="form-head">
            <h2>Cycles concernés</h2>
            <span class="form-sub">Sans sélection, l'épreuve est ouverte à tous.</span>
        </div>
        <div class="form-body">
            <div class="pick-list">
                @foreach ($formations as $formation)
                    <label class="pick">
                        <input type="checkbox" name="id_formation[]" value="{{ $formation->id_formation }}"
                               @checked($cyclesChoisis->contains((int) $formation->id_formation))>
                        <span>{{ $formation->niveau }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('epreuves.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer l'épreuve</button>
    </div>
</form>

<script>
    (function () {
        // Le lien n'a de sens que pour une épreuve à distance.
        var bascule = document.querySelector('[data-distanciel]');
        var champ = document.querySelector('[data-url-distanciel]');
        if (!bascule || !champ) return;

        bascule.addEventListener('change', function () {
            champ.hidden = ! bascule.checked;
        });
    })();
</script>
@endsection
