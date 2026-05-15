<section class="feed-section">
    <div class="feed-toolbar">
        <div class="section-heading">
            <span>Events</span>
            <h2>Available events</h2>
        </div>

        <form method="get" action="/events" class="feed-filters">
            <input type="search" name="q" placeholder="Search events" value="<?= e($filters['query'] ?? '') ?>">
            <input type="text" name="city" placeholder="Ville" value="<?= e($filters['city'] ?? '') ?>">
            <input type="date" name="date" value="<?= e($filters['date'] ?? '') ?>">
            <select name="category">
                <option value="">All types</option>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= ($filters['category'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button button-primary">Filter</button>
        </form>
    </div>

    <?php if (empty($events)): ?>
        <div class="auth-card">
            <p>No published events yet. Create sample events in the admin area to test the cart flow.</p>
        </div>
    <?php else: ?>
        <div class="feed-list feed-list-wide">
            <?php foreach ($events as $event): ?>
                <article class="feed-card feed-card-wide">
                    <div class="feed-card-media">
                        <?php if (!empty($event['image_path'])): ?>
                            <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="feed-card-body">
                        <div class="feed-card-meta">
                            <span class="feed-pill"><?= e($categories[$event['category'] ?? 'concert'] ?? 'Event') ?></span>
                            <span><?= e($event['event_date']) ?></span>
                        </div>
                        <h3><a href="/events/<?= (int) $event['id'] ?>"><?= e($event['title']) ?></a></h3>
                        <p><?= e($event['description']) ?></p>
                        <div class="feed-card-footer">
                            <strong><?= e($event['artist_name']) ?></strong>
                            <span><?= e($event['location']) ?></span>
                        </div>

                        <?php if (is_admin()): ?>
                            <div class="hero-actions" style="margin-top:1rem;">
                                <a class="button button-secondary" href="/events/edit?id=<?= e((string) $event['id']) ?>">Edit</a>
                                <form method="post" action="/events/delete">
                                    <input type="hidden" name="id" value="<?= e((string) $event['id']) ?>">
                                    <button class="button button-secondary" type="submit">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>

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

                                <?php if (is_admin()): ?>
                                    <div class="hero-actions" style="margin:0 0 0.5rem 0;">
                                        <a class="button button-secondary" href="/tickets/edit?id=<?= e((string) $ticket['id']) ?>">Edit ticket</a>
                                        <form method="post" action="/tickets/delete">
                                            <input type="hidden" name="id" value="<?= e((string) $ticket['id']) ?>">
                                            <button class="button button-secondary" type="submit">Delete ticket</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
