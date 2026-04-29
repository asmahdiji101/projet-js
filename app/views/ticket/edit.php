<section class="auth-card">
    <span class="eyebrow">Admin</span>
    <h1>Edit ticket</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/tickets/update" class="auth-form">
        <input type="hidden" name="id" value="<?= e((string) $ticket['id']) ?>">

        <label>
            Ticket name
            <input type="text" name="name" value="<?= e($ticket['name']) ?>" required>
        </label>

        <label>
            Price
            <input type="number" step="0.01" name="price" value="<?= e((string) $ticket['price']) ?>" required>
        </label>

        <label>
            Quantity total
            <input type="number" name="quantity_total" value="<?= e((string) $ticket['quantity_total']) ?>" required>
        </label>

        <button type="submit" class="button button-primary">Update ticket</button>
    </form>

    <p style="margin-top:1rem;">
        Back to <a href="/events/edit?id=<?= e((string) $ticket['event_id']) ?>">event editor</a>
    </p>
</section>
