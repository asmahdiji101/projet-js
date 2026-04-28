<section class="auth-card">
    <h1>Connexion</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/login" class="auth-form">
        <label>
            Email
            <input type="email" name="email" required>
        </label>

        <label>
            Mot de passe
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="button button-primary">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="/register">Créer un compte</a></p>
</section>
