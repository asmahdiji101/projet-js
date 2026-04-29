<section class="auth-card">
    <span class="eyebrow">Cart</span>
    <h1>Your selection</h1>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error"><?= e($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?= e($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (empty($tickets)): ?>
        <p>Your cart is empty. Go to <a href="/events">events</a> and add tickets.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($tickets as $ticket): ?>
                <article class="cart-item">
                    <div>
                        <h3><?= e($ticket['name']) ?></h3>
                        <p>Quantity: <?= e((string) $ticket['quantity']) ?></p>
                        <p>Unit price: <?= e(number_format((float) $ticket['price'], 2)) ?> EUR</p>
                    </div>
                    <div>
                        <strong><?= e(number_format((float) $ticket['line_total'], 2)) ?> EUR</strong>
                        <form method="post" action="/cart/remove">
                            <input type="hidden" name="ticket_id" value="<?= e((string) $ticket['id']) ?>">
                            <button class="button button-secondary" type="submit">Remove</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <strong>Total: <?= e(number_format((float) $total, 2)) ?> EUR</strong>
            <form method="post" action="/checkout">
                <button class="button button-primary" type="submit">Confirm booking</button>
            </form>
        </div>
    <?php endif; ?>
</section>
