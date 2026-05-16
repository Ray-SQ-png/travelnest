<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$search = $_GET['search'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

$query = "SELECT * FROM services WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND titre LIKE ?";
    $params[] = "%$search%";
}

if ($maxPrice) {
    $query .= " AND prix <= ?";
    $params[] = $maxPrice;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$services = $stmt->fetchAll();
?>

<h1>Explore Services</h1>

<div class="card">
    <form method="GET">
        <input name="search" placeholder="Search services...">
        <input name="max_price" type="number" placeholder="Max price">
        <button class="btn">Search</button>
    </form>
</div>

<?php foreach ($services as $s): ?>

<?php
// GET AVERAGE RATING
$stmt = $pdo->prepare("
    SELECT AVG(rating) as avg_rating, COUNT(*) as total
    FROM reviews
    WHERE service_id = ?
");
$stmt->execute([$s['id']]);
$rating = $stmt->fetch();
?>

    <div class="card">
        <h3><?= htmlspecialchars($s['titre']) ?></h3>

        <?php if ($rating['total'] > 0): ?>
            <p>
                Rating:
                <?= str_repeat("⭐", round($rating['avg_rating'])) ?>
                (<?= $rating['total'] ?> reviews)
            </p>
        <?php else: ?>
            <p>No reviews yet</p>
        <?php endif; ?>

        <?php if ($s['image']): ?>
            <img src="/assets/uploads/services/<?= $s['image'] ?>"
                 style="width:100%; max-width:320px; border-radius:12px;">
        <?php endif; ?>

        <p><strong><?= $s['prix'] ?> € / day</strong></p>

        <a class="btn" href="reservation.php?id=<?= $s['id'] ?>">
            Book Now
        </a>
    </div>

<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>