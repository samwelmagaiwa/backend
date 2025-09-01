import { createStore } from 'vuex'
import auth from './modules/auth'
import sidebar from './modules/sidebar'

export default createStore({
  modules: {
    auth,
    sidebar
  },

  strict: process.env.NODE_ENV !== 'production'
})
