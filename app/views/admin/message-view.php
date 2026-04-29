<section class="auth-card">
    <span class="eyebrow">Admin</span>
    <h1>Message from <?= e($message['full_name']) ?></h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div style="background:#f5f5f5;padding:1rem;border-radius:4px;margin:1rem 0;">
        <p><strong>From:</strong> <?= e($message['full_name']) ?> (<?= e($message['email']) ?>)</p>
        <p><strong>Type:</strong> <?= e(ucfirst($message['sender_type'])) ?></p>
        <p><strong>Subject:</strong> <?= e($message['subject']) ?></p>
        <p><strong>Date:</strong> <?= e($message['created_at']) ?></p>
        <hr>
        <p><?= nl2br(e($message['message'])) ?></p>
    </div>

    <?php if (!empty($message['admin_reply'])): ?>
        <div style="background:#e8f5e9;padding:1rem;border-radius:4px;margin:1rem 0;">
            <p><strong>Your reply:</strong></p>
            <p><?= nl2br(e($message['admin_reply'])) ?></p>
            <small><?= e($message['replied_at']) ?></small>
        </div>
    <?php else: ?>
        <form method="post" action="/admin/message/reply" class="auth-form">
            <input type="hidden" name="id" value="<?= e((string) $message['id']) ?>">

            <label>
                Your reply
                <textarea name="reply" rows="6" required></textarea>
            </label>

            <button type="submit" class="button button-primary">Send reply</button>
        </form>
    <?php endif; ?>

    <a href="/admin/messages" class="button button-secondary" style="margin-top:1rem;">Back to messages</a>
</section>
