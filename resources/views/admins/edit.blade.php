@extends('layouts.app')

@section('title', 'Modifier administrateur')

@section('content')
    <a href="{{ route('admins.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $admin->username }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('admins.update', $admin) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="{{ old('nom', $admin->nom) }}" required>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $admin->prenom) }}" required>

        <label for="username">Identifiant</label>
        <input type="text" name="username" id="username" value="{{ old('username', $admin->username) }}" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required>

        <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
        <input type="password" name="password" id="password">

        <label for="profil">Rôle</label>
        <select name="profil" id="profil" required>
            @foreach ($roles as $role)
                <option value="{{ $role->nom_machine }}" @selected(old('profil', $admin->profil) === $role->nom_machine)>{{ $role->nom_role }}</option>
            @endforeach
        </select>

        <label for="id_service">Service</label>
        <select name="id_service" id="id_service" required>
            @foreach ($services as $service)
                <option value="{{ $service->id_service }}" @selected(old('id_service', $admin->id_service) == $service->id_service)>{{ $service->libelle }}</option>
            @endforeach
        </select>

        <label for="avatar">Remplacer l'avatar</label>
        <input type="file" name="avatar" id="avatar">

        @php $etablissementsSelectionnes = old('etablissements', $admin->etablissements->pluck('id_etablissement')->all()); @endphp
        <label for="etablissements">Établissements</label>
        <select name="etablissements[]" id="etablissements" multiple size="6">
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(collect($etablissementsSelectionnes)->contains($etablissement->id_etablissement))>{{ $etablissement->nom_etablissement }}</option>
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
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
