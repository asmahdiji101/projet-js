<div class="cart-shell">
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error"><?= e($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?= e($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <section class="cart-content">
        <!-- Cart Items Sidebar -->
        <aside class="cart-sidebar">
            <div class="cart-card">
                <h2>Votre panier</h2>

                <?php if (empty($tickets)): ?>
                    <div class="empty-cart">
                        <p>Votre panier est vide</p>
                        <p class="small-text">Ajoutez des billets depuis la liste des événements à gauche</p>
                    </div>
                <?php else: ?>
                    <div class="cart-items-list">
                        <?php foreach ($tickets as $ticket): ?>
                            <article class="cart-item-row">
                                <div class="item-info">
                                    <h4><?= e($ticket['event_title'] ?? $ticket['name']) ?></h4>
                                    <p class="item-ticket"><?= e($ticket['ticket_name'] ?? $ticket['name']) ?></p>
                                    <p class="item-price">
                                        <?= (int)$ticket['quantity'] ?> × <?= e(number_format((float)$ticket['price'], 2)) ?> TND = <strong><?= e(number_format((float)$ticket['line_total'], 2)) ?> TND</strong>
                                    </p>
                                </div>
                                <form method="post" action="/cart/remove" class="item-remove">
                                    <input type="hidden" name="ticket_id" value="<?= e((string)($ticket['ticket_id'] ?? $ticket['id'])) ?>">
                                    <button type="submit" class="button button-secondary button-tiny">✕</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-total">
                        <div class="total-row">
                            <span>Total:</span>
                            <strong><?= e(number_format((float)$total, 2)) ?> TND</strong>
                        </div>
                        <form method="post" action="/checkout">
                            <button class="button button-primary button-block" type="submit">Confirmer la réservation</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </section>

</section>
