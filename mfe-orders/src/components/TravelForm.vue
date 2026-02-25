<template>
  <div class="card">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $t('form.customerName') }}
          </label>
          <div class="relative">
            <input
              id="customer_name"
              v-model="customerName"
              type="text"
              required
              :class="['input', 'pr-10', errors.customerName ? 'input-error' : '']"
            />
            <button
              v-if="customerName"
              @click="customerName = ''"
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <p v-if="errors.customerName" class="mt-1 text-sm text-red-600">{{ errors.customerName }}</p>
        </div>

        <div>
          <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $t('form.destination') }}
          </label>
          <div class="relative">
            <input
              id="destination"
              v-model="destination"
              type="text"
              required
              :class="['input', 'pr-10', errors.destination ? 'input-error' : '']"
            />
            <button
              v-if="destination"
              @click="destination = ''"
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <p v-if="errors.destination" class="mt-1 text-sm text-red-600">{{ errors.destination }}</p>
        </div>

        <div>
          <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $t('form.departureDate') }}
          </label>
          <div class="relative">
            <input
              id="start_date"
              v-model="startDate"
              type="date"
              required
              :class="['input', 'pr-10', errors.startDate ? 'input-error' : '']"
            />
            <button
              v-if="startDate"
              @click="startDate = ''"
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <p v-if="errors.startDate" class="mt-1 text-sm text-red-600">{{ errors.startDate }}</p>
        </div>

        <div>
          <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $t('form.returnDate') }}
          </label>
          <div class="relative">
            <input
              id="end_date"
              v-model="endDate"
              type="date"
              required
              :class="['input', 'pr-10', errors.endDate ? 'input-error' : '']"
            />
            <button
              v-if="endDate"
              @click="endDate = ''"
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <p v-if="errors.endDate" class="mt-1 text-sm text-red-600">{{ errors.endDate }}</p>
        </div>
      </div>

      <div v-if="formError" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        {{ formError }}
      </div>

      <div class="flex gap-4">
        <button type="submit" :disabled="loading" class="btn btn-primary flex-1">
          <span v-if="loading">{{ $t('form.submitting') }}</span>
          <span v-else>{{ $t('form.createOrder') }}</span>
        </button>
        <button type="button" @click="resetForm" :disabled="loading" class="btn btn-secondary">
          {{ $t('form.clear') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { useTravelStore } from '@/stores/travelStore.js'

const { t } = useI18n()
const travelStore = useTravelStore()

const customerName = ref('')
const destination = ref('')
const startDate = ref('')
const endDate = ref('')
const loading = ref(false)
const formError = ref(null)
const errors = reactive({
  customerName: '',
  destination: '',
  startDate: '',
  endDate: ''
})

const validateForm = () => {
  let valid = true
  
  // Reset errors
  Object.keys(errors).forEach(key => errors[key] = '')
  formError.value = null

  if (!customerName.value.trim()) {
    errors.customerName = t('validation.nameRequired')
    valid = false
  }

  if (!destination.value.trim()) {
    errors.destination = t('validation.destinationRequired')
    valid = false
  }

  if (!startDate.value) {
    errors.startDate = t('validation.departureDateRequired')
    valid = false
  }

  if (!endDate.value) {
    errors.endDate = t('validation.returnDateRequired')
    valid = false
  }

  if (startDate.value && endDate.value && new Date(endDate.value) < new Date(startDate.value)) {
    formError.value = t('validation.returnDateAfterDeparture')
    valid = false
  }

  return valid
}

const handleSubmit = async () => {
  if (!validateForm()) return

  loading.value = true
  
  try {
    await travelStore.create({
      customer_name: customerName.value.trim(),
      destination: destination.value.trim(),
      departure_date: startDate.value,
      return_date: endDate.value
    })

    resetForm()
  } catch (err) {
    formError.value = err.message || t('messages.createOrderError')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  customerName.value = ''
  destination.value = ''
  startDate.value = ''
  endDate.value = ''
  formError.value = null
  Object.keys(errors).forEach(key => errors[key] = '')
}
</script>