<section class="auth-card">
    <span class="eyebrow">Admin</span>
    <h1>Add ticket</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/tickets/store" class="auth-form">
        <input type="hidden" name="event_id" value="<?= e((string) $event['id']) ?>">

        <label>
            Ticket name
            <input type="text" name="name" required>
        </label>

        <label>
            Price
            <input type="number" step="0.01" name="price" required>
        </label>

        <label>
            Quantity total
            <input type="number" name="quantity_total" required>
        </label>

        <button type="submit" class="button button-primary">Save ticket</button>
    </form>

    <p style="margin-top:1rem;">
        Back to <a href="/events/edit?id=<?= e((string) $event['id']) ?>">event editor</a>
    </p>
</section>
