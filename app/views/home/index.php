<section class="home-shell">
    <aside class="home-sidebar home-sidebar-left">
        <div class="home-panel home-brand-panel">
            <div class="home-brand-row">
                <img src="/images/logo.svg" alt="AIO Events" class="home-brand-logo">
                <div>
                    <strong>AIO Events</strong>
                    <p>Discover, book, follow.</p>
                </div>
            </div>
        </div>

        <div class="home-panel home-menu-panel">
            <div class="home-menu-title">Navigation</div>
            <a class="home-menu-item is-active" href="/">Accueil</a>
            <a class="home-menu-item" href="/events">Carte</a>
            <a class="home-menu-item" href="/notifications">Notifications</a>
            <a class="home-menu-item" href="/contact">Support</a>

            <?php if (is_authenticated()): ?>
                <a class="home-menu-item" href="/dashboard">Mon compte</a>
                <a class="home-menu-item" href="/account/edit">Modifier le compte</a>

                <?php if ($_SESSION['user']['role'] === 'artist'): ?>
                    <a class="home-menu-item" href="/events/create">Créer un événement</a>
                    <a class="home-menu-item" href="/events/artist-events">Mes événements</a>
                <?php endif; ?>

                <?php if (is_admin()): ?>
                    <a class="home-menu-item" href="/events/create">Créer un événement</a>
                    <a class="home-menu-item" href="/artists">Artistes</a>
                    <a class="home-menu-item" href="/admin">Administration</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="home-panel home-download-panel">
            <div class="home-menu-title">Télécharger l'App</div>
            <p>Keep your events and tickets close at hand.</p>
            <a class="button button-primary button-block" href="/events">Open events</a>
        </div>
    </aside>

    <section class="home-center">
        <div class="home-panel home-search-panel">
            <form method="get" action="/" class="home-search-form">
                <label class="home-search-field home-search-main">
                    <span>Rechercher...</span>
                    <input type="search" name="q" value="<?= e($filters['query'] ?? '') ?>" placeholder="Search events">
                </label>
                <label class="home-search-field">
                    <span>Date</span>
                    <input type="date" name="date" value="<?= e($filters['date'] ?? '') ?>">
                </label>
                <label class="home-search-field">
                    <span>Ville</span>
                    <input type="text" name="city" value="<?= e($filters['city'] ?? '') ?>" placeholder="City">
                </label>
                <label class="home-search-field">
                    <span>Type</span>
                    <select name="category">
                        <option value="">All</option>
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= ($filters['category'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button type="submit" class="home-search-submit" aria-label="Search events">⌕</button>
            </form>
        </div>

        <div class="home-categories" aria-label="Event types">
            <?php foreach ($categories as $key => $label): ?>
                <a class="home-category-card <?= ($filters['category'] ?? '') === $key ? 'is-active' : '' ?>" href="/?category=<?= e($key) ?>">
                    <img class="home-category-icon-image" src="/images/event-types/<?= e($key) ?>.svg" alt="<?= e($label) ?>">
                    <strong><?= e($label) ?></strong>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="home-feed">
            <?php if (empty($events)): ?>
                <div class="home-empty-state">
                    <h2>No events match your filters yet.</h2>
                    <p>Try another city, date or event type.</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <a class="home-feed-card" href="/events/<?= (int) $event['id'] ?>">
                        <div class="home-feed-header">
                            <div class="home-feed-author">
                                <strong><?= e($event['artist_name'] ?? 'Event') ?></strong>
                                <span><?= e($event['location']) ?></span>
                            </div>
                            <span class="feed-pill"><?= e($categories[$event['category'] ?? 'concert'] ?? 'Event') ?></span>
                        </div>

                        <div class="home-feed-media">
                            <?php if (!empty($event['image_path'])): ?>
                                <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>">
                            <?php else: ?>
                                <div class="home-feed-placeholder">AIO</div>
                            <?php endif; ?>
                        </div>

                        <div class="home-feed-body">
                            <div class="home-feed-title-row">
                                <h3><?= e($event['title']) ?></h3>
                                <span><?= e($event['event_date']) ?></span>
                            </div>
                            <p><?= e($event['description']) ?></p>
                            <div class="home-feed-actions">
                                <span>Open details</span>
                                <strong>Book tickets</strong>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <aside class="home-sidebar home-sidebar-right">
        <div class="home-panel home-trend-panel">
            <div class="home-side-title">
                <span>↗</span>
                <strong>Tendances</strong>
            </div>

            <div class="home-side-list">
                <?php foreach ($homeTrends as $event): ?>
                    <a class="home-side-card" href="/events/<?= (int) $event['id'] ?>">
                        <strong><?= e($event['title']) ?></strong>
                        <small><?= e($event['location']) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="home-panel home-places-panel">
            <div class="home-side-title">
                <span>⌂</span>
                <strong>Lieux populaires</strong>
            </div>

            <div class="home-side-list">
                <?php foreach ($homePlaces as $place): ?>
                    <div class="home-place-card">
                        <strong><?= e($place) ?></strong>
                        <small>Popular venue</small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</section>
