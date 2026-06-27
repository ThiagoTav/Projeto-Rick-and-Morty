<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/character_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    logout_user();
    header('Location: index.php?page=login');
    exit;
}

$page = $_GET['page'] ?? 'home';

$allowed_pages = ['home', 'characters', 'character_detail', 'about', 'login', 'register'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

require_once 'includes/layout/header.php';
require_once "pages/{$page}.php";
require_once 'includes/layout/footer.php';
