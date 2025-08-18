<?php
session_start();
session_unset();
session_destroy();

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
header("Location: $redirect");
exit;
?>