<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // CHECK EXISTING EMAIL
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        $error = "This email is already registered ❌";
    } else {

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO users (nom, prenom, email, password)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$nom, $prenom, $email, $hashed]);

        $success = "User created successfully ✅";
    }
}
?>

<div class="grid" style="align-items:center; gap:30px;">

    <!-- LEFT SIDE -->
    <div class="card" style="
        background:
        linear-gradient(rgba(15,23,42,0.75), rgba(15,23,42,0.75)),
        url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800');
        background-size: cover;
        background-position: center;
        color: white;
        min-height: 500px;
        display:flex;
        flex-direction:column;
        justify-content:center;
    ">
        <h1 style="font-size:38px;">🌍 Join TravelNest</h1>

        <p style="font-size:18px; line-height:1.6;">
            Create your account and start planning unforgettable journeys.
        </p>

        <div style="margin-top:20px;">
            <p>🧳 Discover services</p>
            <p>📅 Reserve travel experiences</p>
            <p>⭐ Leave reviews</p>
            <p>💳 Manage bookings easily</p>
        </div>
    </div>

    <!-- RIGHT SIDE REGISTER -->
    <div class="card">
        <h2 style="font-size:30px;">📝 Register</h2>
        <p style="color:#6b7280;">Create your TravelNest account</p>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Last Name</label>
            <input name="nom" placeholder="Enter your last name" required>

            <label>First Name</label>
            <input name="prenom" placeholder="Enter your first name" required>

            <label>Email</label>
            <input name="email" type="email" placeholder="Enter your email" required>

            <label>Password</label>
            <input name="password" type="password" placeholder="Create a password" required>

            <button class="btn" style="width:100%; margin-top:10px;">
                Register
            </button>
        </form>

        <p style="margin-top:18px; text-align:center;">
            Already have an account?
            <a href="login.php" style="color:#2563eb; font-weight:600;">
                Login here
            </a>
        </p>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>