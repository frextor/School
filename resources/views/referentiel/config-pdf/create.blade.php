@extends('layouts.app')

@section('title', 'Nouvelle config PDF')

@section('content')
    <a href="{{ route('referentiel.config-pdf.index', $type) }}">&larr; Retour</a>
    <h1>Nouvelle configuration PDF — {{ ucfirst($type) }}</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.config-pdf.store', $type) }}" enctype="multipart/form-data">
        @csrf

        <label for="logo">Logo</label>
        <input type="file" name="logo" id="logo" required>

        @if ($type === 'attestation')
            <label for="texte_attestation">Texte de l'attestation</label>
            <textarea name="texte_attestation" id="texte_attestation" rows="4">{{ old('texte_attestation') }}</textarea>
        @else
            <label for="texte_facture">Texte facture (formation initiale)</label>
            <textarea name="texte_facture" id="texte_facture" rows="4">{{ old('texte_facture') }}</textarea>

            <label for="texte_facture_contrat_apprentissage">Texte facture (contrat d'apprentissage)</label>
            <textarea name="texte_facture_contrat_apprentissage" id="texte_facture_contrat_apprentissage" rows="4">{{ old('texte_facture_contrat_apprentissage') }}</textarea>

            <label for="texte_facture_contrat_pro">Texte facture (contrat de professionnalisation)</label>
            <textarea name="texte_facture_contrat_pro" id="texte_facture_contrat_pro" rows="4">{{ old('texte_facture_contrat_pro') }}</textarea>

            <label for="footer_apprentissage">Pied de page (apprentissage)</label>
            <textarea name="footer_apprentissage" id="footer_apprentissage" rows="2">{{ old('footer_apprentissage') }}</textarea>

            <label for="footer_professionnalisation">Pied de page (professionnalisation)</label>
            <textarea name="footer_professionnalisation" id="footer_professionnalisation" rows="2">{{ old('footer_professionnalisation') }}</textarea>
        @endif

        <label for="footer_initial">Pied de page (formation initiale)</label>
        <textarea name="footer_initial" id="footer_initial" rows="2" required>{{ old('footer_initial') }}</textarea>

        <label for="id_etablissement">Établissements</label>
        <select name="id_etablissement[]" id="id_etablissement" multiple size="6">
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}" @selected(collect(old('id_etablissement', []))->contains($etablissement->id_etablissement))>{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Créer</button>
        </p>
    </form>
@endsection
