<!-- src/views/pharmacien/MedicamentsListView.vue -->
<template>
  <div class="medicaments-list">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Médicaments</h1>
      <router-link to="/medicaments/create" class="btn-primary">
        + Nouveau médicament
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input 
          type="text" 
          v-model="filters.search" 
          @keyup.enter="search"
          placeholder="Rechercher..."
          class="input-field"
        >
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
        <button @click="search" class="btn-secondary">Filtrer</button>
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
            <th class="px-4 py-3 text-left text-sm font-semibold">Nom</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">DCI</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Forme</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Dosage</th>
            <th class="px-4 py-3 text-center text-sm font-semibold">Stock</th>
            <th class="px-4 py-3 text-right text-sm font-semibold">Prix vente</th>
            <th class="px-4 py-3 text-center text-sm font-semibold">Ordonnance</th>
            <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="med in medicaments" :key="med.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-medium">{{ med.nom }}</td>
            <td class="px-4 py-3 text-sm">{{ med.dci }}</td>
            <td class="px-4 py-3 text-sm">{{ med.forme }}</td>
            <td class="px-4 py-3 text-sm">{{ med.dosage }}</td>
            <td class="px-4 py-3 text-center">
              <span 
                :class="med.stock_actuel < med.seuil_alerte ? 'text-red-600 font-bold' : 'text-gray-600'"
              >
                {{ med.stock_actuel }}
              </span>
              <span v-if="med.stock_actuel < med.seuil_alerte" class="ml-1 text-red-500">⚠️</span>
            </td>
            <td class="px-4 py-3 text-right font-medium">{{ formatPrice(med.prix_vente) }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="med.ordonnance_requise ? 'text-orange-600' : 'text-green-600'">
                {{ med.ordonnance_requise ? 'Oui' : 'Non' }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <router-link :to="`/medicaments/${med.id}/edit`" class="text-blue-600 hover:text-blue-800">
                  ✏️
                </router-link>
                <button v-if="isAdmin" @click="confirmDelete(med)" class="text-red-600 hover:text-red-800">
                  🗑️
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
        <p>Voulez-vous vraiment supprimer <strong>{{ medicamentToDelete?.nom }}</strong> ?</p>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showDeleteModal = false" class="btn-secondary">Annuler</button>
          <button @click="deleteMedicament" class="btn-danger">Supprimer</button>
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
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
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