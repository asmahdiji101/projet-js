<section class="auth-card">
    <span class="eyebrow">Admin</span>
    <h1>Create event</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/events/store" enctype="multipart/form-data" class="auth-form">
        <label>
            Artist
            <select name="artist_id" required>
                <option value="">Select an artist</option>
                <?php foreach ($artists as $artist): ?>
                    <option value="<?= e((string) $artist['id']) ?>"><?= e($artist['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Title
            <input type="text" name="title" required>
        </label>

        <label>
            Description
            <textarea name="description" rows="4" required></textarea>
        </label>

        <label>
            Date and time
            <input type="datetime-local" name="event_date" required>
        </label>

        <label>
            Location
            <input type="text" name="location" required>
        </label>

        <label>
            Image
            <input type="file" name="image" accept="image/*">
        </label>

        <h2>Starter ticket</h2>

        <label>
            Ticket name
            <input type="text" name="ticket_name" value="Standard">
        </label>

        <label>
            Ticket price
            <input type="number" step="0.01" name="ticket_price" value="25.00">
        </label>

        <label>
            Ticket quantity
            <input type="number" name="ticket_quantity" value="100">
        </label>

        <button type="submit" class="button button-primary">Save event</button>
    </form>
</section>