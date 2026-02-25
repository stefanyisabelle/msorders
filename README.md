# Microserviço de Gestão de Pedidos

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.0">
  <img src="https://img.shields.io/badge/Docker-Latest-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/Tests-38_Passing-success?style=for-the-badge" alt="Tests">
</p>

## Sobre

Microserviço RESTful para gestão de pedidos de viagem (Laravel 12, JWT, Docker, MySQL). Permite CRUD de pedidos, autenticação, roles, notificações assíncronas e fila, com testes automatizados e arquitetura em camadas.

**💡 Inclui frontend Vue 3 totalmente integrado** — Interface moderna com formulário de criação, dashboard com filtros, gestão de status e autenticação JWT.

## 🎯 Critérios de Aceite — Status de Implementação

### Backend ✅
- ✅ **Criar pedido de viagem** — Inclui ID, solicitante, destino, datas, status
- ✅ **Consultar pedido por ID** — Endpoint GET `/api/orders/{id}` com detalhes completos
- ✅ **Listar todos os pedidos** — Endpoint GET `/api/orders` com filtros por status, período e destino
- ✅ **Atualizar status** — Apenas admin pode aprovar/cancelar via PATCH `/api/orders/{id}/status`
- ✅ **Regra de cancelamento** — Pedido aprovado não pode ser cancelado (validação em Policy e Service)
- ✅ **Notificações** — Enviadas via fila assíncrona ao aprovar/cancelar (email + database)

### Frontend ✅
- ✅ **Dashboard** — Tabela interativa com todos os pedidos e filtro por status
- ✅ **Formulário de criação** — Componente TravelForm com validação de datas
- ✅ **Atualização de status** — Botões inline na tabela (aprovar/cancelar), apenas para admin
- ✅ **Autenticação JWT** — Login/logout completo, token armazenado, rotas protegidas
- ✅ **Feedback visual** — Toast notifications, loading states, mensagens de erro
- ✅ **Boas práticas Vue 3** — Composition API, Pinia stores, storeToRefs, composables, mapeamento de dados
- ✅ **Repositório separado** — Frontend em `mfe-orders/` rodando independente na porta 5173

## 🚀 Instalação Completa (Backend + Frontend)

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
php artisan db:seed # opcional - cria usuários admin e user
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

### Estrutura de Pastas
```
mfe-orders/src/
├── api/              # Configuração Axios (interceptors, baseURL)
├── components/       # Componentes Vue reutilizáveis
│   ├── TravelForm.vue         # Formulário de criação
│   ├── TravelTable.vue        # Tabela de pedidos
│   └── StatusBadge.vue        # Badge de status
├── layouts/          # Layouts compartilhados (MainLayout com navbar)
├── router/           # Rotas e guards de autenticação
├── services/         # Camada de comunicação com API
│   ├── authService.js         # Auth endpoints
│   └── travelRequestService.js # Orders endpoints
├── stores/           # Pinia stores (estado global)
│   ├── authStore.js           # Estado de autenticação
│   └── travelStore.js         # Estado dos pedidos
├── utils/            # Utilitários e constantes
│   └── statusMapper.js        # Mapeamento status PT↔EN
└── views/            # Páginas principais
    ├── LoginView.vue
    └── DashboardView.vue
```

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

**Mapeamento de Status:**
- Frontend usa português (`solicitado`, `aprovado`, `cancelado`)
- Backend usa inglês (`pending`, `confirmed`, `cancelled`)
- Conversão automática via `utils/statusMapper.js`

**Mapeamento de Campos:**
- Frontend: `customer_name`, `departure_date`, `return_date`
- Alinhado com schema do backend Laravel

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

## 🔧 Comandos Úteis

### Backend (Laravel)
```bash
# Entrar no container
docker-compose exec app bash

# Rodar migrations
php artisan migrate

# Rodar seeders
php artisan db:seed

# Rodar testes
php artisan test

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rotas disponíveis
php artisan route:list

# Processar fila manualmente
php artisan queue:work

# Ver logs
docker-compose logs -f app
docker-compose logs -f worker
docker-compose logs -f frontend
```

### Frontend (Vue 3)
```bash
# Com Docker (recomendado - já incluído no docker-compose)
docker-compose up -d frontend
docker-compose logs -f frontend

# OU Local (sem Docker)
cd mfe-orders

# Instalar dependências
npm install

# Rodar em desenvolvimento
npm run dev

# Build para produção
npm run build

# Preview da build
npm run preview
```

### Docker
```bash
# Subir todos os containers (backend + frontend + db + worker)
docker-compose up -d

# Parar todos os containers
docker-compose down

# Rebuild dos containers
docker-compose up -d --build

# Ver status dos containers
docker-compose ps

# Ver logs em tempo real
docker-compose logs -f
```

## 🐛 Troubleshooting

### Backend não inicia
- Verifique se as portas 8000, 3306 e 80 estão livres
- Rode `docker-compose logs app` para ver erros
- Certifique-se que `.env` foi criado a partir de `.env.example`
- Verifique se `JWT_SECRET` foi gerado com `php artisan jwt:secret`

### Frontend não inicia (Docker)
- Verifique se a porta 5173 está livre: `lsof -i :5173`
- Veja os logs: `docker-compose logs frontend`
- Certifique-se que `mfe-orders/.env` existe
- Recrie o container: `docker-compose up -d --build frontend`
- Verifique se `package.json` tem os scripts corretos

### Frontend não conecta com backend
- Verifique se `VITE_API_URL` no `.env` do frontend está correto (padrão: `http://localhost:8000/api`)
- Certifique-se que backend está rodando (`docker-compose ps`)
- Verifique CORS no backend (`config/cors.php` deve ter `FRONTEND_URL` configurado)
- Abra o console do navegador (F12) para ver erros de rede
- Se rodando via Docker, o backend deve ser acessível do host (não de dentro do container)

### Erro "CORS policy" no navegador
- Verifique se `FRONTEND_URL=http://localhost:5173` está configurado no `.env` do backend
- Reinicie o container do backend: `docker-compose restart app`
- Verifique `config/cors.php` - deve ter `'supports_credentials' => false` ou `true` dependendo do uso

### Token JWT expirado
- Frontend redireciona automaticamente para login (interceptor 401)
- Use o endpoint `/api/auth/refresh` para renovar o token
- Token expira em 60 minutos (configurável em `config/jwt.php`)

### Notificações não são enviadas
- Verifique se o worker está rodando: `docker-compose ps` (deve mostrar container `msorders_worker`)
- Ver logs do worker: `docker-compose logs -f worker`
- Verifique configuração de email no `.env` do backend (`MAIL_MAILER=log` para desenvolvimento)
- Cheque as notificações no banco: tabela `notifications`

### Migrations falham
- Certifique-se que banco de dados está acessível
- Tente limpar cache: `php artisan config:clear`
- Rode migrations em ordem: `php artisan migrate:fresh --seed`

### Containers não sobem
- Verifique se Docker está rodando: `docker ps`
- Limpe containers antigos: `docker-compose down -v`
- Recrie tudo: `docker-compose up -d --build`
- Verifique conflitos de porta: `docker-compose ps` e `lsof -i :8000 -i :5173 -i :3306`

## 📄 Licença

Este projeto é open-source e está disponível sob a licença MIT.