<template>
  <div class="space-y-8">

    <div class="text-align-left font-bold text-gray-900">
      <p class="text-gray-600">{{ $t('dashboard.title') }}</p>
    </div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">{{ $t('dashboard.pending') }}</p>
            <p class="text-3xl font-bold text-yellow-600">{{ stats.solicitado }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">{{ $t('dashboard.confirmed') }}</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.aprovado }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">{{ $t('dashboard.cancelled') }}</p>
            <p class="text-3xl font-bold text-red-600">{{ stats.cancelado }}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">{{ $t('dashboard.total') }}</p>
            <p class="text-3xl font-bold text-primary-600">{{ stats.total }}</p>
          </div>
        </div>
      </div>
    </div>

    <!--  Form -->
    <div class="text-align-left font-bold text-gray-900">
      <p class="text-gray-600">{{ $t('dashboard.newOrder') }}</p>
    </div>
    <TravelForm />

    <div class="text-align-left font-bold text-gray-900">
        <p class="text-gray-600">{{ $t('dashboard.ordersList') }}</p>
    </div>

    <!-- Table -->
    <div class="card">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <p class="mt-2 text-gray-600">{{ $t('dashboard.loadingOrders') }}</p>
      </div>
      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        {{ error }}
      </div>
      <TravelTable
        v-else
        :requests="requests"
        :loading="loading"
        :filters="filters"
        @update:filters="updateFilters"
        @update-status="handleUpdateStatus"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { useTravelStore } from '@/stores/travelStore.js'
import TravelTable from '@/components/TravelTable.vue'
import TravelForm from '@/components/TravelForm.vue'

const { t } = useI18n()
const toast = useToast()
const travelStore = useTravelStore()
const { requests, loading, error } = storeToRefs(travelStore)
const { fetchRequests, updateStatus, setFilters } = travelStore

const filters = reactive({
  id: '',
  status: [],
  destination: '',
  departureDate: '',
  returnDate: '',
})

const stats = computed(() => {
  const counts = {
    solicitado: 0,
    aprovado: 0,
    cancelado: 0,
    total: 0
  }

  requests.value.forEach(req => {
    if (req.status) {
      const status = req.status.toLowerCase()
      if (counts.hasOwnProperty(status)) {
        counts[status]++
      }
    }
  })

  counts.total = requests.value.length
  return counts
})

const updateFilters = (newFilters) => {
  filters.id = newFilters.id || ''
  filters.status = newFilters.status
  filters.destination = newFilters.destination
  filters.departureDate = newFilters.departureDate || ''
  filters.returnDate = newFilters.returnDate || ''
  setFilters(filters)
}

const handleUpdateStatus = async ({ id, status, done }) => {
  try {
    await updateStatus(id, status)
    const statusText = status === 'aprovado' ? t('messages.orderApproved') : t('messages.orderCancelled')
    toast.success(t('messages.orderUpdated', { status: statusText }))
  } catch (err) {
    toast.error(err.message || t('messages.updateStatusError'))
  } finally {
    done()
  }
}

onMounted(() => {
  fetchRequests()
})
</script>
