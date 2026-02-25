import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/authStore'

vi.mock('@/services/authService.js', () => ({
  default: {
    login: vi.fn(),
    logout: vi.fn(),
    getUser: vi.fn(),
  }
}))

vi.mock('@/router', () => ({
  default: {
    push: vi.fn()
  }
}))

vi.mock('@/i18n', () => ({
  default: {
    global: {
      t: (key) => key
    }
  }
}))

import authService from '@/services/authService.js'

describe('AuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
    localStorage.clear()
  })

  it('deve ter estado inicial correto', () => {
    const store = useAuthStore()

    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.loading).toBe(false)
    expect(store.error).toBeNull()
  })

  it('deve fazer login com sucesso', async () => {
    const store = useAuthStore()
    const mockToken = 'fake-jwt-token'
    const mockUser = { id: 1, name: 'Test User', email: 'test@test.com' }

    authService.login.mockResolvedValue({ token: mockToken })
    authService.getUser.mockResolvedValue(mockUser)

    await store.login({ email: 'test@test.com', password: 'password' })

    expect(store.token).toBe(mockToken)
    expect(store.user).toEqual(mockUser)
    expect(store.loading).toBe(false)
    expect(store.error).toBeNull()
    expect(localStorage.setItem).toHaveBeenCalledWith('token', mockToken)
  })

  it('deve tratar erro de login', async () => {
    const store = useAuthStore()
    
    authService.login.mockRejectedValue({ 
      status: 401,
      message: 'Unauthorized'
    })

    await store.login({ email: 'test@test.com', password: 'wrong' })

    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.loading).toBe(false)
    expect(store.error).toBe('auth.invalidCredentials')
  })

  it('deve fazer logout corretamente', async () => {
    const store = useAuthStore()
    store.user = { id: 1, name: 'Test' }
    store.token = 'fake-token'

    authService.logout.mockResolvedValue(true)

    await store.logout()

    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(localStorage.removeItem).toHaveBeenCalledWith('token')
  })

  it('deve ter loading states separados', () => {
    const store = useAuthStore()

    expect(store.loading).toBe(false)
    expect(store.loadingUser).toBe(false)
    expect(store.loggingOut).toBe(false)
  })
})
