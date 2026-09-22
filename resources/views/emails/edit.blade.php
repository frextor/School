@extends('layouts.app')

@section('title', "Modèle · ".$email->categorie)

@section('content')
<div class="crumb">
    <a href="{{ route('emails.index') }}">Modèles d'e-mails</a>
    <span class="sep">/</span>
    <span class="current">{{ $email->categorie }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $email->categorie }}</h1>
            <span class="badge">{{ strtoupper($email->lang) }}</span>
            @if ($email->statut)
                <span class="badge badge-success">Actif</span>
            @else
                <span class="badge">Désactivé</span>
            @endif
        </div>
        <p class="page-sub">Le message envoyé automatiquement pour cette catégorie.</p>
    </div>
</div>

<form method="post" action="{{ route('emails.update', $email) }}" class="form-page">
    @csrf
    @method('PUT')

    <section class="form-card">
        <div class="form-head"><h2>Le message</h2></div>

        <div class="form-grid">
            <label class="field">
                <span>Titre interne</span>
                <input type="text" name="titre" required value="{{ old('titre', $email->titre) }}">
                <small class="field-aide">Ne figure pas dans l'e-mail : sert à le reconnaître ici.</small>
            </label>

            <label class="field">
                <span>Sujet de l'e-mail</span>
                <input type="text" name="sujet" required value="{{ old('sujet', $email->sujet) }}">
            </label>

            <label class="field field-full">
                <span>Corps du message</span>
                <textarea name="message" rows="14">{{ old('message', $email->message) }}</textarea>
                <small class="field-aide">Les variables entre accolades sont remplacées à l'envoi.</small>
            </label>
        </div>

        <div class="form-body" style="padding-top:0">
            <label class="check-card">
                <input type="checkbox" name="statut" value="1" @checked(old('statut', $email->statut))>
                <span>
                    <strong>Modèle actif</strong>
                    Désactivé, aucun e-mail n'est envoyé pour cette catégorie — utile pour couper une relance sans perdre son texte.
                </span>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('emails.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>
@endsection
