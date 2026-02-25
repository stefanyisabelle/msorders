import { defineStore } from 'pinia'
import travelService from '@/services/travelRequestService.js'
import { useToast } from 'vue-toastification'
import { toFrontend, toBackend } from '@/utils/statusMapper.js'

const toast = useToast()

export const useTravelStore = defineStore('travel', {
  state: () => ({
    requests: [],
    loading: false,
    error: null,
    filters: {
      status: '',
      destination: '',
      startDate: '',
      endDate: '',
    },
  }),

  actions: {
    async create(payload) {
        this.loading = true
        this.error = null
        try {
            await travelService.create(payload)
            await this.fetchRequests() // Atualiza tabela automaticamente
            toast.success('Pedido criado com sucesso!')
        } catch (err) {
            this.error = err.message || 'Erro ao criar pedido'
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
        const data = await travelService.getAll(this.filters)
        // Mapear status e campos do backend para frontend
        this.requests = Array.isArray(data) ? data.map(req => ({
          ...req,
          status: toFrontend(req.status),
        })) : []
      } catch (err) {
        this.error = err.message || 'Falha ao buscar pedidos'
        toast.error(this.error)
      } finally {
        this.loading = false
      }
    },

    async updateStatus(id, status) {
  const request = this.requests.find(r => r.id === id)

  if (!request) {
    throw new Error('Pedido não encontrado')
  }

  if (status === 'cancelado' && request.status === 'aprovado') {
    throw new Error('Não é possível cancelar um pedido já aprovado')
  }

  if (status === 'aprovado' && request.status !== 'solicitado') {
    throw new Error('Apenas pedidos solicitados podem ser aprovados')
  }

  this.loading = true
  try {
    // Converter status para backend antes de enviar
    await travelService.updateStatus(id, toBackend(status))
    await this.fetchRequests()
  } catch (err) {
    this.error = err.message || 'Falha ao atualizar status'
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