<?php
require_once '../includes/header.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT r.*, s.titre 
    FROM reservations r
    JOIN services s ON r.service_id = s.id
    WHERE r.id = ? AND r.user_id = ?
");

$stmt->execute([$id, $_SESSION['user_id']]);
$res = $stmt->fetch();

if (!$res) {
    die("Reservation not found");
}

/* GET EMAIL */
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (isset($_POST['pay'])) {

    $stmt = $pdo->prepare("
        UPDATE reservations 
        SET status = 'paid'
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    /* =========================
       EMAIL - PAYMENT CONFIRMATION
    ========================= */

    $subject = "Payment Successful - TravelNest";

    $message = "
        <h2>Payment Confirmed 💳</h2>
        <p>Service: <strong>{$res['titre']}</strong></p>
        <p>Amount Paid: {$res['montant_total']} €</p>
        <p>Status: Paid ✔</p>
    ";

    sendMail($user['email'], $subject, $message);

    $res['status'] = 'paid';

    echo "<div class='card'>Payment successful + email sent 📧</div>";
}
?>

<div class="card">
    <h2>Payment</h2>

    <p><strong><?= htmlspecialchars($res['titre']) ?></strong></p>
    <p>Total: <?= $res['montant_total'] ?> €</p>
    <p>Status: <strong><?= $res['status'] ?></strong></p>

    <?php if ($res['status'] === 'pending'): ?>
        <form method="POST">
            <button class="btn" name="pay">Pay Now</button>
        </form>
    <?php else: ?>
        <p>Already paid ✔</p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>