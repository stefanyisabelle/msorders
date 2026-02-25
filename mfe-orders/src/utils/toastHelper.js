import { useToast } from 'vue-toastification'

const toast = useToast()

export const showErrorToast = (message, severity = 'normal') => {
  const timeout = severity === 'critical' ? 7000 : 5000
  
  toast.error(message, {
    timeout,
    closeButton: true,
  })
}

export const showSuccessToast = (message) => {
  toast.success(message, {
    timeout: 3000,
  })
}

export const showWarningToast = (message) => {
  toast.warning(message, {
    timeout: 5000,
  })
}

export const showInfoToast = (message) => {
  toast.info(message, {
    timeout: 4000,
  })
}

export default {
  showErrorToast,
  showSuccessToast,
  showWarningToast,
  showInfoToast,
}
