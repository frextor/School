@extends('layouts.app')

@section('title', 'Nouveau niveau')

@section('content')
<div class="crumb">
    <a href="{{ route('referentiel.niveaux.index') }}">Niveaux &amp; cycles</a>
    <span class="sep">/</span>
    <span class="current">Nouveau niveau</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Nouveau niveau</h1>
        <p class="page-sub">Une année de scolarité, rattachée à un cycle. Ses matières et coefficients se règlent ensuite au référentiel.</p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.niveaux.store') }}" class="form-page">
    @csrf

    <section class="form-card">
        <div class="form-head"><h2>Le niveau</h2></div>

        <div class="form-grid">
            <label class="field">
                <span>Code</span>
                <input type="text" name="code_niveau" maxlength="50" required placeholder="1AEP"
                       value="{{ old('code_niveau') }}">
                <small class="field-aide">Abréviation courte, unique.</small>
            </label>

            <label class="field">
                <span>Nom</span>
                <input type="text" name="nom_niveau" maxlength="64" required placeholder="1ère année primaire"
                       value="{{ old('nom_niveau') }}">
            </label>

            <label class="field">
                <span>Cycle</span>
                <select name="id_formation" required>
                    @foreach ($formations as $formation)
                        <option value="{{ $formation->id_formation }}" @selected(old('id_formation') == $formation->id_formation)>{{ $formation->niveau }}</option>
                    @endforeach
                </select>
                <small class="field-aide">Maternelle, Primaire, Collège ou Lycée.</small>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('referentiel.niveaux.index') }}" class="btn btn-ghost">Annuler</a>
        <button type="submit" class="btn">Créer le niveau</button>
    </div>
</form>
@endsection
