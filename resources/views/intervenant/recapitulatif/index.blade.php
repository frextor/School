@extends('layouts.app')

@section('title', "Récapitulatif d'heures")

@section('content')
@php
    // Sans type de cours en base, la saisie est refusée par la validation :
    // mieux vaut le dire que d'afficher un formulaire qui échouera.
    $referentielIncomplet = $typesCours->isEmpty() || $classes->isEmpty() || $coursListe->isEmpty();
@endphp

<div class="page-head">
    <div>
        <h1>Récapitulatif d'heures</h1>
        <p class="page-sub">Déclarez les heures que vous avez effectuées : date, classe, nature de l'heure et volume.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('recapitulatif.resume') }}" class="btn btn-ghost">Voir le récapitulatif global</a>
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

@if ($referentielIncomplet)
    <div class="re-bloc">
        @include('partials.icon', ['n' => 'alert', 's' => 26, 'c' => '#b45309', 'w' => 1.8])
        <p>Saisie indisponible</p>
        <span>
            Il manque un référentiel pour déclarer des heures
            ({{ collect([
                $typesCours->isEmpty() ? "nature de l'heure" : null,
                $classes->isEmpty() ? 'classes' : null,
                $coursListe->isEmpty() ? 'matières' : null,
            ])->filter()->implode(', ') }}).
            Demandez à l'administration de le compléter.
        </span>
    </div>
@else
    <form method="post" action="{{ route('recapitulatif.store') }}" class="re-form">
        @csrf

        <div class="re-grid">
            <label class="stack">
                <span>Date</span>
                <input type="date" name="date_recap[]" value="{{ now()->format('Y-m-d') }}" required>
            </label>

            <label class="stack">
                <span>Établissement</span>
                <select name="etablissementH[]" required>
                    @foreach ($etablissements as $etablissement)
                        <option value="{{ $etablissement->id_etablissement }}">{{ $etablissement->nom_etablissement }}</option>
                    @endforeach
                </select>
            </label>

            <label class="stack">
                <span>Classe</span>
                <select name="classe_recap[]" required>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id_classe }}">{{ $classe->classe }}</option>
                    @endforeach
                </select>
            </label>

            <label class="stack">
                <span>Nature de l'heure</span>
                <select name="type_cour_recap[]" required>
                    @foreach ($typesCours as $type)
                        <option value="{{ $type->id_type_cours }}">{{ $type->type_cours }}</option>
                    @endforeach
                </select>
            </label>

            <label class="stack">
                <span>Matière</span>
                <select name="intitule_recap_id[]" required>
                    @foreach ($coursListe as $cours)
                        <option value="{{ $cours->id_cours }}">{{ $cours->nom_cours }}</option>
                    @endforeach
                </select>
            </label>

            <label class="stack">
                <span>Début</span>
                <input type="time" name="time_debut_recap[]" data-debut required>
            </label>

            <label class="stack">
                <span>Fin</span>
                <input type="time" name="time_fin_recap[]" data-fin required>
            </label>

            <label class="stack">
                <span>Volume horaire</span>
                <input type="time" name="volumeh[]" data-volume required>
            </label>
        </div>

        <div class="re-pied">
            <p class="re-aide">Le volume est calculé à partir des heures saisies ; vous pouvez le corriger.</p>
            <button type="submit" class="btn">Enregistrer</button>
        </div>
    </form>
@endif

<style>
    /* Saisie d'heures : un formulaire étiqueté plutôt qu'une ligne de tableau
       où les listes déroulantes étaient écrasées à trente pixels de large. */
    .re-form { max-width: none; margin: 0; background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px; }
    .re-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 13px; }
    .re-form .stack { display: block; margin: 0; min-width: 0; }
    .re-form .stack > span:first-child { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .re-form input, .re-form select { width: 100%; max-width: none; }
    .re-pied { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-top: 16px; padding-top: 15px; border-top: 1px solid var(--border-soft); }
    .re-aide { margin: 0; font-size: 12px; color: var(--muted); }
    .re-pied .btn { margin-left: auto; }

    .re-bloc {
        text-align: center; padding: 42px 20px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
    }
    .re-bloc p { margin: 12px 0 0; font-size: 14px; font-weight: 600; }
    .re-bloc span { display: block; margin: 4px auto 0; max-width: 52ch; font-size: 13px; color: var(--muted); text-wrap: pretty; }
</style>

<script>
    (function () {
        var debut = document.querySelector('[data-debut]');
        var fin = document.querySelector('[data-fin]');
        var volume = document.querySelector('[data-volume]');
        if (!debut || !fin || !volume) return;

        // Volume déduit des heures saisies : c'est une soustraction, personne
        // ne devrait avoir à la faire de tête. Reste modifiable à la main.
        var modifieALaMain = false;
        volume.addEventListener('input', function () { modifieALaMain = true; });

        function calculer() {
            if (modifieALaMain || !debut.value || !fin.value) return;

            var d = debut.value.split(':');
            var f = fin.value.split(':');
            var minutes = (f[0] * 60 + +f[1]) - (d[0] * 60 + +d[1]);
            if (minutes <= 0) return;

            var h = Math.floor(minutes / 60), m = minutes % 60;
            volume.value = ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2);
        }

        debut.addEventListener('change', calculer);
        fin.addEventListener('change', calculer);
    })();
</script>
@endsection
