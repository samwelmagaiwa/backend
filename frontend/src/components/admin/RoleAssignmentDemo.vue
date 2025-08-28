<template>
  <div class="flex flex-col h-screen">
    <AppHeader />
    <div class="flex flex-1 overflow-hidden">
      <ModernSidebar />
      <main class="flex-1 p-6 bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 overflow-y-auto relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute inset-0 opacity-5">
            <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
              <div
                v-for="i in 48"
                :key="i"
                class="bg-white rounded-full w-2 h-2 animate-pulse"
                :style="{ animationDelay: i * 0.1 + 's' }"
              ></div>
            </div>
          </div>
        </div>

        <div class="max-w-10xl mx-auto relative z-10">
          <!-- Header Section -->
          <div class="medical-glass-card rounded-t-3xl p-6 mb-0 border-b border-blue-300/30">
            <div class="text-center">
              <h1 class="text-4xl font-bold text-white mb-2 tracking-wide drop-shadow-lg">
                <i class="fas fa-cogs mr-3 text-blue-300"></i>
                Role Assignment System Demo
              </h1>
              <p class="text-blue-100 text-lg">Clean implementation with database integration</p>
            </div>
          </div>

          <!-- Main Content -->
          <div class="medical-glass-card rounded-b-3xl overflow-hidden">
            <div class="p-6 space-y-8">

              <!-- Features Overview -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="medical-card bg-gradient-to-r from-green-600/25 to-emerald-600/25 border-2 border-green-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-database text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Database Integration</h3>
                  </div>
                  <ul class="text-green-100 text-sm space-y-2">
                    <li><i class="fas fa-check mr-2 text-green-300"></i>Roles captured from database</li>
                    <li><i class="fas fa-check mr-2 text-green-300"></i>Real-time data synchronization</li>
                    <li><i class="fas fa-check mr-2 text-green-300"></i>Proper relationships</li>
                  </ul>
                </div>

                <div class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-code text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Clean Code</h3>
                  </div>
                  <ul class="text-blue-100 text-sm space-y-2">
                    <li><i class="fas fa-check mr-2 text-blue-300"></i>Modular components</li>
                    <li><i class="fas fa-check mr-2 text-blue-300"></i>Service layer separation</li>
                    <li><i class="fas fa-check mr-2 text-blue-300"></i>Error handling</li>
                  </ul>
                </div>

                <div class="medical-card bg-gradient-to-r from-purple-600/25 to-indigo-600/25 border-2 border-purple-400/40 p-6 rounded-2xl backdrop-blur-sm">
                  <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                      <i class="fas fa-server text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Laravel Backend</h3>
                  </div>
                  <ul class="text-purple-100 text-sm space-y-2">
                    <li><i class="fas fa-check mr-2 text-purple-300"></i>API endpoints</li>
                    <li><i class="fas fa-check mr-2 text-purple-300"></i>Validation rules</li>
                    <li><i class="fas fa-check mr-2 text-purple-300"></i>Security measures</li>
                  </ul>
                </div>
              </div>

              <!-- Implementation Details -->
              <div class="medical-card bg-gradient-to-r from-gray-600/25 to-slate-600/25 border-2 border-gray-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6">
                  <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-list-check text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white">Implementation Features</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">
                      <i class="fas fa-user-interface mr-2 text-gray-300"></i>
                      User Interface
                    </h4>
                    <ul class="text-gray-100 space-y-2">
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>Dropdown Selection:</strong> "Choose a role to add..."</span>
                      </li>
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>List View:</strong> "Or select from list"</span>
                      </li>
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>Toggle Option:</strong> "Show roles list"</span>
                      </li>
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>Real-time Preview:</strong> Selected roles display</span>
                      </li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">
                      <i class="fas fa-cog mr-2 text-gray-300"></i>
                      Backend Processing
                    </h4>
                    <ul class="text-gray-100 space-y-2">
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>Role Fetching:</strong> Direct from database</span>
                      </li>
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>Validation:</strong> Laravel form requests</span>
                      </li>
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>Security:</strong> Role-based access control</span>
                      </li>
                      <li class="flex items-center">
                        <i class="fas fa-chevron-right mr-2 text-gray-400"></i>
                        <span><strong>Audit Trail:</strong> Role change logging</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Code Structure -->
              <div class="medical-card bg-gradient-to-r from-orange-600/25 to-red-600/25 border-2 border-orange-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6">
                  <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-folder-tree text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white">Code Structure</h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">Frontend Components</h4>
                    <div class="bg-black/30 rounded-lg p-4 font-mono text-sm">
                      <div class="text-orange-300">📁 frontend/src/</div>
                      <div class="ml-4 text-orange-200">📁 components/admin/</div>
                      <div class="ml-8 text-green-300">📄 CleanRoleAssignment.vue</div>
                      <div class="ml-4 text-orange-200">📁 services/</div>
                      <div class="ml-8 text-green-300">📄 roleAssignmentService.js</div>
                    </div>
                  </div>

                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">Backend Structure</h4>
                    <div class="bg-black/30 rounded-lg p-4 font-mono text-sm">
                      <div class="text-orange-300">📁 backend/app/</div>
                      <div class="ml-4 text-orange-200">📁 Http/Controllers/Api/v1/</div>
                      <div class="ml-8 text-green-300">📄 RoleController.php</div>
                      <div class="ml-8 text-green-300">📄 UserRoleController.php</div>
                      <div class="ml-4 text-orange-200">📁 Http/Requests/</div>
                      <div class="ml-8 text-green-300">📄 AssignRoleRequest.php</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- API Endpoints -->
              <div class="medical-card bg-gradient-to-r from-teal-600/25 to-cyan-600/25 border-2 border-teal-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6">
                  <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-network-wired text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white">API Endpoints</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">Role Management</h4>
                    <div class="space-y-2">
                      <div class="bg-black/30 rounded-lg p-3 font-mono text-sm">
                        <span class="text-green-400">GET</span> <span class="text-teal-300">/api/roles</span>
                        <div class="text-gray-300 text-xs mt-1">Get all roles from database</div>
                      </div>
                      <div class="bg-black/30 rounded-lg p-3 font-mono text-sm">
                        <span class="text-blue-400">POST</span> <span class="text-teal-300">/api/roles</span>
                        <div class="text-gray-300 text-xs mt-1">Create new role</div>
                      </div>
                    </div>
                  </div>

                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">User Role Assignment</h4>
                    <div class="space-y-2">
                      <div class="bg-black/30 rounded-lg p-3 font-mono text-sm">
                        <span class="text-green-400">GET</span> <span class="text-teal-300">/api/user-roles</span>
                        <div class="text-gray-300 text-xs mt-1">Get users with roles</div>
                      </div>
                      <div class="bg-black/30 rounded-lg p-3 font-mono text-sm">
                        <span class="text-blue-400">POST</span> <span class="text-teal-300">/api/user-roles/{user}/assign</span>
                        <div class="text-gray-300 text-xs mt-1">Assign roles to user</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-wrap gap-4 justify-center">
                <router-link
                  to="/admin/clean-role-assignment"
                  class="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 px-8 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-3"
                >
                  <i class="fas fa-rocket"></i>
                  <span>Try Clean Role Assignment</span>
                </router-link>

                <router-link
                  to="/admin/user-roles"
                  class="bg-gradient-to-r from-purple-600 to-purple-700 text-white py-4 px-8 rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-3"
                >
                  <i class="fas fa-users-cog"></i>
                  <span>Original Role Assignment</span>
                </router-link>

                <button
                  @click="showTechnicalDetails = !showTechnicalDetails"
                  class="bg-gradient-to-r from-gray-600 to-gray-700 text-white py-4 px-8 rounded-xl font-semibold hover:from-gray-700 hover:to-gray-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-3"
                >
                  <i class="fas fa-code"></i>
                  <span>{{ showTechnicalDetails ? 'Hide' : 'Show' }} Technical Details</span>
                </button>
              </div>

              <!-- Technical Details (Collapsible) -->
              <div v-if="showTechnicalDetails" class="medical-card bg-gradient-to-r from-indigo-600/25 to-purple-600/25 border-2 border-indigo-400/40 p-6 rounded-2xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6">
                  <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-terminal text-white text-lg"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white">Technical Implementation Details</h3>
                </div>

                <div class="space-y-6">
                  <!-- Database Schema -->
                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">Database Schema</h4>
                    <div class="bg-black/40 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                      <div class="text-yellow-300">-- Roles Table</div>
                      <div class="text-green-300">CREATE TABLE roles (</div>
                      <div class="ml-4 text-blue-300">id BIGINT PRIMARY KEY,</div>
                      <div class="ml-4 text-blue-300">name VARCHAR(255) UNIQUE,</div>
                      <div class="ml-4 text-blue-300">description TEXT,</div>
                      <div class="ml-4 text-blue-300">permissions JSON,</div>
                      <div class="ml-4 text-blue-300">is_system_role BOOLEAN,</div>
                      <div class="ml-4 text-blue-300">is_deletable BOOLEAN,</div>
                      <div class="ml-4 text-blue-300">sort_order INTEGER,</div>
                      <div class="ml-4 text-blue-300">timestamps</div>
                      <div class="text-green-300">);</div>
                      <br>
                      <div class="text-yellow-300">-- Role User Pivot Table</div>
                      <div class="text-green-300">CREATE TABLE role_user (</div>
                      <div class="ml-4 text-blue-300">user_id BIGINT,</div>
                      <div class="ml-4 text-blue-300">role_id BIGINT,</div>
                      <div class="ml-4 text-blue-300">assigned_at TIMESTAMP,</div>
                      <div class="ml-4 text-blue-300">assigned_by BIGINT,</div>
                      <div class="ml-4 text-blue-300">UNIQUE(user_id, role_id)</div>
                      <div class="text-green-300">);</div>
                    </div>
                  </div>

                  <!-- Service Layer -->
                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">Service Layer Example</h4>
                    <div class="bg-black/40 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                      <div class="text-yellow-300">// roleAssignmentService.js</div>
                      <div class="text-green-300">export const roleAssignmentService = {</div>
                      <div class="ml-4 text-blue-300">async getAllRoles() {</div>
                      <div class="ml-8 text-gray-300">const response = await apiClient.get('/roles')</div>
                      <div class="ml-8 text-gray-300">return response.data</div>
                      <div class="ml-4 text-blue-300">},</div>
                      <div class="ml-4 text-blue-300">async assignRolesToUser(userId, roleIds) {</div>
                      <div class="ml-8 text-gray-300">return await apiClient.post(`/user-roles/${userId}/assign`, {</div>
                      <div class="ml-12 text-gray-300">role_ids: roleIds</div>
                      <div class="ml-8 text-gray-300">})</div>
                      <div class="ml-4 text-blue-300">}</div>
                      <div class="text-green-300">}</div>
                    </div>
                  </div>

                  <!-- Laravel Controller -->
                  <div>
                    <h4 class="text-lg font-semibold text-white mb-3">Laravel Controller Example</h4>
                    <div class="bg-black/40 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                      <div class="text-yellow-300">// UserRoleController.php</div>
                      <div class="text-green-300">public function assignRoles(AssignRoleRequest $request, User $user)</div>
                      <div class="text-green-300">{</div>
                      <div class="ml-4 text-blue-300">$roleIds = $request->validated()['role_ids'];</div>
                      <div class="ml-4 text-blue-300">$user->roles()->sync($roleIds);</div>
                      <div class="ml-4 text-blue-300">return response()->json(['success' => true]);</div>
                      <div class="text-green-300">}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import ModernSidebar from '@/components/ModernSidebar.vue'

export default {
  name: 'RoleAssignmentDemo',
  components: {
    AppHeader,
    ModernSidebar
  },
  setup() {
    // Sidebar state now managed by Pinia - no local state needed
    const showTechnicalDetails = ref(false)

    return {
      showTechnicalDetails
    }
  }
}
</script>

<style scoped>
/* Medical Glass morphism effects */
.medical-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.medical-card {
  position: relative;
  overflow: hidden;
  background: rgba(59, 130, 246, 0.1);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
}

.medical-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(96, 165, 250, 0.2),
    transparent
  );
  transition: left 0.5s;
}

.medical-card:hover::before {
  left: 100%;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(59, 130, 246, 0.1);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: rgba(96, 165, 250, 0.5);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(96, 165, 250, 0.7);
}
</style>
