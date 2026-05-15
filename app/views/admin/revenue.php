<section>
    <div class="section-heading">
        <span>Admin</span>
        <h2>Revenue analytics</h2>
    </div>

    <div class="cards admin-stats">
        <article class="card">
            <h3>Total revenue</h3>
            <strong><?= e(number_format((float) $totalRevenue, 2)) ?> EUR</strong>
        </article>
        <article class="card">
            <h3>Total bookings</h3>
            <strong><?= e((string) $totalBookings) ?></strong>
        </article>
    </div>

    <div class="section-heading" style="margin-top:2rem;">
        <span>By category</span>
        <h2>Revenue per event type</h2>
    </div>

    <div class="cart-items">
        <?php foreach ($byCategory as $row): ?>
            <article class="cart-item admin-message-item">
                <div>
                    <h3><?= e(ucfirst((string) $row['category'])) ?></h3>
                    <p><?= e((string) $row['bookings_count']) ?> bookings</p>
                </div>
                <strong><?= e(number_format((float) $row['revenue'], 2)) ?> EUR</strong>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="section-heading" style="margin-top:2rem;">
        <span>By city</span>
        <h2>Revenue per location</h2>
    </div>

    <div class="cart-items">
        <?php foreach ($byLocation as $row): ?>
            <article class="cart-item admin-message-item">
                <div>
                    <h3><?= e((string) $row['location']) ?></h3>
                    <p><?= e((string) $row['bookings_count']) ?> bookings</p>
                </div>
                <strong><?= e(number_format((float) $row['revenue'], 2)) ?> EUR</strong>
            </article>
        <?php endforeach; ?>
    </div>
</section>