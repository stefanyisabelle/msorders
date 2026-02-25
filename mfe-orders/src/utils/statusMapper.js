/**
 * Mapeamento de status entre backend (inglês) e frontend (português)
 */
export const STATUS_MAP = {
  // Backend → Frontend
  backend: {
    pending: 'solicitado',
    confirmed: 'aprovado',
    cancelled: 'cancelado',
  },
  // Frontend → Backend
  frontend: {
    solicitado: 'pending',
    aprovado: 'confirmed',
    cancelado: 'cancelled',
  },
}

/**
 * Converte status do backend (inglês) para frontend (português)
 * @param {string} status - Status em inglês (pending, confirmed, cancelled)
 * @returns {string} Status em português (solicitado, aprovado, cancelado)
 */
export const toFrontend = (status) => {
  return STATUS_MAP.backend[status] || status
}

/**
 * Converte status do frontend (português) para backend (inglês)
 * @param {string} status - Status em português (solicitado, aprovado, cancelado)
 * @returns {string} Status em inglês (pending, confirmed, cancelled)
 */
export const toBackend = (status) => {
  return STATUS_MAP.frontend[status] || status
}

/**
 * Constantes de status em português (para uso no frontend)
 */
export const ORDER_STATUS = {
  SOLICITADO: 'solicitado',
  APROVADO: 'aprovado',
  CANCELADO: 'cancelado',
}

/**
 * Constantes de roles de usuário
 */
export const ROLES = {
  ADMIN: 'admin',
  USER: 'user',
}
