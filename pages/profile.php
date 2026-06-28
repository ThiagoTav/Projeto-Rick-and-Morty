<?php
if (!is_logged_in()) {
    header('Location: index.php?page=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['profile_image'])) {
    update_profile_image((int)$_SESSION['user_id'], trim($_POST['profile_image']));
    $_SESSION['flash_success'] = 'Avatar atualizado com sucesso!';
    header('Location: index.php?page=profile');
    exit;
}

$flash = '';
if (isset($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <div class="card border-0 shadow-sm p-4 mt-2">
                <div class="d-flex align-items-center gap-4 mb-3">
                    <?php if (!empty($_SESSION['user_profile_image'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user_profile_image']) ?>"
                             class="rounded-circle border border-3 border-app"
                             style="width: 72px; height: 72px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle border border-3 border-app d-flex align-items-center justify-content-center fw-bold text-app"
                             style="width: 72px; height: 72px; font-size: 1.8rem; background: #fff;">
                            <?= htmlspecialchars(strtoupper(substr($_SESSION['user_name'], 0, 1))) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($_SESSION['user_name']) ?></h5>
                        <small class="text-muted">Escolha um personagem abaixo para usar como avatar</small>
                    </div>
                </div>

                <?php if ($flash): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm p-4 mt-3">
                <h6 class="mb-3 text-app fw-semibold">Personagens disponíveis</h6>

                <div id="loading" class="text-center py-4 text-muted">Carregando personagens...</div>
                <div id="characters-grid" class="row g-3"></div>
                <div id="pagination" class="d-flex justify-content-center mt-4 gap-2"></div>
            </div>

        </div>
    </div>
</div>

<!-- Form hidden — preenchido pelo JS ao clicar num personagem -->
<form method="POST" action="index.php?page=profile" id="avatar-form">
    <input type="hidden" name="profile_image" id="selected-image">
</form>

<script>
    const currentAvatar = <?= json_encode($_SESSION['user_profile_image'] ?? null) ?>;
    let currentPage = 1;

    async function loadCharacters(page) {
        document.getElementById('loading').classList.remove('d-none');
        document.getElementById('characters-grid').innerHTML = '';

        try {
            const response = await fetch(`https://rickandmortyapi.com/api/character?page=${page}`);
            if (!response.ok) throw new Error('Erro ao buscar personagens.');
            const data = await response.json();
            renderGrid(data.results);
            renderPagination(data.info.pages, page);
        } catch (error) {
            document.getElementById('loading').innerHTML =
                `<div class="alert alert-danger">${error.message}</div>`;
            return;
        }

        document.getElementById('loading').classList.add('d-none');
    }

    function renderGrid(characters) {
        const grid = document.getElementById('characters-grid');

        grid.innerHTML = characters.map(character => {
            const isSelected = currentAvatar === character.image;
            return `
                <div class="col-6 col-md-3">
                    <div class="card border-0 h-100 character-card ${isSelected ? 'selected-card' : ''}"
                         style="cursor: pointer;"
                         data-image="${character.image}"
                         onclick="selectAvatar(this.dataset.image)">
                        <img src="${character.image}" class="card-img-top rounded-top" alt="${character.name}">
                        <div class="card-footer-app text-center small py-1 rounded-bottom">
                            ${character.name}
                        </div>
                        ${isSelected ? '<div class="selected-badge">✓</div>' : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderPagination(totalPages, page) {
        const container = document.getElementById('pagination');
        const prevDisabled = page <= 1 ? 'disabled' : '';
        const nextDisabled = page >= totalPages ? 'disabled' : '';

        container.innerHTML = `
            <button class="btn btn-outline-secondary ${prevDisabled}" onclick="changePage(${page - 1})" ${prevDisabled}>
                &laquo; Anterior
            </button>
            <span class="btn btn-light disabled">Página ${page} de ${totalPages}</span>
            <button class="btn btn-outline-secondary ${nextDisabled}" onclick="changePage(${page + 1})" ${nextDisabled}>
                Próxima &raquo;
            </button>
        `;
    }

    function changePage(page) {
        currentPage = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        loadCharacters(page);
    }

    function selectAvatar(imageUrl) {
        document.getElementById('selected-image').value = imageUrl;
        document.getElementById('avatar-form').submit();
    }

    loadCharacters(currentPage);
</script>

<style>
    .character-card {
        transition: transform 0.15s, box-shadow 0.15s;
        position: relative;
    }
    .character-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(59, 89, 152, 0.25) !important;
    }
</style>
