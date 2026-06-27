<?php $characters = get_all_characters(); ?>

<div class="container">
    <?php if (empty($characters)): ?>
        <div class="text-center py-5 text-muted">
            <p>Nenhum personagem salvo ainda.</p>
            <a href="index.php?page=home" class="btn btn-app">
                Explorar personagens
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($characters as $character): ?>
                <div class="col-md-4">
                    <a href="index.php?page=character_detail&id=<?= $character['id'] ?>&from=characters" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="<?= htmlspecialchars($character['image']) ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($character['name']) ?>">
                            <div class="card-footer text-center fw-semibold py-2" style="background-color: #a8b8d8; color: #2c3e6b;">
                                <?= htmlspecialchars($character['name']) ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
