@extends('layouts.app')

@section('title', 'Appel')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <a href="{{ route('absences.index') }}">Assiduité</a>
    <span class="sep">/</span>
    <span class="current">Appel</span>
</div>

<div class="page-head">
    <div>
        <h1>Faire l'appel</h1>
        <p class="page-sub">Choisissez la classe et la séance, puis marquez les élèves absents ou en retard.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absences.index') }}" class="btn btn-ghost">
            @include('partials.icon', ['n' => 'file', 's' => 15, 'w' => 2])Suivi des absences
        </a>
    </div>
</div>

@if (session('status'))
    <div class="status">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="status error">
        @include('partials.icon', ['n' => 'alert', 's' => 15, 'w' => 2.2])
        <span>@foreach ($errors->all() as $error){{ $error }} @endforeach</span>
    </div>
@endif

{{-- Sélection de la séance : rechargement en GET pour afficher la liste --}}
<form method="get" class="filter-card">
    <div class="filter-row">
        <label class="stack" style="flex:1 1 200px">
            <span>Classe</span>
            <select name="classe" required onchange="this.form.submit()">
                <option value="">-- Choisir une classe --</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id_classe }}" @selected($classe?->id_classe === $c->id_classe)>{{ $c->classe }}</option>
                @endforeach
            </select>
        </label>
        <label class="stack">
            <span>Date</span>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
        </label>
        <label class="stack">
            <span>Heure</span>
            <input type="time" name="heure" value="{{ $heure }}" onchange="this.form.submit()">
        </label>
        <label class="stack" style="flex:1 1 180px">
            <span>Matière (optionnel)</span>
            <select name="id_cours" onchange="this.form.submit()">
                <option value="">--</option>
                @foreach ($cours as $c)
                    <option value="{{ $c->id_cours }}" @selected($idCours === $c->id_cours)>{{ $c->nom_cours }}</option>
                @endforeach
            </select>
        </label>
    </div>
</form>

@if (! $classe)
    <div class="table-card">
        <div class="empty-cell">
            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
            <span>Choisissez une classe pour afficher la liste des élèves.</span>
        </div>
    </div>
@elseif ($eleves->isEmpty())
    <div class="table-card">
        <div class="empty-cell">
            @include('partials.icon', ['n' => 'users', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
            <span>Aucun élève actif dans la classe « {{ $classe->classe }} ».</span>
        </div>
    </div>
@else
    <form method="post" action="{{ route('absences.appel.store') }}">
        @csrf
        <input type="hidden" name="classe" value="{{ $classe->id_classe }}">
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="heure" value="{{ $heure }}">
        <input type="hidden" name="id_cours" value="{{ $idCours }}">

        <div class="table-card">
            <div class="table-head">
                <span class="table-count">
                    {{ $classe->classe }} · {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }} à {{ $heure }}
                    · {{ $eleves->count() }} élève(s)
                </span>
                <div class="bulk-actions">
                    <button type="button" class="btn btn-ghost" data-tous="present">Tous présents</button>
                    <button type="submit" class="btn">Enregistrer l'appel</button>
                </div>
            </div>

            <div class="appel-list">
                @foreach ($eleves as $eleve)
                    @php
                        $existante = $existantes->get($eleve->id_eleve);
                        $statut = $existante ? $existante->nature : 'present';
                    @endphp
                    <div class="appel-row">
                        <span class="cell-avatar">{{ mb_strtoupper(mb_substr($eleve->contact?->prenom ?? '?', 0, 1).mb_substr($eleve->contact?->nom ?? '', 0, 1)) }}</span>
                        <span class="appel-nom">
                            {{ $eleve->contact?->nom_complet ?? 'Élève #'.$eleve->id_eleve }}
                            @if ($existante?->justifie)
                                <span class="appel-just">justifiée</span>
                            @endif
                        </span>

                        <div class="appel-choix">
                            @foreach (['present' => 'Présent', 'absence' => 'Absent', 'retard' => 'Retard'] as $valeur => $libelle)
                                <label class="appel-opt is-{{ $valeur }} {{ $statut === $valeur ? 'is-on' : '' }}">
                                    <input type="radio" name="statuts[{{ $eleve->id_eleve }}]" value="{{ $valeur }}"
                                           @checked($statut === $valeur)>
                                    {{ $libelle }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="table-foot" style="display:flex;justify-content:flex-end;padding:14px 16px">
                <button type="submit" class="btn">Enregistrer l'appel</button>
            </div>
        </div>
    </form>
@endif

<style>
    .filter-card .stack { margin: 0; }
    .filter-card .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .filter-card .stack input, .filter-card .stack select { width: 100%; max-width: none; }

    .appel-list { display: flex; flex-direction: column; }
    .appel-row { display: flex; align-items: center; gap: 12px; padding: 11px 16px; border-bottom: 1px solid #f6f7fa; flex-wrap: wrap; }
    .appel-row:hover { background: #fafbff; }
    .cell-avatar {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: var(--brand-light);
        color: var(--brand-deep); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .appel-nom { flex: 1 1 200px; min-width: 0; font-size: 13.5px; font-weight: 600; }
    .appel-just { margin-left: 7px; padding: 1px 7px; border-radius: 999px; background: #e7f6f2; color: #0f766e; font-size: 10.5px; font-weight: 700; }

    .appel-choix { display: flex; gap: 6px; flex-shrink: 0; }
    .appel-opt {
        margin: 0; padding: 7px 13px; border: 1px solid var(--border); border-radius: 999px; background: #fff;
        color: var(--muted); font-size: 12.5px; font-weight: 600; cursor: pointer;
        transition: border-color .14s ease, background .14s ease, color .14s ease;
    }
    .appel-opt input { position: absolute; opacity: 0; width: 0; height: 0; }
    .appel-opt:hover { border-color: #c3c6f5; }
    .appel-opt.is-present.is-on { border-color: #c3e6da; background: #e7f6f2; color: #0f766e; }
    .appel-opt.is-absence.is-on { border-color: #fecaca; background: var(--danger-bg); color: var(--danger-dark); }
    .appel-opt.is-retard.is-on { border-color: #f5d9a8; background: #fdf3e3; color: #b45309; }
</style>

<script>
    (function () {
        // Surlignage de l'option choisie
        document.querySelectorAll('.appel-opt input').forEach(function (input) {
            input.addEventListener('change', function () {
                input.closest('.appel-choix').querySelectorAll('.appel-opt').forEach(function (opt) {
                    opt.classList.toggle('is-on', opt.querySelector('input').checked);
                });
            });
        });

        // Raccourci « tous présents »
        var tous = document.querySelector('[data-tous]');
        if (tous) {
            tous.addEventListener('click', function () {
                document.querySelectorAll('.appel-opt.is-present input').forEach(function (input) {
                    input.checked = true;
                    input.dispatchEvent(new Event('change'));
                });
            });
        }
    })();
</script>
@endsection
