@extends('layouts.app')

@section('title', 'Import de contacts')

@section('content')
    <h1>Import de contacts</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p>
        Le fichier (CSV ou Excel) doit contenir les colonnes suivantes en en-tête :
        <code>Civilite, Nom, Prenom, Telephone, Email, Code_postal, Ville, Annee_rentree, Ecole</code>.
        Les lignes sans téléphone, sans année de rentrée, ou dont l'email existe déjà, sont ignorées.
    </p>

    <form method="post" action="{{ route('import.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="source">Source</label>
        <select name="source" id="source">
            <option value="">--</option>
            @foreach ($sources as $source)
                <option value="{{ $source->id_source }}">{{ $source->titre }}</option>
            @endforeach
        </select>

        <label for="upload_file">Fichier (CSV/Excel)</label>
        <input type="file" name="upload_file" id="upload_file" accept=".csv,.xlsx,.xls,.txt" required>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Importer</button>
        </p>
    </form>
@endsection
