<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$error = "";

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

        if (isset($user['nom'])) {
            $_SESSION['user_name'] = $user['nom'];
        }

        if ($user['role'] === 'admin') {
            header("Location: /admin/index.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;

    } else {
        $error = "Invalid credentials ❌";
    }
}
?>

<div class="grid" style="align-items:center; gap:30px;">

    <!-- LEFT SIDE -->
    <div class="card" style="
        background:
        linear-gradient(rgba(15,23,42,0.75), rgba(15,23,42,0.75)),
        url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e');
        background-size: cover;
        background-position: center;
        color: white;
        min-height: 420px;
        display:flex;
        flex-direction:column;
        justify-content:center;
    ">
        <h1 style="font-size:38px;">🌍 Welcome to TravelNest</h1>

        <p style="font-size:18px; line-height:1.6;">
            Discover destinations, reserve services, and manage your travel experience in one place.
        </p>

        <div style="margin-top:20px;">
            <p>✈ Book services easily</p>
            <p>📅 Manage reservations</p>
            <p>💳 Secure payment flow</p>
            <p>⭐ Review your experience</p>
        </div>
    </div>

    <!-- RIGHT SIDE LOGIN -->
    <div class="card">
        <h2 style="font-size:30px;">🔐 Login</h2>
        <p style="color:#6b7280;">Access your TravelNest account</p>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Email</label>
            <input name="email" type="email" placeholder="Enter your email" required>

            <label>Password</label>
            <input name="password" type="password" placeholder="Enter your password" required>

            <button class="btn" style="width:100%; margin-top:10px;">
                Login
            </button>
        </form>

        <p style="margin-top:18px; text-align:center;">
            Don’t have an account?
            <a href="register.php" style="color:#2563eb; font-weight:600;">
                Register here
            </a>
        </p>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>