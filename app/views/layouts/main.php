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
        <div class="topbar-actions">
            <?php if (is_authenticated()): ?>
                <a href="/notifications" class="topbar-icon-link" aria-label="Notifications">
                    <?php $unread = (new \App\Models\Notification())->unreadCount((int) ($_SESSION['user']['id'] ?? 0)); ?>
                    <span class="topbar-icon">🔔</span>
                    <?php if ($unread > 0): ?>
                        <span class="notif-badge"><?= e((string) $unread) ?></span>
                    <?php endif; ?>
                </a>
                <?php if (is_admin()): ?>
                    <a class="topbar-icon-link" href="/admin" aria-label="Administration">
                        <span class="topbar-icon">▣</span>
                    </a>
                <?php endif; ?>
                <a class="topbar-icon-link" href="/dashboard" aria-label="Mon compte">
                    <span class="topbar-icon">☺</span>
                </a>
                <a href="/logout" data-confirm="Voulez-vous vraiment vous déconnecter ?" class="topbar-icon-link" aria-label="Déconnexion">
                    <span class="topbar-icon">⎋</span>
                </a>

                <?php $avatar = avatar_url($_SESSION['user']['profile_picture_path'] ?? null); ?>
                <img src="<?= e($avatar) ?>" alt="avatar" class="nav-avatar" onerror="this.src='/images/default-avatar.svg'">
            <?php else: ?>
                <a href="/login" class="topbar-action">Connexion</a>
                <a href="/register" class="topbar-action topbar-action-primary">Inscription</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <?php require $viewFile; ?>
    </main>

    <script src="/js/main.js"></script>
</body>
</html>
