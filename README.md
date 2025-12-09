# 🗂️ Mini Trello - Clone do Trello em Laravel

Um aplicativo web de gerenciamento de tarefas inspirado no Trello, desenvolvido com Laravel 12.

## 📋 Funcionalidades

- ✅ Autenticação de usuários (registro/login)
- ✅ Criação e gerenciamento de quadros (boards)
- ✅ Listas de tarefas personalizáveis
- ✅ Cartões (cards) com drag & drop
- ✅ Interface moderna e responsiva

## 🛠️ Tecnologias

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade, TailwindCSS, Alpine.js
- **Database:** SQLite (padrão) / PostgreSQL (produção)
- **Build:** Vite

## 🚀 Instalação Local

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- npm

### Passos

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/minitrello-laravel.git
cd minitrello-laravel
```

2. Instale as dependências:
```bash
composer install
npm install
```

3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

4. Execute as migrations:
```bash
php artisan migrate
```

5. Build dos assets:
```bash
npm run build
```

6. Inicie o servidor:
```bash
php artisan serve
```

7. Acesse: http://localhost:8000

## 🚂 Deploy no Railway

### Variáveis de Ambiente Necessárias

Configure estas variáveis no painel do Railway:

| Variável | Valor |
|----------|-------|
| `APP_NAME` | Mini Trello |
| `APP_ENV` | production |
| `APP_KEY` | *(gerar com `php artisan key:generate --show`)* |
| `APP_DEBUG` | false |
| `APP_URL` | https://seu-app.railway.app |
| `DB_CONNECTION` | sqlite |
| `SESSION_DRIVER` | database |
| `CACHE_STORE` | file |
| `QUEUE_CONNECTION` | sync |

### Deploy Automático

1. Faça push do código para o GitHub
2. Conecte o repositório ao Railway
3. Configure as variáveis de ambiente
4. O deploy será feito automaticamente

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/Controllers/   # Controllers da aplicação
│   ├── Models/             # Models Eloquent
│   └── Policies/           # Políticas de autorização
├── database/
│   └── migrations/         # Migrations do banco
├── resources/
│   └── views/              # Templates Blade
├── routes/
│   └── web.php             # Rotas da aplicação
├── Procfile                # Configuração Railway
└── nixpacks.toml           # Build configuration
```

## 📄 Licença

Este projeto está sob a licença MIT.
