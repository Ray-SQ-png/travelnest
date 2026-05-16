<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

/* =========================
   DELETE USER (SAFE)
========================= */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    // Prevent deleting yourself
    if ($id == $_SESSION['user_id']) {
        echo "<div class='card'>⚠️ You cannot delete your own account</div>";
    } else {

        // Check reservations
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ?");
        $stmt->execute([$id]);
        $resCount = $stmt->fetchColumn();

        // Check reviews
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ?");
        $stmt->execute([$id]);
        $revCount = $stmt->fetchColumn();

        if ($resCount > 0 || $revCount > 0) {
            echo "<div class='card'>⚠️ Cannot delete: user has related data (reservations or reviews)</div>";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            echo "<div class='card'>User deleted 🗑️</div>";
        }
    }
}

/* =========================
   FETCH USERS
========================= */
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<h1>Users Management</h1>

<?php foreach ($users as $u): ?>
    <div class="card">
        <h3><?= htmlspecialchars($u['nom']) ?></h3>

        <p>Email: <?= htmlspecialchars($u['email']) ?></p>
        <p>Role: <?= $u['role'] ?></p>

        <?php if ($u['id'] != $_SESSION['user_id']): ?>
            <a class="btn btn-danger"
               href="users.php?delete=<?= $u['id'] ?>"
               onclick="return confirm('Delete this user?')">
               Delete
            </a>
        <?php else: ?>
            <p><em>This is you</em></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>