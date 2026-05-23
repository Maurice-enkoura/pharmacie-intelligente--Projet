<!-- src/views/pharmacien/StockHistoriqueView.vue -->
<template>
  <div class="stock-historique">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Historique des mouvements de stock</h1>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Médicament</label>
          <select v-model="filters.medicament_id" @change="loadHistorique" class="input-field w-full">
            <option :value="null">Tous les médicaments</option>
            <option v-for="med in medicaments" :key="med.id" :value="med.id">
              {{ med.nom }} ({{ med.dosage }})
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Type de mouvement</label>
          <select v-model="filters.type" @change="loadHistorique" class="input-field w-full">
            <option :value="null">Tous</option>
            <option value="entree">Entrées</option>
            <option value="sortie">Sorties (ventes)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Date début</label>
          <input type="date" v-model="filters.date_debut" @change="loadHistorique" class="input-field w-full">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Date fin</label>
          <input type="date" v-model="filters.date_fin" @change="loadHistorique" class="input-field w-full">
        </div>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <!-- Résumé -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total entrées</p>
            <p class="text-2xl font-bold text-green-600">{{ totalEntrees }} unités</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">📥</div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total sorties</p>
            <p class="text-2xl font-bold text-red-600">{{ totalSorties }} unités</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-2xl">📤</div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Variation nette</p>
            <p class="text-2xl font-bold" :class="variationNette >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ variationNette >= 0 ? '+' : '' }}{{ variationNette }} unités
            </p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">📊</div>
        </div>
      </div>
    </div>
    
    <!-- Tableau historique -->
    <div v-if="!loading" class="card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Médicament</th>
            <th class="px-4 py-3 text-center">Type</th>
            <th class="px-4 py-3 text-center">Quantité</th>
            <th class="px-4 py-3 text-right">Prix unitaire</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3 text-left">Référence</th>
            <th class="px-4 py-3 text-left">Fournisseur/Client</th>
          </tr>
        </thead>
        <tbody>
          <!-- Entrées -->
          <tr v-for="entree in entrees" :key="'entree-' + entree.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm">{{ formatDateTime(entree.date_reception) }}</td>
            <td class="px-4 py-3 text-sm font-medium">{{ entree.medicament?.nom }}</td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Entrée</span>
            </td>
            <td class="px-4 py-3 text-center text-green-600 font-medium">+{{ entree.quantite_restante }}</td>
            <td class="px-4 py-3 text-right">{{ formatPrice(entree.prix_achat_unitaire) }}</td>
            <td class="px-4 py-3 text-right">{{ formatPrice(entree.prix_achat_unitaire * entree.quantite_restante) }}</td>
            <td class="px-4 py-3 text-sm">{{ entree.lot_number }}</td>
            <td class="px-4 py-3 text-sm">{{ entree.fournisseur?.nom }}</td>
          </tr>
          
          <!-- Sorties -->
          <tr v-for="sortie in sorties" :key="'sortie-' + sortie.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm">{{ formatDateTime(sortie.vente?.date_vente) }}</td>
            <td class="px-4 py-3 text-sm font-medium">{{ sortie.medicament?.nom }}</td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Sortie</span>
            </td>
            <td class="px-4 py-3 text-center text-red-600 font-medium">-{{ sortie.quantite }}</td>
            <td class="px-4 py-3 text-right">{{ formatPrice(sortie.prix_unitaire) }}</td>
            <td class="px-4 py-3 text-right">{{ formatPrice(sortie.sous_total) }}</td>
            <td class="px-4 py-3 text-sm">{{ sortie.vente?.numero_facture }}</td>
            <td class="px-4 py-3 text-sm">{{ sortie.vente?.client?.prenom }} {{ sortie.vente?.client?.nom }}</td>
          </tr>
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td colspan="7" class="px-4 py-3 text-right font-bold">Solde final :</td>
            <td class="px-4 py-3 text-left font-bold text-primary-600">{{ soldeFinal }} unités</td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        {{ pagination.total }} mouvements
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { stockService } from '@/services/stock'
import { medicamentService } from '@/services/medicament'

const medicaments = ref([])
const entrees = ref([])
const sorties = ref([])
const loading = ref(false)
const pagination = ref(null)

const filters = ref({
  medicament_id: null,
  type: null,
  date_debut: '',
  date_fin: '',
  page: 1
})

const totalEntrees = computed(() => {
  return entrees.value.reduce((sum, e) => sum + e.quantite_restante, 0)
})

const totalSorties = computed(() => {
  return sorties.value.reduce((sum, s) => sum + s.quantite, 0)
})

const variationNette = computed(() => {
  return totalEntrees.value - totalSorties.value
})

const soldeFinal = computed(() => {
  const medicament = medicaments.value.find(m => m.id === filters.value.medicament_id)
  return medicament?.stock_actuel || 0
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('fr-FR')
}

const loadMedicaments = async () => {
  try {
    const response = await medicamentService.getAll({ page: 1 })
    medicaments.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement médicaments:', error)
  }
}

const loadHistorique = async () => {
  loading.value = true
  try {
    const response = await stockService.getHistorique(filters.value.medicament_id)
    entrees.value = response.entrees || []
    sorties.value = response.sorties || []
    
    // Appliquer filtres supplémentaires
    if (filters.value.type === 'entree') {
      sorties.value = []
    } else if (filters.value.type === 'sortie') {
      entrees.value = []
    }
    
    if (filters.value.date_debut) {
      const debut = new Date(filters.value.date_debut)
      entrees.value = entrees.value.filter(e => new Date(e.date_reception) >= debut)
      sorties.value = sorties.value.filter(s => new Date(s.vente?.date_vente) >= debut)
    }
    
    if (filters.value.date_fin) {
      const fin = new Date(filters.value.date_fin)
      fin.setHours(23, 59, 59)
      entrees.value = entrees.value.filter(e => new Date(e.date_reception) <= fin)
      sorties.value = sorties.value.filter(s => new Date(s.vente?.date_vente) <= fin)
    }
    
    pagination.value = {
      current_page: 1,
      last_page: 1,
      total: entrees.value.length + sorties.value.length
    }
  } catch (error) {
    console.error('Erreur chargement historique:', error)
  } finally {
    loading.value = false
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadHistorique()
  }
}

onMounted(() => {
  loadMedicaments()
  loadHistorique()
})
</script>