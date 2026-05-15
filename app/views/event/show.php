<section class="event-detail">
    <div class="event-header">
        <h1><?= e($event['title']) ?></h1>
        <p><?= e($event['event_date']) ?> — <?= e($event['location']) ?></p>
        <span class="feed-pill"><?= e(
            $event['category'] === 'concert' ? 'Concerts' : (
                ($event['category'] === 'excursion' ? 'Excursions' : (
                    ($event['category'] === 'festival' ? 'Festivals' : (
                        ($event['category'] === 'food' ? 'Food' : (
                            ($event['category'] === 'humanitaire' ? 'Humanitaires' : (
                                ($event['category'] === 'loisir' ? 'Loisirs' : (
                                    ($event['category'] === 'professionnel' ? 'Professionnels' : 'Event')
                                ))
                            ))
                        ))
                    ))
                ))
            )
        ) ?></span>
    </div>

    <?php if (!empty($event['image_path'])): ?>
        <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" class="event-detail-image">
    <?php endif; ?>

    <div class="event-body">
        <p><?= nl2br(e($event['description'])) ?></p>

        <div class="event-booking-panel">
            <div>
                <h3>Book your ticket</h3>
                <p>Select a ticket and send it to your cart to confirm the reservation.</p>
            </div>
        </div>

        <h3>Tickets</h3>
        <?php if (empty($tickets)): ?>
            <p>No tickets available.</p>
        <?php else: ?>
            <div class="ticket-list ticket-list-detail">
                <?php foreach ($tickets as $t): ?>
                    <form method="post" action="/cart/add" class="ticket-form ticket-form-detail">
                        <input type="hidden" name="ticket_id" value="<?= e((string) $t['id']) ?>">
                        <div>
                            <strong><?= e($t['name']) ?></strong>
                            <div><?= e(number_format((float)$t['price'],2)) ?> EUR</div>
                            <small><?= e((string)$t['quantity_total']) ?> total</small>
                        </div>
                        <label>
                            Qty
                            <input type="number" name="quantity" min="1" value="1" max="<?= e((string) ((int) $t['quantity_total'] - (int) $t['quantity_sold'])) ?>">
                        </label>
                        <button type="submit" class="button button-primary">Book ticket</button>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
