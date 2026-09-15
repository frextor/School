{{-- Mise en page commune des documents administratifs (certificats, reçus).
     Mise en page 100 % tableaux et police DejaVu Sans : contraintes dompdf.
     Paramètres : $titreDocument, $sousTitre (optionnel), $etablissement,
     $ville, et la section `document`. --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $titreDocument }}@isset($eleve) — {{ $eleve->contact?->nom_complet }}@endisset</title>
    <style>
        @page { margin: 18mm 18mm 14mm; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5pt; color: #171a23; line-height: 1.6; margin: 0; }
        table { border-collapse: collapse; width: 100%; }
        td { vertical-align: top; }
        .reset td { border: 0; padding: 0; }
        strong { font-weight: bold; }

        .head { border-bottom: 2px solid #171a23; padding-bottom: 10px; }
        .head .school { font-size: 13pt; font-weight: bold; letter-spacing: -.2px; }
        .head .addr { font-size: 8.5pt; color: #585e72; }
        .mark {
            width: 30px; height: 30px; background: #4338ca; color: #fff;
            font-size: 13pt; font-weight: bold; text-align: center; line-height: 30px;
        }

        .titre {
            margin-top: 34px; text-align: center; font-size: 17pt; font-weight: bold;
            letter-spacing: 1.5px; text-transform: uppercase;
        }
        .soustitre { text-align: center; font-size: 9pt; color: #585e72; margin-top: 4px; letter-spacing: .5px; }

        .corps { margin-top: 32px; text-align: justify; }
        .corps p { margin: 0 0 14px; }

        .recap { margin-top: 26px; border: 1px solid #e8eaf1; }
        .recap td { border: 1px solid #e8eaf1; padding: 7px 10px; }
        .recap .label {
            font-size: 7pt; font-weight: bold; text-transform: uppercase;
            letter-spacing: .5px; color: #6b7280; width: 32%;
        }
        .recap .value { font-size: 10pt; font-weight: bold; }
        .recap .value.is-ok { color: #0f766e; }
        .recap .value.is-warn { color: #b45309; }

        .mention { margin-top: 26px; font-size: 9.5pt; color: #3f4451; font-style: italic; }
        .alerte {
            margin-top: 22px; padding: 10px 12px; border: 1px solid #f5d9a8; background: #fdf3e3;
            font-size: 9.5pt; color: #92400e;
        }

        .signature { margin-top: 46px; }
        .signature .lieu { font-size: 10pt; }
        .signature .bloc { text-align: center; font-size: 9.5pt; }
        .signature .role { font-weight: bold; }
        .signature .cadre { margin-top: 58px; border-top: 1px solid #9aa0b0; padding-top: 5px; color: #6b7280; font-size: 8.5pt; }

        .pied { margin-top: 40px; border-top: 1px solid #e8eaf1; padding-top: 7px; font-size: 7.5pt; color: #9aa0b0; text-align: center; }
    </style>
</head>
<body>

@php
    $nomEcole = $etablissement?->nom_etablissement ?: config('app.name');
@endphp

<table class="head reset">
    <tr>
        <td style="width:38px"><div class="mark">{{ mb_strtoupper(mb_substr($nomEcole, 0, 1)) }}</div></td>
        <td style="padding-left:10px">
            <div class="school">{{ $nomEcole }}</div>
            @if ($etablissement?->adresse)
                <div class="addr">{{ $etablissement->adresse }}</div>
            @endif
        </td>
    </tr>
</table>

<div class="titre">{{ $titreDocument }}</div>
@isset($sousTitre)
    <div class="soustitre">{{ $sousTitre }}</div>
@endisset

@yield('document')

{{-- Lieu, date et signature --}}
<table class="signature reset">
    <tr>
        <td style="width:55%" class="lieu">
            Fait à {{ $ville ?: '. . . . . . . . . . . .' }}, le {{ \Illuminate\Support\Carbon::now()->format('d/m/Y') }}
        </td>
        <td class="bloc">
            <div class="role">La Direction</div>
            <div class="cadre">Signature et cachet de l'établissement</div>
        </td>
    </tr>
</table>

<div class="pied">
    Document généré le {{ \Illuminate\Support\Carbon::now()->format('d/m/Y à H:i') }} — {{ $nomEcole }}
</div>

</body>
</html>
