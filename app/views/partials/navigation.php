<?php

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$currentPath = rtrim($currentPath, '/') ?: '/';

$navItemClass = static fn(string $path): string => $currentPath === $path ? 'home-menu-item is-active' : 'home-menu-item';
$nestedItemClass = static fn(string $path): string => $currentPath === $path ? 'home-menu-item home-menu-item--nested is-active' : 'home-menu-item home-menu-item--nested';
?>
<div class="home-panel home-menu-panel">
    <div class="home-menu-title">Navigation</div>
    <a class="<?= $navItemClass('/') ?>" href="/">Accueil</a>

    <?php if (!is_authenticated()): ?>
        <a class="<?= $navItemClass('/contact') ?>" href="/contact">Support</a>
        <a class="<?= $navItemClass('/login') ?>" href="/login">Connexion</a>
    <?php else: ?>
        <a class="<?= $navItemClass('/cart') ?>" href="/cart">Panier</a>
        <a class="<?= $navItemClass('/contact') ?>" href="/contact">Support</a>

        <a class="<?= $navItemClass('/dashboard') ?>" href="/dashboard" style="display: flex; align-items: center; gap: 0.75rem;">
            <img src="<?= e(avatar_url($_SESSION['user']['profile_picture_path'] ?? null)) ?>" alt="Avatar" class="nav-avatar" style="margin:0; width:28px; height:28px; border:none;" onerror="this.src='/images/default-avatar.svg'">
            Mon compte
        </a>

        <?php if ($_SESSION['user']['role'] === 'artist'): ?>
            <a class="<?= $navItemClass('/events/artist-events') ?>" href="/events/artist-events">Mes événements</a>
        <?php endif; ?>

        <?php if (is_admin()): ?>
            <a class="<?= $navItemClass('/admin') ?>" href="/admin">Administration</a>
        <?php endif; ?>

        <a class="home-menu-item" href="/logout" data-confirm="Voulez-vous vraiment vous déconnecter ?">Déconnexion</a>
    <?php endif; ?>
</div>
