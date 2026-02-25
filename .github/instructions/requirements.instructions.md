Você é um arquiteto de software sênior especializado em backend REST, autenticação JWT e frontend com Vue 3.

Objetivo:
Revisar os critérios de aceite abaixo para garantir que todos os requisitos estejam implementados corretamente, seguindo boas práticas, separação de responsabilidades e organização de código, conteinerização e escalabilidade.

Critérios de Aceite:

BACKEND
- Criar pedido de viagem (id, solicitante, destino, data ida, data volta, status: solicitado | aprovado | cancelado)
- Consultar pedido por ID
- Listar pedidos com filtros por status, período e destino
- Atualizar status (somente admin pode aprovar/cancelar)
- Cancelar pedido apenas se ainda não aprovado
- Enviar notificação ao usuário ao aprovar ou cancelar

FRONTEND
- Dashboard com tabela e filtro por status
- Formulário de criação de pedido
- Atualização de status na tabela ou página dedicada
- Autenticação com JWT
- Feedback visual (loading + mensagens de sucesso/erro)
- Boas práticas com Vue 3 + Composition API
- Frontend em repositório separado

- O README geral deve explicar e trazer o passo a passo para os serviços sejam subidos localmente em outro ambiente, além de uma visão geral da arquitetura, tecnologias usadas, regras de negócio e detalhes de implementação

Regras:
- Aplicar Clean Architecture ou arquitetura em camadas
- Aplicar SOLID
- Garantir segurança e validação de dados
- Separar claramente backend e frontend
- Pensar como sistema pronto para produção mas lembrando que é um teste técnico (foco em qualidade, organização e boas práticas, não em features extras)