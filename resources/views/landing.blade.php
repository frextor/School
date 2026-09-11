<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Gestion scolaire</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #4f46e5; --brand-dark: #3730a3; --ink: #1a1d29; --muted: #6b7280;
            --border: #e5e7eb; --bg: #f8f9fc; --surface: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Inter', system-ui, sans-serif; color: var(--ink);
            background: var(--bg); line-height: 1.5;
        }
        a { color: inherit; text-decoration: none; }

        header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 2rem; max-width: 1100px; margin: 0 auto;
        }
        .brand { display: flex; align-items: center; gap: 0.6rem; font-weight: 800; font-size: 1.25rem; }
        .brand .logo {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800;
        }
        header nav { display: flex; gap: 1.5rem; font-weight: 500; color: var(--muted); }
        header nav a:hover { color: var(--ink); }

        .hero {
            max-width: 1100px; margin: 0 auto; padding: 4rem 2rem 5rem; text-align: center;
        }
        .hero h1 { font-size: 2.6rem; font-weight: 800; margin: 0 0 1rem; letter-spacing: -0.02em; }
        .hero h1 span { color: var(--brand); }
        .hero p { color: var(--muted); font-size: 1.15rem; max-width: 620px; margin: 0 auto 2.25rem; }
        .hero .cta { display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }

        .btn {
            display: inline-block; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;
            border: 1px solid transparent;
        }
        .btn-primary { background: var(--brand); color: #fff; }
        .btn-primary:hover { background: var(--brand-dark); }
        .btn-ghost { border-color: var(--border); color: var(--ink); background: #fff; }
        .btn-ghost:hover { border-color: var(--brand); color: var(--brand); }

        .portals {
            max-width: 1100px; margin: 0 auto; padding: 0 2rem 5rem;
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;
        }
        .portal {
            background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
            padding: 1.75rem; text-align: left;
        }
        .portal .icon { font-size: 1.8rem; margin-bottom: 0.75rem; }
        .portal h3 { margin: 0 0 0.4rem; font-size: 1.05rem; }
        .portal p { margin: 0 0 1.1rem; color: var(--muted); font-size: 0.92rem; }
        .portal a { font-weight: 600; color: var(--brand); font-size: 0.92rem; }
        .portal a:hover { text-decoration: underline; }

        footer {
            text-align: center; padding: 2rem; color: var(--muted); font-size: 0.85rem;
            border-top: 1px solid var(--border);
        }

        @media (prefers-color-scheme: dark) {
            :root:not([data-theme="light"]) {
                --ink: #eef0f6; --muted: #9aa1b4; --border: #2a2e3d; --bg: #12131a; --surface: #191b26;
            }
        }
        :root[data-theme="dark"] {
            --ink: #eef0f6; --muted: #9aa1b4; --border: #2a2e3d; --bg: #12131a; --surface: #191b26;
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">
            <span class="logo">S</span>
            {{ config('app.name') }}
        </div>
        <nav>
            <a href="#espaces">Espaces</a>
            <a href="{{ route('intervenant-inscription.create') }}">Devenir intervenant</a>
            <a href="{{ route('admin.login') }}">Connexion</a>
        </nav>
    </header>

    <section class="hero">
        <h1>La gestion de <span>{{ config('school.name') }}</span>,<br>simplifiée.</h1>
        <p>
            {{ config('app.name') }} centralise élèves, candidats, intervenants, entreprises partenaires et
            emploi du temps dans un seul espace — pour l'administration comme pour chaque profil connecté.
        </p>
        <div class="cta">
            <a class="btn btn-primary" href="{{ route('admin.login') }}">Accéder à mon espace</a>
            <a class="btn btn-ghost" href="{{ route('intervenant-inscription.create') }}">Devenir intervenant</a>
        </div>
    </section>

    <section class="portals" id="espaces">
        <div class="portal">
            <div class="icon">🏫</div>
            <h3>Administration</h3>
            <p>Élèves, candidats, référentiel pédagogique, CRM, planning, bulletins.</p>
            <a href="{{ route('admin.login') }}">Se connecter →</a>
        </div>
        <div class="portal">
            <div class="icon">🎓</div>
            <h3>Espace élève</h3>
            <p>Planning, résultats, documents et suivi de scolarité.</p>
            <a href="{{ route('eleve.login') }}">Se connecter →</a>
        </div>
        <div class="portal">
            <div class="icon">👩‍🏫</div>
            <h3>Espace intervenant</h3>
            <p>Planning, classes, récapitulatif d'heures enseignées.</p>
            <a href="{{ route('intervenant.login') }}">Se connecter →</a>
        </div>
        <div class="portal">
            <div class="icon">🤝</div>
            <h3>Espace entreprise</h3>
            <p>Suivi des contrats et conventions avec les établissements.</p>
            <a href="{{ route('entreprise.login') }}">Se connecter →</a>
        </div>
    </section>

    <footer>
        © {{ date('Y') }} {{ config('app.name') }} — {{ config('school.name') }}
    </footer>
</body>
</html>
