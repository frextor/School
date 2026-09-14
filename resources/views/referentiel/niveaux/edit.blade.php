@extends('layouts.app')

@section('title', 'Modifier niveau')

@section('content')
    <a href="{{ route('referentiel.niveaux.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $niveau->nom_niveau }} »</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.niveaux.update', $niveau) }}">
        @csrf
        @method('PUT')

        <label for="code_niveau">Code</label>
        <input type="text" name="code_niveau" id="code_niveau" value="{{ old('code_niveau', $niveau->code_niveau) }}" required>

        <label for="nom_niveau">Nom</label>
        <input type="text" name="nom_niveau" id="nom_niveau" value="{{ old('nom_niveau', $niveau->nom_niveau) }}" required>

        <label for="id_formation">Formation</label>
        <select name="id_formation" id="id_formation" required>
            @foreach ($formations as $formation)
                <option value="{{ $formation->id_formation }}" @selected(old('id_formation', $niveau->id_formation) == $formation->id_formation)>{{ $formation->niveau }}</option>
            @endforeach
        </select>

        <label for="id_niveau_future">Niveau suivant (passage automatique)</label>
        <select name="id_niveau_future" id="id_niveau_future">
            <option value="">-- Aucun --</option>
            @foreach ($niveaux as $autre)
                <option value="{{ $autre->id_niveau }}" @selected(old('id_niveau_future', $niveau->id_niveau_future) == $autre->id_niveau)>{{ $autre->nom_niveau }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>

    <h2 style="margin-top:2rem">Options facturables</h2>
    <p>Proposées (avec leur montant, modifiable au cas par cas) lors de la création d'un règlement pour un élève de ce niveau.</p>

    @forelse ($niveau->options()->orderBy('annee', 'desc')->orderBy('ordre')->get() as $option)
        <form method="post" action="{{ route('referentiel.niveaux.options.update', [$niveau, $option]) }}"
              id="option-form-{{ $option->id_niveau_option }}"
              style="display:flex;gap:0.5rem;align-items:end;flex-wrap:wrap;margin-bottom:0.5rem;padding:0.5rem;border:1px solid #e5e5e5;border-radius:6px">
            @csrf
            <input type="hidden" name="_method" id="method-{{ $option->id_niveau_option }}" value="PUT">

            <span>
                <label>Titre</label><br>
                <input type="text" name="titre" value="{{ $option->titre }}" required>
            </span>
            <span>
                <label>Objet de paiement</label><br>
                <select name="id_objet_paiement" required>
                    @foreach ($objetsPaiement as $objet)
                        <option value="{{ $objet->id_objet_paiement }}" @selected($option->id_objet_paiement == $objet->id_objet_paiement)>{{ $objet->objet_paiement }}</option>
                    @endforeach
                </select>
            </span>
            <span>
                <label>Montant</label><br>
                <input type="number" step="0.01" name="montant" value="{{ $option->montant }}" required style="width:6rem">
            </span>
            <span>
                <label>Année</label><br>
                <input type="number" name="annee" value="{{ $option->annee }}" required style="width:5rem">
            </span>
            <span>
                <label>Ordre</label><br>
                <input type="number" name="ordre" value="{{ $option->ordre }}" style="width:3.5rem">
            </span>

            <button type="submit">Enregistrer</button>
            <button type="submit"
                    formaction="{{ route('referentiel.niveaux.options.destroy', [$niveau, $option]) }}"
                    onclick="document.getElementById('method-{{ $option->id_niveau_option }}').value='DELETE'; return confirm('Supprimer cette option ?')">
                Supprimer
            </button>
        </form>
    @empty
        <p>Aucune option pour ce niveau.</p>
    @endforelse

    <h3 style="margin-top:1rem">Ajouter une option</h3>
    <form method="post" action="{{ route('referentiel.niveaux.options.store', $niveau) }}">
        @csrf

        <label for="opt_titre">Titre</label>
        <input type="text" name="titre" id="opt_titre" placeholder="Frais de dossier, Assurance..." required>

        <label for="opt_objet">Objet de paiement</label>
        <select name="id_objet_paiement" id="opt_objet" required>
            @foreach ($objetsPaiement as $objet)
                <option value="{{ $objet->id_objet_paiement }}">{{ $objet->objet_paiement }}</option>
            @endforeach
        </select>

        <label for="opt_montant">Montant</label>
        <input type="number" step="0.01" name="montant" id="opt_montant" required>

        <label for="opt_annee">Année</label>
        <input type="number" name="annee" id="opt_annee" value="{{ date('Y') }}" required>

        <label for="opt_ordre">Ordre d'affichage</label>
        <input type="number" name="ordre" id="opt_ordre" value="0">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Ajouter</button>
        </p>
    </form>
@endsection
