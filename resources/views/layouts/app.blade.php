<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%234f46e5%22 stroke-width=%222%22><path d=%22M22 10L12 5 2 10l10 5 10-5z%22/><path d=%22M6 12.5V17c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.5%22/></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #4f46e5;
            --brand-dark: #4338ca;
            --brand-deep: #3730a3;
            --brand-light: #eef0fe;
            --ink: #171a23;
            --muted: #6b7280;
            --faint: #9aa0b0;
            --border: #e8eaf1;
            --border-soft: #f0f1f6;
            --bg: #f5f6fa;
            --surface: #ffffff;
            --danger: #dc2626;
            --danger-dark: #b91c1c;
            --danger-bg: #fef2f2;
            --success-bg: #ecfdf3;
            --success-border: #a7f3d0;
            --radius: 10px;
            --radius-lg: 12px;
            --shadow-sm: 0 1px 2px rgba(23,26,35,0.05);
            --shadow-md: 0 6px 18px rgba(23,26,35,0.07);
            --shadow-lg: 0 14px 36px rgba(23,26,35,0.12);
        }

        * { box-sizing: border-box; }
        ::selection { background: var(--brand); color: #fff; }

        ::-webkit-scrollbar { width: 9px; height: 9px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d7dae4; border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: #bfc4d2; }

        body {
            font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
            margin: 0;
            color: var(--ink);
            background: var(--bg);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        a { color: var(--brand); text-decoration: none; transition: color .15s ease; }
        a:hover { color: var(--brand-dark); }

        h1 { font-size: 23px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 1.1rem; }
        h2 { font-size: 16px; font-weight: 600; margin: 1.7rem 0 .7rem; }
        h3 { font-size: 14px; font-weight: 600; margin: 1.2rem 0 .55rem; }

        /* ---------- App shell ---------- */
        .app-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: 264px; flex-shrink: 0; background: var(--surface);
            border-right: 1px solid var(--border);
            position: sticky; top: 0; height: 100vh; overflow-y: auto;
            display: flex; flex-direction: column; scrollbar-width: thin;
        }
        .sidebar .brand {
            display: flex; align-items: center; gap: 10px; padding: 18px 18px 16px;
            position: sticky; top: 0; background: var(--surface); z-index: 2;
            border-bottom: 1px solid var(--border-soft); color: var(--ink);
        }
        .brand-mark {
            width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
            background: linear-gradient(140deg, #6366f1, var(--brand-dark));
            display: flex; align-items: center; justify-content: center;
        }
        .brand-name { display: block; font-size: 14.5px; font-weight: 700; letter-spacing: -0.01em; line-height: 1.1; }
        .brand-sub { display: block; font-size: 10.5px; color: var(--muted); font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }

        .nav-search { padding: 12px 14px 8px; position: relative; }
        .nav-search input {
            width: 100%; border: 1px solid var(--border); background: #fafbfd;
            border-radius: 8px; padding: 7px 10px 7px 30px; font: inherit; font-size: 12.5px;
            color: var(--ink); max-width: none;
        }
        .nav-search input:focus { border-color: #c3c6f5; background: #fff; box-shadow: 0 0 0 3px rgba(79,70,229,.10); outline: none; }

        #admin-nav { padding: 2px 10px 24px; display: flex; flex-direction: column; gap: 2px; }

        .nav-group { margin-top: 12px; }
        .nav-group > summary {
            display: flex; align-items: center; justify-content: space-between; gap: 6px;
            padding: 6px 10px 4px; cursor: pointer; list-style: none;
            color: var(--muted); font-size: 10.5px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
        }
        .nav-group > summary::-webkit-details-marker { display: none; }
        .nav-group > summary:hover { color: var(--brand); }
        .nav-group:not([open]) > summary svg { transform: rotate(-90deg); }
        .nav-items { display: flex; flex-direction: column; gap: 1px; }

        .nav-link {
            display: flex; align-items: center; gap: 10px; padding: 7px 10px;
            border-radius: 8px; color: #585e72; font-size: 12.8px; font-weight: 500;
            transition: background .12s ease, color .12s ease;
        }
        .nav-link:hover { background: #f4f5fa; color: var(--ink); }
        .nav-link.active { background: var(--brand-light); color: var(--brand-deep); font-weight: 600; }
        .nav-link.active svg { stroke: var(--brand); }

        .main-col { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        .topbar {
            background: var(--surface); border-bottom: 1px solid var(--border);
            padding: 0 24px; min-height: 58px; display: flex; align-items: center; gap: 16px;
            position: sticky; top: 0; z-index: 10;
        }
        .topbar-search { flex: 1; min-width: 0; max-width: 420px; position: relative; }
        .topbar-search input {
            width: 100%; max-width: none; border: 1px solid var(--border); background: #fafbfd;
            border-radius: 999px; padding: 8px 12px 8px 33px; font: inherit; font-size: 13px;
        }
        .topbar-search input:focus { border-color: #c3c6f5; background: #fff; box-shadow: 0 0 0 3px rgba(79,70,229,.10); outline: none; }
        .topbar-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }
        .icon-btn {
            position: relative; width: 34px; height: 34px; border-radius: 9px;
            border: 1px solid var(--border); background: #fff; display: flex;
            align-items: center; justify-content: center; cursor: pointer; padding: 0;
        }
        .icon-btn:hover { background: #f7f8fc; }
        .icon-btn .dot {
            position: absolute; top: -5px; right: -5px; min-width: 17px; height: 17px; padding: 0 4px;
            border-radius: 999px; background: var(--brand); color: #fff; font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; border: 2px solid #fff;
        }
        .who { display: flex; align-items: center; gap: 9px; }
        .who .avatar {
            width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(140deg, #6366f1, var(--brand-dark));
            color: #fff; font-weight: 700; font-size: 11px;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .who-text { display: flex; flex-direction: column; line-height: 1.15; }
        .who-name { font-size: 12.5px; font-weight: 600; color: var(--ink); }
        .who-org { font-size: 10.5px; color: var(--muted); }
        .topbar form { display: inline; }
        .logout-btn {
            background: #fff; border: 1px solid var(--border); color: var(--muted);
            padding: .4rem .75rem; border-radius: 9px; font-size: 12.5px; cursor: pointer; font-family: inherit;
            transition: all .15s ease;
        }
        .logout-btn:hover { background: var(--danger-bg); color: var(--danger); border-color: #fecaca; }

        .menu-toggle { display: none; }

        /* Largeur de contenu : les listes (8 à 10 colonnes) respirent sur
           1720px, tandis que les écrans de lecture restent bornés plus bas
           par `form` et `.page-sub` pour ne pas devenir illisibles. */
        .content { padding: 26px 32px 48px; max-width: 1720px; width: 100%; margin: 0 auto; }
        .content > *:first-child { margin-top: 0; }

        /* ---------- En-tête de page / fil d'Ariane ---------- */
        .eyebrow { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .page-head {
            display: flex; align-items: flex-end; justify-content: space-between;
            gap: 16px; flex-wrap: wrap; margin-bottom: 24px;
        }
        .page-head h1 { margin: 0; }
        .page-sub { margin: 6px 0 0; font-size: 13.5px; color: var(--muted); max-width: 60ch; text-wrap: pretty; }
        .page-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .page-header-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
        .page-header-row h1 { margin: 0; }

        .crumb { display: flex; align-items: center; gap: .4rem; font-size: 12px; color: var(--muted); margin-bottom: .6rem; }
        .crumb a { color: var(--muted); }
        .crumb a:hover { color: var(--brand); }
        .crumb .sep { color: #cdd1dc; }
        .crumb .current { color: var(--ink); font-weight: 500; }

        .badge {
            display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .6rem;
            border-radius: 999px; font-size: 11.5px; font-weight: 600; background: #eef0f6; color: var(--muted);
        }
        .badge-brand { background: var(--brand-light); color: var(--brand-deep); }
        .badge-success { background: var(--success-bg); color: #15803d; }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        .badge-warning { background: #fffbeb; color: #b45309; }

        /* ---------- Tableau de bord : sections + cartes ---------- */
        .dash-section { margin-bottom: 26px; }
        .section-head { display: flex; align-items: center; gap: 10px; margin-bottom: 11px; }
        .section-head h2 { margin: 0; font-size: 13px; font-weight: 700; }
        .section-head .rule { flex: 1; height: 1px; background: var(--border); }
        .section-count { font-size: 11.5px; color: var(--muted); font-weight: 500; }

        .dash-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; }
        .dash-box {
            display: flex; align-items: center; gap: 13px; background: var(--surface);
            border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 14px 15px;
            color: inherit; transition: border-color .15s ease, box-shadow .15s ease;
        }
        .dash-box:hover { border-color: #c3c6f5; box-shadow: var(--shadow-md); color: inherit; }
        .dash-icon { width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .tint-indigo { background: #eef0fe; } .tint-indigo svg { stroke: #4f46e5; }
        .tint-teal   { background: #e7f6f2; } .tint-teal svg   { stroke: #0f766e; }
        .tint-amber  { background: #fdf3e3; } .tint-amber svg  { stroke: #b45309; }
        .tint-rose   { background: #fdecef; } .tint-rose svg   { stroke: #be123c; }
        .tint-violet { background: #f4ecfd; } .tint-violet svg { stroke: #7c3aed; }
        .tint-slate  { background: #eef1f6; } .tint-slate svg  { stroke: #475569; }
        .dash-text { min-width: 0; }
        .dash-label { display: block; font-size: 13.5px; font-weight: 600; letter-spacing: -0.005em; }
        .dash-desc { display: block; font-size: 11.8px; color: var(--muted); margin-top: 2px; }

        .dash-portals { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 12px; }
        .dash-portal {
            display: flex; align-items: center; gap: 13px; padding: 16px; color: inherit;
            background: linear-gradient(150deg, #f2f3ff, #fff 70%);
            border: 1px solid #e4e6f6; border-radius: var(--radius-lg);
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .dash-portal:hover { border-color: #c3c6f5; box-shadow: var(--shadow-md); color: inherit; }
        .portal-icon {
            width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; background: #fff;
            border: 1px solid #e6e8f4; display: flex; align-items: center; justify-content: center;
        }

        /* ---------- Connexion : écran en deux volets ---------- */
        .auth-split { display: flex; min-height: 100vh; background: #fff; }
        .auth-panel {
            flex: 1 1 46%; min-width: 0; position: relative; overflow: hidden;
            padding: 44px 48px; display: flex; flex-direction: column; justify-content: space-between;
            background: linear-gradient(155deg, #2c2a6b 0%, var(--brand-dark) 48%, var(--brand) 100%);
        }
        .auth-panel-dots {
            position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.16) 1px, transparent 1px);
            background-size: 26px 26px; opacity: .55;
            -webkit-mask-image: linear-gradient(180deg, rgba(0,0,0,.8), transparent 78%);
            mask-image: linear-gradient(180deg, rgba(0,0,0,.8), transparent 78%);
        }
        .auth-panel-glow {
            position: absolute; width: 420px; height: 420px; right: -140px; top: -120px; border-radius: 50%;
            background: radial-gradient(circle, rgba(129,140,248,.45), transparent 65%);
        }
        .auth-panel > *:not(.auth-panel-dots):not(.auth-panel-glow) { position: relative; }
        .auth-panel-brand { display: flex; align-items: center; gap: 11px; }
        .auth-panel-mark {
            width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
            background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.22);
            display: flex; align-items: center; justify-content: center;
        }
        .auth-panel-name { display: block; color: #fff; font-size: 15px; font-weight: 700; letter-spacing: -0.01em; line-height: 1.1; }
        .auth-panel-sub { display: block; color: rgba(255,255,255,.72); font-size: 10.5px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
        .auth-panel-body { max-width: 34ch; }
        .auth-panel-body h2 { margin: 0; color: #fff; font-size: 31px; font-weight: 700; letter-spacing: -0.025em; line-height: 1.18; text-wrap: pretty; }
        .auth-panel-body p { margin: 16px 0 0; color: rgba(255,255,255,.78); font-size: 14px; line-height: 1.55; max-width: 40ch; text-wrap: pretty; }
        .auth-panel-body ul { list-style: none; padding: 0; margin: 28px 0 0; display: flex; flex-direction: column; gap: 10px; }
        .auth-panel-body li { display: flex; align-items: center; gap: 9px; color: rgba(255,255,255,.9); font-size: 13px; }
        .auth-panel-foot { color: rgba(255,255,255,.62); font-size: 11.5px; }

        .auth-form-col { flex: 1 1 54%; min-width: 0; display: flex; align-items: center; justify-content: center; padding: 40px 28px; }
        .auth-form-wrap { width: 100%; max-width: 392px; }
        .auth-chip {
            display: inline-flex; align-items: center; gap: 7px; padding: 5px 11px; border-radius: 999px;
            background: var(--brand-light); color: var(--brand-deep);
            font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; margin-bottom: 20px;
        }
        .auth-form-wrap h1 { margin: 0; font-size: 25px; }
        .auth-subtitle { margin: 7px 0 26px; font-size: 13.5px; color: var(--muted); }
        .auth-form { max-width: none; display: flex; flex-direction: column; gap: 15px; }
        .auth-form .field-head { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; }
        .auth-form .field-head a { font-size: 12px; font-weight: 600; }
        .auth-form label { margin: 0 0 6px; font-size: 12.5px; }
        .auth-form .field-head label { margin: 0; }
        .auth-form .check { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #585e72; font-weight: 500; cursor: pointer; margin: 0; }
        .auth-form .check input { width: 15px; height: 15px; margin: 0; }
        .input-icon { position: relative; }
        .input-icon input { padding-left: 37px; max-width: none; border-radius: var(--radius); background: #fafbfd; }
        .input-icon .reveal {
            position: absolute; right: 6px; top: 6px; width: 30px; height: 30px; border-radius: 8px;
            border: none; background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0;
        }
        .input-icon .reveal:hover { background: #f1f2f8; }
        .input-icon .reveal.is-on svg { stroke: var(--brand); }
        .input-icon input[type="password"], .input-icon input[type="text"]#password { padding-right: 42px; }
        .btn-block { width: 100%; justify-content: center; padding: 11px 16px; font-size: 14px; }
        .auth-others { margin-top: 26px; padding-top: 20px; border-top: 1px solid #eceef4; }
        .auth-others-title { font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); margin-bottom: 11px; }
        .auth-others-links { display: flex; flex-wrap: wrap; gap: 8px; }
        .auth-others-links a {
            display: inline-flex; align-items: center; gap: 7px; padding: 7px 12px; border-radius: 999px;
            border: 1px solid var(--border); background: #fff; color: #585e72; font-size: 12.5px; font-weight: 600;
        }
        .auth-others-links a:hover { border-color: #c3c6f5; color: var(--brand-deep); background: #fafbff; }
        .auth-back { display: inline-flex; align-items: center; gap: 6px; margin-top: 22px; font-size: 12.5px; color: var(--muted); font-weight: 500; }
        .auth-back:hover { color: var(--ink); }

        /* ---------- Éléments de contenu génériques ---------- */
        .status {
            background: var(--success-bg); border: 1px solid var(--success-border); color: #15803d;
            padding: .7rem 1rem; border-radius: var(--radius); margin-bottom: 1.25rem; font-size: 13px;
            display: flex; align-items: flex-start; gap: .55rem; animation: status-in .2s ease;
        }
        .status.error, .status[style*="ffecec"] {
            background: var(--danger-bg) !important; border-color: #fecaca !important; color: var(--danger-dark) !important;
        }
        @keyframes status-in { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }

        table {
            border-collapse: separate; border-spacing: 0; width: 100%; margin-top: .5rem;
            background: var(--surface); border-radius: var(--radius-lg); overflow: hidden;
            box-shadow: var(--shadow-sm); border: 1px solid var(--border);
        }
        th, td { border-bottom: 1px solid var(--border-soft); padding: .72rem 1.1rem; text-align: left; font-size: 13px; }
        th:first-child, td:first-child { padding-left: 1.3rem; }
        th:last-child, td:last-child { padding-right: 1.3rem; }
        th { background: #fafbfd; font-weight: 600; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .05em; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background .1s ease; }
        tbody tr:hover { background: #fafbff; }

        .btn, button[type="submit"], input[type="submit"] {
            display: inline-flex; align-items: center; gap: .45rem; padding: 9px 14px;
            background: var(--brand); color: #fff !important; border: none; border-radius: 9px;
            font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;
            box-shadow: 0 2px 8px rgba(79,70,229,.25); transition: background .15s ease, box-shadow .15s ease, transform .05s ease;
        }
        .btn:hover, button[type="submit"]:hover, input[type="submit"]:hover { background: var(--brand-dark); box-shadow: 0 3px 12px rgba(79,70,229,.3); }
        .btn:active, button[type="submit"]:active { transform: translateY(1px); }
        .btn:disabled, button[type="submit"]:disabled { opacity: .55; cursor: not-allowed; box-shadow: none; }
        .btn-ghost { background: #fff; color: #585e72 !important; border: 1px solid var(--border); box-shadow: none; }
        .btn-ghost:hover { background: #f7f8fc; color: var(--ink) !important; box-shadow: none; }
        .btn-danger { background: #fff; color: var(--danger) !important; border: 1px solid #fecaca; box-shadow: none; }
        .btn-danger:hover { background: var(--danger-bg); color: var(--danger-dark) !important; box-shadow: none; }

        button:not([type="submit"]):not(.btn):not(.icon-btn):not(.reveal):not(.logout-btn) {
            background: #fff; border: 1px solid var(--border); color: var(--ink);
            padding: .42rem .8rem; border-radius: 9px; font-size: 12.5px; cursor: pointer; font-family: inherit;
            transition: all .15s ease;
        }
        button:not([type="submit"]):not(.btn):hover { background: #f9fafb; border-color: #d5d8e2; }
        button[onclick*="confirm"] { color: var(--danger); }
        button[onclick*="confirm"]:hover { background: var(--danger-bg); border-color: #fecaca; color: var(--danger-dark); }
        td button, td .btn { padding: .3rem .65rem; font-size: 12px; box-shadow: none; }
        td form { display: inline-block; }

        .filters {
            margin: 0 0 1.1rem; display: flex; flex-wrap: wrap; gap: .6rem; align-items: center;
            background: var(--surface); padding: .85rem 1rem; border-radius: var(--radius-lg);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
        }
        .filters input, .filters select { width: auto; max-width: 220px; margin: 0; }
        .filters label { display: inline-flex; align-items: center; gap: .35rem; margin: 0; font-weight: 500; font-size: 12.5px; }
        .filters input[name="recherche"], .filters input[type="search"] {
            border-radius: 999px; padding-left: 2.3rem; min-width: 220px;
            background-repeat: no-repeat; background-position: .85rem center; background-size: 15px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239aa1b4' stroke-width='2' stroke-linecap='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
        }
        .filters button, .filters .btn { margin-left: auto; }

        label { display: block; margin-top: .9rem; margin-bottom: .3rem; font-weight: 600; font-size: 12.5px; color: var(--ink); }
        label:has(input[type="checkbox"]) { display: flex; align-items: center; gap: .4rem; font-weight: 500; margin-top: .7rem; }
        label:has(input[type="checkbox"]) input { width: auto; }

        input, select, textarea {
            padding: 10px 13px; width: 100%; max-width: 420px; border: 1px solid var(--border);
            border-radius: var(--radius); font-size: 13.5px; font-family: inherit;
            background: var(--surface); color: var(--ink); transition: border-color .15s ease, box-shadow .15s ease;
        }
        input:hover, select:hover, textarea:hover { border-color: #d5d8e2; }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: #a5a8f0; background: #fff; box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        input:disabled, select:disabled, textarea:disabled { background: #f3f4f6; color: var(--muted); cursor: not-allowed; }
        input[type="checkbox"], input[type="radio"] { accent-color: var(--brand); }
        input[type="file"] { padding: .4rem; background: #fafbfd; cursor: pointer; }

        form { max-width: 620px; }
        form.filters, .content > form + table { max-width: none; }
        img { border-radius: 8px; }
        code { background: #f1f2f8; padding: .1rem .4rem; border-radius: 4px; font-size: 12.5px; }
        ul { padding-left: 1.1rem; }

        /* ---------- Écrans de liste modernisés (recherche/filtres + tableau) ----------
           Extrait de eleves/index.blade.php pour être partagé par toutes les listes
           (candidats, contacts, entreprises, etc.) plutôt que dupliqué par page. */
        .title-row { display: flex; align-items: center; gap: 10px; }
        .title-row h1 { margin: 0; }

        .filter-card { max-width: none; background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 14px; margin-bottom: 14px; }
        .filter-row { display: flex; gap: 9px; flex-wrap: wrap; align-items: center; }
        .filter-search { position: relative; flex: 1 1 300px; min-width: 0; }
        .filter-search input { width: 100%; max-width: none; padding-left: 38px; background: #fafbfd; }
        .filter-row select { flex: 0 1 170px; min-width: 130px; max-width: none; cursor: pointer; color: #585e72; }
        .filter-row .btn-ghost { flex-shrink: 0; }

        .filter-advanced { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 11px; margin-top: 13px; padding-top: 13px; border-top: 1px solid var(--border-soft); }
        .filter-advanced label { margin: 0; }
        .filter-advanced label > span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
        .filter-advanced select { width: 100%; max-width: none; cursor: pointer; }

        .filter-foot { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 13px; padding-top: 13px; border-top: 1px solid var(--border-soft); }
        .filter-foot-label { font-size: 11.5px; font-weight: 600; color: var(--muted); }
        .chip {
            display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px 5px 11px; border-radius: 999px;
            border: 1px solid #dfe2f4; background: #f7f8ff; color: var(--brand-deep); font-size: 12px; font-weight: 600;
        }
        .chip:hover { background: var(--brand-light); color: var(--brand-deep); }
        .filter-reset { margin-left: auto; padding: 7px 12px; border-radius: 9px; color: var(--muted); font-size: 12.5px; font-weight: 600; }
        .filter-reset:hover { background: #f4f5fa; color: var(--ink); }

        .table-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
        .table-head { display: flex; align-items: center; gap: 10px; padding: 12px 15px; border-bottom: 1px solid var(--border-soft); flex-wrap: wrap; min-height: 50px; }
        .table-count { font-size: 12.5px; color: var(--muted); }
        .bulk-bar { display: flex; align-items: center; gap: 10px; width: 100%; flex-wrap: wrap; }
        .bulk-count { font-size: 12.5px; color: var(--brand-deep); font-weight: 600; }
        .bulk-actions { display: flex; gap: 7px; margin-left: auto; flex-wrap: wrap; }

        .table-scroll { overflow-x: auto; }
        .data-table { min-width: 940px; width: 100%; margin: 0; border: 0; border-radius: 0; box-shadow: none; }
        .data-table th { position: sticky; top: 0; z-index: 1; white-space: nowrap; }
        .data-table th a { display: inline-flex; align-items: center; gap: 5px; color: inherit; }
        .data-table th a:hover { color: var(--brand); }
        .data-table th svg { opacity: .35; }
        .data-table th.is-sorted a { color: var(--brand); }
        .data-table th.is-sorted svg { opacity: 1; }
        .data-table td { vertical-align: middle; }
        .col-check { width: 42px; }
        .col-check input { width: 15px; height: 15px; margin: 0; cursor: pointer; }
        .col-actions { width: 72px; text-align: right; white-space: nowrap; }
        .row-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px;
            border-radius: 8px; border: 1px solid var(--border); margin-left: 4px;
        }
        .row-btn:hover { border-color: #c3c6f5; background: #fafbff; }

        .cell-user { display: flex; align-items: center; gap: 11px; }
        .cell-avatar {
            width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: var(--brand-light);
            color: var(--brand-deep); font-size: 11.5px; font-weight: 700; display: flex; align-items: center; justify-content: center;
        }
        .cell-user-text { min-width: 0; }
        .cell-name { display: block; font-size: 13.5px; font-weight: 600; color: var(--ink); }
        .cell-name:hover { color: var(--brand); }
        .cell-sub { display: block; font-size: 11.5px; color: var(--muted); margin-top: 1px; }
        .cell-wide { max-width: 420px; }
        .num { font-variant-numeric: tabular-nums; color: #585e72; white-space: nowrap; }
        .pill { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 600; }
        .dot-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; }
        .dot-status .dot { width: 7px; height: 7px; border-radius: 50%; }
        tr:has(input[data-check-row]:checked) { background: #fafbff; }

        .empty-cell { text-align: center; padding: 42px 16px; }
        .empty-cell span { display: block; margin-top: 10px; font-size: 13px; color: var(--muted); }
        .empty-cell a { display: inline-block; margin-top: 8px; font-size: 12.5px; font-weight: 600; }

        .table-foot { padding: 13px 15px; }
        .table-foot .pagination { margin: 0; }

        /* ---------- Pagination ---------- */
        .pagination { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1.1rem; flex-wrap: wrap; }
        .pagination-info { font-size: 12px; color: var(--muted); }
        .pagination-links { display: flex; gap: .3rem; }
        .pagination-item {
            display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px;
            padding: 0 .4rem; border-radius: 8px; border: 1px solid var(--border); background: var(--surface);
            color: var(--ink); font-size: 12.5px; transition: all .12s ease;
        }
        .pagination-item:hover { border-color: #c3c6f5; color: var(--brand); }
        .pagination-item.active { background: var(--brand); border-color: var(--brand); color: #fff; font-weight: 600; }
        .pagination-item.disabled { color: #c7cbd6; cursor: default; background: transparent; border-color: transparent; }

        /* ---------- Portails élève / enseignant / entreprise ----------
           Ces espaces n'ont pas le menu latéral de l'administration : leur
           navigation tient sur une barre d'onglets sous l'en-tête. */
        .portal-nav { background: var(--surface); border-bottom: 1px solid var(--border); position: sticky; top: 58px; z-index: 9; }
        .portal-nav-inner {
            display: flex; gap: 4px; max-width: 1720px; margin: 0 auto; padding: 0 32px;
            overflow-x: auto; scrollbar-width: none;
        }
        .portal-nav-inner::-webkit-scrollbar { display: none; }
        .portal-link {
            display: inline-flex; align-items: center; gap: 7px; padding: 13px 14px; white-space: nowrap;
            font-size: 13px; font-weight: 600; color: var(--muted); border-bottom: 2px solid transparent;
        }
        .portal-link svg { stroke: currentColor; }
        .portal-link:hover { color: var(--ink); }
        .portal-link.is-active { color: var(--brand); border-bottom-color: var(--brand); }

        /* Accueil d'un espace : cartes de synthese (prochain cours, chiffres,
           dernieres notes). Partage par les tableaux de bord eleve et enseignant. */
        .ho-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; align-items: start; }
        .ho-card {
            background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
            padding: 18px; display: flex; flex-direction: column; min-width: 0;
        }
        .ho-next { grid-column: span 2; }
        .ho-label { font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
        .ho-card h2 { margin: 9px 0 0; font-size: 18px; font-weight: 700; letter-spacing: -.02em; }
        .ho-rien { color: var(--faint); }
        .ho-quand { margin: 4px 0 0; font-size: 13px; color: var(--muted); }
        .ho-meta { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 12px; }
        .ho-meta span { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #585e72; }
        .ho-lien { display: inline-flex; align-items: center; gap: 6px; margin-top: auto; padding-top: 14px; font-size: 12.5px; font-weight: 600; }
        .ho-chiffre strong { display: block; margin-top: 8px; font-size: 34px; font-weight: 700; letter-spacing: -.03em; line-height: 1; color: var(--brand); }
        .ho-unite { font-size: 12.5px; color: var(--muted); margin-top: 5px; }
        .ho-note { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--border-soft); }
        .ho-notes .ho-note:first-of-type { margin-top: 8px; }
        .ho-note-m { font-size: 13px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ho-note-v { margin-left: auto; font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .ho-note-v.is-low { color: var(--danger); }
        .ho-vide { margin: 10px 0 0; font-size: 13px; color: var(--muted); }

        /* ---------- Responsive ---------- */
        @media (max-width: 1280px) {
            .content { padding: 26px 20px 48px; }
            .portal-nav-inner { padding: 0 20px; }
            th, td { padding: .7rem .8rem; }
            th:first-child, td:first-child { padding-left: 1rem; }
            th:last-child, td:last-child { padding-right: 1rem; }
        }
        @media (max-width: 980px) {
            .auth-panel { display: none; }
            .auth-form-col { flex: 1 1 100%; }
        }
        @media (max-width: 880px) {
            .menu-toggle {
                display: inline-flex; align-items: center; justify-content: center;
                width: 34px; height: 34px; border-radius: 9px; border: 1px solid var(--border);
                background: var(--surface); cursor: pointer; padding: 0;
            }
            .app-shell { position: relative; }
            .sidebar {
                position: fixed; inset: 0 auto 0 0; z-index: 30;
                transform: translateX(-100%); transition: transform .2s ease; box-shadow: var(--shadow-lg);
            }
            #sidebar-toggle:checked ~ .app-shell .sidebar { transform: translateX(0); }
            #sidebar-toggle:checked ~ .app-shell::after { content: ""; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 20; }
            .topbar-search { max-width: none; }
            .who-text { display: none; }
            .content { padding: 18px 14px 40px; }
            .portal-nav-inner { padding: 0 14px; }
            .ho-next { grid-column: span 1; }
        }

        @media print {
            .sidebar, .topbar, .page-actions, form button, .btn, nav, a[href*="logout"] { display: none !important; }
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
    {{-- Écrans de connexion : le contenu gère sa propre mise en page (voir auth/_login-card). --}}
    @yield('content')
@elseif ($adminUser)
    @php
        $adminInitials = mb_strtoupper(mb_substr($adminUser->prenom, 0, 1).mb_substr($adminUser->nom, 0, 1));
    @endphp
    <input type="checkbox" id="sidebar-toggle" style="display:none">
    <div class="app-shell">
        @include('partials.admin-nav')

        <div class="main-col">
            <header class="topbar">
                <label for="sidebar-toggle" class="menu-toggle" aria-label="Ouvrir le menu">
                    @include('partials.icon', ['n' => 'levels', 's' => 16, 'c' => '#585e72', 'w' => 2, 'style' => 'transform:rotate(90deg)'])
                </label>

                <form method="get" action="{{ route('recherche.search') }}" class="topbar-search">
                    @include('partials.icon', ['n' => 'search', 's' => 15, 'c' => '#9aa0b0', 'w' => 2, 'style' => 'position:absolute;left:11px;top:9px'])
                    <input type="search" name="recherche" value="{{ request('recherche') }}"
                           placeholder="Rechercher un élève, un contact, une entreprise…" aria-label="Recherche globale">
                </form>

                <div class="topbar-right">
                    <a href="{{ route('notifications.index') }}" class="icon-btn" aria-label="Notifications">
                        @include('partials.icon', ['n' => 'bell', 's' => 16, 'c' => '#585e72'])
                        @isset($notificationsCount)
                            @if ($notificationsCount > 0)<span class="dot">{{ $notificationsCount }}</span>@endif
                        @endisset
                    </a>
                    <span class="who">
                        <span class="avatar">{{ $adminInitials }}</span>
                        <span class="who-text">
                            <span class="who-name">{{ $adminUser->prenom }} {{ $adminUser->nom }}</span>
                            <span class="who-org">{{ config('app.name') }}</span>
                        </span>
                    </span>
                    <form method="post" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="button" class="logout-btn" onclick="this.form.submit()">Se déconnecter</button>
                    </form>
                </div>
            </header>

            <main class="content">
                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Filtre du menu latéral : masque les entrées et les groupes sans correspondance.
        (function () {
            var input = document.getElementById('nav-filter');
            if (!input) return;
            var groups = document.querySelectorAll('#admin-nav .nav-group');
            input.addEventListener('input', function () {
                var q = input.value.trim().toLowerCase();
                groups.forEach(function (group) {
                    var visible = 0;
                    group.querySelectorAll('.nav-link').forEach(function (link) {
                        var match = !q || link.textContent.trim().toLowerCase().indexOf(q) !== -1;
                        link.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    group.style.display = visible ? '' : 'none';
                    if (q) group.open = true;
                });
            });
        })();
    </script>
@else
    {{-- Portails intervenant / entreprise / élève : barre supérieure légère, sans menu admin --}}
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

        // Navigation du portail : sans elle, une fois connecté on ne pouvait
        // atteindre aucun écran (le tableau de bord ne portait qu'un bouton
        // de déconnexion). Chaque entrée : [route, motif actif, icône, libellé].
        $portalLinks = $intervenantUser
            ? [
                ['intervenant.dashboard', 'intervenant.dashboard', 'home', 'Accueil'],
                ['espace-intervenant.planning', 'espace-intervenant.planning', 'cal', 'Mon emploi du temps'],
                ['espace-intervenant.classes', 'espace-intervenant.*', 'users', 'Mes classes'],
                ['recapitulatif.index', 'recapitulatif.*', 'clock', "Récapitulatif d'heures"],
            ]
            : ($entrepriseUser
                ? [
                    ['entreprise.dashboard', 'entreprise.dashboard', 'home', 'Accueil'],
                    ['entreprise.informations', 'entreprise.informations', 'building', 'Mes informations'],
                ]
                : [
                    ['eleve.dashboard', 'eleve.dashboard', 'home', 'Accueil'],
                    ['espace-eleve.planning', 'espace-eleve.planning', 'cal', 'Mon emploi du temps'],
                    ['espace-eleve.evaluations', 'espace-eleve.evaluations', 'pencil', 'Mes notes'],
                    ['espace-eleve.bulletins', 'espace-eleve.bulletins*', 'printer', 'Mes bulletins'],
                ]);

        // Une route absente (module non déployé) ne doit pas casser le portail.
        $portalLinks = collect($portalLinks)->filter(fn ($lien) => \Illuminate\Support\Facades\Route::has($lien[0]));
    @endphp
    <div class="main-col">
        <header class="topbar">
            <a href="{{ route($homeRoute) }}" class="brand" style="border:0;padding:0;gap:10px">
                <span class="brand-mark">@include('partials.icon', ['n' => 'cap', 's' => 17, 'c' => '#fff', 'w' => 2])</span>
                <span class="brand-text">
                    <span class="brand-name">{{ $portalLabel }}</span>
                    <span class="brand-sub">{{ config('app.name') }}</span>
                </span>
            </a>
            <div class="topbar-right">
                <span class="who">
                    <span class="avatar">{{ $portalInitials }}</span>
                    <span class="who-text"><span class="who-name">{{ $portalName ?? '' }}</span></span>
                </span>
                <form method="post" action="{{ route($logoutRoute) }}">
                    @csrf
                    <button type="button" class="logout-btn" onclick="this.form.submit()">Se déconnecter</button>
                </form>
            </div>
        </header>

        @if ($portalLinks->isNotEmpty())
            <nav class="portal-nav" aria-label="Navigation de l'espace">
                <div class="portal-nav-inner">
                    @foreach ($portalLinks as [$route, $motif, $icone, $libelle])
                        <a href="{{ route($route) }}" class="portal-link @if (request()->routeIs($motif)) is-active @endif">
                            @include('partials.icon', ['n' => $icone, 's' => 15, 'w' => 2]){{ $libelle }}
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif

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
