<?php

function sendMail($to, $subject, $message) {

    // Simulate email (for local dev)
    echo "<div class='card'>";
    echo "<h3>📧 Email Sent (Simulation)</h3>";
    echo "<p><strong>To:</strong> $to</p>";
    echo "<p><strong>Subject:</strong> $subject</p>";
    echo "<div>$message</div>";
    echo "</div>";

    return true;
}