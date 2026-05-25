<!-- src/views/pharmacien/MedicamentsListView.vue -->
<template>
  <div class="medicaments-list">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Médicaments</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez le catalogue des médicaments de votre pharmacie</p>
      </div>
      <router-link to="/medicaments/create" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau médicament
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input 
            type="text" 
            v-model="filters.search" 
            @keyup.enter="search"
            placeholder="Rechercher..."
            class="input-field w-full pl-10"
          >
        </div>
        <select v-model="filters.categorie_id" @change="search" class="input-field">
          <option :value="null">Toutes catégories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.nom }}
          </option>
        </select>
        <select v-model="filters.ordonnance_requise" @change="search" class="input-field">
          <option :value="null">Tous</option>
          <option :value="true">Avec ordonnance</option>
          <option :value="false">Sans ordonnance</option>
        </select>
        <button @click="search" class="btn-secondary flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          Filtrer
        </button>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <!-- Tableau -->
    <div v-else class="card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nom</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">DCI</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Forme</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Dosage</th>
            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Stock</th>
            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Prix vente</th>
            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Ordonnance</th>
            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="med in medicaments" :key="med.id" class="border-t hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ med.nom }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ med.dci }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ med.forme }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ med.dosage }}</td>
            <td class="px-4 py-3 text-center">
              <span 
                :class="med.stock_actuel < med.seuil_alerte ? 'text-red-600 font-bold' : 'text-gray-600'"
                class="inline-flex items-center gap-1"
              >
                <svg v-if="med.stock_actuel < med.seuil_alerte" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ med.stock_actuel }}
              </span>
            </td>
            <td class="px-4 py-3 text-right font-medium text-gray-800">{{ formatPrice(med.prix_vente) }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="med.ordonnance_requise ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ med.ordonnance_requise ? 'Oui' : 'Non' }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center items-center gap-2">
                <!-- Bouton Modifier -->
                <router-link 
                  :to="`/medicaments/${med.id}/edit`" 
                  class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Modifier"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </router-link>
                
                <!-- Bouton Supprimer (admin seulement) -->
                <button 
                  v-if="isAdmin" 
                  @click="confirmDelete(med)" 
                  class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                  title="Supprimer"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        Page {{ pagination.current_page }} sur {{ pagination.last_page }}
      </div>
      <div class="flex space-x-2">
        <button 
          @click="goToPage(pagination.current_page - 1)" 
          :disabled="pagination.current_page <= 1"
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100 transition-colors flex items-center gap-1"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Précédent
        </button>
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page >= pagination.last_page"
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100 transition-colors flex items-center gap-1"
        >
          Suivant
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Modal confirmation suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-800">Confirmer la suppression</h3>
        </div>
        <p class="text-gray-600 mb-6">
          Voulez-vous vraiment supprimer <strong class="text-red-600">{{ medicamentToDelete?.nom }}</strong> ?
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Annuler
          </button>
          <button @click="deleteMedicament" class="btn-danger flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Supprimer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useMedicamentStore } from '@/stores/medicament'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const medicamentStore = useMedicamentStore()
const authStore = useAuthStore()
const { medicaments, loading, pagination } = storeToRefs(medicamentStore)
const { isAdmin } = storeToRefs(authStore)

const filters = ref({ search: '', categorie_id: null, ordonnance_requise: null })
const categories = ref([])
const showDeleteModal = ref(false)
const medicamentToDelete = ref(null)

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const loadMedicaments = async () => {
  await medicamentStore.fetchAll(filters.value)
}

const search = () => {
  filters.value.page = 1
  loadMedicaments()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadMedicaments()
  }
}

const loadCategories = async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/v1/categories', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    })
    const data = await response.json()
    categories.value = data.data || []
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

const confirmDelete = (medicament) => {
  medicamentToDelete.value = medicament
  showDeleteModal.value = true
}

const deleteMedicament = async () => {
  if (medicamentToDelete.value) {
    await medicamentStore.delete(medicamentToDelete.value.id)
    showDeleteModal.value = false
    medicamentToDelete.value = null
    loadMedicaments()
  }
}

onMounted(() => {
  loadMedicaments()
  loadCategories()
})
</script>