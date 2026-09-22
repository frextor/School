<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $panneau->titre }} — {{ config('app.name') }}</title>

    {{-- Un écran de couloir n'a personne pour le recharger : il se rafraîchit
         seul, à l'intervalle configuré sur le panneau (delai_horaire). --}}
    <meta http-equiv="refresh" content="{{ max(15, (int) $panneau->delai_horaire) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --fond: #0f1222;
            --carte: #191d33;
            --trait: #272c48;
            --texte: #f5f6fb;
            --doux: #9aa0c0;
            --accent: #818cf8;
            --vert: #34d399;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0; min-height: 100vh;
            font-family: 'Instrument Sans', system-ui, sans-serif;
            background: var(--fond); color: var(--texte);
            display: flex; flex-direction: column;
        }

        header {
            display: flex; align-items: center; gap: 24px; flex-wrap: wrap;
            padding: 26px 36px; border-bottom: 1px solid var(--trait);
        }
        .titre { font-size: 34px; font-weight: 700; letter-spacing: -.03em; line-height: 1.1; }
        .lieu { margin-top: 4px; font-size: 17px; color: var(--doux); }
        .horloge { margin-left: auto; text-align: right; }
        .heure { font-size: 46px; font-weight: 700; letter-spacing: -.04em; line-height: 1; font-variant-numeric: tabular-nums; }
        .jour { margin-top: 5px; font-size: 16px; color: var(--doux); }

        main { flex: 1; padding: 22px 36px 36px; }

        .portee {
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
            margin-bottom: 18px; font-size: 15px; color: var(--doux);
        }
        .etiquette {
            padding: 4px 12px; border-radius: 999px;
            background: rgba(129, 140, 248, .14); color: #c7ccff; font-weight: 600; font-size: 14px;
        }

        .seances { display: flex; flex-direction: column; gap: 10px; }
        .seance {
            display: flex; align-items: center; gap: 22px;
            background: var(--carte); border: 1px solid var(--trait); border-radius: 16px;
            padding: 16px 22px;
        }
        .seance.is-encours { border-color: var(--vert); background: #16263a; }

        .creneau { width: 190px; flex-shrink: 0; }
        .creneau-heure { font-size: 30px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
        .creneau-fin { font-size: 15px; color: var(--doux); font-variant-numeric: tabular-nums; }
        .creneau-jour { display: block; margin-top: 2px; font-size: 13px; color: var(--doux); text-transform: uppercase; letter-spacing: .06em; }

        .detail { min-width: 0; flex: 1; }
        .matiere { font-size: 25px; font-weight: 600; letter-spacing: -.02em; }
        .infos { margin-top: 3px; font-size: 16px; color: var(--doux); }

        .salle {
            flex-shrink: 0; text-align: right; min-width: 150px;
        }
        .salle-nom { font-size: 25px; font-weight: 700; letter-spacing: -.02em; }
        .salle-label { font-size: 13px; color: var(--doux); text-transform: uppercase; letter-spacing: .06em; }

        .badge-encours {
            display: inline-block; margin-top: 6px; padding: 2px 10px; border-radius: 999px;
            background: rgba(52, 211, 153, .16); color: var(--vert); font-size: 13px; font-weight: 700;
        }

        .vide { text-align: center; padding: 90px 20px; }
        .vide-titre { font-size: 30px; font-weight: 700; letter-spacing: -.02em; }
        .vide-sub { margin-top: 10px; font-size: 18px; color: var(--doux); }

        footer {
            padding: 14px 36px; border-top: 1px solid var(--trait);
            display: flex; gap: 16px; flex-wrap: wrap;
            font-size: 13px; color: #6f77a0;
        }
        footer span:last-child { margin-left: auto; }

        /* Écrans de couloir souvent en 1080p vus de loin : on grossit tout. */
        @media (min-width: 1600px) {
            .titre { font-size: 42px; }
            .heure { font-size: 58px; }
            .creneau-heure, .matiere, .salle-nom { font-size: 32px; }
            .infos, .creneau-fin { font-size: 18px; }
        }
    </style>
</head>
<body>
    <header>
        <div>
            <div class="titre">{{ $panneau->titre }}</div>
            <div class="lieu">{{ $panneau->etablissement?->nom_etablissement ?? '' }}</div>
        </div>
        <div class="horloge">
            <div class="heure" id="heure">{{ now()->format('H:i') }}</div>
            <div class="jour">{{ ucfirst(now()->translatedFormat('l j F Y')) }}</div>
        </div>
    </header>

    <main>
        <div class="portee">
            <span>Cours jusqu'à {{ $fin->format('H:i') }}</span>
            @foreach ($panneau->classes as $classe)
                <span class="etiquette">{{ $classe->classe }}</span>
            @endforeach
            @foreach ($panneau->groupes as $groupe)
                <span class="etiquette">{{ $groupe->nom_groupe }}</span>
            @endforeach
            @if ($panneau->classes->isEmpty() && $panneau->groupes->isEmpty())
                <span class="etiquette">Tout l'établissement</span>
            @endif
        </div>

        <div class="seances">
        @forelse ($seances as $seance)
            @php $enCours = $seance->date_debut->isPast() && $seance->date_fin->isFuture(); @endphp
                <div class="seance @if ($enCours) is-encours @endif">
                    <div class="creneau">
                        <div class="creneau-heure">{{ $seance->date_debut->format('H:i') }}</div>
                        <div class="creneau-fin">jusqu'à {{ $seance->date_fin->format('H:i') }}</div>
                        @unless ($seance->date_debut->isToday())
                            <span class="creneau-jour">{{ $seance->date_debut->translatedFormat('D j M') }}</span>
                        @endunless
                    </div>

                    <div class="detail">
                        <div class="matiere">{{ $seance->cours?->nom_cours ?: 'Cours' }}</div>
                        <div class="infos">
                            {{ collect([
                                $seance->classe?->classe,
                                trim(($seance->intervenant?->nom ?? '').' '.($seance->intervenant?->prenom ?? '')) ?: null,
                            ])->filter()->implode(' · ') ?: '—' }}
                        </div>
                        @if ($enCours)
                            <span class="badge-encours">En cours</span>
                        @endif
                    </div>

                    <div class="salle">
                        <div class="salle-nom">{{ $seance->salle?->nom_salle ?: '—' }}</div>
                        <div class="salle-label">Salle</div>
                    </div>
                </div>
        @empty
            <div class="vide">
                <div class="vide-titre">Aucun cours dans les {{ $panneau->plage_horaire }} prochaines heures</div>
                <div class="vide-sub">Cet écran se met à jour tout seul.</div>
            </div>
        @endforelse
        </div>
    </main>

    <footer>
        <span>{{ config('app.name') }}</span>
        <span>Panneau {{ $panneau->identifiant_panneaux }}</span>
        <span>Mise à jour {{ now()->format('H:i:s') }}</span>
    </footer>

    <script>
        // L'heure avance chaque minute sans attendre le rechargement complet.
        setInterval(function () {
            var d = new Date();
            document.getElementById('heure').textContent =
                ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
        }, 1000);
    </script>
</body>
</html>
