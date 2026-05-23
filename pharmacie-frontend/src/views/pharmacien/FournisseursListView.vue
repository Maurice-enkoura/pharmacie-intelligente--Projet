<!-- src/views/pharmacien/FournisseursListView.vue -->
<template>
  <div class="fournisseurs-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Fournisseurs</h1>
      <router-link to="/fournisseurs/create" class="btn-primary">
        + Nouveau fournisseur
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="flex gap-4">
        <input 
          type="text" 
          v-model="filters.search" 
          @keyup.enter="search"
          placeholder="Rechercher par nom ou contact..."
          class="input-field flex-1"
        >
        <button @click="search" class="btn-secondary">Filtrer</button>
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="fournisseur in fournisseurs" :key="fournisseur.id" class="card hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between">
          <div>
            <h3 class="font-semibold text-lg">{{ fournisseur.nom }}</h3>
            <p class="text-sm text-gray-500">{{ fournisseur.contact }}</p>
          </div>
          <div class="flex space-x-2">
            <router-link :to="`/fournisseurs/${fournisseur.id}/edit`" class="text-blue-600 hover:text-blue-800">
              ✏️
            </router-link>
            <button @click="confirmDelete(fournisseur)" class="text-red-600 hover:text-red-800">
              🗑️
            </button>
          </div>
        </div>
        
        <div class="mt-3 pt-3 border-t space-y-1 text-sm">
          <div class="flex items-center">
            <span class="w-6">📞</span>
            <span>{{ fournisseur.telephone }}</span>
          </div>
          <div class="flex items-center">
            <span class="w-6">📧</span>
            <span>{{ fournisseur.email }}</span>
          </div>
          <div class="flex items-center">
            <span class="w-6">📍</span>
            <span class="truncate">{{ fournisseur.adresse }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        {{ pagination.total }} fournisseurs
      </div>
      <div class="flex space-x-2">
        <button 
          @click="goToPage(pagination.current_page - 1)" 
          :disabled="pagination.current_page <= 1"
          class="px-3 py-1 border rounded-lg disabled:opacity-50"
        >
          Précédent
        </button>
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page >= pagination.last_page"
          class="px-3 py-1 border rounded-lg disabled:opacity-50"
        >
          Suivant
        </button>
      </div>
    </div>
    
    <!-- Modal confirmation suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Confirmer la suppression</h3>
        <p>Voulez-vous vraiment supprimer le fournisseur <strong>{{ fournisseurToDelete?.nom }}</strong> ?</p>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showDeleteModal = false" class="btn-secondary">Annuler</button>
          <button @click="deleteFournisseur" class="btn-danger">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { fournisseurService } from '@/services/fournisseur'

const fournisseurs = ref([])
const loading = ref(false)
const pagination = ref(null)
const showDeleteModal = ref(false)
const fournisseurToDelete = ref(null)

const filters = ref({
  search: '',
  page: 1
})

const search = () => {
  filters.value.page = 1
  loadFournisseurs()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadFournisseurs()
  }
}

const loadFournisseurs = async () => {
  loading.value = true
  try {
    const response = await fournisseurService.getAll(filters.value)
    fournisseurs.value = response.data || []
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      total: response.total
    }
  } catch (error) {
    console.error('Erreur chargement fournisseurs:', error)
  } finally {
    loading.value = false
  }
}

const confirmDelete = (fournisseur) => {
  fournisseurToDelete.value = fournisseur
  showDeleteModal.value = true
}

const deleteFournisseur = async () => {
  if (fournisseurToDelete.value) {
    try {
      await fournisseurService.delete(fournisseurToDelete.value.id)
      showDeleteModal.value = false
      fournisseurToDelete.value = null
      loadFournisseurs()
    } catch (error) {
      alert('Erreur lors de la suppression')
    }
  }
}

onMounted(() => {
  loadFournisseurs()
})
</script>