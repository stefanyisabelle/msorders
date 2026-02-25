import api from '@/api/axios.js'
import handleApiError from '@/utils/apiErrorHandler.js'

export default {
  async login(email, password) {
    try {
      const response = await api.post('/auth/login', { email, password })
      return response.data
    } catch (error) {
      const apiError = handleApiError(error)
      const err = new Error(apiError.message)
      err.status = error.response?.status
      throw err
    }
  },

  async logout() {
    try {
      await api.post('/auth/logout')
      return true
    } catch (error) {
      return false
    }
  },

  async getUser() {
    try {
      const response = await api.get('/auth/user')
      return response.data
    } catch (error) {
      return null
    }
  },
}