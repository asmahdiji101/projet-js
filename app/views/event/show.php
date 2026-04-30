<section class="event-detail">
    <div class="event-header">
        <h1><?= e($event['title']) ?></h1>
        <p><?= e($event['event_date']) ?> — <?= e($event['location']) ?></p>
    </div>

    <?php if (!empty($event['image_path'])): ?>
        <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" style="max-width:100%;border-radius:8px;margin-bottom:1rem;">
    <?php endif; ?>

    <div class="event-body">
        <p><?= nl2br(e($event['description'])) ?></p>

        <h3>Tickets</h3>
        <?php if (empty($tickets)): ?>
            <p>No tickets available.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($tickets as $t): ?>
                    <li><?= e($t['name']) ?> — <?= e(number_format((float)$t['price'],2)) ?> EUR — <?= e((string)$t['quantity_total']) ?> available</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
