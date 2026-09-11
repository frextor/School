@extends('layouts.app')

@section('title', 'Modifier élève')

@section('content')
    <a href="{{ route('eleves.show', $eleve) }}">&larr; Retour à la fiche</a>
    <h1>Modifier l'élève #{{ $eleve->id_eleve }}</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('eleves.update', $eleve) }}">
        @csrf
        @method('PUT')

        <label for="id_niveau">Niveau</label>
        <select name="id_niveau" id="id_niveau" required>
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id_niveau }}" @selected(old('id_niveau', $eleve->id_niveau) == $niveau->id_niveau)>{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <label for="id_classe">Classe</label>
        <select name="id_classe" id="id_classe">
            <option value="">--</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classe }}" @selected(old('id_classe', $eleve->id_classe) == $classe->id_classe)>{{ $classe->classe }}</option>
            @endforeach
        </select>

        <label for="profil">Profil</label>
        <select name="profil" id="profil" required>
            @foreach (['eleve', 'alumni', 'reinscrit', 'abandon'] as $profil)
                <option value="{{ $profil }}" @selected(old('profil', $eleve->profil) === $profil)>{{ ucfirst($profil) }}</option>
            @endforeach
        </select>

        <label><input type="checkbox" name="valide" value="1" @checked(old('valide', $eleve->valide))> Validé</label>
        <label><input type="checkbox" name="visible" value="1" @checked(old('visible', $eleve->visible))> Visible</label>

        <label for="montant_formation">Montant formation</label>
        <input type="text" name="montant_formation" id="montant_formation" value="{{ old('montant_formation', $eleve->montant_formation) }}">

        <label for="commentaire">Commentaire</label>
        <textarea name="commentaire" id="commentaire" rows="4">{{ old('commentaire', $eleve->commentaire) }}</textarea>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
