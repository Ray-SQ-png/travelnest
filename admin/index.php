<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$services = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$reservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

$revenue = $pdo->query("
    SELECT SUM(montant_total)
    FROM reservations
    WHERE status = 'paid'
")->fetchColumn();

$stmt = $pdo->query("
    SELECT DATE(date_debut) as day, SUM(montant_total) as total
    FROM reservations
    WHERE status = 'paid'
    GROUP BY day
    ORDER BY day ASC
");

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$days = [];
$revenues = [];

foreach ($data as $row) {
    $days[] = $row['day'];
    $revenues[] = $row['total'];
}
?>

<h1>📊 Admin Dashboard</h1>
<p style="color:#6b7280;">Manage TravelNest platform activity</p>

<!-- QUICK STATS -->
<div class="grid">

    <div class="stat">
        👤<br>
        Users<br>
        <strong><?= $users ?></strong>
    </div>

    <div class="stat">
        🛎<br>
        Services<br>
        <strong><?= $services ?></strong>
    </div>

    <div class="stat">
        📅<br>
        Reservations<br>
        <strong><?= $reservations ?></strong>
    </div>

    <div class="stat">
        💰<br>
        Revenue<br>
        <strong><?= $revenue ?? 0 ?> €</strong>
    </div>

    <div class="stat">
        ⭐<br>
        Reviews<br>
        <strong><?= $reviews ?></strong>
    </div>

</div>

<!-- QUICK ACTIONS -->
<div class="card">
    <h2>⚡ Quick Actions</h2>

    <div class="grid">

        <a class="btn" href="services.php">🛎 Manage Services</a>

        <a class="btn" href="users.php">👤 Manage Users</a>

        <a class="btn" href="reservations.php">📅 Reservations</a>

        <a class="btn" href="reviews.php">⭐ Reviews</a>

    </div>
</div>

<!-- CHART -->
<div class="card">
    <h2>📈 Revenue Analytics</h2>

    <?php if (count($days) > 0): ?>
        <canvas id="chart"></canvas>
    <?php else: ?>
        <p style="color:#6b7280;">No revenue data available yet.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const days = <?= json_encode($days) ?>;
const revenues = <?= json_encode($revenues) ?>;

if (days.length > 0) {
    new Chart(document.getElementById('chart'), {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                label: 'Revenue (€)',
                data: revenues,
                borderWidth: 3,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>