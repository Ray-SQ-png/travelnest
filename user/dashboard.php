<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   GET RESERVATIONS
========================= */
$stmt = $pdo->prepare("
    SELECT r.*, s.titre 
    FROM reservations r
    JOIN services s ON r.service_id = s.id
    WHERE r.user_id = ?
");

$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();

/* =========================
   SUBMIT REVIEW
========================= */
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {

    $service_id = $_POST['service_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    // prevent duplicate reviews
    $check = $pdo->prepare("
        SELECT id FROM reviews 
        WHERE user_id = ? AND service_id = ?
    ");

    $check->execute([
        $_SESSION['user_id'],
        $service_id
    ]);

    if ($check->rowCount() == 0) {

        $stmt = $pdo->prepare("
            INSERT INTO reviews 
            (user_id, service_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $service_id,
            $rating,
            $comment
        ]);

        $message = "
            <div class='alert alert-success'>
                Review submitted successfully ⭐
            </div>
        ";

    } else {

        $message = "
            <div class='alert alert-error'>
                You already reviewed this service ❌
            </div>
        ";
    }
}
?>

<h2>Your Reservations</h2>

<?= $message ?>

<?php foreach ($reservations as $r): ?>

    <div class="card">

        <h3><?= htmlspecialchars($r['titre']) ?></h3>

        <p>
            <?= $r['date_debut'] ?> → <?= $r['date_fin'] ?>
        </p>

        <p>
            <strong><?= $r['montant_total'] ?> €</strong>
        </p>

        <p>
            Status: <?= ucfirst($r['status']) ?>
        </p>

        <?php if ($r['status'] === 'pending'): ?>

            <a class="btn" href="pay.php?id=<?= $r['id'] ?>">
                Pay Now
            </a>

        <?php else: ?>

            <!-- REVIEW FORM -->
            <form method="POST">

                <input 
                    type="hidden" 
                    name="service_id" 
                    value="<?= $r['service_id'] ?>"
                >

                <!-- STAR RATING -->
                <div class="star-rating">

                    <input type="radio" id="star5<?= $r['id'] ?>" name="rating" value="5" required>
                    <label for="star5<?= $r['id'] ?>">★</label>

                    <input type="radio" id="star4<?= $r['id'] ?>" name="rating" value="4">
                    <label for="star4<?= $r['id'] ?>">★</label>

                    <input type="radio" id="star3<?= $r['id'] ?>" name="rating" value="3">
                    <label for="star3<?= $r['id'] ?>">★</label>

                    <input type="radio" id="star2<?= $r['id'] ?>" name="rating" value="2">
                    <label for="star2<?= $r['id'] ?>">★</label>

                    <input type="radio" id="star1<?= $r['id'] ?>" name="rating" value="1">
                    <label for="star1<?= $r['id'] ?>">★</label>

                </div>

                <input 
                    type="text"
                    name="comment"
                    placeholder="Write your review..."
                    required
                >

                <button class="btn" name="review">
                    Submit Review
                </button>

            </form>

        <?php endif; ?>

    </div>

<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>