<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The click events | Event Ticketing</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <img src="/images/logo.svg" alt="The click events" class="brand-logo">
            <div>
                <strong>The click events</strong>
                <p>Events, tickets and immersive experiences</p>
            </div>
        </div>
        
        <?php if (is_authenticated()): ?>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <a href="/dashboard" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: inherit;">
                    <strong><?= e($_SESSION['user']['full_name']) ?></strong>
                    <img src="<?= e(avatar_url($_SESSION['user']['profile_picture_path'] ?? null)) ?>" alt="Avatar" class="nav-avatar" onerror="this.src='/images/default-avatar.svg'">
                </a>
            </div>
        <?php endif; ?>
    </header>

    <?php $layoutSidebar = $layoutSidebar ?? true; ?>

    <?php if ($layoutSidebar): ?>
        <div class="app-shell">
            <aside class="app-sidebar">
                <?php require VIEW_PATH . '/partials/navigation.php'; ?>
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
