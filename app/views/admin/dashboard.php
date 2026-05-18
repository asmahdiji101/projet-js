<?php
$revenueValues = $revenueCurve['values'] ?? [];
$revenueLabels = $revenueCurve['labels'] ?? [];
$points = [];
$areaPoints = [];
$countValues = count($revenueValues);
$maxRevenue = max(1.0, (float) max($revenueValues ?: [1]));
$minX = 10;
$maxX = 790;
$minY = 150;
$maxY = 10;

for ($index = 0; $index < $countValues; $index++) {
    $x = $countValues > 1 ? $minX + (($maxX - $minX) * $index / ($countValues - 1)) : 400;
    $value = (float) $revenueValues[$index];
    $y = $maxY + (($minY - $maxY) * (1 - ($value / $maxRevenue)));
    $points[] = round($x, 2) . ',' . round($y, 2);
    $areaPoints[] = round($x, 2) . ',' . round($y, 2);
}
$areaPolygon = $points ? '10,170 ' . implode(' ', $areaPoints) . ' 790,170' : '';
?>

<section class="admin-shell">
    <div class="admin-top-grid">
        <div class="admin-curve-card">
            <div class="admin-card-head">
                <div>
                    <span class="eyebrow">Administration</span>
                    <h1>Tableau de bord</h1>
                </div>
                <div class="admin-live-meta">Mise a jour automatique active - <span id="admin-updated-at">initialisation...</span></div>
            </div>
            <div class="admin-curve-summary">
                <div>
                    <span>Revenue</span>
                    <strong id="stat-revenue"><?= e(number_format((float) $stats['revenue'], 2)) ?> EUR</strong>
                </div>
                <div>
                    <span>Current trend</span>
                    <strong><?= e((string) ($stats['events'] ?? 0)) ?> events</strong>
                </div>
            </div>
            <svg viewBox="0 0 800 180" class="revenue-curve" preserveAspectRatio="none" aria-label="Revenue curve">
                <defs>
                    <linearGradient id="curveFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#4de1c1" stop-opacity="0.35" />
                        <stop offset="100%" stop-color="#4de1c1" stop-opacity="0.03" />
                    </linearGradient>
                </defs>
                <polygon points="<?= e($areaPolygon) ?>" fill="url(#curveFill)" />
                <polyline points="<?= e(implode(' ', $points)) ?>" fill="none" stroke="#ff8a3d" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="revenue-months">
                <?php foreach ($revenueLabels as $label): ?>
                    <span><?= e($label) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="admin-summary-panel">
            <div class="admin-summary-strip">
                <article>
                    <span>Users</span>
                    <strong><?= e((string) $stats['users']) ?></strong>
                </article>
                <article>
                    <span>Artists</span>
                    <strong><?= e((string) $stats['artists']) ?></strong>
                </article>
                <article>
                    <span>Events</span>
                    <strong><?= e((string) $stats['events']) ?></strong>
                </article>
                <article>
                    <span>Reservations</span>
                    <strong><?= e((string) $stats['bookings']) ?></strong>
                </article>
            </div>

            <div class="admin-summary-strip admin-summary-strip-secondary">
                <article>
                    <span>Pending events</span>
                    <strong id="stat-pending-events"><?= e((string) $stats['pending_events']) ?></strong>
                </article>
                <article>
                    <span>Messages</span>
                    <strong id="stat-pending-messages"><?= e((string) $stats['pending_messages']) ?></strong>
                </article>
                <article>
                    <span>Notifications</span>
                    <strong id="stat-unread-notifications"><?= e((string) $stats['unread_notifications']) ?></strong>
                </article>
            </div>

            <div class="admin-mini-links">
                <a href="/artists" class="admin-mini-link">Artists</a>
                <a href="/notifications" class="admin-mini-link">Notifications</a>
                <a href="/admin/revenue" class="admin-mini-link">Revenue</a>
                <a href="/admin/bookings" class="admin-mini-link">Reservations</a>
            </div>
        </div>
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
