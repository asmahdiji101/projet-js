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
                    <span class="eyebrow">Admin</span>
                    <h1>Revenue analytics</h1>
                </div>
                <div class="admin-live-meta">Bookings: <?= e((string) $totalBookings) ?></div>
            </div>
            <div class="admin-curve-summary">
                <div>
                    <span>Total revenue</span>
                    <strong><?= e(number_format((float) $totalRevenue, 2)) ?> EUR</strong>
                </div>
                <div>
                    <span>Curve</span>
                    <strong>Monthly trend</strong>
                </div>
            </div>
            <svg viewBox="0 0 800 180" class="revenue-curve" preserveAspectRatio="none" aria-label="Revenue curve">
                <defs>
                    <linearGradient id="curveFillRevenue" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#4de1c1" stop-opacity="0.35" />
                        <stop offset="100%" stop-color="#4de1c1" stop-opacity="0.03" />
                    </linearGradient>
                </defs>
                <polygon points="<?= e($areaPolygon) ?>" fill="url(#curveFillRevenue)" />
                <polyline points="<?= e(implode(' ', $points)) ?>" fill="none" stroke="#ff8a3d" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="revenue-months">
                <?php foreach ($revenueLabels as $label): ?>
                    <span><?= e($label) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="admin-summary-panel">
            <div class="admin-summary-strip admin-summary-strip-secondary">
                <article>
                    <span>Bookings</span>
                    <strong><?= e((string) $totalBookings) ?></strong>
                </article>
                <article>
                    <span>Categories</span>
                    <strong><?= e((string) count($byCategory)) ?></strong>
                </article>
                <article>
                    <span>Cities</span>
                    <strong><?= e((string) count($byLocation)) ?></strong>
                </article>
            </div>

            <div class="admin-mini-links">
                <a href="/admin" class="admin-mini-link">Dashboard</a>
                <a href="/admin/bookings" class="admin-mini-link">Reservations</a>
                <a href="/admin/messages" class="admin-mini-link">Messages</a>
                <a href="/notifications" class="admin-mini-link">Notifications</a>
            </div>
        </div>
    </div>

    <div class="section-heading" style="margin-top:2rem;">
        <span>By category</span>
        <h2>Revenue per event type</h2>
    </div>

    <div class="cart-items">
        <?php foreach ($byCategory as $row): ?>
            <article class="cart-item admin-message-item">
                <div>
                    <h3><?= e(ucfirst((string) $row['category'])) ?></h3>
                    <p><?= e((string) $row['bookings_count']) ?> bookings</p>
                </div>
                <strong><?= e(number_format((float) $row['revenue'], 2)) ?> EUR</strong>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="section-heading" style="margin-top:2rem;">
        <span>By city</span>
        <h2>Revenue per location</h2>
    </div>

    <div class="cart-items">
        <?php foreach ($byLocation as $row): ?>
            <article class="cart-item admin-message-item">
                <div>
                    <h3><?= e((string) $row['location']) ?></h3>
                    <p><?= e((string) $row['bookings_count']) ?> bookings</p>
                </div>
                <strong><?= e(number_format((float) $row['revenue'], 2)) ?> EUR</strong>
            </article>
        <?php endforeach; ?>
    </div>
</section>