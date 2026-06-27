<?php
// O parâmetro "from" indica de onde o usuário veio: "home" (API) ou "characters" (banco local).
// Isso muda completamente o comportamento da página — por isso preciso saber logo no início.
$from    = $_GET['from'] ?? 'home';
$id      = (int)($_GET['id'] ?? 0);
$editing = isset($_GET['edit']) && $_GET['edit'] === '1';

if ($id <= 0) {
    header('Location: index.php?page=home');
    exit;
}

// Processo o POST antes de renderizar qualquer HTML.
// Assim evito o problema de "headers already sent" que acontece
// se eu tentar fazer redirect depois de já ter imprimido algo na tela.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Qualquer ação de escrita (salvar, editar, excluir) exige login.
    // Se não estiver logado, mando para o login e paro aqui.
    if (!is_logged_in()) {
        header('Location: index.php?page=login');
        exit;
    }

    if ($action === 'save') {
        save_character(
            (int)$_POST['api_id'],
            trim($_POST['name']),
            trim($_POST['species']),
            trim($_POST['image']),
            trim($_POST['url'])
        );
        header("Location: index.php?page=character_detail&id={$id}&from=home");
        exit;
    }

    if ($action === 'delete') {
        delete_character((int)$_POST['local_id']);
        header('Location: index.php?page=characters');
        exit;
    }

    if ($action === 'update') {
        update_character(
            (int)$_POST['local_id'],
            trim($_POST['name']),
            trim($_POST['species']),
            trim($_POST['image']),
            trim($_POST['url'])
        );
        header("Location: index.php?page=character_detail&id={$id}&from={$from}");
        exit;
    }
}
?>

