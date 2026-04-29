<section>
    <div class="section-heading">
        <span>Admin</span>
        <h2>Pending Event Approvals</h2>
    </div>

    <?php if (empty($events)): ?>
        <div class="auth-card">
            <p>No pending events.</p>
        </div>
    <?php else: ?>
        <div class="cards">
            <?php foreach ($events as $event): ?>
                <article class="card">
                    <span class="eyebrow"><?= e($event['artist_name'] ?? 'Unknown') ?></span>
                    <h3><?= e($event['title']) ?></h3>
                    <?php if (!empty($event['image_path'])): ?>
                        <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" style="max-width:100%;border-radius:8px;">
                    <?php endif; ?>
                    <p><?= e($event['description']) ?></p>
                    <p><strong>Location:</strong> <?= e($event['location']) ?></p>
                    <p><strong>Date:</strong> <?= e($event['event_date']) ?></p>
                    <p><strong>Status:</strong> <?= e(ucfirst($event['approval_status'])) ?></p>
                    <p><small>Submitted: <?= e($event['created_at']) ?></small></p>

                    <div class="hero-actions" style="margin-top:1rem;">
                        <form method="post" action="/admin/event/approve" style="display:inline-block;">
                            <input type="hidden" name="id" value="<?= e((string) $event['id']) ?>">
                            <button class="button button-primary" type="submit">Approve (10% markup applied)</button>
                        </form>
                        <form method="post" action="/admin/event/reject" style="display:inline-block;margin-left:0.5rem;">
                            <input type="hidden" name="id" value="<?= e((string) $event['id']) ?>">
                            <button class="button button-secondary" type="submit">Reject</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
