Critérios de aceite:

BACKEND ✅ COMPLETO

✅ Criar um pedido de viagem: Um pedido deve incluir o ID do pedido, o nome do solicitante, o destino, a data de ida, a data de volta e o status (solicitado, aprovado, cancelado).
✅ Consultar um pedido de viagem: Retornar as informações detalhadas de um pedido de viagem com base no ID fornecido.
✅ Listar todos os pedidos de viagem: Retornar todos os pedidos de viagem cadastrados, com a opção de filtrar por status, período de tempo (ex: pedidos feitos ou com datas de viagem dentro de uma faixa de datas) e destino.
✅ Atualizar o status de um pedido de viagem: Possibilitar a atualização do status para "aprovado" ou "cancelado". (nota: o usuário que fez o pedido não pode alterar o status do mesmo, somente um usuário administrador)
✅ Cancelar pedido de viagem após aprovação: Implementar uma lógica de negócios que só permita o cancelamento do pedido caso ele ainda não tenha sido aprovado
✅ Notificação de aprovação ou cancelamento: Sempre que um pedido for aprovado ou cancelado, uma notificação deve ser enviada para o usuário que solicitou o pedido.

FRONTEND ✅ COMPLETO

✅ Dashboard: Uma interface principal que exibe todos os pedidos de viagem em uma tabela com a opção de filtrar por status.
✅ Formulário para criação de pedidos: Uma página ou modal para que o usuário possa criar um novo pedido de viagem.
✅ Atualização de status: Possibilite a atualização do status dos pedidos diretamente na tabela ou em uma página dedicada.
✅ Autenticação de usuário: Uma tela de login que consome a API de autenticação e armazena o token JWT para proteger as rotas da aplicação.
✅ Feedback ao usuário: Mensagens claras de sucesso ou erro ao criar ou atualizar pedidos, bem como um loading spinner durante operações assíncronas.
✅ As boas práticas, qualidade e organização de código também devem ser aplicadas no frontend com Vue.js
✅ O interessante é subir o frontend num repositório diferente para manter as boas práticas de desenvolvimento

---

## 📝 Notas de Implementação

### Backend
- **Arquitetura em camadas:** Controllers → Services → Models com Policies e Form Requests
- **Notificações assíncronas:** Fila dedicada com worker separado (email + database)
- **Testes automatizados:** 38 testes (unitários e feature) com SQLite em memória
- **Segurança:** JWT authentication, validação em múltiplas camadas, CORS configurado
- **Status do pedido:** `pending` (solicitado), `confirmed` (aprovado), `cancelled` (cancelado)

### Frontend
- **Vue 3 + Composition API:** Todas as views e componentes usando `<script setup>`
- **Pinia stores:** Gerenciamento de estado com authStore e travelStore
- **Mapeamento de dados:** Backend em inglês, frontend em português (conversão automática)
- **Interceptor 401:** Logout automático quando token expira
- **storeToRefs:** Mantém reatividade corretamente em todos os componentes
- **Estrutura organizada:** Services, stores, components, utils separados
- **Feedback visual:** Toast notifications, loading states, validação de formulários

### Integração
- **Endpoints alinhados:** Frontend consome exatamente os endpoints que backend oferece
- **JWT flow completo:** Login → armazenar token → buscar usuário → proteger rotas → logout
- **Campos mapeados:** `customer_name`, `departure_date`, `return_date` alinhados
- **Status mapeados:** Conversão automática PT↔EN via `utils/statusMapper.js`
- **Roles respeitados:** Apenas admin pode aprovar/cancelar (validado no frontend e backend)