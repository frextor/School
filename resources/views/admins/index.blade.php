@extends('layouts.app')

@section('title', 'Administrateurs')

@section('content')
<div class="crumb">
    <a href="{{ route('admin.dashboard') }}">Accueil</a>
    <span class="sep">/</span>
    <span class="current">Administrateurs</span>
</div>

<div class="page-head">
    <div>
        <div class="title-row">
            <h1>Administrateurs</h1>
            <span class="badge badge-brand">{{ $admins->total() }} compte{{ $admins->total() > 1 ? 's' : '' }}</span>
        </div>
        <p class="page-sub">Les personnes qui accèdent à l'administration de l'école. Leur rôle détermine ce qu'elles peuvent ouvrir.</p>
    </div>
    <div class="page-actions">
        <a class="btn" href="{{ route('admins.create') }}">
            @include('partials.icon', ['n' => 'plus', 's' => 15, 'c' => '#fff', 'w' => 2.2])Nouvel administrateur
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Administrateur</th>
                    <th style="width:170px">Identifiant</th>
                    <th style="width:160px">Rôle</th>
                    <th style="width:160px">Service</th>
                    <th>Établissements</th>
                    <th class="col-actions"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    @php
                        $initiales = mb_strtoupper(mb_substr($admin->nom, 0, 1).mb_substr($admin->prenom, 0, 1));
                        $ecoles = $admin->etablissements->pluck('nom_etablissement');
                    @endphp
                    <tr>
                        <td>
                            <span class="cell-user">
                                <span class="cell-avatar">{{ $initiales }}</span>
                                <span>
                                    <span class="cell-name">{{ $admin->nom }} {{ $admin->prenom }}</span>
                                    <span class="cell-sub">{{ $admin->email }}</span>
                                </span>
                            </span>
                        </td>
                        <td><code>{{ $admin->username }}</code></td>
                        <td><span class="badge badge-brand">{{ $admin->profil }}</span></td>
                        <td>{{ $admin->service?->libelle ?? '—' }}</td>
                        <td>
                            @forelse ($ecoles->take(2) as $nom)
                                <span class="badge">{{ $nom }}</span>
                            @empty
                                <span style="color:var(--faint)">tous</span>
                            @endforelse
                            @if ($ecoles->count() > 2)
                                <span class="badge">+{{ $ecoles->count() - 2 }}</span>
                            @endif
                        </td>
                        <td class="col-actions">
                            <a href="{{ route('admins.edit', $admin) }}" class="row-btn" title="Modifier">
                                @include('partials.icon', ['n' => 'pencil', 's' => 14, 'c' => '#585e72', 'w' => 2])
                            </a>
                            <form method="post" action="{{ route('admins.destroy', $admin) }}" class="inline-form"
                                  onsubmit="return confirm('Supprimer le compte de {{ $admin->nom }} {{ $admin->prenom }} ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn is-danger" title="Supprimer">
                                    @include('partials.icon', ['n' => 'trash', 's' => 14, 'c' => '#b91c1c', 'w' => 2])
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">
                            @include('partials.icon', ['n' => 'key', 's' => 26, 'c' => '#c9cdd9', 'w' => 1.6])
                            <span>Aucun administrateur.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-foot">{{ $admins->links() }}</div>
</div>
@endsection
