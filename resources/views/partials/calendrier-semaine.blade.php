{{--
    Grille hebdomadaire partagée par l'administration, l'espace élève et
    l'espace enseignant.

    Attendus :
    - $semaine      tableau renvoyé par App\Support\Calendrier::semaine()
    - $debutSemaine Carbon du lundi affiché
    - $urlSemaine   closure(Carbon $lundi): string — navigation d'une semaine à l'autre
    - $vide         texte affiché quand la semaine ne contient aucun créneau (optionnel)
--}}
@php
    $vide = $vide ?? 'Aucun cours cette semaine.';
    $aujourdhui = now()->startOfDay();
    $finSemaine = $debutSemaine->copy()->addDays(count($semaine['jours']) - 1);

    // Une même semaine peut chevaucher deux mois ou deux années : on n'affiche
    // le mois qu'une fois quand c'est le même de part et d'autre.
    $intitule = $debutSemaine->isSameMonth($finSemaine)
        ? $debutSemaine->translatedFormat('j').' – '.$finSemaine->translatedFormat('j F Y')
        : $debutSemaine->translatedFormat('j F').' – '.$finSemaine->translatedFormat('j F Y');
@endphp

<div class="cal-card">
    <div class="cal-bar">
        <div class="cal-nav">
            <a class="cal-fleche" href="{{ $urlSemaine($debutSemaine->copy()->subWeek()) }}" title="Semaine précédente" aria-label="Semaine précédente">
                @include('partials.icon', ['n' => 'chevron-left', 's' => 15, 'c' => '#585e72', 'w' => 2.2])
            </a>
            <a class="cal-fleche" href="{{ $urlSemaine($debutSemaine->copy()->addWeek()) }}" title="Semaine suivante" aria-label="Semaine suivante">
                @include('partials.icon', ['n' => 'chevron-right', 's' => 15, 'c' => '#585e72', 'w' => 2.2])
            </a>
            <a class="btn btn-ghost btn-sm" href="{{ $urlSemaine(now()->startOfWeek()) }}">Cette semaine</a>
        </div>

        <div class="cal-titre">
            <span class="cal-periode">{{ $intitule }}</span>
            <span class="cal-total">{{ $semaine['total'] }} cours</span>
        </div>
    </div>

    @if ($semaine['total'] === 0)
        <div class="cal-vide">
            @include('partials.icon', ['n' => 'cal', 's' => 26, 'c' => '#c3c6d4', 'w' => 1.6])
            <span>{{ $vide }}</span>
        </div>
    @else
        <div class="cal-scroll">
            <div class="cal-grille" style="--cal-heures: {{ count($semaine['heures']) - 1 }}; --cal-jours: {{ count($semaine['jours']) }}">
                {{-- Colonne des heures --}}
                <div class="cal-axe">
                    <div class="cal-jour-tete cal-axe-tete"></div>
                    <div class="cal-axe-corps">
                        @foreach ($semaine['heures'] as $index => $heure)
                            <span class="cal-heure" style="top: {{ round($index / max(1, count($semaine['heures']) - 1) * 100, 3) }}%">{{ sprintf('%02dh', $heure) }}</span>
                        @endforeach
                    </div>
                </div>

                @foreach ($semaine['jours'] as $jour)
                    @php $estAujourdhui = $jour['date']->isSameDay($aujourdhui); @endphp
                    <div class="cal-jour @if ($estAujourdhui) is-today @endif">
                        <div class="cal-jour-tete">
                            <span class="cal-jour-nom">{{ ucfirst($jour['date']->translatedFormat('D')) }}</span>
                            <span class="cal-jour-num">{{ $jour['date']->translatedFormat('j') }}</span>
                        </div>

                        <div class="cal-jour-corps">
                            @foreach ($semaine['heures'] as $index => $heure)
                                @if (! $loop->last)
                                    <div class="cal-ligne" style="top: {{ round($index / (count($semaine['heures']) - 1) * 100, 3) }}%"></div>
                                @endif
                            @endforeach

                            @foreach ($jour['evenements'] as $evenement)
                                @php
                                    $largeur = 100 / max(1, $evenement['colonnes']);
                                    $couleur = $evenement['couleur'] ?? '#4f46e5';
                                    $balise = ! empty($evenement['url']) ? 'a' : 'div';
                                @endphp
                                <{{ $balise }}
                                    @if (! empty($evenement['url'])) href="{{ $evenement['url'] }}" @endif
                                    {{-- L'appelant peut accrocher ses propres données au créneau
                                         (l'administration s'en sert pour ouvrir une fiche). --}}
                                    @foreach ($evenement['donnees'] ?? [] as $cle => $valeur)
                                        data-{{ $cle }}="{{ $valeur }}"
                                    @endforeach
                                    class="cal-event"
                                    style="top: {{ $evenement['haut'] }}%;
                                           height: {{ $evenement['hauteur'] }}%;
                                           left: {{ round($evenement['colonne'] * $largeur, 3) }}%;
                                           width: {{ round($largeur, 3) }}%;
                                           --event: {{ $couleur }}"
                                    title="{{ $evenement['titre'] }} — {{ $evenement['debut']->format('H:i') }} à {{ $evenement['fin']->format('H:i') }}{{ ! empty($evenement['meta']) ? ' · '.$evenement['meta'] : '' }}">
                                    <span class="cal-event-h">{{ $evenement['debut']->format('H:i') }}</span>
                                    <span class="cal-event-t">{{ $evenement['titre'] }}</span>
                                    @if (! empty($evenement['meta']))
                                        <span class="cal-event-m">{{ $evenement['meta'] }}</span>
                                    @endif
                                </{{ $balise }}>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sur téléphone la grille horaire devient illisible : même semaine,
             présentée jour par jour. --}}
        <div class="cal-liste">
            @foreach ($semaine['jours'] as $jour)
                @continue (empty($jour['evenements']))
                <div class="cal-liste-jour">
                    <div class="cal-liste-tete @if ($jour['date']->isSameDay($aujourdhui)) is-today @endif">
                        {{ ucfirst($jour['date']->translatedFormat('l j F')) }}
                    </div>
                    @foreach ($jour['evenements'] as $evenement)
                        <div class="cal-liste-ligne" style="--event: {{ $evenement['couleur'] ?? '#4f46e5' }}">
                            <span class="cal-liste-h">{{ $evenement['debut']->format('H:i') }}<br>{{ $evenement['fin']->format('H:i') }}</span>
                            <span class="cal-liste-txt">
                                <span class="cal-liste-t">{{ $evenement['titre'] }}</span>
                                @if (! empty($evenement['meta']))
                                    <span class="cal-liste-m">{{ $evenement['meta'] }}</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</div>

