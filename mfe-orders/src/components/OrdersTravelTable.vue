<template>
  <div>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Solicitante</th>
          <th>Destino</th>
          <th>Ida</th>
          <th>Volta</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="req in requests" :key="req.id">
          <td>{{ req.id }}</td>
          <td>{{ req.customer_name }}</td>
          <td>{{ req.destination }}</td>
          <td>{{ req.departure_date }}</td>
          <td>{{ req.return_date }}</td>
          <td><StatusBadge :status="req.status" /></td>
          <td>
            <!-- Aprovar -->
            <button
              v-if="user?.role === 'admin' && req.status === 'solicitado'"
              @click="handleAction(req.id, 'aprovado')"
              :disabled="loadingIds.includes(req.id)"
            >
              <span v-if="loadingIds.includes(req.id)">...</span>
              <span v-else>Aprovar</span>
            </button>

            <!-- Cancelar -->
            <button
              v-if="user?.role === 'admin' && req.status !== 'cancelado' && req.status !== 'aprovado'"
              @click="handleAction(req.id, 'cancelado')"
              :disabled="loadingIds.includes(req.id)"
            >
              <span v-if="loadingIds.includes(req.id)">...</span>
              <span v-else>Cancelar</span>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import StatusBadge from './StatusBadge.vue'
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/authStore.js'

const props = defineProps({
  requests: { type: Array, required: true },
})
const emit = defineEmits(['update-status'])

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const loadingIds = ref([])

const handleAction = (id, status) => {
  if (!loadingIds.value.includes(id)) {
    loadingIds.value.push(id)
    emit('update-status', { id, status, done: () => {
      const index = loadingIds.value.indexOf(id)
      if (index > -1) loadingIds.value.splice(index, 1)
    }})
  }
}
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ddd;
  padding: 0.5rem;
}
button {
  margin-right: 0.3rem;
}
</style>