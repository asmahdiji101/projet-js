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
        <article class="card">
            <h3>Revenu</h3>
            <strong><?= e(number_format((float) $stats['revenue'], 2)) ?> EUR</strong>
        </article>
    </div>

    <div class="section-heading" style="margin-top:2rem;">
        <span>Admin Tools</span>
        <h2>Management</h2>
    </div>

    <div class="hero-actions">
        <a class="button button-primary" href="/admin/pending-events">Pending Event Approvals</a>
        <a class="button button-primary" href="/admin/messages">Contact Messages</a>
    </div>
</section>
