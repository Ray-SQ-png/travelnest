<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: /admin/index.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;

    } else {
        echo "<div class='card'>Invalid credentials</div>";
    }
}
?>

<div class="card">
    <h2>Login</h2>

    <form method="POST">
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <button class="btn">Login</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>