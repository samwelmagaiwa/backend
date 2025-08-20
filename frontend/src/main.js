import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/main.css'
import './assets/css/responsive.css'

const app = createApp(App)

// Use plugins
app.use(router)

// Mount the app
app.mount('#app')