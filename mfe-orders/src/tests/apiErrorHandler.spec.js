import { describe, it, expect, vi } from 'vitest'
import { handleApiError } from '@/utils/apiErrorHandler'

vi.mock('@/i18n', () => ({
  default: {
    global: {
      t: (key) => {
        const translations = {
          'errors.network': 'Não foi possível conectar ao servidor. Entre em contato com o administrador.',
          'errors.timeout': 'O servidor demorou muito para responder. Tente novamente mais tarde.',
          'errors.server': 'Erro interno do servidor. Entre em contato com o administrador.',
          'errors.unexpected': 'Ocorreu um erro inesperado.',
          'errors.unauthorized': 'Você não tem permissão para atualizar o status deste pedido.',
        }
        return translations[key] || key
      }
    }
  }
}))

vi.mock('@/utils/errorTranslator', () => ({
  default: (msg) => msg
}))

describe('apiErrorHandler', () => {
  it('deve classificar erro de rede (sem response)', () => {
    const error = {
      message: 'Network Error',
      code: 'ERR_NETWORK'
    }

    const result = handleApiError(error)

    expect(result.type).toBe('network')
    expect(result.severity).toBe('critical')
    expect(result.message).toContain('servidor')
  })

  it('deve classificar timeout', () => {
    const error = {
      code: 'ECONNABORTED',
      message: 'timeout of 5000ms exceeded'
    }

    const result = handleApiError(error)

    expect(result.type).toBe('timeout')
    expect(result.severity).toBe('critical')
  })

  it('deve classificar erro 500 como server error', () => {
    const error = {
      response: {
        status: 500,
        data: { message: 'Internal Server Error' }
      }
    }

    const result = handleApiError(error)

    expect(result.type).toBe('server')
    expect(result.severity).toBe('critical')
  })

  it('deve classificar erro de validação (422)', () => {
    const error = {
      response: {
        status: 422,
        data: {
          errors: {
            email: ['The email field is required.']
          }
        }
      }
    }

    const result = handleApiError(error)

    expect(result.type).toBe('validation')
    expect(result.severity).toBe('normal')
  })

  it('deve classificar erro de autorização (403)', () => {
    const error = {
      response: {
        status: 403,
        data: {
          message: 'Forbidden'
        }
      }
    }

    const result = handleApiError(error)

    expect(result.type).toBe('authorization')
    expect(result.severity).toBe('normal')
  })
})
