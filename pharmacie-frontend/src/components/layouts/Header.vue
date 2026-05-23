<template>
  <header class="bg-white shadow-sm">
    <div class="flex justify-between items-center px-6 py-4">
      <div>
        <h2 class="text-xl font-semibold text-gray-800">
          {{ pageTitle }}
        </h2>
      </div>
      
      <div class="flex items-center space-x-4">
        <div class="relative" ref="userMenuRef">
          <button 
            @click="toggleMenu" 
            class="flex items-center space-x-2 text-gray-700 hover:text-gray-900"
          >
            <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center text-white">
              {{ userInitial }}
            </div>
            <span>{{ userName }}</span>
            <span class="text-xs">▼</span>
          </button>
          
          <div v-if="isMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border">
            <div class="px-4 py-2 border-b">
              <p class="text-sm font-medium">{{ userName }}</p>
              <p class="text-xs text-gray-500">{{ userRoleLabel }}</p>
            </div>
            <button @click="handleLogout" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
              Déconnexion
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const isMenuOpen = ref(false)
const userMenuRef = ref(null)

const userName = computed(() => authStore.user?.name || 'Utilisateur')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase())
const userRoleLabel = computed(() => {
  const role = authStore.user?.role
  switch(role) {
    case 'admin': return 'Administrateur'
    case 'pharmacien': return 'Pharmacien'
    case 'caissier': return 'Caissier'
    default: return 'Utilisateur'
  }
})

const pageTitle = computed(() => {
  const path = route.path
  const titles = {
    '/': 'Dashboard',
    '/medicaments': 'Médicaments',
    '/stock/alertes': 'Alertes Stock',
    '/ventes': 'Ventes',
    '/ventes/create': 'Nouvelle Vente',
    '/clients': 'Clients',
    '/fournisseurs': 'Fournisseurs',
    '/commandes': 'Commandes',
    '/rapports': 'Rapports',
    '/utilisateurs': 'Utilisateurs'
  }
  return titles[path] || 'Pharmacie'
})

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const handleLogout = async () => {
  try {
    await authStore.logout()
  } catch (error) {
    console.error('Erreur déconnexion:', error)
  }
  // Redirection forcée même si erreur
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}

const handleClickOutside = (event) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    isMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>