<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #4f46e5;
            --brand-dark: #4338ca;
            --brand-light: #eef2ff;
            --ink: #1e2433;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg: #f4f5f9;
            --surface: #ffffff;
            --danger: #dc2626;
            --danger-dark: #b91c1c;
            --danger-bg: #fef2f2;
            --success-bg: #ecfdf3;
            --success-border: #a7f3d0;
            --radius: 10px;
            --shadow-sm: 0 1px 2px rgba(16,24,40,0.05);
            --shadow-md: 0 4px 16px rgba(16,24,40,0.07);
            --shadow-lg: 0 10px 30px rgba(30,36,51,0.10);
        }

        * { box-sizing: border-box; }

        ::selection { background: var(--brand); color: #fff; }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5e0; border-radius: 8px; border: 2px solid var(--bg); }
        ::-webkit-scrollbar-thumb:hover { background: #b7bccb; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            margin: 0;
            color: var(--ink);
            background: var(--bg);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        a { color: var(--brand); text-decoration: none; transition: color 0.15s ease; }
        a:hover { color: var(--brand-dark); text-decoration: underline; }

        h1 { font-size: 1.4rem; font-weight: 700; margin: 0 0 1.25rem; color: var(--ink); letter-spacing: -0.01em; }

        /* ---------- Fil d'Ariane + en-tête de page (repris de la structure
           .breadcrumb / .page-header de l'ancien thème) ---------- */
        .crumb {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.78rem; color: var(--muted); margin-bottom: 0.6rem;
        }
        .crumb a { color: var(--muted); }
        .crumb a:hover { color: var(--brand); text-decoration: none; }
        .crumb .sep { color: #cdd1dc; }
        .crumb .current { color: var(--ink); font-weight: 500; }

        .page-header-row {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap;
        }
        .page-header-row h1 { margin: 0; }

        .badge {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.2rem 0.6rem; border-radius: 999px;
            font-size: 0.74rem; font-weight: 600;
            background: #eef0f6; color: var(--muted);
        }
        .badge-brand { background: var(--brand-light); color: var(--brand-dark); }
        .badge-success { background: var(--success-bg); color: #15803d; }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        .badge-warning { background: #fffbeb; color: #b45309; }
        h2 { font-size: 1.1rem; font-weight: 600; margin: 1.75rem 0 0.75rem; color: var(--ink); }
        h3 { font-size: 0.95rem; font-weight: 600; margin: 1.25rem 0 0.6rem; color: var(--ink); }

        /* ---------- App shell ---------- */
        .app-shell { display: flex; min-height: 100vh; }

        /* Sidebar claire à séparateurs fins, reprise de l'ancien menu admin
           (application/views/templates/menu_admin.php + thème "clip") : fond
           blanc, items empilés séparés par une ligne, accent de marque sur
           l'actif/survol — plutôt que le panneau sombre générique d'avant. */
        .sidebar {
            width: 252px;
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            color: var(--ink);
            padding: 0 0 1.5rem;
            overflow-y: auto;
            position: sticky;
            top: 0;
            height: 100vh;
            scrollbar-width: thin;
        }
        .sidebar::-webkit-scrollbar-thumb { background: #d1d5e0; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--ink);
            font-weight: 800;
            font-size: 1.05rem;
            padding: 1.1rem 1.1rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 0.5rem;
            position: sticky;
            top: 0;
            background: var(--surface);
            z-index: 1;
        }
        .sidebar .brand:hover { text-decoration: none; }
        .nav-group { margin-bottom: 0.4rem; }
        .nav-group-title {
            font-size: 0.66rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--muted);
            padding: 0.9rem 1.1rem 0.35rem;
            font-weight: 700;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.5rem 1.1rem;
            color: #4b5165;
            font-size: 0.86rem;
            font-weight: 400;
            border-left: 3px solid transparent;
            border-bottom: 1px solid #f1f2f6;
            transition: background 0.12s ease, color 0.12s ease, border-color 0.12s ease;
        }
        .nav-link .ico { font-size: 0.95rem; opacity: 0.85; width: 1.1em; text-align: center; flex-shrink: 0; }
        .nav-link:hover { background: var(--brand-light); text-decoration: none; color: var(--brand-dark); }
        .nav-link.active {
            background: var(--brand-light); color: var(--brand-dark); font-weight: 600;
            border-left-color: var(--brand);
        }
        .nav-link.active .ico { opacity: 1; }

        .main-col { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.7rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: var(--shadow-sm);
        }
        .topbar .who { font-size: 0.85rem; color: var(--muted); display: inline-flex; align-items: center; gap: 0.55rem; }
        .topbar .avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff; font-weight: 700; font-size: 0.72rem;
            display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .topbar form { display: inline; }
        .topbar .logout-btn {
            background: none; border: 1px solid var(--border); color: var(--muted);
            padding: 0.35rem 0.75rem; border-radius: 7px; font-size: 0.82rem; cursor: pointer;
            transition: all 0.15s ease;
        }
        .topbar .logout-btn:hover { background: var(--danger-bg); color: var(--danger); border-color: #fecaca; }

        .menu-toggle { display: none; }

        .content { padding: 1.75rem; max-width: 1200px; width: 100%; margin: 0 auto; }
        .content > *:first-child { margin-top: 0; }

        /* ---------- Auth / centered pages ----------
           Structure reprise de l'ancien écran de connexion (fond plein écran +
           carte blanche centrée + champs à icône) mais habillée en Scoleo :
           un dégradé/motif générique remplace la photo et les logos d'origine. */
        .center-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 1.5rem;
            overflow: hidden;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.13), transparent 45%),
                radial-gradient(circle at 82% 15%, rgba(129,140,248,0.35), transparent 40%),
                radial-gradient(circle at 25% 85%, rgba(99,102,241,0.35), transparent 45%),
                linear-gradient(155deg, #2c2a6b 0%, #4338ca 45%, #4f46e5 100%);
        }
        .center-shell::before {
            content: "";
            position: absolute; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.14) 1px, transparent 1px);
            background-size: 26px 26px;
            opacity: 0.5;
            mask-image: linear-gradient(180deg, rgba(0,0,0,0.7), transparent 75%);
        }
        .center-card {
            position: relative;
            background: var(--surface);
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(20,15,70,0.35);
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 400px;
        }
        .center-card h1 { text-align: center; margin-bottom: 0.35rem; }

        .input-icon { position: relative; }
        .input-icon input { padding-left: 2.5rem; }
        .input-icon .ico {
            position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%);
            color: var(--muted); font-size: 0.95rem; pointer-events: none;
            line-height: 1;
        }
        .center-card label:has(+ .input-icon) { margin-bottom: 0.35rem; }

        .auth-icon {
            width: 56px; height: 56px; margin: 0 auto 1.1rem; border-radius: 16px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; box-shadow: 0 6px 16px rgba(79,70,229,0.35);
        }
        .auth-subtitle { text-align: center; color: var(--muted); font-size: 0.86rem; margin: 0 0 1.75rem; }
        .center-card form { max-width: none; }
        .center-card .btn { width: 100%; justify-content: center; margin-top: 0.4rem; padding: 0.65rem 1rem; font-size: 0.9rem; }
        .auth-footer { text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border); font-size: 0.84rem; color: var(--muted); }
        .auth-footer a { font-weight: 600; }
        .auth-footer + .auth-footer { margin-top: 0.6rem; padding-top: 0; border-top: none; }
        .auth-back { display: block; text-align: center; margin-top: 1.25rem; font-size: 0.82rem; color: var(--muted); }
        .auth-back:hover { color: var(--ink); }

        /* ---------- Tableau de bord : grille de raccourcis ----------
           Reprend la structure de l'ancien "home_admin.php" (cartes
           "miracle-box" : icône à gauche, catégorie en majuscules, sous-titre
           muted, bordures haut/bas plutôt qu'un cadre complet) en palette Scoleo. */
        .dash-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 1rem; margin-top: 1.5rem;
        }
        .dash-box {
            display: flex; align-items: center; gap: 1rem;
            background: var(--surface);
            border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.1rem 1.25rem;
            text-decoration: none;
            transition: box-shadow 0.15s ease, transform 0.1s ease, border-color 0.15s ease;
        }
        .dash-box:hover { box-shadow: var(--shadow-md); border-color: var(--brand); text-decoration: none; transform: translateY(-1px); }
        .dash-box .dash-icon {
            width: 44px; height: 44px; border-radius: 10px; flex-shrink: 0;
            background: var(--brand-light); display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .dash-box .dash-eyebrow { color: var(--brand); font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .dash-box .dash-label { display: block; color: var(--ink); font-size: 0.95rem; font-weight: 600; margin-top: 0.1rem; }
        .dash-box .dash-desc { color: var(--muted); font-size: 0.78rem; margin-top: 0.15rem; }

        .dash-portals { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.75rem; }
        .dash-portal {
            display: flex; flex-direction: column; align-items: center; gap: 0.6rem; text-align: center;
            background: linear-gradient(160deg, var(--brand-light), var(--surface));
            border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem 1rem;
            text-decoration: none; transition: box-shadow 0.15s ease, transform 0.1s ease;
        }
        .dash-portal:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); text-decoration: none; }
        .dash-portal .dash-portal-icon { font-size: 1.9rem; }
        .dash-portal .dash-portal-label { color: var(--ink); font-weight: 600; font-size: 0.88rem; }

        /* ---------- Generic content elements (used across all module views) ---------- */
        .status {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: #15803d;
            padding: 0.65rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: status-in 0.2s ease;
        }
        .status::before { content: "✓"; font-weight: 700; }
        .status.error, .status[style*="ffecec"] {
            background: var(--danger-bg) !important;
            border-color: #fecaca !important;
            color: var(--danger) !important;
        }
        .status.error::before, .status[style*="ffecec"]::before { content: "!"; }

        @keyframes status-in { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 0.5rem;
            background: var(--surface);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }
        th, td {
            border-bottom: 1px solid var(--border);
            padding: 0.65rem 0.9rem;
            text-align: left;
            font-size: 0.86rem;
        }
        th {
            background: #fafafa;
            font-weight: 600;
            color: var(--muted);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.1s ease; }
        tbody tr:hover { background: #fafbff; }

        .btn, button[type="submit"], input[type="submit"] {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            background: var(--brand);
            color: #fff !important;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-size: 0.86rem;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            box-shadow: 0 1px 2px rgba(79,70,229,0.25);
            transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.05s ease;
        }
        .btn:hover, button[type="submit"]:hover, input[type="submit"]:hover {
            background: var(--brand-dark); text-decoration: none; box-shadow: 0 2px 8px rgba(79,70,229,0.35);
        }
        .btn:active, button[type="submit"]:active, input[type="submit"]:active { transform: translateY(1px); }
        .btn:disabled, button[type="submit"]:disabled { opacity: 0.55; cursor: not-allowed; box-shadow: none; }

        button:not([type="submit"]) {
            background: #fff;
            border: 1px solid var(--border);
            color: var(--ink);
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.82rem;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s ease;
        }
        button:not([type="submit"]):hover { background: #f9fafb; border-color: #d1d5db; }

        /* Boutons de suppression : accent rouge au survol, repérable via classe ou libellé */
        button[onclick*="confirm"], .btn-danger { color: var(--danger); }
        form[onsubmit*="confirm"] button:not([type="submit"]):hover,
        button:not([type="submit"]):hover[onclick*="confirm"] { background: var(--danger-bg); border-color: #fecaca; color: var(--danger-dark); }

        /* Small inline delete/close buttons inside tables get a subtle red-on-hover */
        td button, td .btn { padding: 0.3rem 0.65rem; font-size: 0.78rem; box-shadow: none; }
        td form { display: inline-block; }

        .filters {
            margin: 0 0 1.1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            align-items: center;
            background: var(--surface);
            padding: 0.85rem 1rem;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .filters input, .filters select {
            width: auto;
            max-width: 220px;
            margin: 0;
        }
        .filters label { display: inline-flex; align-items: center; gap: 0.35rem; margin: 0; font-weight: 500; font-size: 0.84rem; }

        /* Barre de recherche "pilule" (reprise de .pretty-search-bar de l'ancien
           thème) : appliquée automatiquement au champ nommé "recherche", présent
           sur la quasi-totalité des listes, sans toucher à chaque vue. */
        .filters input[name="recherche"], .filters input[type="search"] {
            border-radius: 999px;
            padding-left: 2.3rem;
            min-width: 220px;
            background-repeat: no-repeat;
            background-position: 0.85rem center;
            background-size: 15px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239aa1b4' stroke-width='2' stroke-linecap='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
        }
        .filters button, .filters .btn { margin-left: auto; }

        label {
            display: block;
            margin-top: 0.9rem;
            margin-bottom: 0.3rem;
            font-weight: 600;
            font-size: 0.82rem;
            color: var(--ink);
        }
        label:has(input[type="checkbox"]) {
            display: flex; align-items: center; gap: 0.4rem; font-weight: 500; margin-top: 0.7rem;
        }
        label:has(input[type="checkbox"]) input { width: auto; }

        input, select, textarea {
            padding: 0.5rem 0.65rem;
            width: 100%;
            max-width: 420px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.87rem;
            font-family: inherit;
            background: var(--surface);
            color: var(--ink);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        input:hover, select:hover, textarea:hover { border-color: #c7cbd6; }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.14);
        }
        input:disabled, select:disabled, textarea:disabled { background: #f3f4f6; color: var(--muted); cursor: not-allowed; }
        input[type="checkbox"], input[type="radio"] { accent-color: var(--brand); }
        input[type="file"] {
            padding: 0.4rem; background: #fafbfc; cursor: pointer;
        }
        select[multiple] { max-width: 420px; }

        form { max-width: 620px; }
        form.filters, .content > form + table { max-width: none; }

        img { border-radius: 8px; }

        code {
            background: #f1f2f8; padding: 0.1rem 0.4rem; border-radius: 4px; font-size: 0.82rem;
        }

        ul { padding-left: 1.1rem; }

        /* ---------- Pagination (voir resources/views/pagination/custom.blade.php) ---------- */
        .pagination {
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            margin-top: 1.1rem; flex-wrap: wrap;
        }
        .pagination-info { font-size: 0.8rem; color: var(--muted); }
        .pagination-links { display: flex; gap: 0.3rem; }
        .pagination-item {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 30px; height: 30px; padding: 0 0.4rem;
            border-radius: 7px; border: 1px solid var(--border); background: var(--surface);
            color: var(--ink); font-size: 0.82rem; text-decoration: none;
            transition: all 0.12s ease;
        }
        .pagination-item:hover { border-color: var(--brand); color: var(--brand); text-decoration: none; }
        .pagination-item.active { background: var(--brand); border-color: var(--brand); color: #fff; font-weight: 600; }
        .pagination-item.disabled { color: #c7cbd6; cursor: default; background: transparent; border-color: transparent; }

        /* ---------- Responsive: collapse the admin sidebar into a hamburger menu ---------- */
        @media (max-width: 880px) {
            .menu-toggle {
                display: inline-flex; align-items: center; justify-content: center;
                width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border);
                background: var(--surface); cursor: pointer; font-size: 1rem;
            }
            .app-shell { position: relative; }
            .sidebar {
                position: fixed; inset: 0 auto 0 0; z-index: 30;
                transform: translateX(-100%); transition: transform 0.2s ease;
                box-shadow: var(--shadow-lg);
            }
            #sidebar-toggle:checked ~ .app-shell .sidebar { transform: translateX(0); }
            #sidebar-toggle:checked ~ .app-shell::after {
                content: ""; position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 20;
            }
            .content { padding: 1.1rem; }
        }

        /* Portage de Imprimer.php : plutôt que des vues d'impression dédiées par
           module (contact/candidat/intervenant/réunion/épreuve), une même règle
           d'impression s'applique à toutes les pages via ce layout partagé. */
        @media print {
            .sidebar, .topbar, form button, .btn, nav, a[href*="logout"] { display: none !important; }
            .app-shell { display: block !important; }
            .content { max-width: none !important; padding: 0 !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body>
@php
    $adminUser = auth('admin')->user();
    $intervenantUser = auth('intervenant')->user();
    $entrepriseUser = auth('entreprise')->user();
    $eleveUser = auth('eleve')->user();
    $isPortalGuest = ! $adminUser && ! $intervenantUser && ! $entrepriseUser && ! $eleveUser;
@endphp

@if ($isPortalGuest)
    {{-- Login screens / public widgets: centered card, no navigation --}}
    <div class="center-shell">
        <div class="center-card">
            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
@elseif ($adminUser)
    @php
        $adminInitials = mb_strtoupper(mb_substr($adminUser->prenom, 0, 1).mb_substr($adminUser->nom, 0, 1));
    @endphp
    <input type="checkbox" id="sidebar-toggle" style="display:none">
    <div class="app-shell">
        <aside class="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="brand">🎓 {{ config('app.name') }}</a>

            <div class="nav-group">
                <div class="nav-group-title">Élèves & Candidats</div>
                <a href="{{ route('eleves.index') }}" class="nav-link @if(request()->routeIs('eleves.*'))active @endif"><span class="ico">🧑‍🎓</span>Élèves</a>
                <a href="{{ route('candidats.index') }}" class="nav-link @if(request()->routeIs('candidats.*'))active @endif"><span class="ico">📝</span>Candidats</a>
                <a href="{{ route('epreuves.index') }}" class="nav-link @if(request()->routeIs('epreuves.*'))active @endif"><span class="ico">🧾</span>Épreuves d'admission</a>
                <a href="{{ route('eleves.index') }}#paiements" class="nav-link"><span class="ico">💳</span>Paiements (via fiche élève)</a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">CRM</div>
                <a href="{{ route('contacts.index') }}" class="nav-link @if(request()->routeIs('contacts.*'))active @endif"><span class="ico">📇</span>Contacts</a>
                <a href="{{ route('entreprises.index') }}" class="nav-link @if(request()->routeIs('entreprises.*'))active @endif"><span class="ico">🏢</span>Entreprises</a>
                <a href="{{ route('taches.index') }}" class="nav-link @if(request()->routeIs('taches.*'))active @endif"><span class="ico">✅</span>Tâches / Relances</a>
                <a href="{{ route('recherche.search') }}" class="nav-link @if(request()->routeIs('recherche.*'))active @endif"><span class="ico">🔍</span>Recherche</a>
                <a href="{{ route('archives.reunions') }}" class="nav-link @if(request()->routeIs('archives.*'))active @endif"><span class="ico">🗄️</span>Archives réunions</a>
                <a href="{{ route('import.index') }}" class="nav-link @if(request()->routeIs('import.*'))active @endif"><span class="ico">📥</span>Import contacts</a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Pédagogie</div>
                <a href="{{ route('referentiel.niveaux.index') }}" class="nav-link @if(request()->routeIs('referentiel.niveaux.*'))active @endif"><span class="ico">📶</span>Niveaux</a>
                <a href="{{ route('referentiel.unites.index') }}" class="nav-link @if(request()->routeIs('referentiel.unites.*'))active @endif"><span class="ico">📚</span>Unités d'enseignement</a>
                <a href="{{ route('referentiel.cours.index') }}" class="nav-link @if(request()->routeIs('referentiel.cours.*'))active @endif"><span class="ico">📖</span>Cours</a>
                <a href="{{ route('referentiel.matieres.index') }}" class="nav-link @if(request()->routeIs('referentiel.matieres.*'))active @endif"><span class="ico">🧮</span>Matières</a>
                <a href="{{ route('referentiel.classes.index') }}" class="nav-link @if(request()->routeIs('referentiel.classes.*'))active @endif"><span class="ico">🏷️</span>Classes</a>
                <a href="{{ route('referentiel.groupes.index') }}" class="nav-link @if(request()->routeIs('referentiel.groupes.*'))active @endif"><span class="ico">👥</span>Groupes</a>
                <a href="{{ route('referentiel.periodes-formation.index') }}" class="nav-link @if(request()->routeIs('referentiel.periodes-formation.*'))active @endif"><span class="ico">🗓️</span>Périodes de formation</a>
                <a href="{{ route('referentiel.ref.index') }}" class="nav-link @if(request()->routeIs('referentiel.ref.*'))active @endif"><span class="ico">⏱️</span>Référentiel des heures</a>
                <a href="{{ route('referentiel.parametrage.index') }}" class="nav-link @if(request()->routeIs('referentiel.parametrage.*'))active @endif"><span class="ico">📊</span>Volumes de formation</a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Notation & Bulletins</div>
                <a href="{{ route('evaluations.index') }}" class="nav-link @if(request()->routeIs('evaluations.*') || request()->routeIs('notes.*'))active @endif"><span class="ico">✏️</span>Évaluations & notes</a>
                <a href="{{ route('types-evaluation.index') }}" class="nav-link @if(request()->routeIs('types-evaluation.*'))active @endif"><span class="ico">🏷️</span>Types d'évaluation</a>
                <a href="{{ route('bulletins.index') }}" class="nav-link @if(request()->routeIs('bulletins.index'))active @endif"><span class="ico">📄</span>Bulletins (décisions)</a>
                <a href="{{ route('bulletin-v2.index') }}" class="nav-link @if(request()->routeIs('bulletin-v2.*'))active @endif"><span class="ico">🖨️</span>Bulletins PDF</a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Établissements & RH</div>
                <a href="{{ route('referentiel.etablissements.index') }}" class="nav-link @if(request()->routeIs('referentiel.etablissements.*'))active @endif"><span class="ico">🏫</span>Établissements</a>
                <a href="{{ route('referentiel.intervenants.index') }}" class="nav-link @if(request()->routeIs('referentiel.intervenants.*'))active @endif"><span class="ico">👩‍🏫</span>Intervenants</a>
                <a href="{{ route('referentiel.signatures.index') }}" class="nav-link @if(request()->routeIs('referentiel.signatures.*'))active @endif"><span class="ico">✒️</span>Signatures</a>
                <a href="{{ route('salles.index') }}" class="nav-link @if(request()->routeIs('salles.*'))active @endif"><span class="ico">🚪</span>Salles</a>
                <a href="{{ route('planning.index') }}" class="nav-link @if(request()->routeIs('planning.*'))active @endif"><span class="ico">📅</span>Planning</a>
                <a href="{{ route('panneaux.index') }}" class="nav-link @if(request()->routeIs('panneaux.*'))active @endif"><span class="ico">💡</span>Panneaux lumineux</a>
            </div>

            <div class="nav-group">
                <div class="nav-group-title">Administration</div>
                <a href="{{ route('admins.index') }}" class="nav-link @if(request()->routeIs('admins.*'))active @endif"><span class="ico">🔑</span>Administrateurs</a>
                <a href="{{ route('roles.index') }}" class="nav-link @if(request()->routeIs('roles.*'))active @endif"><span class="ico">🛡️</span>Rôles</a>
                <a href="{{ route('permissions.index') }}" class="nav-link @if(request()->routeIs('permissions.*'))active @endif"><span class="ico">🔒</span>Permissions</a>
                <a href="{{ route('emails.index') }}" class="nav-link @if(request()->routeIs('emails.*'))active @endif"><span class="ico">✉️</span>Modèles d'emails</a>
                <a href="{{ route('configuration.site') }}" class="nav-link @if(request()->routeIs('configuration.*'))active @endif"><span class="ico">⚙️</span>Config. du site</a>
                <a href="{{ route('notifications.index') }}" class="nav-link @if(request()->routeIs('notifications.*'))active @endif"><span class="ico">🔔</span>Notifications</a>
            </div>
        </aside>

        <div class="main-col">
            <header class="topbar">
                <label for="sidebar-toggle" class="menu-toggle" aria-label="Ouvrir le menu">☰</label>
                <span class="who">
                    <span class="avatar">{{ $adminInitials }}</span>
                    Connecté en tant que <strong>{{ $adminUser->prenom }} {{ $adminUser->nom }}</strong>
                </span>
                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="button" class="logout-btn" onclick="this.form.submit()">Se déconnecter</button>
                </form>
            </header>
            <main class="content">
                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
@else
    {{-- Intervenant / Entreprise / Élève portals: lightweight topbar, no admin sidebar --}}
    @php
        $portalLabel = $intervenantUser ? 'Espace intervenant' : ($entrepriseUser ? 'Espace entreprise' : 'Espace élève');
        $portalName = $intervenantUser?->intervenant
            ? trim($intervenantUser->intervenant->prenom.' '.$intervenantUser->intervenant->nom)
            : ($entrepriseUser?->entreprise?->nom_entreprise
                ?? ($eleveUser?->eleve?->contact
                    ? trim($eleveUser->eleve->contact->prenom.' '.$eleveUser->eleve->contact->nom)
                    : null));
        $logoutRoute = $intervenantUser ? 'intervenant.logout' : ($entrepriseUser ? 'entreprise.logout' : 'eleve.logout');
        $homeRoute = $intervenantUser ? 'intervenant.dashboard' : ($entrepriseUser ? 'entreprise.dashboard' : 'eleve.dashboard');
        $portalInitials = $portalName
            ? mb_strtoupper(collect(explode(' ', trim($portalName)))->map(fn ($mot) => mb_substr($mot, 0, 1))->take(2)->implode(''))
            : '?';
    @endphp
    <div class="main-col">
        <header class="topbar">
            <a href="{{ route($homeRoute) }}" style="font-weight:700;color:var(--ink)">🎓 {{ $portalLabel }}</a>
            <span class="who">
                <span class="avatar">{{ $portalInitials }}</span>
                {{ $portalName ?? '' }}
            </span>
            <form method="post" action="{{ route($logoutRoute) }}">
                @csrf
                <button type="button" class="logout-btn" onclick="this.form.submit()">Se déconnecter</button>
            </form>
        </header>
        <main class="content">
            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
@endif
</body>
</html>
