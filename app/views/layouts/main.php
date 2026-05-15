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
        <nav class="topbar-actions">
            <a class="topbar-action" href="/events">Événements</a>
            <a class="topbar-action" href="/cart">Panier</a>

            <?php if (is_authenticated()): ?>
                <a class="topbar-action" href="/notifications">
                    Notifications
                    <?php $unread = (new \App\Models\Notification())->unreadCount((int) ($_SESSION['user']['id'] ?? 0)); ?>
                    <?php if ($unread > 0): ?>
                        <span class="notif-badge"><?= e((string) $unread) ?></span>
                    <?php endif; ?>
                </a>
                <a class="topbar-action" href="/dashboard">Mon compte</a>
                <a class="topbar-action" href="/account/edit">Modifier le compte</a>

                <?php if ($_SESSION['user']['role'] === 'artist'): ?>
                    <a class="topbar-action" href="/events/create">Créer un événement</a>
                    <a class="topbar-action" href="/events/artist-events">Mes événements</a>
                <?php endif; ?>

                <?php if (is_admin()): ?>
                    <a class="topbar-action" href="/events/create">Créer un événement</a>
                    <a class="topbar-action" href="/artists">Artistes</a>
                    <a class="topbar-action" href="/admin">Administration</a>
                <?php endif; ?>

                <a href="/logout" data-confirm="Voulez-vous vraiment vous déconnecter ?" class="topbar-action">Déconnexion</a>

                <?php $avatar = avatar_url($_SESSION['user']['profile_picture_path'] ?? null); ?>
                <img src="<?= e($avatar) ?>" alt="avatar" class="nav-avatar" onerror="this.src='/images/default-avatar.svg'">
            <?php else: ?>
                <a href="/login" class="topbar-action">Connexion</a>
                <a href="/register" class="topbar-action topbar-action-primary">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php require $viewFile; ?>
    </main>

    <script src="/js/main.js"></script>
</body>
</html>
