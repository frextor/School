@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@php
    // Raccourcis regroupés par domaine. Chaque entrée : [route, libellé].
    // Mêmes domaines et libellés que le menu latéral (partials/admin-nav.blade.php).
    $domaines = [
        ['Scolarité', 'indigo', 'users', [
            ['eleves.index', 'Élèves'],
            ['referentiel.classes.index', 'Classes'],
            ['absences.index', 'Assiduité'],
            ['absences.appel', "Faire l'appel"],
        ]],
        ['Inscriptions & familles', 'teal', 'contact', [
            ['contacts.index', 'Familles & prospects'],
            ['candidats.index', 'Candidats'],
            ['epreuves.index', "Épreuves d'admission"],
            ['taches.index', 'Tâches & relances'],
        ]],
        ['Finances', 'amber', 'card', [
            // Pas de "paiements.index" ici : la route existe mais requiert un {eleve}
            // (les règlements se consultent depuis la fiche élève, pas de liste globale).
            ['echeances.index', 'Échéanciers & impayés'],
            ['echeances.generer', 'Générer un échéancier'],
        ]],
        ['Pédagogie', 'rose', 'book', [
            ['referentiel.niveaux.index', 'Niveaux & cycles'],
            ['referentiel.cours.index', 'Matières'],
            ['planning.index', 'Emploi du temps'],
            ['referentiel.periodes-formation.index', 'Périodes scolaires'],
        ]],
        ['Notation & bulletins', 'violet', 'pencil', [
            ['evaluations.index', 'Évaluations & notes'],
            ['bulletin-v2.index', 'Bulletins de notes'],
            ['bulletins.index', 'Conseils de classe & décisions'],
            ['types-evaluation.index', "Types d'évaluation"],
        ]],
        ['Établissement & administration', 'slate', 'shield', [
            ['referentiel.intervenants.index', 'Enseignants'],
            ['salles.index', 'Salles'],
            ['admins.index', 'Administrateurs'],
            ['configuration.site', "Paramètres de l'école"],
        ]],
    ];

    // L'espace entreprise (alternance/conventions) relève du supérieur : masqué ici.
    $portails = [
        ['eleve.login', 'cap', 'Espace élève', 'Emploi du temps, notes, documents'],
        ['intervenant.login', 'school', 'Espace enseignant', 'Classes, émargement, saisie des notes'],
    ];

    $prenom = auth('admin')->user()->prenom;
    $aujourdhui = ucfirst(\Illuminate\Support\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY'));
    $anneeScolaire = (int) date('n') >= 8
        ? date('Y').' — '.(date('Y') + 1)
        : (date('Y') - 1).' — '.date('Y');
@endphp

{{-- ---------- Bandeau d'accueil ---------- --}}
<section class="hero">
    <span class="hero-glow hero-glow-a"></span>
    <span class="hero-glow hero-glow-b"></span>
    <span class="hero-dots"></span>

    <div class="hero-body">
        <div class="hero-eyebrow">
            <span class="live-dot"></span>
            Année {{ $anneeScolaire }} · rentrée en cours
        </div>
        <h1>Bonjour {{ $prenom }}</h1>
        <p>{{ $aujourdhui }}. Vos modules sont là — tapez un nom, ou reprenez où vous en étiez.</p>

        <div class="hero-search">
            @include('partials.icon', ['n' => 'search', 's' => 19, 'c' => 'rgba(255,255,255,0.65)', 'w' => 2, 'style' => 'position:absolute;left:18px;top:17px;pointer-events:none'])
            <input type="text" id="module-filter" autocomplete="off"
                   placeholder="Aller à un module — élèves, bulletins, planning…"
                   aria-label="Filtrer les modules">
            <span class="hero-kbd"><span>⌘</span><span>K</span></span>
        </div>

        <div class="hero-cta">
            <a href="{{ route('eleves.create') }}" class="hero-btn">
                @include('partials.icon', ['n' => 'plus', 's' => 16, 'w' => 2.3])Nouvel élève
                <span class="sheen"></span>
            </a>
            {{-- Pas de bouton "Nouveau contact" : les contacts se créent depuis Base CRM
                 (contacts.index), il n'existe pas de route contacts.create dédiée. --}}
        </div>
    </div>
</section>

{{-- ---------- Domaines ---------- --}}
<div class="domains" id="domains">
    @foreach ($domaines as [$titre, $tint, $ico, $liens])
        @php $liens = array_values(array_filter($liens, fn ($l) => Route::has($l[0]))); @endphp
        @continue(! count($liens))
        <section class="domain tint-{{ $tint }}">
            <span class="domain-bar"></span>
            <div class="domain-head">
                <span class="domain-icon">@include('partials.icon', ['n' => $ico, 's' => 18])</span>
                <span>
                    <span class="domain-title">{{ $titre }}</span>
                    <span class="domain-count">{{ count($liens) }} {{ count($liens) > 1 ? 'modules' : 'module' }}</span>
                </span>
            </div>
            <div class="domain-links">
                @foreach ($liens as [$route, $libelle])
                    <a href="{{ route($route) }}" class="domain-link">
                        {{ $libelle }}
                        @include('partials.icon', ['n' => 'chevron-right', 's' => 14, 'c' => '#c9cdd9', 'w' => 2, 'style' => 'margin-left:auto'])
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach
    <p class="domains-empty" hidden>Aucun module ne correspond à cette recherche.</p>
</div>

{{-- ---------- Autres espaces ---------- --}}
<div class="section-head">
    <h2>Autres espaces</h2>
    <span class="rule"></span>
</div>
<div class="portals">
    @foreach ($portails as [$route, $ico, $titre, $texte])
        @continue(! Route::has($route))
        <a href="{{ route($route) }}" class="portal" target="_blank" rel="noopener">
            <span class="portal-icon">@include('partials.icon', ['n' => $ico, 's' => 17, 'c' => '#4f46e5'])</span>
            <span class="portal-text">
                <span class="portal-title">{{ $titre }}</span>
                <span class="portal-sub">{{ $texte }}</span>
            </span>
            @include('partials.icon', ['n' => 'external', 's' => 14, 'c' => '#b9bdcc', 'w' => 2, 'style' => 'margin-left:auto'])
        </a>
    @endforeach
</div>

<style>
    /* Tableau de bord : styles spécifiques (le reste vient de layouts/app.blade.php) */
    @keyframes dash-drift {
        0%, 100% { transform: translate3d(-4%, -2%, 0) scale(1); }
        50% { transform: translate3d(6%, 3%, 0) scale(1.12); }
    }
    @keyframes dash-drift-b {
        0%, 100% { transform: translate3d(3%, 4%, 0) scale(1.05); }
        50% { transform: translate3d(-5%, -3%, 0) scale(1); }
    }
    @keyframes dash-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
    @keyframes dash-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: .45; transform: scale(.82); } }
    @keyframes dash-sheen { 0% { transform: translateX(-120%); } 100% { transform: translateX(240%); } }

    /* Bandeau */
    .hero {
        position: relative; overflow: hidden; border-radius: 20px; padding: 34px 34px 30px;
        background: linear-gradient(150deg, #241f52 0%, #3b32a8 46%, #4f46e5 100%);
        animation: dash-rise .5s cubic-bezier(.22,1,.36,1) both;
    }
    .hero-glow { position: absolute; border-radius: 50%; pointer-events: none; }
    .hero-glow-a {
        width: 520px; height: 520px; right: -120px; top: -200px;
        background: radial-gradient(circle, rgba(129,140,248,.55), transparent 62%);
        animation: dash-drift 16s ease-in-out infinite;
    }
    .hero-glow-b {
        width: 420px; height: 420px; left: -140px; bottom: -220px;
        background: radial-gradient(circle, rgba(56,189,248,.32), transparent 65%);
        animation: dash-drift-b 21s ease-in-out infinite;
    }
    .hero-dots {
        position: absolute; inset: 0; pointer-events: none; opacity: .5;
        background-image: radial-gradient(rgba(255,255,255,.14) 1px, transparent 1px);
        background-size: 24px 24px;
        -webkit-mask-image: linear-gradient(120deg, rgba(0,0,0,.9), transparent 62%);
        mask-image: linear-gradient(120deg, rgba(0,0,0,.9), transparent 62%);
    }
    .hero-body { position: relative; }
    .hero-eyebrow {
        display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,.78);
    }
    .live-dot { width: 7px; height: 7px; border-radius: 50%; background: #5eead4; animation: dash-pulse 2.2s ease-in-out infinite; }
    .hero h1 { margin: 16px 0 0; font-size: 36px; font-weight: 700; letter-spacing: -.03em; color: #fff; line-height: 1.1; }
    .hero > .hero-body > p { margin: 10px 0 0; max-width: 54ch; font-size: 15px; color: rgba(255,255,255,.8); text-wrap: pretty; }

    .hero-search { position: relative; max-width: 600px; margin-top: 26px; }
    .hero-search input {
        width: 100%; max-width: none; border: 1px solid rgba(255,255,255,.28);
        background: rgba(255,255,255,.12); border-radius: 13px;
        padding: 16px 104px 16px 48px; font: inherit; font-size: 16px; color: #fff;
        transition: background .18s ease, border-color .18s ease, box-shadow .18s ease;
    }
    .hero-search input::placeholder { color: rgba(255,255,255,.62); }
    .hero-search input:focus {
        outline: none; background: rgba(255,255,255,.2);
        border-color: rgba(255,255,255,.5); box-shadow: 0 0 0 4px rgba(255,255,255,.12);
    }
    .hero-kbd { position: absolute; right: 16px; top: 15px; display: flex; gap: 4px; pointer-events: none; }
    .hero-kbd span {
        padding: 3px 7px; border-radius: 6px; border: 1px solid rgba(255,255,255,.28);
        background: rgba(255,255,255,.12); font-size: 11px; font-weight: 700; color: rgba(255,255,255,.82);
    }

    .hero-cta { display: flex; gap: 9px; flex-wrap: wrap; margin-top: 18px; }
    .hero-btn {
        position: relative; overflow: hidden; display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 18px; border-radius: 10px; background: #fff; color: var(--brand-deep);
        font-size: 14px; font-weight: 600;
        transition: transform .16s cubic-bezier(.22,1,.36,1), box-shadow .16s ease, background .16s ease;
    }
    .hero-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(0,0,0,.22); color: var(--brand-deep); }
    .hero-btn svg { stroke: currentColor; }
    .hero-btn .sheen {
        position: absolute; top: 0; left: 0; width: 28%; height: 100%; pointer-events: none;
        background: linear-gradient(90deg, transparent, rgba(79,70,229,.16), transparent);
        animation: dash-sheen 4.2s ease-in-out infinite;
    }
    .hero-btn.is-ghost { background: transparent; border: 1px solid rgba(255,255,255,.32); color: #fff; }
    .hero-btn.is-ghost:hover { background: rgba(255,255,255,.14); color: #fff; }

    /* Domaines */
    .domains { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-top: 26px; }
    .domain {
        position: relative; overflow: hidden; background: var(--surface);
        border: 1px solid var(--border); border-radius: 16px; padding: 19px 19px 15px;
        transition: transform .18s cubic-bezier(.22,1,.36,1), box-shadow .18s ease, border-color .18s ease;
    }
    .domain:hover { transform: translateY(-3px); border-color: #c3c6f5; box-shadow: 0 14px 34px rgba(23,26,35,.09); }
    .domain-bar { position: absolute; top: 0; left: 0; right: 0; height: 3px; opacity: .85; }
    .domain-head { display: flex; align-items: center; gap: 11px; }
    .domain-icon { width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
    .domain-title { display: block; font-size: 14.5px; font-weight: 700; letter-spacing: -.01em; }
    .domain-count { display: block; font-size: 11.5px; color: var(--muted); }
    .domain-links { display: flex; flex-direction: column; gap: 1px; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--border-soft); }
    .domain-link {
        display: flex; align-items: center; gap: 9px; padding: 7px 9px; margin: 0 -9px;
        border-radius: 9px; color: var(--ink); font-size: 13.5px; font-weight: 500;
        transition: background .14s ease, color .14s ease, padding-left .16s cubic-bezier(.22,1,.36,1);
    }
    .domain-link:hover { background: #f7f8ff; color: var(--brand-deep); padding-left: 14px; }

    .tint-indigo .domain-bar { background: #4f46e5; } .tint-indigo .domain-icon { background: #eef0fe; } .tint-indigo .domain-icon svg { stroke: #4f46e5; }
    .tint-teal   .domain-bar { background: #0f766e; } .tint-teal   .domain-icon { background: #e7f6f2; } .tint-teal   .domain-icon svg { stroke: #0f766e; }
    .tint-amber  .domain-bar { background: #b45309; } .tint-amber  .domain-icon { background: #fdf3e3; } .tint-amber  .domain-icon svg { stroke: #b45309; }
    .tint-rose   .domain-bar { background: #be123c; } .tint-rose   .domain-icon { background: #fdecef; } .tint-rose   .domain-icon svg { stroke: #be123c; }
    .tint-violet .domain-bar { background: #7c3aed; } .tint-violet .domain-icon { background: #f4ecfd; } .tint-violet .domain-icon svg { stroke: #7c3aed; }
    .tint-slate  .domain-bar { background: #475569; } .tint-slate  .domain-icon { background: #eef1f6; } .tint-slate  .domain-icon svg { stroke: #475569; }

    .domains-empty { grid-column: 1 / -1; margin: 0; padding: 32px 4px; text-align: center; font-size: 13px; color: var(--muted); }

    /* Autres espaces */
    .section-head { display: flex; align-items: center; gap: 10px; margin: 34px 0 12px; }
    .section-head h2 { margin: 0; font-size: 12.5px; font-weight: 700; letter-spacing: .02em; }
    .section-head .rule { flex: 1; height: 1px; background: var(--border); }
    .portals { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 12px; }
    .portal {
        display: flex; align-items: center; gap: 13px; padding: 16px; color: inherit;
        border: 1px solid #e4e6f6; border-radius: 14px;
        background: linear-gradient(150deg, #f2f3ff, #fff 68%);
        transition: transform .18s cubic-bezier(.22,1,.36,1), box-shadow .18s ease, border-color .18s ease;
    }
    .portal:hover { transform: translateY(-3px); border-color: #c3c6f5; box-shadow: 0 12px 30px rgba(23,26,35,.09); color: inherit; }
    .portal-icon {
        width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0; background: #fff;
        border: 1px solid #e6e8f4; display: flex; align-items: center; justify-content: center;
    }
    .portal-text { min-width: 0; }
    .portal-title { display: block; font-size: 13.5px; font-weight: 600; }
    .portal-sub { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }

    @media (max-width: 720px) {
        .hero { padding: 26px 22px 24px; border-radius: 16px; }
        .hero h1 { font-size: 28px; }
        .hero-search input { font-size: 15px; padding-right: 20px; }
        .hero-kbd { display: none; }
    }
    @media (prefers-reduced-motion: reduce) {
        .hero, .hero-glow, .live-dot, .hero-btn .sheen { animation: none; }
        .domain, .portal, .hero-btn, .domain-link { transition: none; }
    }
</style>

<script>
    // Filtre des raccourcis : masque les liens puis les domaines sans correspondance.
    (function () {
        var input = document.getElementById('module-filter');
        if (!input) return;
        var domains = Array.prototype.slice.call(document.querySelectorAll('#domains .domain'));
        var empty = document.querySelector('.domains-empty');

        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            var visibles = 0;

            domains.forEach(function (domain) {
                var shown = 0;
                domain.querySelectorAll('.domain-link').forEach(function (link) {
                    var match = !q || link.textContent.trim().toLowerCase().indexOf(q) !== -1;
                    link.style.display = match ? '' : 'none';
                    if (match) shown++;
                });
                domain.hidden = shown === 0;
                if (shown) visibles++;
            });

            if (empty) empty.hidden = visibles > 0;
        });

        // ⌘K / Ctrl+K place le curseur dans le champ
        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                input.focus();
                input.select();
            }
        });
    })();
</script>
@endsection
