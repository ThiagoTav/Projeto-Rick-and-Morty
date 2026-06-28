<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rick and Morty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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
            object-fit: cover;
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

        .btn-app {
            background-color: #3b5998;
            color: #ffffff;
        }

        .btn-app:hover {
            background-color: #2d4373;
            color: #ffffff;
        }

        .text-app {
            color: #3b5998;
        }

        .border-app {
            border-color: #3b5998 !important;
        }

        .card-footer-app {
            background-color: #a8b8d8;
            color: #2c3e6b;
        }

        .navbar-brand-text {
            color: #dfe3ee;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .logo-circle-initial {
            color: #3b5998;
            font-size: 1rem;
        }

        .selected-card {
            outline: 3px solid #3b5998;
            border-radius: 8px;
        }

        .selected-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            background: #3b5998;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2"
           href="<?= is_logged_in() ? 'index.php?page=profile' : 'index.php' ?>">
            <?php if (is_logged_in() && !empty($_SESSION['user_profile_image'])): ?>
                <img src="<?= htmlspecialchars($_SESSION['user_profile_image']) ?>"
                     class="logo-circle">
            <?php elseif (is_logged_in()): ?>
                <span class="logo-circle d-flex align-items-center justify-content-center fw-bold logo-circle-initial">
                    <?= htmlspecialchars(strtoupper(substr($_SESSION['user_name'], 0, 1))) ?>
                </span>
            <?php else: ?>
                <span class="logo-circle"></span>
            <?php endif; ?>
            <?php if (is_logged_in()): ?>
                <span class="navbar-brand-text">
                    Olá, <?= htmlspecialchars($_SESSION['user_name']) ?>
                </span>
            <?php endif; ?>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <form method="POST" action="index.php" class="d-inline">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="nav-link border-0 bg-transparent">Sair</button>
                    </form>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'login' || $page === 'register') ? 'active' : '' ?>" href="index.php?page=login">Login / Cadastro</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
