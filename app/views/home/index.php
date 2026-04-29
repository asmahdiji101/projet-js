<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">Premium event platform</span>
        <h1>Book, manage and experience events with a modern ticketing flow.</h1>
        <p>
            A dynamic PHP + JavaScript project focused on immersive events, artist management,
            ticket booking and admin analytics.
        </p>
        <div class="hero-actions">
            <a class="button button-primary" href="/events">Explore events</a>
            <?php if (is_admin()): ?>
                <a class="button button-secondary" href="#">Admin demo</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="hero-panel">
        <div class="stats-card">
            <span>Available now</span>
            <strong>3 event spaces</strong>
            <small>Live showcases, workshops and VIP access</small>
        </div>
    </div>
</section>

<section class="featured" id="featured">
    <div class="section-heading">
        <span>Available now</span>
        <h2>All events on NeonPass</h2>
    </div>

    <div class="cards">
        <?php if (empty($events)): ?>
            <div class="auth-card"><p>No events available yet.</p></div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <article class="card">
                    <h3><?= e($event['title']) ?></h3>
                    <?php if (!empty($event['image_path'])): ?>
                        <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" style="max-width:100%;border-radius:8px;">
                    <?php endif; ?>
                    <p><?= e($event['description']) ?></p>
                    <small><?= e($event['event_date']) ?> — <?= e($event['location']) ?></small>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
