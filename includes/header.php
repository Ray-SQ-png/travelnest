<?php session_start(); ?>
<link rel="stylesheet" href="/assets/css/style.css">

<div class="navbar">
    <div>
        <a href="/">TravelNest</a>
    </div>

    <div>
        <?php if (isset($_SESSION['user_id'])): ?>

            <a href="/user/dashboard.php">Dashboard</a>
            <a href="/user/services.php">Services</a>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="/admin/index.php">Admin</a>
                <a href="/admin/services.php">Services</a>
                <a href="/admin/users.php">Users</a>
                <a href="/admin/reservations.php">Reservations</a>
                <a href="/admin/reviews.php">Reviews</a>
            <?php endif; ?>

            <a href="/user/logout.php">Logout</a>

        <?php else: ?>
            <a href="/user/login.php">Login</a>
            <a href="/user/register.php">Register</a>
        <?php endif; ?>

        <button class="btn" onclick="toggleDarkMode()" style="margin-left:10px;">
            🌙
        </button>
    </div>
</div>

<div class="container">

<script>
function toggleDarkMode() {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }
}

window.onload = function () {
    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark");
    }
};
</script>