<div class="auth-shell">
    <div class="auth-card">
        <h2>Historique de réservation</h2>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <p>Vous n'avez pas encore de réservations.</p>
                <a href="/events" class="button button-primary">Découvrir les événements</a>
            </div>
        <?php else: ?>
            <div class="bookings-list">
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <div>
                                <h3><?= e($booking['event_title']) ?></h3>
                                <p class="booking-detail">
                                    Billet: <?= e($booking['ticket_name']) ?>
                                    <?php if (!empty($booking['buyer_name'])): ?>
                                        · Acheteur: <?= e($booking['buyer_name']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <span class="booking-status status-<?= e($booking['status']) ?>">
                                <?= ucfirst(e($booking['status'])) ?>
                            </span>
                        </div>

                        <div class="booking-body">
                            <div class="booking-row">
                                <span>Quantité:</span>
                                <strong><?= (int) $booking['quantity'] ?></strong>
                            </div>
                            <div class="booking-row">
                                <span>Prix total:</span>
                                <strong><?= number_format((float) $booking['total_price'], 2, ',', ' ') ?> TND</strong>
                            </div>
                            <div class="booking-row">
                                <span>Date de réservation:</span>
                                <strong><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></strong>
                            </div>
                        </div>

                        <a href="/events/<?= (int) $booking['event_id'] ?>" class="button button-secondary button-small">
                            Voir l'événement
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .auth-shell {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 2rem 1rem;
        min-height: 100vh;
    }

    .auth-card {
        width: 100%;
        max-width: 700px;
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .auth-card h2 {
        margin: 0 0 2rem 0;
        font-size: 1.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state p {
        margin: 0 0 1rem 0;
        color: #666;
    }

    .bookings-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .booking-card {
        border: 1px solid #e0e0e0;
        border-radius: 0.8rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        transition: box-shadow 0.2s ease;
    }

    .booking-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }

    .booking-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    .booking-detail {
        margin: 0.5rem 0 0 0;
        color: #666;
        font-size: 0.9rem;
    }

    .booking-status {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 0.4rem;
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-confirmed {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #f57f17;
    }

    .status-completed {
        background: rgba(33, 150, 243, 0.15);
        color: #1565c0;
    }

    .status-cancelled {
        background: rgba(244, 67, 54, 0.15);
        color: #c62828;
    }

    .booking-body {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1rem 0;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }

    .booking-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
    }

    .booking-row span {
        color: #666;
    }

    .button-small {
        align-self: flex-start;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
</style>
