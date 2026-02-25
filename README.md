# Gestão de Pedidos

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0">
  <img src="https://img.shields.io/badge/Docker-Latest-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
</p>

## Sobre

Microserviço RESTful para gestão de pedidos de viagem (Laravel 12, JWT, Docker, MySQL). Permite CRUD de pedidos, autenticação, roles, notificações assíncronas e fila, com testes automatizados e arquitetura em camadas.

Frontend Vue 3 totalmente integrado** — Interface moderna com formulário de criação, dashboard com filtros, gestão de status e autenticação JWT.

## Instalação Completa (Backend + Frontend)

### Opção 1: Usando Docker Compose (Recomendado)

O Docker Compose sobe automaticamente **backend + frontend + banco de dados + worker de fila** em um único comando:

```bash
# 1. Instale dependências PHP do backend
composer install

# 2. Copie e edite o .env do backend
cp .env.example .env
# Configure variáveis se necessário (DB, JWT, etc)

# 3. Copie e edite o .env do frontend
cp mfe-orders/.env.example mfe-orders/.env
# Padrão: VITE_API_URL=http://localhost:8000/api

# 4. Suba todos os containers (backend + frontend + db + worker)
docker-compose up -d --build

# 5. Configure a aplicação Laravel (dentro do container)
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
# 1. Instale dependências
composer install

# 2. Configure .env
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# 3. Configure banco de dados (ajuste .env para MySQL local)
php artisan migrate
php artisan db:seed

# 4. Inicie servidor
php artisan serve
# API disponível em http://localhost:8000

# 5. Em outro terminal, rode o worker de fila
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
## Rodando Testes

- **No container:**
  ```bash
  docker-compose exec app php artisan test
  ```
- **Local:**
  ```bash
  php artisan test
  ```
  (usa SQLite em memória, via `.env.testing`)

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

- **Arquitetura:** Controllers finos, Service Layer, Policies, Form Requests, Resources, Notificações assíncronas
- **Stack:** Laravel 12, PHP 8.5, MySQL 8, JWT, Docker, Nginx
- **Testes:** 38 automatizados (unitários e feature, SQLite em memória)
- **Fila:** Worker dedicado, notificações por email e database
- **Segurança:** JWT, CORS, validação em múltiplas camadas

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

O sistema é dividido em múltiplos containers para garantir isolamento, escalabilidade e facilidade de manutenção:

- **app:** Container principal, executa a aplicação Laravel (API, comandos artisan, migrations, etc). Responsável por servir a aplicação PHP.
- **worker:** Executa o comando de worker de fila do Laravel (`php artisan queue:work`). Processa tarefas assíncronas, como envio de notificações, sem impactar o tempo de resposta da API. Permite escalar horizontalmente apenas o processamento de filas, se necessário.
- **nginx:** Servidor web reverso, responsável por receber as requisições HTTP e repassá-las para o container `app`. Garante performance, segurança e serve arquivos estáticos.
- **db:** Banco de dados MySQL, armazena dados da aplicação, pedidos, usuários e notificações.

**Por que essa divisão?**

- Permite escalar cada parte de forma independente (ex: mais workers para alta demanda de notificações).
- Isola responsabilidades: web, processamento assíncrono, banco e aplicação ficam separados, facilitando debug, deploy e manutenção.
- Segue boas práticas de arquitetura de microsserviços e conteinerização.