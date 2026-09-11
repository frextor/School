@extends('layouts.app')

@section('title', 'Nouvel administrateur')

@section('content')
    <a href="{{ route('admins.index') }}">&larr; Retour</a>
    <h1>Nouvel administrateur</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('admins.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" required>

        <label for="username">Identifiant</label>
        <input type="text" name="username" id="username" value="{{ old('username') }}" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required>

        <label for="profil">Rôle</label>
        <select name="profil" id="profil" required>
            @foreach ($roles as $role)
                <option value="{{ $role->nom_machine }}" @selected(old('profil') === $role->nom_machine)>{{ $role->nom_role }}</option>
            @endforeach
        </select>

        <label for="id_service">Service</label>
        <select name="id_service" id="id_service" required>
            @foreach ($services as $service)
                <option value="{{ $service->id_service }}" @selected(old('id_service') == $service->id_service)>{{ $service->libelle }}</option>
            @endforeach
        </select>

        <label for="avatar">Avatar</label>
        <input type="file" name="avatar" id="avatar">

        <label for="etablissements">Établissements</label>
        <select name="etablissements[]" id="etablissements" multiple size="6">
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(collect(old('etablissements', []))->contains($etablissement->id_etablissement))>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="etablissement_principal">Établissement principal</label>
        <select name="etablissement_principal" id="etablissement_principal">
            <option value="">--</option>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
