# Gestão de Pedidos (Monorepo)

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/Vue-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue 3">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0">
  <img src="https://img.shields.io/badge/Docker-Latest-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
</p>

## 📁 Sobre o Monorepo

Este é um monorepo completo para gestão de pedidos de viagem, contendo:

- **`ms-orders/`** — Microserviço RESTful backend (Laravel 12, JWT, Docker, MySQL)
  - CRUD de pedidos com autenticação e autorização (roles)
  - Notificações assíncronas via fila
  - Testes automatizados e arquitetura em camadas
  
- **`mfe-orders/`** — Frontend web (Vue 3, Vite, Pinia)
  - Interface moderna com formulário de criação
  - Dashboard com filtros e gestão de status
  - Autenticação JWT integrada com backend

## 📂 Estrutura do Monorepo

```
Project/
├── docker-compose.yml          # Orquestra todos os containers
├── .gitignore                  # Regras globais do monorepo
├── .editorconfig               # Configurações do editor
├── README.md                   # Documentação geral
├── ms-orders/                  # Backend Laravel
│   ├── app/                    # Código da aplicação
│   ├── config/                 # Configurações
│   ├── database/               # Migrations, seeds, factories
│   ├── routes/                 # Rotas da API
│   ├── tests/                  # Testes automatizados
│   ├── docker/                 # Configs Docker (nginx, php, mysql)
│   ├── Dockerfile              # Build do backend
│   ├── composer.json           # Dependências PHP
│   ├── .env.example            # Template de variáveis
│   └── ...
└── mfe-orders/                 # Frontend Vue
    ├── src/                    # Código Vue
    ├── public/                 # Arquivos estáticos
    ├── Dockerfile              # Build do frontend
    ├── package.json            # Dependências Node
    ├── vite.config.js          # Configuração Vite
    ├── .env.example            # Template de variáveis
    └── ...
```

## 🚀 Instalação Completa (Backend + Frontend)

### Opção 1: Usando Docker Compose (Recomendado)

O Docker Compose sobe automaticamente **backend + frontend + banco de dados + worker de fila** em um único comando:

```bash
# 1. Clone o repositório e entre no diretório
cd Project

# 2. Instale dependências PHP do backend
cd ms-orders
composer install
cd ..

# 3. Configure o .env do backend
cp ms-orders/.env.example ms-orders/.env
# Edite ms-orders/.env se necessário (DB, JWT, etc)

# 4. Configure o .env do frontend
cp mfe-orders/.env.example mfe-orders/.env
# Padrão: VITE_API_URL=http://localhost:8000/api

# 5. Suba todos os containers (backend + frontend + db + worker)
docker-compose up -d --build

# 6. Configure a aplicação Laravel (dentro do container)
docker-compose exec app bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan db:seed # opcional - cria usuários admin e user para fins de teste
exit
```

**Acesse:**
- 🔗 **Frontend:** http://localhost:5173
- 🔗 **API Backend:** http://localhost:8000/api

**Containers rodando:**
- `mfe_orders_frontend` — Frontend Vue 3 (porta 5173)
- `msorders` — API Laravel (porta 8000 via nginx)
- `msorders_nginx` — Servidor web
- `msorders_db` — MySQL 8.0
- `msorders_worker` — Worker de fila (notificações)

### Opção 2: Rodando Manualmente (Desenvolvimento Local)

Se preferir rodar sem Docker:

#### Backend (API Laravel)

```bash
# 1. Entre no diretório do backend
cd ms-orders

# 2. Instale dependências
composer install

# 3. Configure .env
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# 4. Configure banco de dados (ajuste .env para MySQL local)
php artisan migrate
php artisan db:seed

# 5. Inicie servidor
php artisan serve
# API disponível em http://localhost:8000

# 6. Em outro terminal, rode o worker de fila
cd ms-orders
php artisan queue:work
```

#### Frontend (Vue 3)