<?php if ($from === 'home'): ?>

    <?php
    // Verifico no banco se esse personagem já foi salvo pelo usuário.
    // O resultado disso define quais botões aparecem: Salvar (se não salvou) ou Editar/Excluir (se já salvou).
    $saved = get_character_by_api_id($id);
    ?>

    <?php if ($editing && $saved): ?>

        <div class="container">
            <div class="card border-0 shadow-sm p-4 mt-2">
                <form method="POST" action="index.php?page=character_detail&id=<?= $id ?>&from=home">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="local_id" value="<?= $saved['id'] ?>">
                    <div class="row align-items-start">
                        <div class="col-md-4 text-center">
                            <img src="<?= htmlspecialchars($saved['image']) ?>" alt="<?= htmlspecialchars($saved['name']) ?>"
                                 class="rounded-circle mb-3" style="width: 250px; height: 250px; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($saved['name']) ?>" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Espécie</label>
                                    <input type="text" name="species" class="form-control" value="<?= htmlspecialchars($saved['species']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Imagem (URL)</label>
                                    <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($saved['image']) ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">URL</label>
                                <input type="text" name="url" class="form-control" value="<?= htmlspecialchars($saved['url']) ?>">
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <a href="index.php?page=character_detail&id=<?= $id ?>&from=home" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn px-4 btn-app">Salvar alterações</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <?php else: ?>

        <div class="container">
            <div class="card border-0 shadow-sm p-4 mt-2">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <!-- Placeholder cinza enquanto a imagem carrega via JS -->
                        <div id="char-image-wrapper">
                            <div class="rounded-circle bg-secondary d-inline-block" style="width: 250px; height: 250px;"></div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="char-info">
                            <p class="text-muted">Carregando...</p>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <?php if ($saved): ?>
                                <a href="index.php?page=character_detail&id=<?= $id ?>&from=home&edit=1"
                                   class="btn btn-outline-secondary">Editar</a>
                                <form method="POST" action="index.php?page=character_detail&id=<?= $id ?>&from=home"
                                      onsubmit="return confirm('Deseja excluir este personagem?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="local_id" value="<?= $saved['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                </form>
                            <?php else: ?>
                                <!-- Os inputs hidden são preenchidos pelo fetch() abaixo.
                                     Assim quando o usuário clicar em Salvar, o PHP recebe os dados corretos do personagem. -->
                                <form method="POST" action="index.php?page=character_detail&id=<?= $id ?>&from=home" id="save-form">
                                    <input type="hidden" name="action" value="save">
                                    <input type="hidden" name="api_id" id="input-api-id">
                                    <input type="hidden" name="name" id="input-name">
                                    <input type="hidden" name="species" id="input-species">
                                    <input type="hidden" name="image" id="input-image">
                                    <input type="hidden" name="url" id="input-url">
                                    <button type="submit" class="btn px-5 btn-app">Salvar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Busco os dados do personagem direto na API usando o id que veio pela URL.
            // Faço isso no cliente (JS) para não precisar de uma chamada server-side em PHP,
            // o que exigiria curl ou extensões extras.
            fetch('https://rickandmortyapi.com/api/character/<?= $id ?>')
                .then(response => {
                    if (!response.ok) throw new Error('Personagem não encontrado.');
                    return response.json();
                })
                .then(character => {
                    document.getElementById('char-image-wrapper').innerHTML = `
                        <img src="${character.image}" alt="${character.name}"
                             class="rounded-circle"
                             style="width: 250px; height: 250px; object-fit: cover;">
                    `;

                    document.getElementById('char-info').innerHTML = `
                        <h4 class="mb-3">${character.name}</h4>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <span class="text-muted">Espécie</span>
                                <p class="fw-semibold">${character.species}</p>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">Gênero</span>
                                <p class="fw-semibold">${character.gender}</p>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <span class="text-muted">Localização</span>
                                <p class="fw-semibold">${character.location.name}</p>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">URL</span>
                                <p><a href="${character.url}" target="_blank" class="fw-semibold text-decoration-none">Ver na API</a></p>
                            </div>
                        </div>
                    `;

                    // Preencho os inputs hidden com os dados que vieram da API
                    // para que o formulário de salvar tenha as informações certas ao ser submetido
                    const apiIdInput = document.getElementById('input-api-id');
                    if (apiIdInput) {
                        apiIdInput.value = character.id;
                        document.getElementById('input-name').value    = character.name;
                        document.getElementById('input-species').value = character.species;
                        document.getElementById('input-image').value   = character.image;
                        document.getElementById('input-url').value     = character.url;
                    }
                })
                .catch(error => {
                    document.getElementById('char-info').innerHTML =
                        `<div class="alert alert-danger">${error.message}</div>`;
                });
        </script>

    <?php endif; ?>

<?php else: ?>

    <?php
    // Se veio da página de personagens salvos, busco os dados do banco local em vez da API.
    // O id aqui é o id do banco (AUTOINCREMENT), não o id da API.
    $character = get_character_by_id($id);

    if (!$character) {
        header('Location: index.php?page=characters');
        exit;
    }
    ?>

    <div class="container">
        <div class="card border-0 shadow-sm p-4 mt-2">

            <?php if ($editing): ?>

                <form method="POST" action="index.php?page=character_detail&id=<?= $id ?>&from=characters">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="local_id" value="<?= $character['id'] ?>">
                    <div class="row align-items-start">
                        <div class="col-md-4 text-center">
                            <img src="<?= htmlspecialchars($character['image']) ?>" alt="<?= htmlspecialchars($character['name']) ?>"
                                 class="rounded-circle mb-3" style="width: 250px; height: 250px; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nome</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($character['name']) ?>" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Espécie</label>
                                    <input type="text" name="species" class="form-control" value="<?= htmlspecialchars($character['species']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Imagem (URL)</label>
                                    <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($character['image']) ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">URL</label>
                                <input type="text" name="url" class="form-control" value="<?= htmlspecialchars($character['url']) ?>">
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-2">
                                <a href="index.php?page=character_detail&id=<?= $id ?>&from=characters" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn px-4 btn-app">Salvar alterações</button>
                            </div>
                        </div>
                    </div>
                </form>

            <?php else: ?>

                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <img src="<?= htmlspecialchars($character['image']) ?>" alt="<?= htmlspecialchars($character['name']) ?>"
                             class="rounded-circle" style="width: 250px; height: 250px; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <h4 class="mb-3"><?= htmlspecialchars($character['name']) ?></h4>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <span class="text-muted">Espécie</span>
                                <p class="fw-semibold"><?= htmlspecialchars($character['species']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">URL</span>
                                <p>
                                    <a href="<?= htmlspecialchars($character['url']) ?>" target="_blank" class="fw-semibold text-decoration-none">
                                        Ver na API
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="index.php?page=character_detail&id=<?= $id ?>&from=characters&edit=1"
                               class="btn btn-outline-secondary">Editar</a>
                            <form method="POST" action="index.php?page=character_detail&id=<?= $id ?>&from=characters"
                                  onsubmit="return confirm('Deseja excluir este personagem?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="local_id" value="<?= $character['id'] ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>

<?php endif; ?>