@once
<style>
    /* ---------- Calendrier hebdomadaire (partials/calendrier-semaine) ---------- */
    .cal-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }

    .cal-bar { display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-bottom: 1px solid var(--border-soft); flex-wrap: wrap; }
    .cal-nav { display: flex; align-items: center; gap: 7px; }
    .cal-fleche {
        display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px;
        border-radius: 9px; border: 1px solid var(--border); background: #fff;
    }
    .cal-fleche:hover { border-color: #c3c6f5; background: #fafbff; }
    .cal-titre { display: flex; align-items: baseline; gap: 10px; margin-left: auto; flex-wrap: wrap; }
    .cal-periode { font-size: 14px; font-weight: 700; letter-spacing: -.01em; }
    .cal-total { font-size: 12.5px; color: var(--muted); }
    .btn-sm { padding: .34rem .7rem; font-size: 12px; box-shadow: none; }

    .cal-vide { text-align: center; padding: 48px 16px; }
    .cal-vide span { display: block; margin-top: 10px; font-size: 13px; color: var(--muted); }

    .cal-scroll { overflow-x: auto; }
    /* Une heure = 54px : assez pour lire un cours d'une heure sans le tronquer. */
    .cal-grille { display: grid; grid-template-columns: 52px repeat(var(--cal-jours, 6), minmax(122px, 1fr)); min-width: 780px; }

    .cal-jour-tete {
        height: 46px; display: flex; align-items: center; justify-content: center; gap: 6px;
        border-bottom: 1px solid var(--border-soft); border-left: 1px solid var(--border-soft);
    }
    .cal-axe-tete { border-left: 0; }
    .cal-jour-nom { font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); }
    .cal-jour-num { font-size: 15px; font-weight: 700; letter-spacing: -.02em; }
    .cal-jour.is-today .cal-jour-nom, .cal-jour.is-today .cal-jour-num { color: var(--brand); }
    .cal-jour.is-today .cal-jour-corps { background: #fafbff; }

    .cal-axe-corps, .cal-jour-corps { position: relative; height: calc(var(--cal-heures) * 54px); }
    .cal-jour-corps { border-left: 1px solid var(--border-soft); }
    .cal-heure {
        position: absolute; right: 8px; font-size: 10.5px; font-weight: 600; color: var(--faint);
        font-variant-numeric: tabular-nums; transform: translateY(-50%);
    }
    .cal-ligne { position: absolute; left: 0; right: 0; border-top: 1px solid var(--border-soft); }

    .cal-event {
        position: absolute; overflow: hidden; padding: 5px 7px; border-radius: 7px;
        /* Teinte de la classe, éclaircie ; le fond uni sert de repli si le
           navigateur ne connaît pas color-mix(). */
        background: #f5f6ff;
        background: color-mix(in srgb, var(--event) 12%, #fff);
        border-left: 3px solid var(--event);
        display: flex; flex-direction: column; gap: 1px; min-height: 22px;
    }
    a.cal-event:hover { background: color-mix(in srgb, var(--event) 22%, #fff); }
    .cal-event-h { font-size: 10.5px; font-weight: 700; color: var(--event); font-variant-numeric: tabular-nums; }
    .cal-event-t { font-size: 12px; font-weight: 600; color: var(--ink); line-height: 1.25; }
    .cal-event-m { font-size: 11px; color: var(--muted); line-height: 1.2; }

    .cal-liste { display: none; }
    .cal-liste-jour + .cal-liste-jour { border-top: 1px solid var(--border-soft); }
    .cal-liste-tete { padding: 10px 15px; background: #fafbfd; font-size: 12px; font-weight: 700; color: var(--muted); }
    .cal-liste-tete.is-today { color: var(--brand); }
    .cal-liste-ligne { display: flex; gap: 12px; padding: 11px 15px; border-top: 1px solid var(--border-soft); }
    .cal-liste-h { font-size: 11.5px; font-weight: 700; color: var(--event); font-variant-numeric: tabular-nums; text-align: right; flex-shrink: 0; }
    .cal-liste-t { display: block; font-size: 13px; font-weight: 600; }
    .cal-liste-m { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    @media (max-width: 760px) {
        .cal-scroll { display: none; }
        .cal-liste { display: block; }
        .cal-titre { margin-left: 0; }
    }
</style>
@endonce
