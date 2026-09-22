@extends('layouts.app')

@section('title', 'Matière · '.$cours->nom_cours)

@section('content')
@php $n = fn ($v) => rtrim(rtrim(number_format((float) $v, 1, ',', ' '), '0'), ','); @endphp

<div class="crumb">
    <a href="{{ route('referentiel.cours.index') }}">Matières</a>
    <span class="sep">/</span>
    <span class="current">{{ $cours->nom_cours }}</span>
</div>

@include('partials.erreurs')

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>{{ $cours->nom_cours }}</h1>
            <span class="badge">{{ $cours->code_cours }}</span>
        </div>
        <p class="page-sub">
            @if ($niveaux->isEmpty())
                Enseignée à aucun niveau pour le moment.
            @else
                Enseignée à {{ $niveaux->count() }} niveau{{ $niveaux->count() > 1 ? 'x' : '' }}.
            @endif
        </p>
    </div>
</div>

<form method="post" action="{{ route('referentiel.cours.update', $cours) }}" class="form-page">
    @csrf
    @method('PUT')
    @include('referentiel.cours._form', ['cours' => $cours])
    <div class="form-actions">
        <a href="{{ route('referentiel.cours.index') }}" class="btn btn-ghost">Retour</a>
        <button type="submit" class="btn">Enregistrer</button>
    </div>
</form>

{{-- Où la matière est enseignée : sans ce rappel, on la supprimerait sans
     voir qu'elle pèse dans les bulletins de douze niveaux. --}}
<div class="form-page" style="margin-top:14px">
    <section class="form-card">
        <div class="form-head">
            <h2>Niveaux qui l'enseignent</h2>
            <span class="form-sub">Réglé dans le référentiel pédagogique.</span>
        </div>

        @if ($niveaux->isEmpty())
            <div class="form-body">
                <p class="field-aide" style="margin:0">
                    Cette matière n'est rattachée à aucun niveau : aucune note ne peut y être saisie.
                    <a href="{{ route('referentiel.ref.index') }}">L'ajouter à un niveau</a>.
                </p>
            </div>
        @else
            <div class="table-scroll">
                <table class="data-table" style="min-width:auto">
                    <thead>
                        <tr>
                            <th>Niveau</th>
                            <th style="width:140px">Cycle</th>
                            <th style="width:130px">Coefficient</th>
                            <th style="width:150px">Heures / semaine</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($niveaux as $niveau)
                            @php $pivot = $niveau->matieres->firstWhere('id_cours', $cours->id_cours)?->pivot; @endphp
                            <tr>
                                <td class="cell-name">{{ $niveau->nom_niveau }}</td>
                                <td>{{ $niveau->formation?->niveau ?? '—' }}</td>
                                <td>{{ $pivot ? $n($pivot->coefficient) : '—' }}</td>
                                <td>{{ $pivot ? $n($pivot->volume_horaire).' h' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<form method="post" action="{{ route('referentiel.cours.destroy', $cours) }}" class="danger-zone form-page"
      onsubmit="return confirm('Supprimer la matière « {{ $cours->nom_cours }} » ?@if ($niveaux->isNotEmpty())\n\nElle est enseignée à {{ $niveaux->count() }} niveau(x) : les bulletins concernés perdront cette ligne.@endif')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-ghost is-danger">
        @include('partials.icon', ['n' => 'trash', 's' => 15, 'w' => 2])Supprimer cette matière
    </button>
</form>
@endsection
