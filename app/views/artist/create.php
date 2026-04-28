<section class="auth-card">
    <h1>Créer un artiste</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/artists/store" enctype="multipart/form-data" class="auth-form">
        <label>
            Nom de l'artiste
            <input type="text" name="name" required>
        </label>

        <label>
            Description
            <textarea name="description" required rows="4"></textarea>
        </label>

        <label>
            Image
            <input type="file" name="image" accept="image/*">
        </label>

        <button type="submit" class="button button-primary">Enregistrer</button>
    </form>
</section>
