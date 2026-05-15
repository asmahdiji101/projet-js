<section class="auth-card">
    <span class="eyebrow">Admin</span>
    <h1>Edit event</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error"><?= e($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <form method="post" action="/events/update" enctype="multipart/form-data" class="auth-form">
        <input type="hidden" name="id" value="<?= e((string) $event['id']) ?>">

        <label>
            Artist
            <select name="artist_id" required>
                <?php foreach ($artists as $artist): ?>
                    <option value="<?= e((string) $artist['id']) ?>" <?= (int) $artist['id'] === (int) $event['artist_id'] ? 'selected' : '' ?>>
                        <?= e($artist['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Type of event
            <select name="category" required>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= ($event['category'] ?? 'concert') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Title
            <input type="text" name="title" value="<?= e($event['title']) ?>" required>
        </label>

        <label>
            Description
            <textarea name="description" rows="4" required><?= e($event['description']) ?></textarea>
        </label>

        <label>
            Date and time
            <input type="datetime-local" name="event_date" value="<?= e(str_replace(' ', 'T', substr((string) $event['event_date'], 0, 16))) ?>" required>
        </label>

        <label>
            Ville / location
            <input type="text" name="location" value="<?= e($event['location']) ?>" required>
        </label>

        <label>
            Status
            <select name="status" required>
                <?php foreach (['draft', 'published', 'cancelled'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= $event['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            <?php if (!empty($event['image_path'])): ?>
                <label>
                    Current image
                    <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" style="max-width:100%;border-radius:8px;">
                </label>
            <?php endif; ?>

            Replace image
            <input type="file" name="image" accept="image/*">
        </label>

        <button type="submit" class="button button-primary">Update event</button>
    </form>

    <div class="section-heading" style="margin-top:2rem;">
        <span>Tickets</span>
        <h2>Manage tickets</h2>
    </div>

    <?php if (empty($tickets)): ?>
        <p>No tickets yet.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($tickets as $ticket): ?>
                <article class="cart-item">
                    <div>
                        <h3><?= e($ticket['name']) ?></h3>
                        <p>Price: <?= e(number_format((float) $ticket['price'], 2)) ?> EUR</p>
                        <p>Total quantity: <?= e((string) $ticket['quantity_total']) ?></p>
                        <p>Sold: <?= e((string) $ticket['quantity_sold']) ?></p>
                    </div>
                    <div class="hero-actions" style="margin-top:0;">
                        <a class="button button-secondary" href="/tickets/edit?id=<?= e((string) $ticket['id']) ?>">Edit</a>
                        <form method="post" action="/tickets/delete">
                            <input type="hidden" name="id" value="<?= e((string) $ticket['id']) ?>">
                            <button class="button button-secondary" type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="section-heading" style="margin-top:2rem;">
        <span>New ticket</span>
        <h2>Add ticket type</h2>
    </div>

    <form method="post" action="/tickets/store" class="auth-form">
        <input type="hidden" name="event_id" value="<?= e((string) $event['id']) ?>">

        <label>
            Ticket name
            <input type="text" name="name" placeholder="VIP" required>
        </label>

        <label>
            Price
            <input type="number" step="0.01" name="price" required>
        </label>

        <label>
            Quantity total
            <input type="number" name="quantity_total" required>
        </label>

        <button type="submit" class="button button-primary">Add ticket</button>
    </form>

    <form method="post" action="/events/delete" style="margin-top:1rem;">
        <input type="hidden" name="id" value="<?= e((string) $event['id']) ?>">
        <button class="button button-secondary" type="submit">Delete event</button>
    </form>
</section>
