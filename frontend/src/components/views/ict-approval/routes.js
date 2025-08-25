// ICT Approval Routes Configuration
// Add these routes to your main router configuration

import RequestsList from './RequestsList.vue'
import RequestDetails from './RequestDetails.vue'

export const ictApprovalRoutes = [
  {
    path: '/ict-approval/requests',
    name: 'RequestsList',
    component: RequestsList,
    meta: {
      requiresAuth: true,
      role: 'ict_officer'
    }
  },
  {
    path: '/ict-approval/request/:id',
    name: 'RequestDetails',
    component: RequestDetails,
    meta: {
      requiresAuth: true,
      role: 'ict_officer'
    }
  }
]

// Example of how to integrate into your main router:
/*
import { createRouter, createWebHistory } from 'vue-router'
import { ictApprovalRoutes } from '@/components/views/ict-approval/routes'

const routes = [
  // Your existing routes
  ...ictApprovalRoutes
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
*/
