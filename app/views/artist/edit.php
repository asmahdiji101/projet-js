<section class="auth-card">
    <span class="eyebrow">Admin</span>
    <h1>Edit artist</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/artists/update" enctype="multipart/form-data" class="auth-form">
        <input type="hidden" name="id" value="<?= e((string) $artist['id']) ?>">

        <label>
            Name
            <input type="text" name="name" value="<?= e($artist['name']) ?>" required>
        </label>

        <label>
            Description
            <textarea name="description" rows="4" required><?= e($artist['description']) ?></textarea>
        </label>

        <?php if (!empty($artist['image_path'])): ?>
            <label>
                Current image
                <img src="<?= e($artist['image_path']) ?>" alt="<?= e($artist['name']) ?>" style="max-width:100%;border-radius:8px;">
            </label>
        <?php endif; ?>

        <label>
            Replace image
            <input type="file" name="image" accept="image/*">
        </label>

        <button type="submit" class="button button-primary">Save artist</button>
    </form>
</section>
