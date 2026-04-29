<section>
    <div class="section-heading">
        <span>Events</span>
        <h2>Available events</h2>
    </div>

    <?php if (empty($events)): ?>
        <div class="auth-card">
            <p>No published events yet. Create sample events in the admin area to test the cart flow.</p>
        </div>
    <?php else: ?>
        <div class="cards">
            <?php foreach ($events as $event): ?>
                <article class="card">
                    <span class="eyebrow"><?= e($event['artist_name']) ?></span>
                    <h3><?= e($event['title']) ?></h3>
                    <p><?= e($event['description']) ?></p>
                    <p><strong><?= e($event['location']) ?></strong></p>
                    <p><?= e($event['event_date']) ?></p>

                    <div class="ticket-list">
                        <?php foreach ($event['tickets'] as $ticket): ?>
                            <form method="post" action="/cart/add" class="ticket-form">
                                <input type="hidden" name="ticket_id" value="<?= e((string) $ticket['id']) ?>">
                                <div>
                                    <strong><?= e($ticket['name']) ?></strong>
                                    <div><?= e(number_format((float) $ticket['price'], 2)) ?> EUR</div>
                                    <small>Available: <?= e((string) ((int) $ticket['quantity_total'] - (int) $ticket['quantity_sold'])) ?></small>
                                </div>
                                <label>
                                    Qty
                                    <input type="number" name="quantity" min="1" value="1" max="<?= e((string) ((int) $ticket['quantity_total'] - (int) $ticket['quantity_sold'])) ?>">
                                </label>
                                <button type="submit" class="button button-primary">Add to cart</button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
