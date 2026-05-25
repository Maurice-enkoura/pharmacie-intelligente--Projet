<!-- src/views/pharmacien/StockEntreesView.vue -->
<template>
  <div class="stock-entrees">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Entrées de stock</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez les entrées de stock de votre pharmacie</p>
      </div>
      <button @click="showModal = true" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle entrée
      </button>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <select v-model="filters.medicament_id" @change="loadEntrees" class="input-field w-full pl-10">
            <option :value="null">Tous les médicaments</option>
            <option v-for="med in medicaments" :key="med.id" :value="med.id">
              {{ med.nom }} ({{ med.dosage }})
            </option>
          </select>
        </div>
        <input type="date" v-model="filters.date_debut" @change="loadEntrees" class="input-field">
        <input type="date" v-model="filters.date_fin" @change="loadEntrees" class="input-field">
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <!-- Tableau des entrées -->
    <div v-else class="card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Médicament</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Lot</th>
            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Quantité</th>
            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Prix unitaire</th>
            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Total</th>
            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Date péremption</th>
            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Fournisseur</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="entree in entrees" :key="entree.id" class="border-t hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm">{{ formatDate(entree.date_reception) }}</td>
            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ entree.medicament?.nom }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ entree.lot_number }}</td>
            <td class="px-4 py-3 text-center text-sm font-medium">{{ entree.quantite_restante }}</td>
            <td class="px-4 py-3 text-right text-sm">{{ formatPrice(entree.prix_achat_unitaire) }}</td>
            <td class="px-4 py-3 text-right text-sm font-medium">{{ formatPrice(entree.prix_achat_unitaire * entree.quantite_restante) }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="getPeremptionClass(entree.date_peremption)" class="px-2 py-1 rounded-full text-xs">
                {{ formatDate(entree.date_peremption) }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm">{{ entree.fournisseur?.nom }}</td>
          </tr>
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td colspan="5" class="px-4 py-3 text-right font-bold">Total entrées :</td>
            <td class="px-4 py-3 text-right font-bold text-green-600">{{ formatPrice(totalEntrees) }}</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        {{ pagination.total }} entrées
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
    
    <!-- Modal nouvelle entrée avec barre de recherche -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-800">📦 Nouvelle entrée de stock</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="submitEntree">
          <div class="space-y-4">
            <!-- Recherche Médicament -->
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Médicament *</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                  type="text"
                  v-model="medicamentSearch"
                  @input="filterMedicaments"
                  placeholder="Rechercher un médicament..."
                  class="input-field w-full pl-10"
                >
              </div>
              <select v-model="newEntree.medicament_id" class="input-field w-full mt-2" required size="5" style="height: auto;">
                <option :value="null">-- Sélectionner un médicament --</option>
                <option v-for="med in filteredMedicaments" :key="med.id" :value="med.id" class="py-2">
                  {{ med.nom }} - {{ med.dosage }} 
                  <span class="text-gray-400 text-xs ml-2">(Stock: {{ med.stock_actuel }})</span>
                </option>
              </select>
            </div>
            
            <!-- Recherche Fournisseur -->
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Fournisseur *</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                  type="text"
                  v-model="fournisseurSearch"
                  @input="filterFournisseurs"
                  placeholder="Rechercher un fournisseur..."
                  class="input-field w-full pl-10"
                >
              </div>
              <select v-model="newEntree.fournisseur_id" class="input-field w-full mt-2" required size="5" style="height: auto;">
                <option :value="null">-- Sélectionner un fournisseur --</option>
                <option v-for="four in filteredFournisseurs" :key="four.id" :value="four.id" class="py-2">
                  {{ four.nom }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Numéro de lot *</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                <input 
                  type="text" 
                  v-model="newEntree.lot_number" 
                  class="input-field w-full pl-10" 
                  required
                  placeholder="LOT-2025-001"
                >
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Quantité *</label>
                <div class="relative">
                  <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  <input 
                    type="number" 
                    v-model="newEntree.quantite" 
                    class="input-field w-full pl-10" 
                    required
                    min="1"
                  >
                </div>
              </div>
              
              <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Prix d'achat (FCFA) *</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs">CFA</span>
                  <input 
                    type="number" 
                    v-model="newEntree.prix_achat" 
                    class="input-field w-full pl-12" 
                    required
                    min="0"
                    step="1"
                  >
                </div>
              </div>
            </div>
            
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Date de péremption *</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <input 
                  type="date" 
                  v-model="newEntree.date_peremption" 
                  class="input-field w-full pl-10" 
                  required
                  :min="minDate"
                >
              </div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="btn-secondary flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Annuler
            </button>
            <button type="submit" :disabled="submitting" class="btn-primary flex items-center gap-2">
              <svg v-if="submitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              {{ submitting ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { stockService } from '@/services/stock'
import { medicamentService } from '@/services/medicament'
import { fournisseurService } from '@/services/fournisseur'

const entrees = ref([])
const medicaments = ref([])
const fournisseurs = ref([])
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)
const pagination = ref(null)

// Recherche dans le modal
const medicamentSearch = ref('')
const fournisseurSearch = ref('')

const filters = ref({
  medicament_id: null,
  date_debut: '',
  date_fin: '',
  page: 1
})

const newEntree = ref({
  medicament_id: null,
  fournisseur_id: null,
  lot_number: '',
  quantite: 1,
  prix_achat: 0,
  date_peremption: ''
})

const filteredMedicaments = computed(() => {
  if (!medicamentSearch.value) return medicaments.value
  const search = medicamentSearch.value.toLowerCase()
  return medicaments.value.filter(med => 
    med.nom.toLowerCase().includes(search) || 
    med.dci?.toLowerCase().includes(search)
  )
})

const filteredFournisseurs = computed(() => {
  if (!fournisseurSearch.value) return fournisseurs.value
  const search = fournisseurSearch.value.toLowerCase()
  return fournisseurs.value.filter(four => 
    four.nom.toLowerCase().includes(search)
  )
})

const minDate = computed(() => {
  const date = new Date()
  date.setDate(date.getDate() + 1)
  return date.toISOString().split('T')[0]
})

const totalEntrees = computed(() => {
  return entrees.value.reduce((sum, e) => sum + (e.prix_achat_unitaire * e.quantite_restante), 0)
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getPeremptionClass = (date) => {
  if (!date) return 'bg-gray-100 text-gray-700'
  const daysLeft = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  if (daysLeft <= 30) return 'bg-red-100 text-red-700'
  if (daysLeft <= 90) return 'bg-orange-100 text-orange-700'
  return 'bg-green-100 text-green-700'
}

const loadEntrees = async () => {
  loading.value = true
  try {
    const response = await stockService.getHistorique(filters.value.medicament_id)
    entrees.value = response.entrees || []
    pagination.value = {
      current_page: 1,
      last_page: 1,
      total: entrees.value.length
    }
  } catch (error) {
    console.error('Erreur chargement entrées:', error)
  } finally {
    loading.value = false
  }
}

const loadMedicaments = async () => {
  try {
    const response = await medicamentService.getAll({ page: 1 })
    medicaments.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement médicaments:', error)
  }
}

const loadFournisseurs = async () => {
  try {
    const response = await fournisseurService.getAll({ page: 1 })
    fournisseurs.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement fournisseurs:', error)
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadEntrees()
  }
}

const submitEntree = async () => {
  if (!newEntree.value.medicament_id || !newEntree.value.fournisseur_id || !newEntree.value.lot_number || !newEntree.value.quantite || !newEntree.value.prix_achat || !newEntree.value.date_peremption) {
    alert('Veuillez remplir tous les champs')
    return
  }
  
  submitting.value = true
  try {
    await stockService.entrees({
      medicament_id: newEntree.value.medicament_id,
      fournisseur_id: newEntree.value.fournisseur_id,
      lot_number: newEntree.value.lot_number,
      quantite: newEntree.value.quantite,
      prix_achat: newEntree.value.prix_achat,
      date_peremption: newEntree.value.date_peremption
    })
    
    closeModal()
    loadEntrees()
    loadMedicaments()
    alert('✅ Entrée de stock enregistrée avec succès')
  } catch (error) {
    console.error('Erreur:', error)
    alert('❌ Erreur lors de l\'enregistrement')
  } finally {
    submitting.value = false
  }
}

const closeModal = () => {
  showModal.value = false
  medicamentSearch.value = ''
  fournisseurSearch.value = ''
  newEntree.value = {
    medicament_id: null,
    fournisseur_id: null,
    lot_number: '',
    quantite: 1,
    prix_achat: 0,
    date_peremption: ''
  }
}

onMounted(() => {
  loadEntrees()
  loadMedicaments()
  loadFournisseurs()
})
</script>