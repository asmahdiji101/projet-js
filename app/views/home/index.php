<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">Premium event platform</span>
        <h1>Book, manage and experience events with a modern ticketing flow.</h1>
        <p>
            A dynamic PHP + JavaScript project focused on immersive events, artist management,
            ticket booking and admin analytics.
        </p>
        <div class="hero-actions">
            <a class="button button-primary" href="#featured">Explore events</a>
            <a class="button button-secondary" href="#">Admin demo</a>
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
        <span>Featured concepts</span>
        <h2>Events designed for the project scope</h2>
    </div>

    <div class="cards">
        <?php foreach ($featuredEvents as $event): ?>
            <article class="card">
                <h3><?= e($event['title']) ?></h3>
                <p><?= e($event['subtitle']) ?></p>
                <strong><?= e($event['price']) ?></strong>
            </article>
        <?php endforeach; ?>
    </div>
</section>
