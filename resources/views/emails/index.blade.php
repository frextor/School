@extends('layouts.app')

@section('title', "Modèles d'e-mails")

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Modèles d'e-mails</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Modèles d'e-mails</h1>
            <span class="badge badge-brand">{{ $textes->total() }}</span>
        </div>
        <p class="page-sub">Les messages que l'application envoie aux familles et au personnel. Un modèle désactivé n'est pas envoyé.</p>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:200px">Catégorie</th>
                    <th>Sujet</th>
                    <th style="width:110px">Langue</th>
                    <th style="width:120px">État</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($textes as $email)
                    <tr>
                        <td class="cell-name">{{ $email->categorie }}</td>
                        <td>{{ $email->sujet }}</td>
                        <td><span class="badge">{{ strtoupper($email->lang) }}</span></td>
                        <td>
                            @if ($email->statut)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge">Désactivé</span>
                            @endif
                        </td>
                        <td class="col-actions">
                            <a href="{{ route('emails.edit', $email) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-cell">
                            @include('partials.icon', ['n' => 'mail', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun modèle d'e-mail.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $textes->links() }}</div>
</div>
@endsection
