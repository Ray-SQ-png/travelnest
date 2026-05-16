<?php
require_once '../includes/header.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("No service selected");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    die("Service not found");
}

/* GET USER EMAIL */
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $d1 = $_POST['date_debut'];
    $d2 = $_POST['date_fin'];

    $today = date('Y-m-d');

    if ($d1 < $today) {
        $error = "Start date cannot be in the past ❌";
    }

    elseif ($d2 <= $d1) {
        $error = "End date must be after start date ❌";
    }

    else {

        /* =========================
           FIND OVERLAPPING BOOKINGS
        ========================= */
        $stmt = $pdo->prepare("
            SELECT date_debut, date_fin
            FROM reservations
            WHERE service_id = ?
            AND status != 'cancelled'
            AND (? < date_fin AND ? > date_debut)
            ORDER BY date_fin ASC
            LIMIT 1
        ");

        $stmt->execute([$id, $d1, $d2]);
        $conflict = $stmt->fetch();

        if ($conflict) {

            $nextAvailable = date('Y-m-d', strtotime($conflict['date_fin'] . ' +1 day'));

            $error = "
                ❌ This service is already booked<br>
                <strong>Unavailable:</strong> {$conflict['date_debut']} → {$conflict['date_fin']}<br>
                <strong>Next available from:</strong> {$nextAvailable}
            ";

        } else {

            $days = (strtotime($d2) - strtotime($d1)) / 86400;
            $total = $days * $service['prix'];

            $stmt = $pdo->prepare("
                INSERT INTO reservations 
                (user_id, service_id, date_debut, date_fin, montant_total, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");

            $stmt->execute([
                $_SESSION['user_id'],
                $id,
                $d1,
                $d2,
                $total
            ]);

            $subject = "Booking Confirmed - TravelNest";

            $message = "
                <h2>Booking Confirmed ✅</h2>
                <p>Service: <strong>{$service['titre']}</strong></p>
                <p>Total: {$total} €</p>
            ";

            sendMail($user['email'], $subject, $message);

            $success = "Reservation created successfully ✔";
        }
    }
}
?>

<div class="card">
    <h2>Book: <?= htmlspecialchars($service['titre']) ?></h2>

    <p><?= $service['prix'] ?> € / day</p>

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
        <label>Start Date</label>
        <input type="date" name="date_debut" required min="<?= date('Y-m-d') ?>">

        <label>End Date</label>
        <input type="date" name="date_fin" required>

        <button class="btn">Confirm Booking</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>