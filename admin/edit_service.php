<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

if (!isset($_GET['id'])) {
    die("No service selected");
}

$id = $_GET['id'];

/* =========================
   GET SERVICE
========================= */
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    die("Service not found");
}

/* =========================
   UPDATE SERVICE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = $_POST['titre'];
    $prix  = $_POST['prix'];

    $image = $service['image'];

    if (!empty($_FILES['image']['name'])) {

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . "." . $ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/uploads/services/" . $image
        );
    }

    $stmt = $pdo->prepare("
        UPDATE services 
        SET titre = ?, prix = ?, image = ?
        WHERE id = ?
    ");

    $stmt->execute([$titre, $prix, $image, $id]);

    echo "<div class='card'>Service updated ✅</div>";

    // refresh updated data
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch();
}
?>

<div class="card">
    <h2>Edit Service</h2>

    <form method="POST" enctype="multipart/form-data">
        <input name="titre" value="<?= htmlspecialchars($service['titre']) ?>" required>
        <input name="prix" value="<?= $service['prix'] ?>" required>
        <input type="file" name="image">

        <button class="btn">Update</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>