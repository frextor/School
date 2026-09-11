<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 16px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 1rem; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; }
        th { background: #eee; }
        .moyenne { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Bulletin de notes — {{ $eleve->contact?->nom_complet }}</h1>
    <p>
        Année {{ $annee }}/{{ $annee + 1 }}
        @if ($semestre) — Semestre {{ $semestre }} @endif
    </p>

    @forelse ($ues as $ue)
        <table>
            <thead>
                <tr>
                    <th>{{ $ue['nom_ue'] ?? 'UE' }}</th>
                    <th>Type</th>
                    <th>Note</th>
                    <th>Session</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ue['notes'] as $note)
                    <tr>
                        <td>{{ $note->evaluation?->matiere?->nom_cours }}</td>
                        <td>{{ $note->evaluation?->typeEvaluation?->type?->type }}</td>
                        <td>{{ $note->note }}</td>
                        <td>{{ $note->session }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="moyenne">Moyenne UE</td>
                    <td class="moyenne" colspan="2">{{ $ue['moyenne'] ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    @empty
        <p>Aucune note saisie pour cette période.</p>
    @endforelse
</body>
</html>
