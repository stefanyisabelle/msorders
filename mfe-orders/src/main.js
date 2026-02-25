import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './assets/styles/tailwind.css'
import App from './App.vue'
import router from './router'
import i18n from './i18n'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import { useAuthStore } from './stores/authStore'

const app = createApp(App)

app.use(createPinia())
app.use(i18n)

app.use(Toast, {
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false,
  toastDefaults: {
    // Success: 3 seconds
    success: {
      timeout: 3000,
    },
    // Error: 5 seconds
    error: {
      timeout: 5000,
    },
    // Warning: 5 seconds
    warning: {
      timeout: 5000,
    },
    // Info: 4 seconds
    info: {
      timeout: 4000,
    },
  },
})

// Load user data if token exists
const authStore = useAuthStore()
if (authStore.token) {
  authStore.fetchUser()
}

app.use(router)

app.mount('#app')