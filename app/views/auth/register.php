<section class="auth-card">
    <h1>Inscription</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/register" class="auth-form" enctype="multipart/form-data">
        <label>
            Nom complet
            <input type="text" name="full_name" required>
        </label>

        <label>
            Email
            <input type="email" name="email" required>
        </label>

        <label>
            Mot de passe
            <input type="password" name="password" required>
        </label>

        <label>
            Confirmer le mot de passe
            <input type="password" name="confirm_password" required>
        </label>

        <fieldset style="border:1px solid #ccc;padding:1rem;border-radius:4px;margin:1rem 0;">
            <legend>Type de compte</legend>
            <label>
                <input type="radio" name="account_type" value="participant" checked> 
                Participant (book events, receive notifications)
            </label>
            <br>
            <label>
                <input type="radio" name="account_type" value="artist"> 
                Artist (create events for approval)
            </label>
        </fieldset>

        <label id="profile-picture-label">
            Photo de profil <span id="profile-picture-required"></span>
            <input type="file" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp">
            <small>JPG, PNG, GIF, or WebP. Required for artists, optional for participants.</small>
        </label>

        <div class="form-actions">
            <button type="submit" class="button button-primary">Créer le compte</button>
            <a href="/login" class="button button-secondary">Se connecter</a>
        </div>
    </form>

    <script>
        const participantRadio = document.querySelector('input[value="participant"]');
        const artistRadio = document.querySelector('input[value="artist"]');
        const profilePictureInput = document.querySelector('input[name="profile_picture"]');
        const requiredSpan = document.getElementById('profile-picture-required');

        function updateProfilePictureRequirement() {
            if (artistRadio.checked) {
                profilePictureInput.required = true;
                requiredSpan.textContent = '(required)';
            } else {
                profilePictureInput.required = false;
                requiredSpan.textContent = '(optional)';
            }
        }

        participantRadio.addEventListener('change', updateProfilePictureRequirement);
        artistRadio.addEventListener('change', updateProfilePictureRequirement);
        updateProfilePictureRequirement();
    </script>
</section>
