@extends('layouts.app')

@section('title', 'Nouveau règlement')

@section('content')
    <a href="{{ route('paiements.index', $eleve) }}">&larr; Retour</a>
    <h1>Nouveau règlement — {{ $eleve->contact?->nom_complet }}</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('paiements.store', $eleve) }}">
        @csrf

        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required>

        <label for="statut_paiement">Statut</label>
        <select name="statut_paiement" id="statut_paiement" required>
            <option value="paye" @selected(old('statut_paiement') === 'paye')>Payé</option>
            <option value="accord_opco" @selected(old('statut_paiement') === 'accord_opco')>Accord OPCO</option>
            <option value="cas_particulier" @selected(old('statut_paiement') === 'cas_particulier')>Cas particulier</option>
        </select>

        <label for="mode_paiement">Mode de paiement</label>
        <select name="mode_paiement" id="mode_paiement">
            <option value="">--</option>
            <option value="CB">Carte bancaire</option>
            <option value="CHEQUE">Chèque</option>
            <option value="VIREMENT">Virement</option>
        </select>

        <label for="annee_rentree">Année de rentrée</label>
        <input type="number" name="annee_rentree" id="annee_rentree" value="{{ old('annee_rentree', date('Y')) }}">

        <label for="commentaire">Commentaire</label>
        <textarea name="commentaire" id="commentaire" rows="3">{{ old('commentaire') }}</textarea>

        @if ($optionsDisponibles->isNotEmpty())
            <h3 style="margin-top:1.5rem">Options facturables</h3>
            <p>Cochez les options à facturer sur ce règlement ; le montant proposé vient du catalogue du niveau mais reste modifiable.</p>

            @foreach ($optionsDisponibles as $option)
                <p style="display:flex;gap:0.5rem;align-items:center">
                    <input type="checkbox" name="options[]" value="{{ $option->id_niveau_option }}"
                           id="option-{{ $option->id_niveau_option }}"
                           @checked(collect(old('options', []))->contains($option->id_niveau_option))>
                    <label for="option-{{ $option->id_niveau_option }}" style="flex:1">{{ $option->titre }}</label>
                    <input type="number" step="0.01" name="montant_option[{{ $option->id_niveau_option }}]"
                           value="{{ old('montant_option.'.$option->id_niveau_option, $option->montant) }}" style="width:6rem">
                    <span>€</span>
                </p>
            @endforeach
        @else
            <p style="margin-top:1rem;color:#666">Aucune option facturable définie pour le niveau de cet élève
                (configurable depuis <a href="{{ route('referentiel.niveaux.index') }}">Référentiel &rsaquo; Niveaux</a>).</p>
        @endif

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
