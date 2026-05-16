<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

/* =========================
   DELETE REVIEW
========================= */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$id]);

    echo "<div class='card'>Review deleted 🗑️</div>";
}

/* =========================
   FETCH REVIEWS
========================= */
$stmt = $pdo->query("
    SELECT r.*, u.nom, s.titre
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN services s ON r.service_id = s.id
    ORDER BY r.created_at DESC
");

$reviews = $stmt->fetchAll();
?>

<h1>Reviews Management</h1>

<?php foreach ($reviews as $r): ?>
    <div class="card">
        <h3><?= htmlspecialchars($r['nom']) ?></h3>

        <p><strong>Service:</strong> <?= htmlspecialchars($r['titre']) ?></p>

        <p>
            Rating:
            <?= str_repeat("⭐", $r['rating']) ?>
        </p>

        <p><?= htmlspecialchars($r['comment']) ?></p>

        <small><?= $r['created_at'] ?></small>

        <br><br>

        <a class="btn btn-danger"
           href="reviews.php?delete=<?= $r['id'] ?>"
           onclick="return confirm('Delete this review?')">
           Delete
        </a>
    </div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>