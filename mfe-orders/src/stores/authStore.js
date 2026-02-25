import { defineStore } from 'pinia'
import authService from '@/services/authService.js'
import router from '@/router'
import i18n from '@/i18n'

const { t } = i18n.global

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    loadingUser: false,
    loggingOut: false,
    error: null,
  }),

  actions: {
    async login({ email, password }) {
      this.loading = true
      this.error = null
      try {
        const data = await authService.login(email, password)
        this.token = data.token
        localStorage.setItem('token', data.token)
        
        // Buscar dados do usuário após login
        this.loadingUser = true
        await this.fetchUser()
        this.loadingUser = false
        
        router.push('/')
      } catch (err) {
        // Mostrar mensagem específica para erro de autenticação
        if (err.status === 401 || err.status === 422) {
          this.error = t('auth.invalidCredentials')
        } else {
          this.error = err.message || t('auth.loginError')
        }
      } finally {
        this.loading = false
        this.loadingUser = false
      }
    },

    async logout() {
      this.loggingOut = true
      try {
        await authService.logout()
      } finally {
        this.user = null
        this.token = null
        this.loggingOut = false
        localStorage.removeItem('token')
        router.push('/login')
      }
    },

    async fetchUser() {
      if (!this.token) return
      this.user = await authService.getUser()
    },
  },
})