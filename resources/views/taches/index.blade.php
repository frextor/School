@extends('layouts.app')

@section('title', 'Tâches & relances')

@section('content')
@php $aujourdhui = \Illuminate\Support\Carbon::today(); @endphp

<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Tâches &amp; relances</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Tâches &amp; relances</h1>
            <span class="badge badge-brand">{{ $taches->total() }} ouverte{{ $taches->total() > 1 ? 's' : '' }}</span>
        </div>
        <p class="page-sub">Les rappels de suivi posés sur les familles et les prospects. Clôturez-les une fois traités.</p>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:240px">Contact</th>
                    <th style="width:170px">Type</th>
                    <th>Commentaire</th>
                    <th style="width:190px">Échéance</th>
                    <th style="width:140px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($taches as $tache)
                    @php
                        $echue = $tache->deadline && $tache->deadline->lt($aujourdhui);
                        $aujourdhuiMeme = $tache->deadline && $tache->deadline->isSameDay($aujourdhui);
                    @endphp
                    <tr>
                        <td class="cell-name">
                            @if ($tache->contact)
                                <a href="{{ route('contacts.show', $tache->contact) }}">{{ $tache->contact->nom_complet }}</a>
                            @else
                                <span style="color:var(--faint)">—</span>
                            @endif
                        </td>
                        <td>{{ $tache->type?->libelle ?? '—' }}</td>
                        <td class="cell-wide">{{ $tache->commentaire ?: '—' }}</td>
                        <td>
                            @if (! $tache->deadline)
                                <span style="color:var(--faint)">sans échéance</span>
                            @elseif ($echue)
                                {{-- En retard : c'est l'information qui décide de l'ordre
                                     dans lequel le secrétariat traite sa journée. --}}
                                <span class="badge badge-danger">en retard</span>
                                <span class="cell-sub">{{ $tache->deadline->format('d/m/Y H:i') }}</span>
                            @elseif ($aujourdhuiMeme)
                                <span class="badge badge-brand">aujourd'hui</span>
                                <span class="cell-sub">{{ $tache->deadline->format('H:i') }}</span>
                            @else
                                {{ $tache->deadline->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td>
                            <form method="post" action="{{ route('taches.close', $tache) }}" class="inline-form"
                                  onsubmit="return confirm('Clôturer cette tâche ?')">
                                @csrf
                                <button type="submit" class="btn btn-ghost">Clôturer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-cell">
                            @include('partials.icon', ['n' => 'check', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucune tâche ouverte. Tout est traité.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $taches->links() }}</div>
</div>
@endsection
