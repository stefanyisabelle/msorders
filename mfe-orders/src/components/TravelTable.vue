<template>
  <div class="overflow-x-auto">
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      <p class="mt-2 text-gray-600">{{ $t('table.loading') }}</p>
    </div>

    <table v-else class="table">
      <thead>
        <tr>
          <th>
            <div class="flex items-center gap-2">
              {{ $t('table.id') }}
              <button @click="showIdFilter = !showIdFilter" :class="hasIdFilter ? 'text-primary-600' : 'text-gray-400 hover:text-primary-600'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :fill="hasIdFilter ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
              </button>
            </div>
          </th>
          <th>{{ $t('table.customer') }}</th>
          <th>
            <div class="flex items-center gap-2">
              {{ $t('table.destination') }}
              <button @click="showDestinationFilter = !showDestinationFilter" :class="hasDestinationFilter ? 'text-primary-600' : 'text-gray-400 hover:text-primary-600'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :fill="hasDestinationFilter ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
              </button>
            </div>
          </th>
          <th>
            <div class="flex items-center gap-2">
              {{ $t('table.departureDate') }}
              <button @click="showDepartureDateFilter = !showDepartureDateFilter" :class="hasDepartureDateFilter ? 'text-primary-600' : 'text-gray-400 hover:text-primary-600'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :fill="hasDepartureDateFilter ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
              </button>
            </div>
          </th>
          <th>
            <div class="flex items-center gap-2">
              {{ $t('table.returnDate') }}
              <button @click="showReturnDateFilter = !showReturnDateFilter" :class="hasReturnDateFilter ? 'text-primary-600' : 'text-gray-400 hover:text-primary-600'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :fill="hasReturnDateFilter ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
              </button>
            </div>
          </th>
          <th>
            <div class="flex items-center gap-2">
              {{ $t('table.status') }}
              <button @click="showStatusFilter = !showStatusFilter" :class="hasStatusFilter ? 'text-primary-600' : 'text-gray-400 hover:text-primary-600'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :fill="hasStatusFilter ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
              </button>
            </div>
          </th>
          <th v-if="isAdmin">{{ $t('table.actions') }}</th>
        </tr>
        <tr v-if="showIdFilter || showDestinationFilter || showDepartureDateFilter || showReturnDateFilter || showStatusFilter" class="bg-gray-50">
          <th>
            <input
              v-if="showIdFilter"
              type="number"
              v-model="idInput"
              @input="handleIdInput"
              placeholder="#"
              class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            />
          </th>
          <th></th>
          <th>
            <div v-if="showDestinationFilter" class="relative">
              <input
                type="text"
                v-model="destinationInput"
                @input="handleDestinationInput"
                :placeholder="$t('table.filterDestination')"
                class="w-full px-2 py-1 pr-8 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
              <button
                v-if="destinationInput"
                @click="clearDestination"
                type="button"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </th>
          <th>
            <div v-if="showDepartureDateFilter" class="relative">
              <input
                type="date"
                v-model="departureDateInput"
                @change="handleDepartureDateChange"
                class="w-full px-3 py-3 pr-10 text-base border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
              <button
                v-if="departureDateInput"
                @click="clearDepartureDate"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </th>
          <th>
            <div v-if="showReturnDateFilter" class="relative">
              <input
                type="date"
                v-model="returnDateInput"
                @change="handleReturnDateChange"
                class="w-full px-3 py-3 pr-10 text-base border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
              <button
                v-if="returnDateInput"
                @click="clearReturnDate"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </th>
          <th>
            <div v-if="showStatusFilter" class="space-y-2">
              <label v-for="status in statusOptions" :key="status.value" class="flex items-center gap-2 text-sm font-normal">
                <input
                  type="checkbox"
                  :value="status.value"
                  v-model="tempSelectedStatuses"
                  class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                />
                <span>{{ status.label }}</span>
              </label>
              <button
                @click="applyStatusFilter"
                class="w-full mt-2 px-3 py-1.5 text-sm bg-primary-600 text-white rounded hover:bg-primary-700 transition-colors"
              >
                {{ $t('actions.apply') }}
              </button>
            </div>
          </th>
          <th v-if="isAdmin"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="requests.length === 0">
          <td :colspan="isAdmin ? 7 : 6" class="text-center py-8 text-gray-500">
            {{ $t('table.noResults') }}
          </td>
        </tr>
        <tr v-for="req in requests" :key="req.id">
          <td class="font-semibold text-primary-600">#{{ req.id }}</td>
          <td>{{ req.customer_name }}</td>
          <td>{{ req.destination }}</td>
          <td>{{ formatDate(req.departure_date) }}</td>
          <td>{{ formatDate(req.return_date) }}</td>
          <td>
            <StatusBadge :status="req.status" />
          </td>
          <td v-if="isAdmin">
            <button 
              @click="openActionModal(req)" 
              class="text-gray-600 hover:text-primary-600 p-1 rounded hover:bg-gray-100"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal de Ações -->
    <div v-if="showModal" @click.self="closeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $t('table.orderActions') }} #{{ selectedRequest?.id }}</h3>
        
        <div class="mb-4 text-sm text-gray-600">
          <p><strong>{{ $t('table.customer') }}:</strong> {{ selectedRequest?.customer_name }}</p>
          <p><strong>{{ $t('table.destination') }}:</strong> {{ selectedRequest?.destination }}</p>
          <p><strong>{{ $t('table.status') }}:</strong> <StatusBadge :status="selectedRequest?.status" /></p>
        </div>

        <div class="space-y-3">
          <button
            v-if="selectedRequest?.status === 'solicitado'"
            @click="handleAction(selectedRequest.id, 'aprovado')"
            :disabled="loadingIds.includes(selectedRequest.id)"
            class="w-full px-4 py-2 rounded-lg font-medium transition-all bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          >
            <span v-if="loadingIds.includes(selectedRequest.id)">{{ $t('actions.processing') }}</span>
            <span v-else>{{ $t('actions.approve') }}</span>
          </button>

          <button
            v-if="selectedRequest?.status !== 'cancelado' && selectedRequest?.status !== 'aprovado'"
            @click="showCancelConfirmation = true"
            :disabled="loadingIds.includes(selectedRequest.id)"
            class="w-full px-4 py-2 rounded-lg font-medium transition-all bg-red-400 text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
          >
            <span v-if="loadingIds.includes(selectedRequest.id)">{{ $t('actions.processing') }}</span>
            <span v-else>{{ $t('actions.cancel') }}</span>
          </button>

          <button @click="closeModal" class="btn btn-secondary w-full">
            {{ $t('actions.logout') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmação de Cancelamento -->
    <div v-if="showCancelConfirmation" @click.self="showCancelConfirmation = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $t('modal.cancelOrderTitle') }}</h3>
        
        <p class="text-gray-600 mb-6">
          {{ $t('modal.cancelOrderMessage', { id: selectedRequest?.id }) }}
        </p>

        <div class="flex gap-3">
          <button
            @click="confirmCancel"
            :disabled="loadingIds.includes(selectedRequest?.id)"
            class="flex-1 px-4 py-2 rounded-lg font-medium transition-all bg-red-400 text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
          >
            <span v-if="loadingIds.includes(selectedRequest?.id)">{{ $t('actions.processing') }}</span>
            <span v-else>{{ $t('actions.confirmCancel') }}</span>
          </button>
          <button
            @click="showCancelConfirmation = false"
            class="flex-1 px-4 py-2 rounded-lg font-medium transition-all bg-gray-200 text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
          >
            {{ $t('actions.no') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/authStore.js'
import StatusBadge from './StatusBadge.vue'

const { t } = useI18n()

const props = defineProps({
  requests: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  filters: { type: Object, default: () => ({ id: '', status: [], destination: '', departureDate: '', returnDate: '' }) }
})

const emit = defineEmits(['update-status', 'update:filters'])

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)

const loadingIds = ref([])
const idInput = ref(props.filters.id || '')
const destinationInput = ref(props.filters.destination)
const departureDateInput = ref(props.filters.departureDate || '')
const returnDateInput = ref(props.filters.returnDate || '')
const selectedStatuses = ref(Array.isArray(props.filters.status) ? props.filters.status : [])
const tempSelectedStatuses = ref([...selectedStatuses.value])
const showIdFilter = ref(false)
const showDestinationFilter = ref(false)
const showDepartureDateFilter = ref(false)
const showReturnDateFilter = ref(false)
const showStatusFilter = ref(false)
const showModal = ref(false)
const showCancelConfirmation = ref(false)
const selectedRequest = ref(null)

let debounceTimeout = null

const statusOptions = [
  { value: 'solicitado', label: t('status.pending') },
  { value: 'aprovado', label: t('status.confirmed') },
  { value: 'cancelado', label: t('status.cancelled') }
]

const hasIdFilter = computed(() => !!idInput.value)
const hasDestinationFilter = computed(() => !!destinationInput.value)
const hasDepartureDateFilter = computed(() => !!departureDateInput.value)
const hasReturnDateFilter = computed(() => !!returnDateInput.value)
const hasStatusFilter = computed(() => selectedStatuses.value.length > 0)

watch(() => props.filters.id, (newVal) => {
  idInput.value = newVal || ''
})

watch(() => props.filters.destination, (newVal) => {
  destinationInput.value = newVal
})

watch(() => props.filters.departureDate, (newVal) => {
  departureDateInput.value = newVal || ''
})

watch(() => props.filters.returnDate, (newVal) => {
  returnDateInput.value = newVal || ''
})

watch(() => props.filters.status, (newVal) => {
  selectedStatuses.value = Array.isArray(newVal) ? newVal : []
  tempSelectedStatuses.value = [...selectedStatuses.value]
})

const handleIdInput = () => {
  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
  }
  debounceTimeout = setTimeout(() => {
    emit('update:filters', { ...props.filters, id: idInput.value })
  }, 1000)
}

const handleDestinationInput = () => {
  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
  }
  debounceTimeout = setTimeout(() => {
    emit('update:filters', { ...props.filters, destination: destinationInput.value })
  }, 1000)
}

const clearDestination = () => {
  destinationInput.value = ''
  emit('update:filters', { ...props.filters, destination: '' })
}

const handleDepartureDateChange = () => {
  emit('update:filters', { ...props.filters, departureDate: departureDateInput.value })
}

const handleReturnDateChange = () => {
  emit('update:filters', { ...props.filters, returnDate: returnDateInput.value })
}

const clearDepartureDate = () => {
  departureDateInput.value = ''
  emit('update:filters', { ...props.filters, departureDate: '' })
}

const clearReturnDate = () => {
  returnDateInput.value = ''
  emit('update:filters', { ...props.filters, returnDate: '' })
}

const applyStatusFilter = () => {
  selectedStatuses.value = [...tempSelectedStatuses.value]
  emit('update:filters', { ...props.filters, status: selectedStatuses.value })
}

const isAdmin = computed(() => user.value?.role === 'admin')

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const [year, month, day] = dateString.split('-')
  const date = new Date(year, month - 1, day)
  return date.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const openActionModal = (request) => {
  selectedRequest.value = request
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  showCancelConfirmation.value = false
  selectedRequest.value = null
}

const confirmCancel = () => {
  if (selectedRequest.value) {
    handleAction(selectedRequest.value.id, 'cancelado')
    showCancelConfirmation.value = false
  }
}

const handleAction = (id, status) => {
  if (!loadingIds.value.includes(id)) {
    loadingIds.value.push(id)
    emit('update-status', { 
      id, 
      status, 
      done: () => {
        const index = loadingIds.value.indexOf(id)
        if (index > -1) loadingIds.value.splice(index, 1)
        closeModal()
      }
    })
  }
}
</script>