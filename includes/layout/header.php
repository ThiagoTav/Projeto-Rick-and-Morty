<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rick and Morty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ebee;
        }

        .navbar-custom {
            background-color: #3b5998;
        }

        .navbar-custom .navbar-brand .logo-circle {
            width: 36px;
            height: 36px;
            background-color: #ffffff;
            border-radius: 50%;
            display: inline-block;
        }

        .navbar-custom .nav-link {
            color: #dfe3ee !important;
            background-color: #4a69ad;
            border-radius: 4px;
            margin-left: 6px;
            padding: 6px 14px !important;
            font-size: 0.875rem;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            background-color: #5b7ec9;
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="index.php">
            <span class="logo-circle"></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'home') ? 'active' : '' ?>" href="index.php?page=home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'characters') ? 'active' : '' ?>" href="index.php?page=characters">Personagens</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'about') ? 'active' : '' ?>" href="index.php?page=about">Sobre</a>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link <?= ($page === 'login') ? 'active' : '' ?>" href="index.php?action=logout">Sair</a>
                    <?php else: ?>
                        <a class="nav-link <?= ($page === 'login' || $page === 'register') ? 'active' : '' ?>" href="index.php?page=login">Login / Cadastro</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
