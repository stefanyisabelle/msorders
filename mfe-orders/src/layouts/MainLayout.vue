<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-primary-600">{{ $t('auth.appTitle') }}</h1>
          </div>
          
          <div class="flex items-center gap-4">
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ user?.name }}</p>
              <p v-if="user?.role === 'admin'" class="text-xs text-primary-600">Administrador</p>
              <p v-else class="text-xs text-gray-500">Usuário</p>
            </div>
            <button @click="handleLogout" :disabled="loggingOut" class="btn btn-secondary btn-sm">
              <span v-if="loggingOut">{{ $t('actions.loggingOut') }}</span>
              <span v-else>{{ $t('actions.logout') }}</span>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore.js'

const authStore = useAuthStore()
const router = useRouter()
const { user, loggingOut } = storeToRefs(authStore)
const { logout } = authStore

const handleLogout = async () => {
  await logout()
}
</script>
