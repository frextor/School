<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin — {{ $eleve->contact?->nom_complet }}</title>
    <style>
        /* Mise en page 100 % tableaux : compatible dompdf (pas de flex ni de grid). */
        @page { margin: 13mm 14mm 11mm; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #171a23; line-height: 1.45; margin: 0; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        .reset td { border: 0; padding: 0; }

        /* En-tête */
        .head { border-bottom: 2px solid #171a23; padding-bottom: 9px; }
        .head .school { font-size: 11.5pt; font-weight: bold; letter-spacing: -.2px; }
        .head .addr { font-size: 8pt; color: #585e72; }
        .head .doc { font-size: 12pt; font-weight: bold; text-align: right; }
        .head .period { font-size: 8pt; color: #585e72; text-align: right; }
        .mark {
            width: 26px; height: 26px; background: #4338ca; color: #fff;
            font-size: 12pt; font-weight: bold; text-align: center; line-height: 26px;
        }

        /* Identité */
        .ident { margin-top: 10px; border: 1px solid #e8eaf1; }
        .ident td { border: 1px solid #e8eaf1; padding: 5px 7px; width: 25%; }
        .ident .label { font-size: 6.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; }
        .ident .value { font-size: 9pt; font-weight: bold; }

        /* Unités d'enseignement */
        .ue { margin-top: 9px; border: 1px solid #e8eaf1; page-break-inside: avoid; }
        .ue-head { background: #f5f6fa; border-bottom: 1px solid #e8eaf1; }
        .ue-head td { padding: 4px 8px; }
        .ue-name { font-size: 9pt; font-weight: bold; }
        .ue-ects { font-size: 7.5pt; color: #6b7280; }
        .ue-avg-label { font-size: 6.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; text-align: right; }
        .ue-avg { font-size: 10.5pt; font-weight: bold; text-align: right; width: 42px; }
        .ue-avg.is-low { color: #b91c1c; }

        .notes th {
            padding: 4px 8px; font-size: 6.5pt; font-weight: bold; text-transform: uppercase;
            letter-spacing: .5px; color: #6b7280; border-bottom: 1px solid #eceef4; text-align: left;
        }
        .notes td { padding: 4px 8px; font-size: 9pt; border-bottom: 1px solid #f6f7fa; }
        .notes .c { text-align: center; }
        .notes .r { text-align: right; }
        .notes .muted { color: #585e72; }
        .notes .note { font-weight: bold; text-align: right; }
        .notes tr:last-child td { border-bottom: 0; }

        /* Synthèse */
        .synth { margin-top: 11px; border: 1px solid #171a23; }
        .synth td { border: 1px solid #171a23; padding: 7px 9px; width: 25%; }
        .synth .label { font-size: 6.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; }
        .synth .value { font-size: 14pt; font-weight: bold; }
        .synth .hint { font-size: 7.5pt; color: #6b7280; }
        .synth .is-dark { background: #171a23; }
        .synth .is-dark .label, .synth .is-dark .hint { color: #cfd2e0; }
        .synth .is-dark .value { color: #fff; }
        .synth .is-brand { color: #3730a3; }

        /* Appréciation */
        .appr { border: 1px solid #e8eaf1; border-top: 0; padding: 8px 9px; min-height: 18mm; }
        .appr .label { font-size: 6.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; }
        .appr p { margin: 4px 0 0; font-size: 9pt; }

        /* Pied */
        .foot { margin-top: 11px; border-top: 1px solid #e8eaf1; padding-top: 9px; }
        .foot .legal { font-size: 7.5pt; color: #6b7280; }
        .foot .sign { width: 56mm; text-align: center; }
        .sign-line { border-bottom: 1px solid #171a23; height: 15mm; }
        .sign-role { font-size: 7.5pt; font-weight: bold; padding-top: 4px; }
        .sign-place { font-size: 7pt; color: #6b7280; }

        .empty { border: 1px solid #e8eaf1; padding: 18px; text-align: center; font-size: 9pt; color: #6b7280; margin-top: 10px; }
    </style>
</head>
<body>
@php
    // Valeurs optionnelles : la vue fonctionne même si le contrôleur ne les fournit pas.
    $etab = $etablissement ?? $eleve->etablissement ?? null;
    $contact = $eleve->contact ?? null;
    $note = fn ($valeur) => $valeur === null || $valeur === '' ? '—' : number_format((float) $valeur, 2, ',', ' ');
    $moyenneGenerale = $moyenneGenerale ?? null;
    $ectsAcquis = $ectsAcquis ?? null;
    $ectsTotal = $ectsTotal ?? null;
    $rang = $rang ?? null;
    $effectif = $effectif ?? null;
    $decision = $decision ?? null;
    $mention = $mention ?? null;
    $appreciation = $appreciation ?? null;
    $genereLe = now()->format('d/m/Y');

    $identite = array_filter([
        'Élève' => $contact?->nom_complet,
        'Identifiant' => $eleve->id_eleve,
        'Date de naissance' => $contact?->date_naissance ? \Illuminate\Support\Carbon::parse($contact->date_naissance)->format('d/m/Y') : null,
        'Campus' => $etab?->nom_etablissement,
        'Formation' => $eleve->niveau?->nom_niveau,
        'Classe' => $eleve->classe?->classe,
        'Période' => $semestre ? 'Semestre '.$semestre : 'Année complète',
        'Session' => 'Session '.(($session ?? 0) + 1),
    ], fn ($v) => $v !== null && $v !== '');
@endphp

{{-- En-tête --}}
<table class="reset head">
    <tr>
        <td style="width:30px"><div class="mark">M</div></td>
        <td style="padding-left:8px">
            <div class="school">{{ $etab?->nom_etablissement ?? config('app.name') }}</div>
            @if ($etab?->adresse ?? false)
                <div class="addr">{{ $etab->adresse }}</div>
            @endif
        </td>
        <td>
            <div class="doc">Bulletin de notes</div>
            <div class="period">
                Année {{ $annee }} / {{ $annee + 1 }}@if ($semestre) — Semestre {{ $semestre }} @endif
            </div>
        </td>
    </tr>
</table>

{{-- Identité de l'élève --}}
@php $lignesIdentite = array_chunk($identite, 4, true); @endphp
<table class="ident">
    @foreach ($lignesIdentite as $ligne)
        <tr>
            @foreach ($ligne as $label => $valeur)
                <td>
                    <div class="label">{{ $label }}</div>
                    <div class="value">{{ $valeur }}</div>
                </td>
            @endforeach
            @for ($i = count($ligne); $i < 4; $i++)<td></td>@endfor
        </tr>
    @endforeach
</table>

{{-- Unités d'enseignement --}}
@forelse ($ues as $ue)
    @php $moyenneUe = $ue['moyenne'] ?? null; @endphp
    <table class="ue">
        <tr class="ue-head">
            <td>
                <span class="ue-name">{{ $ue['nom_ue'] ?? 'Unité d\'enseignement' }}</span>
                @if ($ue['ects'] ?? false)<span class="ue-ects">&nbsp;&nbsp;{{ $ue['ects'] }} ECTS</span>@endif
            </td>
            <td class="ue-avg-label">Moyenne</td>
            <td class="ue-avg {{ $moyenneUe !== null && (float) $moyenneUe < 10 ? 'is-low' : '' }}">{{ $note($moyenneUe) }}</td>
        </tr>
        <tr>
            <td colspan="3" style="padding:0">
                <table class="notes">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Type d'évaluation</th>
                            <th class="c" style="width:52px">Session</th>
                            <th class="r" style="width:42px">Coef.</th>
                            <th class="r" style="width:42px">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ue['notes'] as $ligne)
                            <tr>
                                <td>{{ $ligne->evaluation?->matiere?->nom_cours ?? '—' }}</td>
                                <td class="muted">{{ $ligne->evaluation?->typeEvaluation?->type?->type ?? '—' }}</td>
                                <td class="c muted">{{ $ligne->session }}</td>
                                <td class="r muted">{{ $ligne->evaluation?->coefficient ?? '1' }}</td>
                                <td class="note">{{ $note($ligne->note) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
@empty
    <div class="empty">Aucune note saisie pour cette période.</div>
@endforelse

{{-- Synthèse --}}
@if ($ues->isNotEmpty() ?? count($ues))
    <table class="synth">
        <tr>
            <td class="is-dark">
                <div class="label">Moyenne générale</div>
                <div class="value">{{ $note($moyenneGenerale) }}</div>
                <div class="hint">sur 20 — pondérée</div>
            </td>
            <td>
                <div class="label">ECTS acquis</div>
                <div class="value">{{ $ectsAcquis !== null ? $ectsAcquis.' / '.($ectsTotal ?? 30) : '—' }}</div>
                <div class="hint">crédits du semestre</div>
            </td>
            <td>
                <div class="label">Rang</div>
                <div class="value">{{ $rang !== null ? $rang.' / '.($effectif ?? '—') : '—' }}</div>
                <div class="hint">au sein de la classe</div>
            </td>
            <td>
                <div class="label">Décision</div>
                <div class="value is-brand">{{ $decision ?? '—' }}</div>
                <div class="hint">{{ $mention ? 'mention '.$mention : 'sous réserve du jury' }}</div>
            </td>
        </tr>
    </table>

    <div class="appr">
        <div class="label">Appréciation générale</div>
        <p>{{ $appreciation ?: 'Aucune appréciation saisie pour cette période.' }}</p>
    </div>
@endif

{{-- Pied de page et signature --}}
<table class="reset foot">
    <tr>
        <td class="legal">
            Document généré le {{ $genereLe }} par {{ config('app.name') }}. Les notes sont exprimées sur 20.
            La moyenne générale est pondérée par les coefficients des unités d'enseignement.
            Bulletin établi sous réserve de la validation du jury.
        </td>
        <td class="sign">
            <div class="sign-line"></div>
            <div class="sign-role">Le directeur du campus</div>
            <div class="sign-place">{{ $etab?->ville ?? '' }}{{ ($etab?->ville ?? false) ? ', le ' : 'Le ' }}{{ $genereLe }}</div>
        </td>
    </tr>
</table>
</body>
</html>
