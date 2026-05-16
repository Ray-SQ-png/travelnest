<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

/* =========================
   DELETE RESERVATION
========================= */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$id]);

    echo "<div class='card'>Reservation deleted 🗑️</div>";
}

/* =========================
   UPDATE STATUS
========================= */
if (isset($_POST['update_status'])) {

    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("
        UPDATE reservations 
        SET status = ?
        WHERE id = ?
    ");
    $stmt->execute([$status, $id]);

    echo "<div class='card'>Status updated ✅</div>";
}

/* =========================
   FETCH RESERVATIONS
========================= */
$stmt = $pdo->query("
    SELECT r.*, u.nom, u.email, s.titre
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN services s ON r.service_id = s.id
    ORDER BY r.id DESC
");

$reservations = $stmt->fetchAll();
?>

<h1>Reservations Management</h1>

<?php foreach ($reservations as $r): ?>
    <div class="card">

        <h3><?= htmlspecialchars($r['titre']) ?></h3>

        <p><strong>User:</strong> <?= htmlspecialchars($r['nom']) ?> (<?= $r['email'] ?>)</p>

        <p>
            <strong>Dates:</strong>
            <?= $r['date_debut'] ?> → <?= $r['date_fin'] ?>
        </p>

        <p><strong>Total:</strong> <?= $r['montant_total'] ?> €</p>

        <p><strong>Status:</strong> <?= $r['status'] ?></p>

        <!-- UPDATE STATUS -->
        <form method="POST">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">

            <select name="status">
                <option value="pending" <?= $r['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="paid" <?= $r['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="cancelled" <?= $r['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>

            <button class="btn" name="update_status">Update</button>
        </form>

        <br>

        <!-- DELETE -->
        <a class="btn btn-danger"
           href="reservations.php?delete=<?= $r['id'] ?>"
           onclick="return confirm('Delete this reservation?')">
           Delete
        </a>

    </div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>