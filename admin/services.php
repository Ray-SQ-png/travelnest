<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

/* =========================
   DELETE SERVICE (SAFE)
========================= */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    // Check if service is used in reservations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE service_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "<div class='card'>⚠️ Cannot delete: this service has reservations</div>";
    } else {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);

        echo "<div class='card'>Service deleted 🗑️</div>";
    }
}

/* =========================
   ADD SERVICE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = $_POST['titre'];
    $prix  = $_POST['prix'];

    $image = "";

    if (!empty($_FILES['image']['name'])) {

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . "." . $ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/uploads/services/" . $image
        );
    }

    $stmt = $pdo->prepare("INSERT INTO services (titre, prix, image) VALUES (?, ?, ?)");
    $stmt->execute([$titre, $prix, $image]);

    echo "<div class='card'>Service added ✅</div>";
}

/* =========================
   FETCH SERVICES
========================= */
$services = $pdo->query("SELECT * FROM services")->fetchAll();
?>

<h2>Manage Services</h2>

<!-- ADD SERVICE FORM -->
<div class="card">
    <h3>Add Service</h3>

    <form method="POST" enctype="multipart/form-data">
        <input name="titre" placeholder="Title" required>
        <input name="prix" placeholder="Price" required>
        <input type="file" name="image">
        <button class="btn">Add</button>
    </form>
</div>

<!-- SERVICES LIST -->
<?php foreach ($services as $s): ?>
    <div class="card">
        <strong><?= htmlspecialchars($s['titre']) ?></strong><br>
        <?= $s['prix'] ?> €

        <?php if ($s['image']): ?>
            <br>
            <img src="/assets/uploads/services/<?= $s['image'] ?>" width="120">
        <?php endif; ?>

        <br><br>

        <!-- EDIT -->
        <a class="btn" href="edit_service.php?id=<?= $s['id'] ?>">Edit</a>

        <!-- DELETE -->
        <a class="btn" style="background:red;"
           href="services.php?delete=<?= $s['id'] ?>"
           onclick="return confirm('Delete this service?')">
           Delete
        </a>
    </div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>