<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AIO Events | Event Ticketing</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <img src="/images/logo.svg" alt="AIO Events" class="brand-logo">
            <div>
                <strong>AIO Events</strong>
                <p>Events, tickets and immersive experiences</p>
            </div>
        </div>
    </header>

    <?php $layoutSidebar = $layoutSidebar ?? true; ?>

    <?php if ($layoutSidebar): ?>
        <div class="app-shell">
            <aside class="app-sidebar">
                <div class="home-panel home-menu-panel">
                    <div class="home-menu-title">Navigation</div>
                    <a class="home-menu-item" href="/">Accueil</a>
                    <a class="home-menu-item" href="/events">Carte</a>

                    <?php if (is_authenticated()): ?>
                        <a class="home-menu-item" href="/notifications">Notifications</a>
                    <?php else: ?>
                        <a class="home-menu-item" href="/contact">Contact</a>
                    <?php endif; ?>

                    <a class="home-menu-item" href="/contact">Support</a>

                    <?php if (is_authenticated()): ?>
                        <a class="home-menu-item" href="/dashboard">Mon compte</a>
                        <a class="home-menu-item" href="/account/edit">Modifier le compte</a>

                        <?php if ($_SESSION['user']['role'] === 'artist'): ?>
                            <a class="home-menu-item" href="/events/create">Créer un événement</a>
                            <a class="home-menu-item" href="/events/artist-events">Mes événements</a>
                        <?php endif; ?>

                        <?php if (is_admin()): ?>
                            <a class="home-menu-item" href="/events/create">Créer un événement</a>
                            <a class="home-menu-item" href="/artists">Artistes</a>
                            <a class="home-menu-item" href="/admin">Administration</a>
                        <?php endif; ?>

                        <a class="home-menu-item" href="/logout" data-confirm="Voulez-vous vraiment vous déconnecter ?">Déconnexion</a>
                    <?php else: ?>
                        <a class="home-menu-item" href="/login">Connexion</a>
                        <a class="home-menu-item" href="/register">Inscription</a>
                    <?php endif; ?>
                </div>
            </aside>

            <main class="app-main">
                <?php require $viewFile; ?>
            </main>
        </div>
    <?php else: ?>
        <main>
            <?php require $viewFile; ?>
        </main>
    <?php endif; ?>

    <script src="/js/main.js"></script>
</body>
</html>
