<?php
if (is_logged_in()) {
    header('Location: index.php?page=home');
    exit;
}

$error   = '';
$flash   = '';
$warning = '';

if (isset($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

if (isset($_SESSION['flash_warning'])) {
    $warning = $_SESSION['flash_warning'];
    unset($_SESSION['flash_warning']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Todos os campos são obrigatórios.';
    } elseif (!login_user($email, $password)) {
        $error = 'E-mail ou senha incorretos.';
    } else {
        header('Location: index.php?page=home');
        exit;
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mt-2">

                <?php if ($flash): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
                <?php endif; ?>

                <?php if ($warning): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($warning) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=login">

                    <div class="mb-3">
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="E-mail"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <input
                                type="password"
                                name="password"
                                id="input-password"
                                class="form-control"
                                placeholder="Senha"
                                required
                            >
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password" tabindex="-1">
                                <i class="bi bi-eye" id="icon-password"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn px-5 btn-app">
                            Entrar
                        </button>
                    </div>

                </form>

                <div class="text-center mt-3">
                    <a href="index.php?page=register" class="text-muted text-decoration-none">
                        Cadastrar-se
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-password').addEventListener('click', () => {
        const input = document.getElementById('input-password');
        const icon  = document.getElementById('icon-password');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
