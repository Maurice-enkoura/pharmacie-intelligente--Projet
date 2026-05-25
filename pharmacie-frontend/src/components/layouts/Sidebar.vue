<template>
  <aside class="w-64 bg-gradient-to-br from-green-800 to-green-900 text-white flex flex-col shadow-2xl">
    <!-- Logo -->
    <div class="p-5 border-b border-green-700/50">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
        </div>
        <div>
          <h1 class="text-lg font-semibold tracking-wide">Pharma<span class="text-green-300">Care</span></h1>
          <p class="text-[10px] text-green-300/70">Gestion de pharmacie</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-3 overflow-y-auto scrollbar-thin scrollbar-thumb-green-700">
      
      <!-- ADMIN -->
      <template v-if="isAdmin">
        <div class="space-y-0.5">
          <!-- Dashboard -->
          <router-link to="/" class="nav-link" :class="{ active: $route.path === '/' }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Tableau de bord</span>
          </router-link>

          <!-- Médicaments -->
          <router-link to="/medicaments" class="nav-link" :class="{ active: $route.path.startsWith('/medicaments') }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Médicaments</span>
          </router-link>

          <!-- Section Stock -->
          <div class="nav-section">
            <div class="nav-section-title">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              <span>Stock</span>
            </div>
            <div class="ml-6 space-y-0.5">
              <router-link to="/stock/alertes" class="nav-link-sub" :class="{ active: $route.path === '/stock/alertes' }">
                <span>⚠️ Alertes</span>
              </router-link>
              <router-link to="/stock/entrees" class="nav-link-sub" :class="{ active: $route.path === '/stock/entrees' }">
                <span>📥 Entrées</span>
              </router-link>
              <router-link to="/stock/historique" class="nav-link-sub" :class="{ active: $route.path === '/stock/historique' }">
                <span>📜 Historique</span>
              </router-link>
            </div>
          </div>

          <!-- Ventes -->
          <div class="nav-section">
            <div class="nav-section-title">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Ventes</span>
            </div>
            <div class="ml-6 space-y-0.5">
              <router-link to="/ventes" class="nav-link-sub" :class="{ active: $route.path === '/ventes' }">
                <span>📋 Toutes les ventes</span>
              </router-link>
              <router-link to="/ventes/create" class="nav-link-sub" :class="{ active: $route.path === '/ventes/create' }">
                <span>➕ Nouvelle vente</span>
              </router-link>
            </div>
          </div>

          <!-- Clients -->
          <router-link to="/clients" class="nav-link" :class="{ active: $route.path.startsWith('/clients') }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Clients</span>
          </router-link>

          <!-- Fournisseurs -->
          <router-link to="/fournisseurs" class="nav-link" :class="{ active: $route.path.startsWith('/fournisseurs') }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Fournisseurs</span>
          </router-link>

          <!-- Commandes -->
          <router-link to="/commandes" class="nav-link" :class="{ active: $route.path.startsWith('/commandes') }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span>Commandes</span>
          </router-link>

          <!-- Rapports -->
          <router-link to="/rapports" class="nav-link" :class="{ active: $route.path === '/rapports' }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Rapports</span>
          </router-link>

          <!-- Utilisateurs -->
          <router-link to="/utilisateurs" class="nav-link" :class="{ active: $route.path === '/utilisateurs' }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Utilisateurs</span>
          </router-link>
        </div>
      </template>

      <!-- PHARMACIEN -->
      <template v-else-if="isPharmacien">
        <div class="space-y-0.5">
          <router-link to="/medicaments" class="nav-link" :class="{ active: $route.path.startsWith('/medicaments') }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Médicaments</span>
          </router-link>

          <div class="nav-section">
            <div class="nav-section-title">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              <span>Stock</span>
            </div>
            <div class="ml-6 space-y-0.5">
              <router-link to="/stock/alertes" class="nav-link-sub" :class="{ active: $route.path === '/stock/alertes' }">
                <span>⚠️ Alertes</span>
              </router-link>
              <router-link to="/stock/entrees" class="nav-link-sub" :class="{ active: $route.path === '/stock/entrees' }">
                <span>📥 Entrées</span>
              </router-link>
              <router-link to="/stock/historique" class="nav-link-sub" :class="{ active: $route.path === '/stock/historique' }">
                <span>📜 Historique</span>
              </router-link>
            </div>
          </div>

          <div class="nav-section">
            <div class="nav-section-title">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Ventes</span>
            </div>
            <div class="ml-6 space-y-0.5">
              <router-link to="/ventes" class="nav-link-sub" :class="{ active: $route.path === '/ventes' }">
                <span>📋 Toutes les ventes</span>
              </router-link>
              <router-link to="/ventes/create" class="nav-link-sub" :class="{ active: $route.path === '/ventes/create' }">
                <span>➕ Nouvelle vente</span>
              </router-link>
            </div>
          </div>

          <router-link to="/clients" class="nav-link" :class="{ active: $route.path.startsWith('/clients') }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Clients</span>
          </router-link>
        </div>
      </template>

      <!-- CAISSIER -->
      <template v-else-if="isCaissier">
        <div class="space-y-0.5">
          <div class="nav-section">
            <div class="nav-section-title">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Ventes</span>
            </div>
            <div class="ml-6 space-y-0.5">
              <router-link to="/ventes" class="nav-link-sub" :class="{ active: $route.path === '/ventes' }">
                <span>📋 Mes ventes</span>
              </router-link>
              <router-link to="/ventes/create" class="nav-link-sub" :class="{ active: $route.path === '/ventes/create' }">
                <span>➕ Nouvelle vente</span>
              </router-link>
            </div>
          </div>

          <router-link to="/clients" class="nav-link" :class="{ active: $route.path.startsWith('/clients') }">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Clients</span>
          </router-link>
        </div>
      </template>
    </nav>

    <!-- Footer -->
    <div class="p-3 border-t border-green-700/50 mt-auto">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-md">
          {{ userInitial }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-white truncate">{{ userName }}</p>
          <p class="text-[10px] text-green-300">{{ userRoleLabel }}</p>
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

<style scoped>
.nav-link {
  @apply flex items-center gap-3 px-4 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-200 text-green-100 hover:text-white hover:bg-green-700/40;
}

.nav-link.active {
  @apply bg-green-600/80 text-white shadow-md;
}

.nav-icon {
  @apply w-4 h-4 flex-shrink-0;
}

.nav-section {
  @apply mt-2;
}

.nav-section-title {
  @apply flex items-center gap-2 px-4 py-2 text-[10px] font-semibold text-green-300 uppercase tracking-wider;
}

.nav-link-sub {
  @apply flex items-center gap-2 px-2 py-1.5 rounded-md text-xs text-green-200 hover:text-white hover:bg-green-700/30 transition-colors duration-150;
}

.nav-link-sub.active {
  @apply text-white bg-green-600/50;
}
</style>