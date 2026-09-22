@extends('layouts.app')

@section('title', 'Volumes horaires')

@section('content')
@php
    $n = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', ' '), '0'), ',');
    $parCycle = $niveaux->groupBy(fn ($niveau) => $niveau->formation?->niveau ?: 'Sans cycle');
    $rang = 0;
@endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Volumes horaires</span>
</div>

<div class="page-head">
    <div>
        <h1>Volumes horaires prévisionnels</h1>
        <p class="page-sub">Effectif attendu, nombre de classes et volume de cours par niveau : de quoi dimensionner la rentrée.</p>
    </div>
</div>

<form method="get" action="{{ route('referentiel.parametrage.index') }}" class="filter-card">
    <div class="filter-row">
        <label class="field" style="flex:1 1 300px">
            <span>Établissement</span>
            <select name="id_etablissement" onchange="this.form.submit()">
                <option value="">— Choisir un établissement —</option>
                @foreach ($etablissements as $etablissement)
                    <option value="{{ $etablissement->id_etablissement }}" @selected($idEtablissement == $etablissement->id_etablissement)>{{ $etablissement->nom_etablissement }}</option>
                @endforeach
            </select>
        </label>
    </div>
</form>

@if (! $idEtablissement)
    <div class="vide-card">
        @include('partials.icon', ['n' => 'chart', 's' => 28, 'c' => '#c9cdd9', 'w' => 1.6])
        <p>Choisissez un établissement</p>
        <span>Les volumes se saisissent campus par campus : un même niveau n'a pas le même effectif partout.</span>
    </div>
@else
    @if (! empty($totaux))
        <div class="vol-totaux">
            @foreach ($totaux as $cycle => $total)
                <div class="vol-total">
                    <span class="vol-label">{{ $cycle }}</span>
                    <span class="vol-value">{{ $total['effectif'] }}</span>
                    <span class="vol-hint">élèves · {{ $total['nb_classe'] }} classes · {{ $n($total['volume_cours']) }} h</span>
                </div>
            @endforeach
            <div class="vol-total is-brand">
                <span class="vol-label">Total</span>
                <span class="vol-value">{{ collect($totaux)->sum('effectif') }}</span>
                <span class="vol-hint">élèves · {{ collect($totaux)->sum('nb_classe') }} classes · {{ $n(collect($totaux)->sum('volume_cours')) }} h</span>
            </div>
        </div>
    @endif

    <form method="post" action="{{ route('referentiel.parametrage.save') }}" class="form-page is-full">
        @csrf
        <input type="hidden" name="id_etablissement" value="{{ $idEtablissement }}">

        <div class="table-card">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Niveau</th>
                            <th style="width:150px">Effectif</th>
                            <th style="width:150px">Classes</th>
                            <th style="width:170px">Volume de cours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parCycle as $nomCycle => $niveauxCycle)
                            <tr class="grp-row">
                                <td colspan="4">{{ $nomCycle }}</td>
                            </tr>
                            @foreach ($niveauxCycle as $niveau)
                                @php $volume = $volumes->get($niveau->id_niveau); @endphp
                                <tr>
                                    <td class="cell-name">
                                        {{ $niveau->nom_niveau }}
                                        <input type="hidden" name="lignes[{{ $rang }}][id_niveau]" value="{{ $niveau->id_niveau }}">
                                    </td>
                                    <td><input type="number" min="0" class="vol-champ" name="lignes[{{ $rang }}][effectif]" value="{{ $volume->effectif ?? '' }}" aria-label="Effectif {{ $niveau->nom_niveau }}"></td>
                                    <td><input type="number" min="0" class="vol-champ" name="lignes[{{ $rang }}][nb_classe]" value="{{ $volume->nb_classe ?? '' }}" aria-label="Classes {{ $niveau->nom_niveau }}"></td>
                                    <td><input type="number" step="0.01" min="0" class="vol-champ" name="lignes[{{ $rang }}][volume_cours]" value="{{ $volume->volume_cours ?? '' }}" aria-label="Volume {{ $niveau->nom_niveau }}"></td>
                                </tr>
                                @php $rang++; @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Enregistrer les volumes</button>
        </div>
    </form>
@endif

<style>
    .vol-totaux { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-bottom: 14px; }
    .vol-total { background: var(--surface); border: 1px solid var(--border); border-radius: 13px; padding: 13px 15px; }
    .vol-total.is-brand { background: #fafbff; border-color: #dfe2fb; }
    .vol-label { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .vol-value { display: block; margin-top: 5px; font-size: 24px; font-weight: 700; letter-spacing: -.025em; font-variant-numeric: tabular-nums; }
    .vol-total.is-brand .vol-value { color: var(--brand-deep); }
    .vol-hint { display: block; margin-top: 2px; font-size: 11.5px; color: var(--muted); }

    .vol-champ { max-width: none; width: 100%; padding: 7px 10px; text-align: right; font-variant-numeric: tabular-nums; }
    .grp-row td {
        background: #fafbfd; padding: 7px 14px; font-size: 11px; font-weight: 700;
        letter-spacing: .06em; text-transform: uppercase; color: var(--muted);
    }
    .form-actions .btn:last-child { margin-left: 0; }
</style>
@endsection
