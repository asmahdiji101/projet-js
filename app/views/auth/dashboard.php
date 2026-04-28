<section class="auth-card">
    <span class="eyebrow">Compte</span>
    <h1>Bienvenue, <?= e($user['full_name']) ?></h1>
    <p>Email: <?= e($user['email']) ?></p>
    <p>Rôle: <?= e($user['role']) ?></p>
    <a class="button button-secondary" href="/logout">Se déconnecter</a>
</section>
