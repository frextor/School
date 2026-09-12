{{-- Panneau de marque du volet gauche des écrans "auth-split" (connexion, inscription...).
     Paramètres : panelTitle, panelTagline, panelPoints (array). --}}
@php
    $panelTagline = $panelTagline ?? "Élèves, CRM, pédagogie, notation et bulletins — depuis un seul espace de gestion.";
@endphp
<div class="auth-panel">
    <div class="auth-panel-dots"></div>
    <div class="auth-panel-glow"></div>

    <div class="auth-panel-brand">
        <span class="auth-panel-mark">@include('partials.icon', ['n' => 'cap', 's' => 18, 'c' => '#fff', 'w' => 2])</span>
        <span>
            <span class="auth-panel-name">{{ config('app.name') }}</span>
            <span class="auth-panel-sub">School Tech</span>
        </span>
    </div>

    <div class="auth-panel-body">
        <h2>{{ $panelTitle }}</h2>
        <p>{{ $panelTagline }}</p>
        <ul>
            @foreach ($panelPoints as $point)
                <li>@include('partials.icon', ['n' => 'check', 's' => 15, 'c' => 'rgba(255,255,255,0.9)', 'w' => 2.2]){{ $point }}</li>
            @endforeach
        </ul>
    </div>

    <div class="auth-panel-foot">© {{ date('Y') }} {{ config('app.name') }} by School Tech</div>
</div>
