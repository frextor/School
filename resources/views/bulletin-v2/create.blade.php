@extends('layouts.app')

@section('title', 'Générer un bulletin')

@section('content')
    <a href="{{ route('bulletin-v2.index') }}">&larr; Retour</a>
    <h1>Générer un bulletin</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('bulletin-v2.generate') }}">
        @csrf

        <label for="id_etablissement">Établissement</label>
        <select name="id_etablissement" id="id_etablissement" required>
            @foreach ($etablissements as $etablissement)
                <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
            @endforeach
        </select>

        <label for="id_niveau">Niveau</label>
        <select name="id_niveau" id="id_niveau" required>
            @foreach ($niveaux as $niveau)
                <option value="{{ $niveau->id_niveau }}">{{ $niveau->nom_niveau }}</option>
            @endforeach
        </select>

        <label for="id_eleve">ID de l'élève</label>
        <input type="number" name="id_eleve" id="id_eleve" required>

        <label for="annee">Année</label>
        <input type="number" name="annee" id="annee" value="{{ date('Y') }}" required>

        <label for="semestre">Semestre (vide = les deux)</label>
        <input type="number" name="semestre" id="semestre" min="1" max="2">

        <label for="session">Session</label>
        <input type="number" name="session" id="session" value="0">

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Générer le PDF</button>
        </p>
    </form>
@endsection
