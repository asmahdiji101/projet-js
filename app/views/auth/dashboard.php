<section class="auth-card">
    <span class="eyebrow">Compte</span>
    <h1>Bienvenue, <?= e($user['full_name']) ?></h1>
    <p>Email: <?= e($user['email']) ?></p>
    <p>Rôle: <?= e($user['role']) ?></p>
    <a class="button button-secondary" href="/logout">Se déconnecter</a>

    <div class="section-heading" style="margin-top:2rem;">
        <span>Bookings</span>
        <h2>Booking history</h2>
    </div>

    <?php if (empty($bookings)): ?>
        <p>No bookings yet. Visit the <a href="/events">events page</a> to add tickets to your cart.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($bookings as $booking): ?>
                <article class="cart-item">
                    <div>
                        <h3><?= e($booking['event_title']) ?></h3>
                        <p><?= e($booking['ticket_name']) ?> x<?= e((string) $booking['quantity']) ?></p>
                        <small><?= e($booking['created_at']) ?></small>
                    </div>
                    <strong><?= e(number_format((float) $booking['total_price'], 2)) ?> EUR</strong>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
