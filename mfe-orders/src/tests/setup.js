import { vi } from 'vitest'
import { config } from '@vue/test-utils'

// Mock do vue-router
vi.mock('vue-router', () => ({
  useRouter: vi.fn(() => ({
    push: vi.fn(),
  })),
  useRoute: vi.fn(() => ({
    params: {},
    query: {},
  })),
}))

// Mock do vue-i18n
config.global.mocks = {
  $t: (key) => key,
}

// Mock do localStorage
const localStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn(),
}

global.localStorage = localStorageMock
