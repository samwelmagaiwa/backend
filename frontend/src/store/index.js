import { createStore } from 'vuex'
import auth from './modules/auth'
import roleManagement from './modules/roleManagement'

export default createStore({
  modules: {
    auth,
    roleManagement
  },

  strict: process.env.NODE_ENV !== 'production'
})
