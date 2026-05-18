<section class="auth-card">
    <span class="eyebrow">Account</span>
    <h1>Modifier le compte</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php $avatar = avatar_url($user['profile_picture_path'] ?? null); ?>
    <div class="account-hero">
        <img src="<?= e($avatar) ?>" alt="avatar" class="account-avatar" onerror="this.src='/images/default-avatar.svg'">
        <div>
            <p class="account-note">Change your name, email, password and profile picture here.</p>
            <?php if (($user['role'] ?? 'user') === 'artist' && !empty($artist)): ?>
                <p class="account-note">This account is linked to the artist profile shown in admin.</p>
            <?php endif; ?>
        </div>
    </div>

    <form method="post" action="/account/update" class="auth-form" enctype="multipart/form-data">
        <label>
            Nom complet
            <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required>
        </label>

        <label>
            Email
            <input type="email" name="email" value="<?= e($user['email']) ?>" required>
        </label>

        <label>
            Nouveau mot de passe
            <input type="password" name="new_password" placeholder="Laisser vide pour conserver le mot de passe">
        </label>

        <label>
            Confirmer le mot de passe
            <input type="password" name="confirm_password" placeholder="Repeat new password">
        </label>

        <label>
            Photo de profil
            <input type="file" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp">
        </label>

        <button type="submit" class="button button-primary">Enregistrer les modifications</button>
    </form>
</section>