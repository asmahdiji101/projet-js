<div class="page-header">
    <h1>Mes événements</h1>
    <a href="/events/artist/create" class="button button-primary">+ Créer un événement</a>
</div>

<?php if (empty($events)): ?>
    <p>Aucun événement soumis. <a href="/events/artist/create">Créer votre premier événement</a></p>
<?php else: ?>
    <div class="events-grid">
        <?php foreach ($events as $event): ?>
            <?php 
                $statusClass = 'status-' . ($event['approval_status'] ?? 'pending');
                $statusLabel = match($event['approval_status'] ?? 'pending') {
                    'approved' => 'Approuvé',
                    'rejected' => 'Rejeté',
                    'pending' => 'En attente',
                    default => 'Inconnu'
                };
            ?>
            <div class="event-card">
                <?php if ($event['image_path']): ?>
                    <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" class="event-image">
                <?php endif; ?>
                
                <div class="event-info">
                    <h3><?= e($event['title']) ?></h3>
                    <p class="event-date">📅 <?= date('d M Y H:i', strtotime($event['event_date'])) ?></p>
                    <p class="event-location">📍 <?= e($event['location']) ?></p>
                    
                    <div class="status-badge <?= $statusClass ?>">
                        <?= $statusLabel ?>
                    </div>
                    
                    <?php
                        $desc = $event['description'] ?? '';
                        $short = strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                    ?>
                    <p class="event-description"><?= e($short) ?></p>
                    
                    <div class="event-actions">
                        <a href="/events/<?= (int) $event['id'] ?>" class="button button-secondary">Voir</a>
                        <?php if (in_array($event['approval_status'], ['pending', 'rejected'], true)): ?>
                            <a href="/events/<?= (int) $event['id'] ?>/edit" class="button button-secondary">Modifier</a>
                            <form method="post" action="/events/<?= (int) $event['id'] ?>/delete" style="display:inline;">
                                <button type="submit" class="button button-danger" onclick="return confirm('Are you sure?')">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if ($event['approval_status'] === 'rejected'): ?>
                        <p class="alert alert-warning" style="margin-top:1rem;">Cette événement a été rejeté. Veuillez le modifier et réessayer.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }
    
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
