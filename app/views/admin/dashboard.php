<section class="auth-card">
    <span class="eyebrow">Administration</span>
    <h1>Tableau de bord</h1>
    <p class="admin-live-meta">Mise a jour automatique active - <span id="admin-updated-at">initialisation...</span></p>

    <div class="cards admin-stats">
        <article class="card">
            <h3>Utilisateurs</h3>
            <strong><?= e((string) $stats['users']) ?></strong>
        </article>
        <article class="card">
            <h3>Artistes</h3>
            <strong><?= e((string) $stats['artists']) ?></strong>
            <a class="admin-card-link" href="/artists">Voir les artistes</a>
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
            <strong id="stat-revenue"><?= e(number_format((float) $stats['revenue'], 2)) ?> EUR</strong>
        </article>
        <article class="card card-pending">
            <h3>Evenements en attente</h3>
            <strong id="stat-pending-events"><?= e((string) $stats['pending_events']) ?></strong>
        </article>
        <article class="card card-pending">
            <h3>Messages en attente</h3>
            <strong id="stat-pending-messages"><?= e((string) $stats['pending_messages']) ?></strong>
        </article>
        <article class="card">
            <h3>Notifications admin</h3>
            <strong id="stat-unread-notifications"><?= e((string) $stats['unread_notifications']) ?></strong>
            <a class="admin-card-link" href="/notifications">Ouvrir les notifications</a>
        </article>
    </div>

    <div class="section-heading" style="margin-top:2rem;">
        <span>Admin Tools</span>
        <h2>Management</h2>
    </div>

    <div class="admin-block-grid">
        <a class="admin-block" href="/admin/pending-events">
            <span>Approvals</span>
            <strong>Pending events</strong>
            <p>Review artist submissions and publish them.</p>
        </a>
        <a class="admin-block" href="/admin/messages">
            <span>Inbox</span>
            <strong>Contact messages</strong>
            <p>Open user and artist requests, then reply.</p>
        </a>
        <a class="admin-block" href="/admin/bookings">
            <span>Reservations</span>
            <strong>All bookings</strong>
            <p>Browse reservation records and ticket details.</p>
        </a>
        <a class="admin-block" href="/admin/revenue">
            <span>Analytics</span>
            <strong>Revenue reports</strong>
            <p>See revenue by event category and city.</p>
        </a>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const setText = function (id, value) {
        const node = document.getElementById(id);
        if (node) {
            node.textContent = value;
        }
    };

    const refreshStats = function () {
        fetch('/admin/stats/live', { headers: { 'Accept': 'application/json' } })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to load live stats');
                }
                return response.json();
            })
            .then(function (data) {
                setText('stat-pending-events', String(data.pending_events ?? 0));
                setText('stat-pending-messages', String(data.pending_messages ?? 0));
                setText('stat-unread-notifications', String(data.unread_notifications ?? 0));
                setText('stat-revenue', Number(data.revenue ?? 0).toFixed(2) + ' EUR');
                setText('admin-updated-at', 'dernier refresh a ' + (data.updated_at ?? '--:--:--'));
            })
            .catch(function () {
                setText('admin-updated-at', 'echec du refresh automatique');
            });
    };

    refreshStats();
    window.setInterval(refreshStats, 15000);
});
</script>
