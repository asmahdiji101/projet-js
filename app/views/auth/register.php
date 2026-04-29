<section class="auth-card">
    <h1>Inscription</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/register" class="auth-form">
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

        <button type="submit" class="button button-primary">Créer le compte</button>
    </form>
</section>
