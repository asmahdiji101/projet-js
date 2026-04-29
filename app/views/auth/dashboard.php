<section class="auth-card">
    <span class="eyebrow">Compte</span>
    <h1>Bienvenue, <?= e($user['full_name']) ?></h1>
    <p>Email: <?= e($user['email']) ?></p>
    <p>Rôle: <?= e(ucfirst($user['role'])) ?></p>
    <a class="button button-secondary" href="/logout">Se déconnecter</a>

    <?php if (!empty($notifications)): ?>
        <div class="section-heading" style="margin-top:2rem;">
            <span>Notifications</span>
            <h2>Recent updates</h2>
        </div>
        <div class="cart-items">
            <?php foreach ($notifications as $notif): ?>
                <article class="cart-item" style="background:<?= $notif['is_read'] ? '#f9f9f9' : '#e3f2fd' ?>;">
                    <div>
                        <h3><?= e($notif['title']) ?></h3>
                        <p><?= e($notif['message']) ?></p>
                        <small><?= e($notif['created_at']) ?></small>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($user['role'] === 'artist'): ?>
        <div class="section-heading" style="margin-top:2rem;">
            <span>Your Events</span>
            <h2>Event submissions</h2>
        </div>

        <?php if (empty($artistEvents)): ?>
            <p>You haven't submitted any events yet. <a href="/events/create">Create your first event</a></p>
        <?php else: ?>
            <div class="cards">
                <?php foreach ($artistEvents as $event): ?>
                    <article class="card">
                        <h3><?= e($event['title']) ?></h3>
                        <p><?= e($event['description']) ?></p>
                        <p><strong>Status:</strong> <?= e(ucfirst($event['status'])) ?> | <strong>Approval:</strong> <?= e(ucfirst($event['approval_status'])) ?></p>
                        <p><small><?= e($event['event_date']) ?> — <?= e($event['location']) ?></small></p>
                        <?php if ($event['approval_status'] === 'pending'): ?>
                            <p style="color:orange;">⏳ Pending admin review</p>
                        <?php elseif ($event['approval_status'] === 'approved'): ?>
                            <p style="color:green;">✓ Approved & Live (10% markup applied to prices)</p>
                        <?php elseif ($event['approval_status'] === 'rejected'): ?>
                            <p style="color:red;">✗ Rejected</p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <a class="button button-primary" href="/events/create" style="margin-top:1rem;">Submit new event</a>
        <a class="button button-secondary" href="/events/artist-events" style="margin-top:1rem;">View all my event statuses</a>
    <?php else: ?>
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
    <?php endif; ?>
</section>
