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
            <?php if (!is_admin()): ?>
                <a href="/contact">Contact</a>
            <?php endif; ?>
            <?php if (is_authenticated()): ?>
                <a href="/notifications" class="nav-notif">Notifications
                    <?php $unread = (new \App\Models\Notification())->unreadCount((int) ($_SESSION['user']['id'] ?? 0)); ?>
                    <?php if ($unread > 0): ?>
                        <span class="notif-badge"><?= e((string)$unread) ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($_SESSION['user']['role'] === 'artist'): ?>
                    <a href="/events/artist-events">Mes événements</a>
                <?php endif; ?>
                <a href="/dashboard">Mon compte</a>
                <?php if (is_admin()): ?>
                    <a href="/events/create">Créer un événement</a>
                    <a href="/artists">Artistes</a>
                    <a href="/admin">Administration</a>
                <?php endif; ?>
                <a href="/logout" data-confirm="Voulez-vous vraiment vous déconnecter ?">Déconnexion</a>

                <?php $avatar = $_SESSION['user']['profile_picture_path'] ?? '/images/default-avatar.svg'; ?>
                <img src="<?= e($avatar) ?>" alt="avatar" class="nav-avatar">
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
