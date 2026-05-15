<section>
    <div class="section-heading">
        <span>Admin</span>
        <h2>Reservations</h2>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="auth-card">
            <p>No reservations yet.</p>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($bookings as $booking): ?>
                <article class="cart-item admin-message-item">
                    <div>
                        <h3><?= e($booking['event_title']) ?></h3>
                        <p><strong>User:</strong> <?= e($booking['user_name']) ?> (<?= e($booking['user_email']) ?>)</p>
                        <p><strong>Ticket:</strong> <?= e($booking['ticket_name']) ?> x<?= e((string) $booking['quantity']) ?></p>
                        <p><strong>Category:</strong> <?= e($booking['event_category']) ?> · <strong>Location:</strong> <?= e($booking['event_location']) ?></p>
                        <small><?= e($booking['event_date']) ?> · <?= e($booking['created_at']) ?></small>
                    </div>
                    <strong><?= e(number_format((float) $booking['total_price'], 2)) ?> EUR</strong>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>