import { defineStore } from 'pinia'
import travelService from '@/services/travelRequestService.js'
import { useToast } from 'vue-toastification'
import { toFrontend, toBackend } from '@/utils/statusMapper.js'
import i18n from '@/i18n'

const toast = useToast()
const { t } = i18n.global

export const useTravelStore = defineStore('travel', {
  state: () => ({
    requests: [],
    loading: false,
    error: null,
    filters: {
      id: '',
      status: [],
      destination: '',
      departureDate: '',
      returnDate: '',
    },
  }),

  actions: {
    async create(payload) {
        this.loading = true
        this.error = null
        try {
            await travelService.create(payload)
            await this.fetchRequests()
            toast.success(t('messages.orderCreated'))
        } catch (err) {
            this.error = err.message || t('messages.createOrderError')
            toast.error(this.error)
            throw err
        } finally {
            this.loading = false
        }
        },
    async fetchRequests() {
      this.loading = true
      this.error = null
      try {
        // Preparar filtros - remover valores vazios
        const cleanFilters = {}
        if (this.filters.id) cleanFilters.id = this.filters.id
        if (Array.isArray(this.filters.status) && this.filters.status.length > 0) {
          cleanFilters.status = this.filters.status.map(s => toBackend(s))
        } else if (typeof this.filters.status === 'string' && this.filters.status) {
          cleanFilters.status = toBackend(this.filters.status)
        }
        if (this.filters.destination) cleanFilters.destination = this.filters.destination
        if (this.filters.departureDate) cleanFilters.departure_date = this.filters.departureDate
        if (this.filters.returnDate) cleanFilters.return_date = this.filters.returnDate

        const data = await travelService.getAll(cleanFilters)
        this.requests = Array.isArray(data) ? data.map(req => ({
          ...req,
          status: toFrontend(req.status),
        })) : []
      } catch (err) {
        this.error = err.message || t('messages.fetchOrdersError')
        toast.error(this.error)
      } finally {
        this.loading = false
      }
    },

    async updateStatus(id, status) {
  const request = this.requests.find(r => r.id === id)

  if (!request) {
    throw new Error(t('messages.orderNotFound'))
  }

  if (status === 'cancelado' && request.status === 'aprovado') {
    throw new Error(t('messages.cannotCancelApproved'))
  }

  if (status === 'aprovado' && request.status !== 'solicitado') {
    throw new Error(t('messages.onlyPendingCanBeApproved'))
  }

  this.loading = true
  try {
    await travelService.updateStatus(id, toBackend(status))
    await this.fetchRequests()
  } catch (err) {
    this.error = err.message || t('messages.updateStatusError')
    toast.error(this.error)
    throw err
  } finally {
    this.loading = false
  }
},

    setFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters }
      this.fetchRequests()
    },
  },
})