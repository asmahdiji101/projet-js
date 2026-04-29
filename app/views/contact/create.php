<section class="auth-card">
    <h1>Contact us</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" action="/contact/store" class="auth-form">
        <label>
            Subject
            <input type="text" name="subject" required>
        </label>

        <label>
            Message
            <textarea name="message" rows="6" required></textarea>
        </label>

        <button type="submit" class="button button-primary">Send message</button>
    </form>
</section>
