import i18n from '@/i18n'
import translateError from './errorTranslator'

const { t } = i18n.global


export const handleApiError = (error) => {
  if (!error.response) {
    if (error.code === 'ECONNABORTED') {
      return {
        type: 'timeout',
        message: t('errors.timeout'),
        originalError: error,
        severity: 'critical'
      }
    }

    return {
      type: 'network',
      message: t('errors.network'),
      originalError: error,
      severity: 'critical'
    }
  }

  const status = error.response.status

  if (status >= 500) {
    return {
      type: 'server',
      message: t('errors.server'),
      originalError: error,
      severity: 'critical'
    }
  }

  if (status >= 400) {
    if (error.response.data?.errors) {
      const errors = error.response.data.errors
      const firstError = Object.values(errors)[0]
      const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError
      
      return {
        type: 'validation',
        message: translateError(errorMessage),
        originalError: error,
        severity: 'normal'
      }
    }

    if (status === 403) {
      const message = error.response.data?.message || ''
      return {
        type: 'authorization',
        message: translateError(message) || t('errors.unauthorized'),
        originalError: error,
        severity: 'normal'
      }
    }

    if (error.response.data?.message) {
      return {
        type: 'business',
        message: translateError(error.response.data.message),
        originalError: error,
        severity: 'normal'
      }
    }
  }

  return {
    type: 'unknown',
    message: t('errors.unexpected'),
    originalError: error,
    severity: 'normal'
  }
}

export default handleApiError
