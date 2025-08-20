<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = null;
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    require_once(__DIR__ . '/../classes/User.php');
    $user = new User($mysqli, $_SESSION['user_id']);
}
?>
