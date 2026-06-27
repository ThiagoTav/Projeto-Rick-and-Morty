# Teste Técnico VITAFOR

Aplicação web desenvolvida em PHP puro que consome a API do [Rick and Morty](https://rickandmortyapi.com/), permitindo listar personagens, salvar favoritos localmente e gerenciar usuários com autenticação.

## Tecnologias utilizadas

- **PHP 8.5** — linguagem principal do backend
- **SQLite** — banco de dados local (sem necessidade de servidor externo)
- **PDO** — camada nativa do PHP para conexão segura com o banco, usando prepared statements para evitar SQL injection
- **Bootstrap 5** — estilização e responsividade do front-end
- **JavaScript com Fetch API** — requisições assíncronas para a API do Rick and Morty

## Pré-requisitos

- PHP 8.5 instalado ([php.net](https://www.php.net/downloads))
- Extensões do PHP habilitadas no `php.ini`:
  - `extension=pdo_sqlite`
  - `extension=sqlite3`

## Como rodar o projeto

```bash
# Clone o repositório
git clone https://github.com/ThiagoTav/VITAFOR-TESTE-THIAGO_TAVARES_SILVA.git

# Acesse a pasta do projeto
cd VITAFOR-TESTE-THIAGO_TAVARES_SILVA

# Inicie o servidor embutido do PHP
php -S localhost:8000
```

Acesse no navegador: [http://localhost:8000](http://localhost:8000)

> O banco de dados SQLite é criado automaticamente na pasta `db/` na primeira vez que o sistema for acessado. Não é necessário nenhuma configuração manual de banco.

## Estrutura de pastas

```
├── index.php                  # Ponto de entrada e roteador da aplicação
├── includes/
│   ├── config.php             # Constantes globais (caminho do banco, URL base)
│   ├── database.php           # Conexão PDO com SQLite e criação das tabelas
│   └── layout/
│       ├── header.php         # Navbar compartilhada entre todas as páginas
│       └── footer.php         # Fechamento do HTML compartilhado
├── pages/
│   ├── home.php               # Listagem de personagens via API
│   ├── characters.php         # Personagens salvos no banco local
│   ├── character_detail.php   # Detalhes de um personagem
│   ├── about.php              # Página sobre o desenvolvedor
│   ├── login.php              # Tela de login
│   └── register.php           # Tela de cadastro
└── db/
    └── database.sqlite        # Gerado automaticamente (ignorado pelo git)
```

## Páginas da aplicação

| Página | Rota | Descrição |
|---|---|---|
| Home | `/?page=home` | Lista personagens da API do Rick and Morty |
| Personagens | `/?page=characters` | Lista personagens salvos no banco local |
| Detalhes | `/?page=character_detail&id=X` | Detalhes de um personagem |
| Sobre | `/?page=about` | Mini currículo do desenvolvedor |
| Login | `/?page=login` | Autenticação de usuários |
| Cadastro | `/?page=register` | Registro de novos usuários |
