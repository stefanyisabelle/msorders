<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-800">
    <div class="card max-w-md w-full mx-4">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $t('auth.appTitle') }}</h1>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $t('auth.email') }}
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            :class="['input', error ? 'input-error' : '']"
            :placeholder="$t('auth.emailPlaceholder')"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $t('auth.password') }}
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            :class="['input', error ? 'input-error' : '']"
            :placeholder="$t('auth.passwordPlaceholder')"
          />
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
          {{ error }}
        </div>

        <button type="submit" :disabled="loading" class="btn btn-primary w-full">
          <span v-if="loading">{{ $t('auth.loggingIn') }}</span>
          <span v-else>{{ $t('auth.login') }}</span>
        </button>
      </form>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/authStore.js'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const router = useRouter()

const authStore = useAuthStore()
const { loading, error } = storeToRefs(authStore)
const { login } = authStore

const handleLogin = async () => {
  await login({ email: email.value, password: password.value })
  if (!error.value) {
    router.push('/')
  }
}
</script>
