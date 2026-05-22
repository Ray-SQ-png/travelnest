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

<h1>🌍 Explore Services</h1>
<p style="color:#6b7280;">Find your next travel experience</p>

<!-- SEARCH CARD -->
<div class="card">
    <form method="GET" class="grid">

        <div>
            <label>Search</label>
            <input name="search"
                   placeholder="Search services..."
                   value="<?= htmlspecialchars($search) ?>">
        </div>

        <div>
            <label>Max Price (€)</label>
            <input name="max_price"
                   type="number"
                   placeholder="Max price"
                   value="<?= htmlspecialchars($maxPrice) ?>">
        </div>

        <div style="display:flex; align-items:end;">
            <button class="btn" style="width:100%;">
                🔍 Search
            </button>
        </div>

    </form>
</div>

<?php if (count($services) > 0): ?>

<div class="grid">

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

        <?php if ($s['image']): ?>
            <img src="/assets/uploads/services/<?= $s['image'] ?>"
                 style="width:100%; height:220px; object-fit:cover; border-radius:14px;">
        <?php else: ?>
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e"
                 style="width:100%; height:220px; object-fit:cover; border-radius:14px;">
        <?php endif; ?>

        <h3 style="margin-top:15px;">
            <?= htmlspecialchars($s['titre']) ?>
        </h3>

        <!-- RATING -->
        <?php if ($rating['total'] > 0): ?>
            <p>
                <span class="badge badge-warning">
                    <?= str_repeat("⭐", round($rating['avg_rating'])) ?>
                </span>
                (<?= $rating['total'] ?> reviews)
            </p>
        <?php else: ?>
            <p style="color:#6b7280;">No reviews yet</p>
        <?php endif; ?>

        <!-- PRICE -->
        <p>
            <span class="badge badge-success">
                <?= $s['prix'] ?> € / day
            </span>
        </p>

        <a class="btn"
           href="reservation.php?id=<?= $s['id'] ?>"
           style="width:100%; text-align:center; margin-top:10px;">
            📅 Book Now
        </a>
    </div>

<?php endforeach; ?>

</div>

<?php else: ?>

<div class="card" style="text-align:center;">
    <h3>😕 No services found</h3>
    <p>Try changing your search filters.</p>
</div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>