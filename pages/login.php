<?php
if (is_logged_in()) {
    header('Location: index.php?page=home');
    exit;
}

$error = '';

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
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Senha"
                            required
                        >
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn px-5 text-white" style="background-color: #3b5998;">
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
