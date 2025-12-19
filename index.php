<?php
/**
 * Root Index - Redirect to public/index.php
 */
require_once __DIR__ . '/config/config.php';
header('Location: ' . BASE_URL . '/public/index.php');
exit;
?>
