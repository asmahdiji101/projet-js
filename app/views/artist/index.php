<section>
    <div class="section-heading">
        <span>Artists</span>
        <h2>All Artists</h2>
    </div>

    <div class="cards">
        <?php foreach ($artists as $artist): ?>
            <article class="card">
                <?php if (!empty($artist['user_email'])): ?>
                    <span class="feed-pill">Artist account</span>
                <?php endif; ?>
                <h3><?= e($artist['name']) ?></h3>
                <?php if (!empty($artist['image_path'])): ?>
                    <img src="<?= e($artist['image_path']) ?>" alt="<?= e($artist['name']) ?>" style="max-width:100%;border-radius:8px;">
                <?php endif; ?>
                <p><?= e($artist['description']) ?></p>
                <?php if (!empty($artist['user_email'])): ?>
                    <p><small>Linked user: <?= e($artist['user_full_name'] ?? $artist['name']) ?> · <?= e($artist['user_email']) ?></small></p>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                    <div class="hero-actions" style="margin-top:0.5rem;">
                        <a class="button button-secondary" href="/artists/edit?id=<?= e((string) $artist['id']) ?>">Edit</a>
                        <form method="post" action="/artists/delete" style="display:inline-block;margin-left:0.5rem;">
                            <input type="hidden" name="id" value="<?= e((string) $artist['id']) ?>">
                            <button class="button button-secondary" type="submit">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (is_admin()): ?>
        <a class="button button-primary" href="/artists/create">Create artist</a>
    <?php endif; ?>
</section>