```bash
# Entre no diretório do frontend
cd mfe-orders

# Instale dependências
npm install

# Configure variáveis de ambiente
cp .env.example .env
# Edite VITE_API_URL se necessário

# Inicie o servidor de desenvolvimento
npm run dev
# Frontend disponível em http://localhost:5173
```

**Acesse:**
- 🔗 **Frontend:** http://localhost:5173
- 🔗 **API Backend:** http://localhost:8000/api

**Credenciais para teste (após seed):**
- **Admin:** admin@onfly.com / password
- **User:** user@onfly.com / password

## 🧪 Rodando Testes

### Backend (Laravel)

- **No container Docker:**
  ```bash
  docker-compose exec app php artisan test
  ```
  
- **Local (sem Docker):**
  ```bash
  cd ms-orders
  php artisan test
  ```
  (usa SQLite em memória, via `ms-orders/.env.testing`)

### Frontend (Vue)

```bash
cd mfe-orders
npm run test
```

## 🎨 Arquitetura Frontend (mfe-orders)

### Stack Tecnológica
- **Vue 3** — Framework progressivo com Composition API
- **Pinia** — Store state management (padrão Vue 3)
- **Vue Router** — Sistema de rotas com guards de autenticação
- **Axios** — Cliente HTTP com interceptors JWT
- **Vite** — Build tool moderna e rápida
- **Vue Toastification** — Notificações toast elegantes

### Padrões e Boas Práticas Implementadas
- ✅ **Composition API** com `<script setup>` em todos os componentes
- ✅ **storeToRefs** para manter reatividade do estado Pinia
- ✅ **Response Interceptor** para logout automático em erro 401
- ✅ **Mapeamento de dados** — Backend em inglês, frontend em português
- ✅ **Loading states** granulares por ação (botões individuais)
- ✅ **Feedback visual** consistente com toast notifications
- ✅ **Guards de rota** para proteger páginas autenticadas
- ✅ **Separação de responsabilidades** — Services → Stores → Components

### Integrações Backend-Frontend
O frontend está totalmente integrado com o backend Laravel:

| Funcionalidade | Backend Endpoint | Frontend | Mapeamento |
|----------------|------------------|----------|------------|
| Login | `POST /auth/login` | authService.login() | Token JWT armazenado |
| Buscar usuário | `GET /auth/user` | authStore.fetchUser() | Carregado na inicialização |
| Criar pedido | `POST /orders` | travelStore.create() | `departure_date` ↔ frontend |
| Listar pedidos | `GET /orders` | travelStore.fetchRequests() | Status `pending` → `solicitado` |
| Atualizar status | `PATCH /orders/{id}/status` | travelStore.updateStatus() | Status PT → EN automático |
| Logout | `POST /auth/logout` | authStore.logout() | Invalida token no backend |

## Exemplos de Uso da API

Os seguintes exemplos usam a API backend (`ms-orders`) exposta via Nginx na porta 8000:

**Registrar:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"João","email":"joao@example.com","password":"senha123","password_confirmation":"senha123","role":"user"}'
```
**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"joao@example.com","password":"senha123"}'
```
**Criar pedido:**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"customer_name":"Maria","destination":"SP","departure_date":"2026-03-15","return_date":"2026-03-22"}'
```
**Atualizar status (admin):**
```bash
curl -X PATCH http://localhost:8000/api/orders/1/status \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status":"confirmed"}'
```
**Listar pedidos:**
```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer {token}"
```
**Ver detalhes de pedido:**
```bash
curl -X GET http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer {token}"
```
**Atualizar pedido:**
```bash
curl -X PATCH http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"customer_name":"Maria","destination":"RJ","departure_date":"2026-03-20","return_date":"2026-03-25"}'
```
**Logout:**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer {token}"
```
**Refresh de token:**
```bash
curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Authorization: Bearer {token}"
```
**Buscar usuário autenticado:**
```bash
curl -X GET http://localhost:8000/api/auth/user \
  -H "Authorization: Bearer {token}"
```

