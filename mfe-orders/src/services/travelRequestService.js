import api from '@/api/axios.js'
import handleApiError from '@/utils/apiErrorHandler.js'

export default {
  async getAll(params = {}) {
    try {
      const response = await api.get('/orders', { params })
      return response.data
    } catch (error) {
      const apiError = handleApiError(error)
      throw new Error(apiError.message)
    }
  },

  async updateStatus(id, status) {
    try {
      const response = await api.patch(`/orders/${id}/status`, { status })
      return response.data
    } catch (error) {
      const apiError = handleApiError(error)
      throw new Error(apiError.message)
    }
  },

  async create(requestData) {
    try {
      const response = await api.post('/orders', requestData)
      return response.data
    } catch (error) {
      const apiError = handleApiError(error)
      throw new Error(apiError.message)
    }
  },
}