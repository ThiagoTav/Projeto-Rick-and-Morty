<div class="container">
    <div id="loading" class="text-center py-5 text-muted">Carregando personagens...</div>
    <div id="error-message" class="alert alert-danger d-none"></div>

    <div id="characters-grid" class="row g-4"></div>

    <div id="pagination" class="d-flex justify-content-center mt-4 gap-2"></div>
</div>

<script>
    let currentPage = 1;

    async function loadCharacters(page) {
        showLoading(true);

        try {
            const response = await fetch(`https://rickandmortyapi.com/api/character?page=${page}`);

            if (!response.ok) throw new Error('Erro ao buscar personagens.');

            const data = await response.json();

            renderCards(data.results);
            renderPagination(data.info.pages, page);
        } catch (error) {
            showError(error.message);
        } finally {
            showLoading(false);
        }
    }

    function renderCards(characters) {
        const grid = document.getElementById('characters-grid');

        grid.innerHTML = characters.map(character => `
            <div class="col-md-4">
                <a href="index.php?page=character_detail&id=${character.id}&from=home" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="${character.image}" class="card-img-top" alt="${character.name}">
                        <div class="card-footer text-center fw-semibold py-2" style="background-color: #a8b8d8; color: #2c3e6b;">
                            ${character.name}
                        </div>
                    </div>
                </a>
            </div>
        `).join('');
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

    function showLoading(visible) {
        document.getElementById('loading').classList.toggle('d-none', !visible);
    }

    function showError(message) {
        const el = document.getElementById('error-message');
        el.textContent = message;
        el.classList.remove('d-none');
    }

    loadCharacters(currentPage);
</script>