## Estrutura e Tecnologias

**Backend (`ms-orders/`):**
- **Arquitetura:** Controllers finos, Service Layer, Policies, Form Requests, Resources, Notificações assíncronas
- **Stack:** Laravel 12, PHP 8.5, MySQL 8, JWT, Docker, Nginx
- **Testes:** 38 automatizados (unitários e feature, SQLite em memória)
- **Fila:** Worker dedicado, notificações por email e database
- **Segurança:** JWT, CORS, validação em múltiplas camadas

**Frontend (`mfe-orders/`):**
- **Framework:** Vue 3 com Composition API
- **Build Tool:** Vite
- **State Management:** Pinia
- **HTTP Client:** Axios com interceptors JWT
- **UI/UX:** Vue Toastification, Vue Router com guards

## Regras de Negócio
- Usuário comum só vê/edita seus pedidos, admin vê todos
- Só admin pode alterar status
- Pedido confirmado não pode ser cancelado
- Datas validadas (partida ≥ hoje, retorno ≥ partida)

## Status possíveis do Pedido

Os pedidos podem assumir os seguintes status:

- `pending` — Pedido criado, aguardando aprovação/confirmação.
- `confirmed` — Pedido aprovado e confirmado.
- `cancelled` — Pedido cancelado (não pode ser revertido após confirmação).

Esses status são usados em toda a API e nas notificações.

## Notificações de Status de Pedido

Sempre que o status de um pedido é alterado (ex: de "aguardando" para "confirmado" ou "cancelado"), o sistema dispara uma notificação assíncrona para o usuário. Essa notificação é enviada por email e também registrada no banco de dados, permitindo consulta posterior via API.

- **Assíncrono:** O envio é feito em background, via fila, para não impactar a performance da API.
- **Canais:** Email e database (ambos configurados na notificação).
- **Fila dedicada:** As notificações usam a fila `notifications`, processada por um worker separado.
- **Conteúdo:** O usuário recebe detalhes do pedido, novo status, destino e datas.

## Estrutura dos Containers Docker

O sistema é dividido em múltiplos containers (configurados em `docker-compose.yml` na raiz do monorepo):

- **frontend:** Container Vue 3 com Vite dev server (porta 5173). Código em `mfe-orders/`.
- **app:** Container principal Laravel, executa a aplicação (API, comandos artisan, migrations). Código em `ms-orders/`.
- **worker:** Executa o worker de fila do Laravel (`php artisan queue:work`). Processa tarefas assíncronas como envio de notificações, sem impactar o tempo de resposta da API.
- **nginx:** Servidor web reverso para o backend, recebe requisições HTTP e repassa para o container `app`. Serve a API na porta 8000.
- **db:** Banco de dados MySQL 8.0, armazena dados da aplicação.

**Por que essa divisão?**

- Permite escalar cada parte de forma independente (ex: mais workers para alta demanda de notificações).
- Isola responsabilidades: web, processamento assíncrono, banco e aplicação ficam separados.
- Segue boas práticas de arquitetura de microsserviços e conteinerização.

## 📝 Notas sobre o Monorepo

Este projeto usa uma estrutura de monorepo para facilitar o desenvolvimento integrado de frontend e backend:

- **Vantagens:**
  - Versionamento sincronizado de frontend e backend
  - Facilita refatorações que afetam ambos os projetos
  - Deploy simplificado com Docker Compose único
  - Documentação centralizada

- **Configurações específicas:**
  - `.gitignore` na raiz: apenas regras globais (OS, IDE)
  - `.gitignore` em cada projeto: regras específicas (node_modules, vendor)
  - `.editorconfig` na raiz: configuração unificada (2 espaços para JS/Vue, 4 para PHP)
  - `docker-compose.yml` na raiz: orquestra todos os serviços