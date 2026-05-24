<!-- src/views/caissier/ClientsListView.vue -->
<template>
  <div class="clients-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Clients</h1>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="flex gap-4">
        <input 
          type="text" 
          v-model="filters.search" 
          @keyup.enter="search"
          placeholder="Rechercher par nom, prénom ou téléphone..."
          class="input-field flex-1"
        >
        <button @click="search" class="btn-secondary">Rechercher</button>
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      <p class="mt-2 text-gray-500">Chargement des clients...</p>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="client in clients" :key="client.id" class="card hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 text-xl font-bold">
              {{ getInitials(client) }}
            </div>
            <div>
              <h3 class="font-semibold">{{ client.prenom }} {{ client.nom }}</h3>
              <p class="text-sm text-gray-500">{{ client.telephone }}</p>
            </div>
          </div>
          <router-link :to="`/ventes/create?client_id=${client.id}`" class="text-green-600 hover:text-green-800 text-sm">
            Vendre →
          </router-link>
        </div>
        <div class="mt-3 pt-3 border-t text-sm text-gray-500">
          <p v-if="client.email">{{ client.email }}</p>
          <p v-if="client.adresse">{{ client.adresse.substring(0, 50) }}...</p>
        </div>
      </div>
      
      <div v-if="clients.length === 0" class="col-span-full text-center py-8 text-gray-500">
        Aucun client trouvé
      </div>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        {{ pagination.total }} clients
      </div>
      <div class="flex space-x-2">
        <button 
          @click="goToPage(pagination.current_page - 1)" 
          :disabled="pagination.current_page <= 1"
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          Précédent
        </button>
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page >= pagination.last_page"
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { clientService } from '@/services/client'

const clients = ref([])
const loading = ref(false)
const pagination = ref(null)

const filters = ref({
  search: '',
  page: 1
})

const getInitials = (client) => {
  return `${(client.prenom?.charAt(0) || '')}${(client.nom?.charAt(0) || '')}`.toUpperCase()
}

const search = () => {
  filters.value.page = 1
  loadClients()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadClients()
  }
}

const loadClients = async () => {
  loading.value = true
  try {
    const response = await clientService.getAll(filters.value)
    clients.value = response.data || []
    pagination.value = {
      current_page: response.current_page || 1,
      last_page: response.last_page || 1,
      total: response.total || 0
    }
  } catch (error) {
    console.error('Erreur chargement clients:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadClients()
})
</script>