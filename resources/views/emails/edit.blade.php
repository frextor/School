@extends('layouts.app')

@section('title', "Modifier modèle d'email")

@section('content')
    <a href="{{ route('emails.index') }}">&larr; Retour</a>
    <h1>Modifier « {{ $email->categorie }} » ({{ $email->lang }})</h1>

    @if ($errors->any())
        <div class="status" style="background:#ffecec;border-color:#f3b4b4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('emails.update', $email) }}">
        @csrf
        @method('PUT')

        <label for="titre">Titre (interne)</label>
        <input type="text" name="titre" id="titre" value="{{ old('titre', $email->titre) }}" required>

        <label for="sujet">Sujet</label>
        <input type="text" name="sujet" id="sujet" value="{{ old('sujet', $email->sujet) }}" required>

        <label for="message">Message</label>
        <textarea name="message" id="message" rows="10">{{ old('message', $email->message) }}</textarea>

        <label><input type="checkbox" name="statut" value="1" @checked(old('statut', $email->statut))> Actif</label>

        <p style="margin-top:1rem">
            <button type="submit" class="btn">Enregistrer</button>
        </p>
    </form>
@endsection
