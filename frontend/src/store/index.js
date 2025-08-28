import { createStore } from 'vuex'
import auth from './modules/auth'
import roleManagement from './modules/roleManagement'
import sidebar from './modules/sidebar'

export default createStore({
  modules: {
    auth,
    roleManagement,
    sidebar
  },

  strict: process.env.NODE_ENV !== 'production'
})
