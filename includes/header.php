<?php session_start(); ?>
<link rel="stylesheet" href="/assets/css/style.css">

<div class="navbar">

    <!-- LEFT: BRAND -->
    <div style="display:flex; align-items:center; gap:12px;">

        <a href="/" style="display:flex; align-items:center; text-decoration:none;">

            <img src="/assets/images/logo.png"
                 alt="TravelNest Logo"
                 style="height:50px; width:auto; margin-right:10px;">

            <div>
                <div style="font-size:22px; font-weight:700; color:white;">
                    TravelNest
                </div>
                <div style="font-size:12px; color:#94a3b8;">
                    Explore • Book • Travel
                </div>
            </div>

        </a>

    </div>

    <!-- RIGHT: NAVIGATION -->
    <div>

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if (isset($_SESSION['user_name'])): ?>
                <span style="color:#cbd5e1; margin-right:15px;">
                    Hi, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
                </span>
            <?php endif; ?>

            <a href="/user/dashboard.php">🏠 Dashboard</a>
            <a href="/user/services.php">🧳 Services</a>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="/admin/index.php">📊 Admin</a>
                <a href="/admin/services.php">🛎 Services</a>
                <a href="/admin/users.php">👤 Users</a>
                <a href="/admin/reservations.php">📅 Reservations</a>
                <a href="/admin/reviews.php">⭐ Reviews</a>
            <?php endif; ?>

            <a href="/user/logout.php">🚪 Logout</a>

        <?php else: ?>
            <a href="/user/login.php">🔐 Login</a>
            <a href="/user/register.php">📝 Register</a>
        <?php endif; ?>

        <!-- DARK MODE BUTTON -->
        <button id="themeBtn"
                class="btn"
                onclick="toggleDarkMode()"
                style="margin-left:10px;">
            🌙
        </button>

    </div>
</div>

<div class="container">

<script>
function toggleDarkMode() {
    document.body.classList.toggle("dark");

    const btn = document.getElementById("themeBtn");

    if (document.body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
        btn.innerHTML = "☀️";
    } else {
        localStorage.setItem("theme", "light");
        btn.innerHTML = "🌙";
    }
}

window.onload = function () {
    const btn = document.getElementById("themeBtn");

    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark");
        btn.innerHTML = "☀️";
    } else {
        btn.innerHTML = "🌙";
    }
};
</script>