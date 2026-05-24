<template>
  <aside class="w-64 bg-gradient-to-b from-green-800 to-green-900 text-white flex flex-col shadow-xl">
    <!-- Logo -->
    <div class="p-6 border-b border-green-700">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-wide">PharmaCare</h1>
          <p class="text-xs text-green-300">Gestion Intelligente</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4 overflow-y-auto">
      
      <!-- ==================== ADMIN ==================== -->
      <template v-if="isAdmin">
        <!-- Dashboard -->
        <router-link
          to="/"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <span>Dashboard</span>
        </router-link>

        <!-- Médicaments -->
        <router-link
          to="/medicaments"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path.startsWith('/medicaments') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>Médicaments</span>
        </router-link>

        <!-- Section Stock -->
        <div class="px-6 mt-4 mb-2">
          <p class="text-xs font-semibold text-green-300 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Gestion du stock
          </p>
        </div>

        <router-link
          to="/stock/alertes"
          class="flex items-center gap-3 px-6 py-2 ml-8 mr-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/stock/alertes' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <span>Alertes stock</span>
        </router-link>

        <router-link
          to="/stock/entrees"
          class="flex items-center gap-3 px-6 py-2 ml-8 mr-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/stock/entrees' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
          </svg>
          <span>Entrées de stock</span>
        </router-link>

        <router-link
          to="/stock/historique"
          class="flex items-center gap-3 px-6 py-2 ml-8 mr-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/stock/historique' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Historique stock</span>
        </router-link>

        <div class="my-3 mx-4 border-t border-green-700"></div>

        <!-- Ventes -->
        <router-link
          to="/ventes"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/ventes' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Ventes</span>
        </router-link>

        <router-link
          to="/ventes/create"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/ventes/create' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Nouvelle vente</span>
        </router-link>

        <div class="my-3 mx-4 border-t border-green-700"></div>

        <!-- Clients -->
        <router-link
          to="/clients"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path.startsWith('/clients') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>Clients</span>
        </router-link>

        <div class="my-3 mx-4 border-t border-green-700"></div>

        <!-- Fournisseurs -->
        <router-link
          to="/fournisseurs"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path.startsWith('/fournisseurs') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          <span>Fournisseurs</span>
        </router-link>

        <!-- Commandes -->
        <router-link
          to="/commandes"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path.startsWith('/commandes') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
          <span>Commandes</span>
        </router-link>

        <!-- Rapports -->
        <router-link
          to="/rapports"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/rapports' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <span>Rapports</span>
        </router-link>

        <!-- Import/Export -->
        <router-link
          to="/import-export"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/import-export' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
          </svg>
          <span>Import / Export</span>
        </router-link>

        <div class="my-3 mx-4 border-t border-green-700"></div>

        <!-- Utilisateurs -->
        <router-link
          to="/utilisateurs"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/utilisateurs' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <span>Utilisateurs</span>
        </router-link>
      </template>

      <!-- ==================== PHARMACIEN ==================== -->
      <template v-else-if="isPharmacien">
        <router-link
          to="/medicaments"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path.startsWith('/medicaments') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>Médicaments</span>
        </router-link>

        <div class="px-6 mt-4 mb-2">
          <p class="text-xs font-semibold text-green-300 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Gestion du stock
          </p>
        </div>

        <router-link
          to="/stock/alertes"
          class="flex items-center gap-3 px-6 py-2 ml-8 mr-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/stock/alertes' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <span>Alertes stock</span>
        </router-link>

        <router-link
          to="/stock/entrees"
          class="flex items-center gap-3 px-6 py-2 ml-8 mr-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/stock/entrees' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
          </svg>
          <span>Entrées de stock</span>
        </router-link>

        <router-link
          to="/stock/historique"
          class="flex items-center gap-3 px-6 py-2 ml-8 mr-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/stock/historique' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Historique stock</span>
        </router-link>

        <div class="my-3 mx-4 border-t border-green-700"></div>

        <router-link
          to="/ventes"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/ventes' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Ventes</span>
        </router-link>

        <router-link
          to="/ventes/create"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/ventes/create' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Nouvelle vente</span>
        </router-link>

        <div class="my-3 mx-4 border-t border-green-700"></div>

        <router-link
          to="/clients"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path.startsWith('/clients') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>Clients</span>
        </router-link>
      </template>

      <!-- ==================== CAISSIER ==================== -->
      <template v-else-if="isCaissier">
        <router-link
          to="/ventes"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/ventes' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Mes ventes</span>
        </router-link>

        <router-link
          to="/ventes/create"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path === '/ventes/create' ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Nouvelle vente</span>
        </router-link>

        <div class="my-3 mx-4 border-t border-green-700"></div>

        <router-link
          to="/clients"
          class="flex items-center gap-3 px-6 py-3 mx-2 rounded-lg transition-all duration-200"
          :class="$route.path.startsWith('/clients') ? 'bg-green-600 shadow-md' : 'hover:bg-green-700/50'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>Clients</span>
        </router-link>
      </template>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-green-700 bg-green-800/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
          {{ userInitial }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-white truncate">{{ userName }}</p>
          <p class="text-xs text-green-300 flex items-center gap-1">
            <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
            {{ userRoleLabel }}
          </p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const authStore = useAuthStore()
const { isAdmin, isPharmacien, isCaissier, userRole, user } = storeToRefs(authStore)

const userRoleLabel = computed(() => {
  switch (userRole.value) {
    case 'admin': return 'Administrateur'
    case 'pharmacien': return 'Pharmacien'
    case 'caissier': return 'Caissier'
    default: return 'Utilisateur'
  }
})

const userName = computed(() => user.value?.name || 'Utilisateur')
const userInitial = computed(() => (user.value?.name || 'U').charAt(0).toUpperCase())
</script>