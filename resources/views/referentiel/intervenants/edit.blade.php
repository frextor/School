@extends('layouts.app')

@section('title', "Modifier l'intervenant")

@section('content')
    <a href="{{ route('referentiel.intervenants.show', $intervenant) }}">&larr; Retour</a>
    <h1>Modifier {{ $intervenant->nom }} {{ $intervenant->prenom }}</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.intervenants.update', $intervenant) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="civilite">Civilité</label>
        <select name="civilite" id="civilite" required>
            <option value="M" @selected(old('civilite', $intervenant->civilite) === 'M')>M</option>
            <option value="Mme" @selected(old('civilite', $intervenant->civilite) === 'Mme')>Mme</option>
        </select>

        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="{{ old('nom', $intervenant->nom) }}" required>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $intervenant->prenom) }}" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $intervenant->email) }}" required>

        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $intervenant->telephone) }}">

        <label for="mobile">Mobile</label>
        <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $intervenant->mobile) }}">

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $intervenant->adresse) }}">

        <label for="code_postal">Code postal</label>
        <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $intervenant->code_postal) }}">

        <label for="ville">Ville</label>
        <input type="text" name="ville" id="ville" value="{{ old('ville', $intervenant->ville) }}">

        <label for="profession">Profession</label>
        <input type="text" name="profession" id="profession" value="{{ old('profession', $intervenant->profession) }}">

        <label for="poste_actuel">Poste actuel</label>
        <input type="text" name="poste_actuel" id="poste_actuel" value="{{ old('poste_actuel', $intervenant->poste_actuel) }}">

        <label for="raison_sociale">Société (optionnel)</label>
        <input type="text" name="raison_sociale" id="raison_sociale" value="{{ old('raison_sociale', $intervenant->societe?->raison_sociale) }}">

        <label for="adresse_societe">Adresse société</label>
        <input type="text" name="adresse_societe" id="adresse_societe" value="{{ old('adresse_societe', $intervenant->societe?->adresse_societe) }}">

        @php $coursSelectionnes = old('cours', $intervenant->cours->pluck('id_cours')->all()); @endphp
        <label for="cours">Cours enseignés</label>
        <select name="cours[]" id="cours" multiple size="6">
            @foreach ($cours as $cour)
                <option value="{{ $cour->id_cours }}" @selected(collect($coursSelectionnes)->contains($cour->id_cours))>{{ $cour->nom_cours }}</option>
            @endforeach
        </select>

        @php $etablissementsSelectionnes = old('etablissements', $intervenant->etablissements->pluck('id_etablissement')->all()); @endphp
        <label for="etablissements">Établissements</label>
        <select name="etablissements[]" id="etablissements" multiple size="4">
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(collect($etablissementsSelectionnes)->contains($etablissement->id_etablissement))>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        @if ($intervenant->competences->isNotEmpty())
            <p style="margin-top:0.5rem;color:var(--muted);font-size:0.82rem">
                Compétences actuelles : {{ $intervenant->competences->pluck('nom_competence')->join(', ') }}
                (non modifiables depuis ce formulaire pour l'instant)
            </p>
        @endif

        @if ($intervenant->cheminCv())
            <p style="margin-top:0.5rem">CV actuel : <a href="{{ Storage::disk('public')->url($intervenant->cheminCv()) }}" target="_blank">Télécharger</a></p>
        @endif
        <label for="cv">Remplacer le CV (pdf/doc)</label>
        <input type="file" name="cv" id="cv">

        @if ($intervenant->cheminPhoto())
            <p style="margin-top:0.5rem"><img src="{{ Storage::disk('public')->url($intervenant->cheminPhoto()) }}" style="max-width:100px"></p>
        @endif
        <label for="photo">Remplacer la photo</label>
        <input type="file" name="photo" id="photo">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
