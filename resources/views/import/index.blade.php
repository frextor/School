@extends('layouts.app')

@section('title', 'Import de familles')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Import de familles</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <h1>Import de familles</h1>
        <p class="page-sub">Reprendre en une fois une liste de prospects issue d'un salon, d'un formulaire ou d'un fichier existant.</p>
    </div>
</div>

<div class="form-page">
    <section class="form-card">
        <div class="form-head">
            <h2>Le fichier attendu</h2>
            <span class="form-sub">CSV ou Excel, une ligne par famille.</span>
        </div>

        <div class="form-body">
            <span class="field-label">Colonnes à prévoir en en-tête</span>
            <div class="pick-list">
                @foreach (['Civilite', 'Nom', 'Prenom', 'Telephone', 'Email', 'Code_postal', 'Ville', 'Annee_rentree', 'Ecole'] as $colonne)
                    <span class="badge">{{ $colonne }}</span>
                @endforeach
            </div>

            {{-- Dire d'avance ce qui sera écarté évite de chercher après coup
                 pourquoi le fichier de 300 lignes n'en a importé que 240. --}}
            <div class="imp-regles">
                <span class="field-label">Lignes ignorées</span>
                <ul>
                    <li>Sans numéro de téléphone.</li>
                    <li>Sans année de rentrée.</li>
                    <li>Dont l'adresse e-mail existe déjà : le contact n'est pas dupliqué.</li>
                </ul>
            </div>
        </div>
    </section>

    <form method="post" action="{{ route('import.store') }}" enctype="multipart/form-data">
        @csrf

        <section class="form-card">
            <div class="form-head"><h2>Le fichier à importer</h2></div>
            <div class="form-grid">
                <label class="field">
                    <span>Source</span>
                    <select name="source">
                        <option value="">Non précisée</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->id_source }}">{{ $source->titre }}</option>
                        @endforeach
                    </select>
                    <small class="field-aide">D'où viennent ces familles : salon, site, partenaire.</small>
                </label>

                <label class="field">
                    <span>Fichier</span>
                    <input type="file" name="upload_file" accept=".csv,.xlsx,.xls,.txt" required>
                </label>
            </div>
        </section>

        <div class="form-actions">
            <button type="submit" class="btn">Importer le fichier</button>
        </div>
    </form>
</div>

<style>
    .imp-regles { margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--border-soft); }
    .imp-regles ul { margin: 0; padding-left: 1.1rem; font-size: 13px; color: var(--muted); }
    .imp-regles li + li { margin-top: 3px; }
</style>
@endsection
