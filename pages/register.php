<?php
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mail inválido.';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não coincidem.';
    } elseif (strlen($password) < 6) {
        $error = 'A senha deve ter no mínimo 6 caracteres.';
    } elseif (is_email_taken($email)) {
        $error = 'Este e-mail já está cadastrado.';
    } else {
        if (register_user($name, $email, $password)) {
            $success = 'Cadastro realizado com sucesso! Faça login para continuar.';
        } else {
            $error = 'Erro ao realizar o cadastro. Tente novamente.';
        }
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

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=register">

                    <div class="mb-3">
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Nome completo"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="E-mail"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                required
                            >
                        </div>
                        <div class="col-md-6">
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Senha"
                                required
                            >
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                placeholder="Confirmar senha"
                                required
                            >
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn px-5 text-white" style="background-color: #3b5998;">
                            Cadastrar
                        </button>
                    </div>

                </form>

                <div class="text-center mt-3">
                    <a href="index.php?page=login" class="text-muted text-decoration-none">
                        Já tem uma conta? Faça login
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
