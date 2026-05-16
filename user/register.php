<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $hashed]);

    echo "<div class='card'>User created successfully</div>";
}
?>

<div class="card">
    <h2>Register</h2>

    <form method="POST">
        <input name="nom" placeholder="Nom" required>
        <input name="prenom" placeholder="Prenom" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <button class="btn">Register</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>