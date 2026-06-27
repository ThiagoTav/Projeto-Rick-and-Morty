<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mail inválido.';
    } elseif (strlen($password) < 8) {
        $error = 'A senha deve ter no mínimo 8 caracteres.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = 'A senha deve conter pelo menos uma letra maiúscula.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = 'A senha deve conter pelo menos uma letra minúscula.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = 'A senha deve conter pelo menos um número.';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não coincidem.';
    } elseif (is_email_taken($email)) {
        $error = 'Este e-mail já está cadastrado.';
    } else {
        if (register_user($name, $email, $password)) {
            $_SESSION['flash_success'] = 'Conta criada com sucesso! Faça login para continuar.';
            header('Location: index.php?page=login');
            exit;
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

                <form method="POST" action="index.php?page=register" id="register-form" novalidate>

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

                    <div class="row mb-1">
                        <div class="col-md-6 mb-3">
                            <input
                                type="email"
                                name="email"
                                id="input-email"
                                class="form-control"
                                placeholder="E-mail"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                required
                            >
                            <div id="email-feedback" class="form-text mt-1" style="min-height: 18px;"></div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="input-group has-validation">
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
                            <ul class="list-unstyled mt-2 mb-0 small" id="password-requirements">
                                <li id="req-length" class="text-secondary">✗ Mínimo 8 caracteres</li>
                                <li id="req-upper"  class="text-secondary">✗ Pelo menos 1 letra maiúscula</li>
                                <li id="req-lower"  class="text-secondary">✗ Pelo menos 1 letra minúscula</li>
                                <li id="req-number" class="text-secondary">✗ Pelo menos 1 número</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group has-validation">
                                <input
                                    type="password"
                                    name="confirm_password"
                                    id="input-confirm"
                                    class="form-control"
                                    placeholder="Confirmar senha"
                                    required
                                >
                                <button class="btn btn-outline-secondary" type="button" id="toggle-confirm" tabindex="-1">
                                    <i class="bi bi-eye" id="icon-confirm"></i>
                                </button>
                            </div>
                            <div id="confirm-feedback" class="form-text mt-1" style="min-height: 18px;"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn px-5 btn-app">
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

<script>
    const emailInput    = document.getElementById('input-email');
    const passwordInput = document.getElementById('input-password');
    const confirmInput  = document.getElementById('input-confirm');
    const emailFeedback   = document.getElementById('email-feedback');
    const confirmFeedback = document.getElementById('confirm-feedback');
    const reqLength = document.getElementById('req-length');
    const reqUpper  = document.getElementById('req-upper');
    const reqLower  = document.getElementById('req-lower');
    const reqNumber = document.getElementById('req-number');

    function setRequirement(el, met) {
        el.textContent = (met ? '✓ ' : '✗ ') + el.textContent.slice(2);
        el.className   = met ? 'text-success fw-semibold' : 'text-secondary';
    }

    function toggleVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type  = isHidden ? 'text' : 'password';
        icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    }

    document.getElementById('toggle-password').addEventListener('click', () => {
        toggleVisibility('input-password', 'icon-password');
    });

    document.getElementById('toggle-confirm').addEventListener('click', () => {
        toggleVisibility('input-confirm', 'icon-confirm');
    });

    emailInput.addEventListener('input', () => {
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
        if (emailInput.value === '') {
            emailFeedback.textContent = '';
            emailInput.classList.remove('is-valid', 'is-invalid');
        } else if (valid) {
            emailFeedback.innerHTML = '<span class="text-success">✓ E-mail válido</span>';
            emailInput.classList.add('is-valid');
            emailInput.classList.remove('is-invalid');
        } else {
            emailFeedback.innerHTML = '<span class="text-danger">✗ E-mail inválido</span>';
            emailInput.classList.add('is-invalid');
            emailInput.classList.remove('is-valid');
        }
    });

    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        setRequirement(reqLength, val.length >= 8);
        setRequirement(reqUpper,  /[A-Z]/.test(val));
        setRequirement(reqLower,  /[a-z]/.test(val));
        setRequirement(reqNumber, /[0-9]/.test(val));
        if (confirmInput.value !== '') validateConfirm();
    });

    confirmInput.addEventListener('input', validateConfirm);

    function validateConfirm() {
        if (confirmInput.value === '') {
            confirmFeedback.textContent = '';
            confirmInput.classList.remove('is-valid', 'is-invalid');
            return;
        }
        if (confirmInput.value === passwordInput.value) {
            confirmFeedback.innerHTML = '<span class="text-success">✓ Senhas coincidem</span>';
            confirmInput.classList.add('is-valid');
            confirmInput.classList.remove('is-invalid');
        } else {
            confirmFeedback.innerHTML = '<span class="text-danger">✗ Senhas não coincidem</span>';
            confirmInput.classList.add('is-invalid');
            confirmInput.classList.remove('is-valid');
        }
    }

    function showFormError(message) {
        let el = document.getElementById('js-form-error');
        if (!el) {
            el = document.createElement('div');
            el.id = 'js-form-error';
            el.className = 'alert alert-danger mt-3';
            document.getElementById('register-form').prepend(el);
        }
        el.textContent = message;
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearFormError() {
        const el = document.getElementById('js-form-error');
        if (el) el.remove();
    }

    document.getElementById('register-form').addEventListener('submit', (e) => {
        const val        = passwordInput.value;
        const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
        const passValid  = val.length >= 8 && /[A-Z]/.test(val) && /[a-z]/.test(val) && /[0-9]/.test(val);
        const matchValid = confirmInput.value === passwordInput.value;

        if (!emailValid || !passValid || !matchValid) {
            e.preventDefault();

            if (!emailValid) {
                emailInput.dispatchEvent(new Event('input'));
                showFormError('Por favor, insira um e-mail válido.');
            } else if (!passValid) {
                showFormError('A senha não atende todos os critérios obrigatórios. Verifique os requisitos abaixo do campo de senha.');
            } else if (!matchValid) {
                validateConfirm();
                showFormError('As senhas não coincidem. Verifique o campo de confirmação.');
            }
        } else {
            clearFormError();
        }
    });
</script>
