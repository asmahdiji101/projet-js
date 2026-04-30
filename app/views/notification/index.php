<section class="auth-card">
    <h1>Notifications</h1>

    <?php if (empty($notifications)): ?>
        <p>No notifications.</p>
    <?php else: ?>
        <div class="cards">
            <?php foreach ($notifications as $n): ?>
                <article class="card" style="background: <?= $n['is_read'] ? '#fff' : '#e8f0ff' ?>;">
                    <h3><?= e($n['title']) ?></h3>
                    <p><?= e($n['message']) ?></p>
                    <small><?= e($n['created_at']) ?></small>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
