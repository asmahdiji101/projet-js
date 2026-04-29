<section>
    <div class="section-heading">
        <span>Admin</span>
        <h2>Contact Messages</h2>
    </div>

    <?php if (empty($messages)): ?>
        <div class="auth-card">
            <p>No messages yet.</p>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($messages as $msg): ?>
                <article class="cart-item">
                    <div>
                        <h3><?= e($msg['subject']) ?></h3>
                        <p><strong>From:</strong> <?= e($msg['full_name']) ?> (<?= e($msg['email']) ?>) — <?= e($msg['sender_type']) ?></p>
                        <p><?= e(substr($msg['message'], 0, 100)) ?>...</p>
                        <small><?= e($msg['created_at']) ?></small>
                        <?php if ($msg['status'] === 'replied'): ?>
                            <p style="color:green;"><strong>✓ Replied</strong></p>
                        <?php else: ?>
                            <p style="color:orange;"><strong>Pending</strong></p>
                        <?php endif; ?>
                    </div>
                    <div class="hero-actions" style="margin-top:0;">
                        <a class="button button-primary" href="/admin/message?id=<?= e((string) $msg['id']) ?>">View & Reply</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
