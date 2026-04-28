<section class="auth-card">
    <span class="eyebrow">Administration</span>
    <h1>Tableau de bord</h1>

    <div class="cards admin-stats">
        <article class="card">
            <h3>Utilisateurs</h3>
            <strong><?= e((string) $stats['users']) ?></strong>
        </article>
        <article class="card">
            <h3>Artistes</h3>
            <strong><?= e((string) $stats['artists']) ?></strong>
        </article>
        <article class="card">
            <h3>Événements</h3>
            <strong><?= e((string) $stats['events']) ?></strong>
        </article>
        <article class="card">
            <h3>Réservations</h3>
            <strong><?= e((string) $stats['bookings']) ?></strong>
        </article>
    </div>
</section>
