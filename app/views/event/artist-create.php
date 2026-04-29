<section class="auth-card">
    <span class="eyebrow">Artist Dashboard</span>
    <h1>Submit event for approval</h1>
    <p>Your event will be reviewed by the admin before publishing. Ticket prices will have a 10% markup applied when approved.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/events/store" enctype="multipart/form-data" class="auth-form">
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
            Ticket price (original)
            <input type="number" step="0.01" name="ticket_price" value="25.00" required>
        </label>

        <label>
            Ticket quantity
            <input type="number" name="ticket_quantity" value="100" required>
        </label>

        <button type="submit" class="button button-primary">Submit event</button>
    </form>
</section>
