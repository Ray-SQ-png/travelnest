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

<h1>Admin Dashboard</h1>

<div class="grid">
    <div class="stat">Users<br><strong><?= $users ?></strong></div>
    <div class="stat">Services<br><strong><?= $services ?></strong></div>
    <div class="stat">Reservations<br><strong><?= $reservations ?></strong></div>
    <div class="stat">Revenue<br><strong><?= $revenue ?? 0 ?> €</strong></div>
    <div class="stat">Reviews<br><strong><?= $reviews ?></strong></div>
</div>

<div class="card">
    <h2>Revenue Analytics</h2>
    <canvas id="chart"></canvas>
</div>

<a class="btn" href="services.php">Manage Services</a>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const days = <?= json_encode($days) ?>;
const revenues = <?= json_encode($revenues) ?>;

new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: days,
        datasets: [{
            label: 'Revenue (€)',
            data: revenues,
            borderWidth: 2,
            tension: 0.3
        }]
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>