<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-teal-900 py-8 px-4 relative overflow-hidden">
    <!-- Medical Background Pattern -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Medical Cross Pattern -->
      <div class="absolute inset-0 opacity-5">
        <div class="grid grid-cols-12 gap-8 h-full transform rotate-45">
          <div v-for="i in 48" :key="i" class="bg-white rounded-full w-2 h-2 animate-pulse" :style="{animationDelay: (i * 0.1) + 's'}"></div>
        </div>
      </div>
      <!-- Floating medical icons -->
      <div class="absolute inset-0">
        <div v-for="i in 15" :key="i" 
             class="absolute text-white opacity-10 animate-float"
             :style="getIconStyle(i)">
          <i :class="getIconClass(i)"></i>
        </div>
      </div>
    </div>
    
    <div class="max-w-8xl mx-auto relative z-10">
      <!-- Professional Header -->
      <div class="medical-glass-card rounded-t-3xl p-8 mb-0 border-b border-blue-300/30">
        <div class="text-center">
          <div class="flex items-center justify-center mb-4">
            <h1 class="text-4xl font-bold text-white tracking-wide drop-shadow-lg">
              TERMS OF SERVICE
            </h1>
          </div>
          
          <p class="text-blue-100 text-lg font-medium">
            Muhimbili National Hospital - Information Technology Department
          </p>
        </div>
        
        <!-- Version Control Information -->
        <div class="bg-white/10 backdrop-blur-sm px-6 py-4 border-t border-blue-300/30 mt-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="text-center md:text-left">
              <span class="font-semibold text-blue-100">Version:</span>
              <span class="text-cyan-300 font-bold ml-1">1.0</span>
            </div>
            <div class="text-center">
              <span class="font-semibold text-blue-100">Approved:</span>
              <span class="text-emerald-300 font-bold ml-1">November 22, 2019</span>
            </div>
            <div class="text-center md:text-right">
              <span class="font-semibold text-blue-100">Last Updated:</span>
              <span class="text-yellow-300 font-bold ml-1">January 15, 2025</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Structured Terms Content -->
      <div class="medical-glass-card rounded-b-3xl p-8">
        
        <!-- 1. Acceptance of Terms -->
        <section class="mb-6">
          <div class="medical-card bg-gradient-to-r from-blue-600/25 to-cyan-600/25 border-2 border-blue-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 group">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <span class="text-white font-bold text-lg">1</span>
              </div>
              <h2 class="text-xl font-bold text-white">ACCEPTANCE OF TERMS</h2>
            </div>
            <div class="ml-16">
              <p class="text-blue-100 leading-relaxed mb-4">
                By using <strong class="text-cyan-300">Muhimbili National Hospital (MNH) ICT systems</strong>, you agree to follow these Terms of Service, the Acceptable ICT Use Policy, and all relevant laws.
              </p>
              <div class="bg-white/15 rounded-xl p-4 border-2 border-cyan-300/30 backdrop-blur-sm">
                <p class="text-sm text-blue-100">
                  <strong class="text-cyan-300">Covered Systems:</strong> Jeeva (Medical Records), Wellsoft (Hospital Management), and Internet Services
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- 2. Permitted Use -->
        <section class="mb-6">
          <div class="medical-card bg-gradient-to-r from-emerald-600/25 to-teal-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <span class="text-white font-bold text-lg">2</span>
              </div>
              <h2 class="text-xl font-bold text-white">PERMITTED USE</h2>
            </div>
            <div class="ml-16">
              <p class="text-blue-100 leading-relaxed mb-4">
                You are authorized to use MNH ICT systems for the following purposes:
              </p>
              <div class="grid md:grid-cols-1 gap-4">
                <div class="space-y-3">
                  <div class="flex items-start">
                    <i class="fas fa-check-circle text-emerald-400 mt-1 mr-3" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                    <span class="text-blue-100"><strong class="text-emerald-300">Official Hospital Duties:</strong> Use ICT systems only for authorized MNH work activities</span>
                  </div>
                  <div class="flex items-start">
                    <i class="fas fa-check-circle text-emerald-400 mt-1 mr-3" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                    <span class="text-blue-100"><strong class="text-emerald-300">Authorized Information Access:</strong> Access only the information you are permitted to view</span>
                  </div>
                  <div class="flex items-start">
                    <i class="fas fa-check-circle text-emerald-400 mt-1 mr-3" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                    <span class="text-blue-100"><strong class="text-emerald-300">Credential Security:</strong> Keep your login credentials confidential and secure</span>
                  </div>
                </div>
              </div>
              <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm mt-4">
                <p class="text-sm text-blue-100">
                  <i class="fas fa-info-circle text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                  <strong class="text-emerald-300">Remember:</strong> All system usage must align with your job responsibilities and hospital policies.
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- 3. Prohibited Actions -->
        <section class="mb-6">
          <div class="medical-card bg-gradient-to-r from-emerald-600/25 to-teal-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <span class="text-white font-bold text-lg">3</span>
              </div>
              <h2 class="text-xl font-bold text-white">PROHIBITED ACTIONS</h2>
            </div>
            <div class="ml-16">
              <p class="text-blue-100 leading-relaxed mb-4">
                <strong class="text-emerald-300">You must NOT engage in any of the following activities:</strong>
              </p>
              
              <!-- Prohibited Actions Grid -->
              <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                  <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                    <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                      <i class="fas fa-ban text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                      Illegal & Inappropriate Activities
                    </h4>
                    <ul class="text-sm text-blue-100 space-y-2">
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Illegal activities or political campaigning
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Accessing obscene, pornographic, or offensive materials
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Sharing discriminatory content
                      </li>
                    </ul>
                  </div>
                  
                  <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                    <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                      <i class="fas fa-shield-halved text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                      Security Violations
                    </h4>
                    <ul class="text-sm text-blue-100 space-y-2">
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Installing unauthorized software
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Disabling security controls
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Bypassing system authentication
                      </li>
                    </ul>
                  </div>
                </div>
                
                <div class="space-y-4">
                  <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                    <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                      <i class="fas fa-globe text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                      Misuse of Internet
                    </h4>
                    <ul class="text-sm text-blue-100 space-y-2">
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Gambling or personal shopping
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Streaming unrelated to work
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Sending spam or chain emails
                      </li>
                    </ul>
                  </div>
                  
                  <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                    <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                      <i class="fas fa-user-secret text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                      Confidentiality Breaches
                    </h4>
                    <ul class="text-sm text-blue-100 space-y-2">
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Disclosing patient information without authorization
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Sharing confidential hospital data
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-times text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Attempting unauthorized access
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm mt-4">
                <p class="text-sm text-blue-100">
                  <i class="fas fa-exclamation-triangle text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                  <strong class="text-emerald-300">Warning:</strong> Violation of these prohibited actions may result in immediate disciplinary action, access termination, or legal proceedings.
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- 4. Security Requirements -->
        <section class="mb-6">
          <div class="medical-card bg-gradient-to-r from-emerald-600/25 to-teal-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <span class="text-white font-bold text-lg">4</span>
              </div>
              <h2 class="text-xl font-bold text-white">SECURITY REQUIREMENTS</h2>
            </div>
            <div class="ml-16">
              <p class="text-blue-100 leading-relaxed mb-4">
                To maintain system security and protect hospital data, you must follow these requirements:
              </p>
              
              <!-- Security Requirements Grid -->
              <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                  <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                    <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                      <i class="fas fa-key text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                      Password Management
                    </h4>
                    <ul class="text-sm text-blue-100 space-y-2">
                      <li class="flex items-start">
                        <i class="fas fa-arrow-right text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Change default passwords on first login
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-arrow-right text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Update passwords every 90 days
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-arrow-right text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Use strong passwords with letters, numbers, and symbols
                      </li>
                    </ul>
                  </div>
                </div>
                
                <div class="space-y-4">
                  <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                    <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                      <i class="fas fa-laptop text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                      Device Security
                    </h4>
                    <ul class="text-sm text-blue-100 space-y-2">
                      <li class="flex items-start">
                        <i class="fas fa-arrow-right text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Lock your computer when unattended
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-arrow-right text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Keep devices updated with security patches
                      </li>
                      <li class="flex items-start">
                        <i class="fas fa-arrow-right text-emerald-400 mr-2 mt-1" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                        Maintain current antivirus software
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm mt-4">
                <p class="text-sm text-blue-100">
                  <i class="fas fa-exclamation-triangle text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                  <strong class="text-emerald-300">Critical:</strong> Failure to follow security requirements may result in immediate suspension of access privileges.
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- 5. Monitoring and Compliance -->
        <section class="mb-6">
          <div class="medical-card bg-gradient-to-r from-emerald-600/25 to-teal-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <span class="text-white font-bold text-lg">5</span>
              </div>
              <h2 class="text-xl font-bold text-white">MONITORING AND COMPLIANCE</h2>
            </div>
            <div class="ml-16">
              <div class="bg-white/15 rounded-xl p-6 border-2 border-emerald-300/30 backdrop-blur-sm">
                <h3 class="text-lg font-bold text-emerald-300 mb-4">System Monitoring</h3>
                <p class="text-blue-100 mb-4">
                  <strong class="text-emerald-300">MNH reserves the right to monitor system usage</strong> to ensure compliance with hospital policies and maintain security.
                </p>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                  <div>
                    <h4 class="font-semibold text-emerald-300 mb-2">Monitored Activities:</h4>
                    <ul class="text-sm text-blue-100 space-y-1">
                      <li>• Email communications</li>
                      <li>• Internet browsing history</li>
                      <li>• System access logs</li>
                      <li>• File transfers and downloads</li>
                    </ul>
                  </div>
                  <div>
                    <h4 class="font-semibold text-emerald-300 mb-2">Compliance Actions:</h4>
                    <ul class="text-sm text-blue-100 space-y-1">
                      <li>• Disciplinary action</li>
                      <li>• Access termination</li>
                      <li>• Legal proceedings</li>
                      <li>• Incident reporting</li>
                    </ul>
                  </div>
                </div>
                
                <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                  <p class="text-sm text-blue-100">
                    <i class="fas fa-info-circle text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                    <strong class="text-emerald-300">Note:</strong> Any violation of these terms may result in immediate disciplinary action, termination of employment, or legal action as deemed appropriate by hospital management.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- 6. Liability -->
        <section class="mb-6">
          <div class="medical-card bg-gradient-to-r from-emerald-600/25 to-teal-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <span class="text-white font-bold text-lg">6</span>
              </div>
              <h2 class="text-xl font-bold text-white">LIABILITY</h2>
            </div>
            <div class="ml-16">
              <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                  <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                    <i class="fas fa-user-check text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                    Your Responsibility
                  </h4>
                  <p class="text-sm text-blue-100 mb-3">
                    You are <strong class="text-emerald-300">fully responsible</strong> for all activity conducted under your assigned user account.
                  </p>
                  <ul class="text-sm text-blue-100 space-y-1">
                    <li>• Secure your login credentials</li>
                    <li>• Report unauthorized access immediately</li>
                    <li>• Ensure compliance with all policies</li>
                    <li>• Maintain professional conduct</li>
                  </ul>
                </div>
                
                <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                  <h4 class="font-bold text-emerald-300 mb-3 flex items-center">
                    <i class="fas fa-hospital text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
                    Hospital Disclaimer
                  </h4>
                  <p class="text-sm text-blue-100 mb-3">
                    <strong class="text-emerald-300">MNH is not responsible</strong> for personal data stored on hospital ICT systems.
                  </p>
                  <ul class="text-sm text-blue-100 space-y-1">
                    <li>• Do not store personal files</li>
                    <li>• Hospital systems are for work only</li>
                    <li>• Data may be monitored or deleted</li>
                    <li>• No privacy expectation for personal use</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Contact Information -->
        <section class="mb-6">
          <div class="medical-card bg-gradient-to-r from-emerald-600/25 to-teal-600/25 border-2 border-emerald-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 group">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center">
              <i class="fas fa-question-circle text-emerald-400 mr-2" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
              Questions or Concerns?
            </h3>
            <p class="text-blue-100 mb-4">If you have questions about these Terms of Service or need clarification, contact:</p>
            <div class="grid md:grid-cols-2 gap-4">
              <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                <h4 class="font-semibold text-emerald-300 mb-2">ICT Department</h4>
                <p class="text-sm text-blue-100">Email: ict@muhimbili.go.tz</p>
                <p class="text-sm text-blue-100">Phone: +255 22 221 5701</p>
                <p class="text-sm text-blue-100">Office: ICT Building, Ground Floor</p>
              </div>
              <div class="bg-white/15 rounded-xl p-4 border-2 border-emerald-300/30 backdrop-blur-sm">
                <h4 class="font-semibold text-emerald-300 mb-2">Legal & Compliance</h4>
                <p class="text-sm text-blue-100">Email: info@mloganzila.go.tz</p>
                <p class="text-sm text-blue-100">Phone: +255 22 221 5701</p>
                <p class="text-sm text-blue-100">Office: Administration Block, 3rd Floor</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Acknowledgment Section -->
        <section class="mb-0">
          <div class="medical-card bg-gradient-to-r from-indigo-600/25 to-purple-600/25 border-2 border-indigo-400/40 p-6 rounded-2xl backdrop-blur-sm hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-500 group">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-handshake text-white text-lg" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;"></i>
              </div>
              <h2 class="text-xl font-bold text-white">ACKNOWLEDGMENT</h2>
            </div>
            <div class="ml-16">
              <p class="text-blue-100 mb-6 leading-relaxed">
                By requesting access to <strong class="text-indigo-300">Jeeva</strong>, <strong class="text-indigo-300">Wellsoft</strong>, or <strong class="text-indigo-300">Internet services</strong>, you confirm that you understand and agree to comply with these Terms of Service.
              </p>
              
              <!-- Checkbox for Agreement -->
              <div class="bg-white/15 rounded-xl p-4 shadow-md border-2 border-indigo-300/30 backdrop-blur-sm">
                <label class="flex items-start cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="acknowledged" 
                    class="mt-1 mr-3 w-6 h-6 text-indigo-500 bg-white/15 border-2 border-indigo-300/40 rounded-lg focus:ring-indigo-400 focus:ring-2 backdrop-blur-sm transition-all duration-300"
                    required
                  />
                  <span class="text-blue-100 leading-relaxed">
                    <strong class="text-white">I acknowledge that I have read and understood these Terms of Service.</strong><br>
                    <span class="text-blue-200 text-sm"> <strong>
                       I agree to comply with all terms and conditions outlined above. I understand that violations may result in disciplinary action, termination, or legal proceedings.</strong>
                    </span>
                  </span>
                </label>
              </div>
              
              <!-- Action Buttons -->
              <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
                <button
                  @click="goBack"
                  class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl font-medium hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:scale-105 shadow-lg border border-gray-400/30 flex items-center justify-center"
                >
                  <i class="fas fa-arrow-left mr-2"></i>
                  Go Back
                </button>
                
                <button
                  @click="acceptTerms"
                  :disabled="!acknowledged"
                  class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl font-medium hover:from-indigo-600 hover:to-purple-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 shadow-lg border border-indigo-400/30 flex items-center justify-center"
                >
                  <i class="fas fa-check mr-2"></i>
                  Accept Terms & Continue
                </button>
              </div>
            </div>
          </div>
        </section>

      </div>

      <!-- Footer -->
      <div class="text-center mt-8">
        <div class="inline-block bg-white/10 backdrop-blur-sm border border-blue-300/30 px-6 py-3 rounded-xl shadow-lg">
          <p class="text-sm text-blue-100">
            © 2025 Muhimbili National Hospital - ICT Department | Terms effective as of publication date
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TermsOfService',
  emits: ['terms-accepted', 'go-back'],
  data() {
    return {
      acknowledged: false
    }
  },
  methods: {
    getIconStyle(index) {
      // Use index to create deterministic but varied positions
      const seed = index * 137.508; // Golden angle for good distribution
      return {
        left: ((seed * 7) % 100) + '%',
        top: ((seed * 11) % 100) + '%',
        animationDelay: ((seed * 3) % 3) + 's',
        animationDuration: (((seed * 2) % 3) + 2) + 's',
        fontSize: (((seed * 5) % 20) + 10) + 'px'
      }
    },
    getIconClass(index) {
      const icons = ['fa-heartbeat', 'fa-user-md', 'fa-hospital', 'fa-stethoscope', 'fa-plus']
      return ['fas', icons[index % icons.length]]
    },
    acceptTerms() {
      if (this.acknowledged) {
        // Store acknowledgment in localStorage with timestamp
        const acknowledgment = {
          acknowledged: true,
          timestamp: new Date().toISOString(),
          version: '1.3'
        }
        localStorage.setItem('termsOfServiceAcknowledged', JSON.stringify(acknowledgment))
        
        // Show success message
        this.showNotification('Terms of Service accepted successfully!', 'success')
        
        // Emit event to parent component (OnboardingFlow)
        this.$emit('terms-accepted', acknowledgment)
      }
    },
    
    goBack() {
      // Emit go-back event to parent component
      this.$emit('go-back')
    },
    
    showNotification(message, type = 'info') {
      // Simple notification system
      const colors = {
        success: 'green',
        error: 'red',
        info: 'blue'
      }
      
      const toast = document.createElement('div')
      toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold z-50 bg-${colors[type]}-600 shadow-lg`
      toast.textContent = message
      document.body.appendChild(toast)
      
      setTimeout(() => {
        if (document.body.contains(toast)) {
          document.body.removeChild(toast)
        }
      }, 5000)
    },
    
    checkExistingAcknowledgment() {
      // Check if user has already acknowledged this version
      const stored = localStorage.getItem('termsOfServiceAcknowledged')
      if (stored) {
        try {
          const acknowledgment = JSON.parse(stored)
          if (acknowledgment.version === '1.3' && acknowledgment.acknowledged) {
            this.acknowledged = true
          }
        } catch (e) {
          // Invalid stored data, ignore
        }
      }
    }
  },
  mounted() {
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth'
    
    // Check for existing acknowledgment
    this.checkExistingAcknowledgment()
  },
  beforeUnmount() {
    // Reset scroll behavior
    document.documentElement.style.scrollBehavior = 'auto'
  }
}
</script>

<style scoped>
/* Custom font imports */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

/* Apply custom font */
* {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Medical Glass morphism effects */
.medical-glass-card {
  background: rgba(59, 130, 246, 0.15);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  border: 2px solid rgba(96, 165, 250, 0.3);
  box-shadow: 0 8px 32px rgba(29, 78, 216, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.medical-card {
  position: relative;
  overflow: hidden;
  background: rgba(59, 130, 246, 0.1);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
}

.medical-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
  transition: left 0.5s;
}

.medical-card:hover::before {
  left: 100%;
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Compact hover effects */
.bg-white:hover {
  transform: translateY(-2px);
  transition: transform 0.2s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #2563eb, #1e40af);
}

/* Responsive design */
@media (max-width: 768px) {
  .lg\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .text-2xl {
    font-size: 1.5rem;
  }
  
  .text-xl {
    font-size: 1.125rem;
  }
}

/* Print styles */
@media print {
  .min-h-screen {
    min-height: auto;
  }
  
  .bg-gradient-to-br,
  .bg-gradient-to-r {
    background: white !important;
    color: black !important;
  }
  
  .shadow-xl,
  .shadow-lg {
    box-shadow: none !important;
  }
  
  .rounded-2xl,
  .rounded-xl {
    border-radius: 8px !important;
  }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  .transform:hover {
    transform: none;
  }
}

/* Focus styles for accessibility */
button:focus,
a:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}
</style>"