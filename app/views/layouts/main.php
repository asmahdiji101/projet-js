<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NeonPass | Event Ticketing</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <span class="brand-mark">NP</span>
            <div>
                <strong>NeonPass</strong>
                <p>Events, tickets and immersive experiences</p>
            </div>
        </div>
        <nav class="nav">
            <a href="/">Accueil</a>
            <a href="/events">Événements</a>
            <a href="/cart">Panier</a>
            <?php if (is_authenticated()): ?>
                <a href="/dashboard">Mon compte</a>
                <?php if (is_admin()): ?>
                    <a href="/admin">Administration</a>
                <?php endif; ?>
                <a href="/logout">Déconnexion</a>
            <?php else: ?>
                <a href="/login">Connexion</a>
                <a href="/register">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php require $viewFile; ?>
    </main>

    <script src="/js/main.js"></script>
</body>
</html>
