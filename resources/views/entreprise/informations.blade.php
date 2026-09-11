@extends('layouts.app')

@section('title', 'Mes informations')

@section('content')
    <a href="{{ route('entreprise.dashboard') }}">&larr; Retour</a>
    <h1>Mes informations</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('entreprise.informations.update') }}">
        @csrf
        @method('PUT')

        <label for="nom_entreprise">Raison sociale</label>
        <input type="text" name="nom_entreprise" id="nom_entreprise" value="{{ old('nom_entreprise', $entreprise->nom_entreprise) }}" required>

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $entreprise->adresse) }}">

        <label for="code_postal">Code postal</label>
        <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $entreprise->code_postal) }}">

        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="{{ old('ville', $entreprise->ville) }}">

        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $entreprise->telephone) }}">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $entreprise->email) }}">

        <label for="site_web">Site web</label>
        <input type="url" name="site_web" id="site_web" value="{{ old('site_web', $entreprise->site_web) }}">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
