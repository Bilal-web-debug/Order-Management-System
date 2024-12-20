<?php
session_start();

// Display success or error message
if (isset($_SESSION['success'])) {
    echo "<p>{$_SESSION['success']}</p>";
    unset($_SESSION['success']);
} else {
    echo "<p>Your order could not be placed. Please try again.</p>";
}
?>
