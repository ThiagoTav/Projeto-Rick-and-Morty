<?php
// Ponto de entrada da aplicação. Toda requisição passa por aqui antes de chegar em qualquer página.
session_start();

// Carrego as dependências principais uma vez só aqui, assim todas as páginas já têm acesso a tudo
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/character_functions.php';

// O logout usa POST em vez de GET para evitar que alguém derrube a sessão do usuário
// só de mandar um link malicioso pra ele clicar (isso se chama CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    logout_user();
    header('Location: index.php?page=login');
    exit;
}

$page = $_GET['page'] ?? 'home';

// Whitelist de páginas permitidas — sem isso alguém poderia passar qualquer coisa na URL
// e o require_once lá embaixo carregaria um arquivo arbitrário do servidor
$allowed_pages = ['home', 'characters', 'character_detail', 'about', 'login', 'register'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

require_once 'includes/layout/header.php';
require_once "pages/{$page}.php";
require_once 'includes/layout/footer.php';
