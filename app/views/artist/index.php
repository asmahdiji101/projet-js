<section>
    <div class="section-heading">
        <span>Artists</span>
        <h2>All Artists</h2>
    </div>

    <div class="cards">
        <?php foreach ($artists as $artist): ?>
            <article class="card">
                <h3><?= e($artist['name']) ?></h3>
                <?php if (!empty($artist['image_path'])): ?>
                    <img src="<?= e($artist['image_path']) ?>" alt="<?= e($artist['name']) ?>" style="max-width:100%;border-radius:8px;">
                <?php endif; ?>
                <p><?= e($artist['description']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (is_admin()): ?>
        <a class="button button-primary" href="/artists/create">Create artist</a>
    <?php endif; ?>
</section>
