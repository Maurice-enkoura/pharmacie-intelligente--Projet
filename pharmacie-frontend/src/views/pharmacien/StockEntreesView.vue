<!-- src/views/pharmacien/StockEntreesView.vue -->
<template>
  <div class="stock-entrees">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Entrées de stock</h1>
      <button @click="showModal = true" class="btn-primary">
        + Nouvelle entrée
      </button>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <select v-model="filters.medicament_id" @change="loadEntrees" class="input-field">
          <option :value="null">Tous les médicaments</option>
          <option v-for="med in medicaments" :key="med.id" :value="med.id">
            {{ med.nom }} ({{ med.dosage }})
          </option>
        </select>
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
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Médicament</th>
            <th class="px-4 py-3 text-left">Lot</th>
            <th class="px-4 py-3 text-center">Quantité</th>
            <th class="px-4 py-3 text-right">Prix unitaire</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3 text-center">Date péremption</th>
            <th class="px-4 py-3 text-left">Fournisseur</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="entree in entrees" :key="entree.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm">{{ formatDate(entree.date_reception) }}</td>
            <td class="px-4 py-3 text-sm font-medium">{{ entree.medicament?.nom }}</td>
            <td class="px-4 py-3 text-sm">{{ entree.lot_number }}</td>
            <td class="px-4 py-3 text-center text-sm">{{ entree.quantite_restante }}</td>
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
            <td class="px-4 py-3 text-right font-bold">{{ formatPrice(totalEntrees) }}</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        {{ pagination.total }} entrées
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
    
    <!-- Modal nouvelle entrée -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold mb-4">Nouvelle entrée de stock</h3>
        
        <form @submit.prevent="submitEntree">
          <div class="space-y-4">
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Médicament *</label>
              <select v-model="newEntree.medicament_id" class="input-field w-full" required>
                <option :value="null">Sélectionner</option>
                <option v-for="med in medicaments" :key="med.id" :value="med.id">
                  {{ med.nom }} - {{ med.dosage }} (Stock: {{ med.stock_actuel }})
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Fournisseur *</label>
              <select v-model="newEntree.fournisseur_id" class="input-field w-full" required>
                <option :value="null">Sélectionner</option>
                <option v-for="four in fournisseurs" :key="four.id" :value="four.id">
                  {{ four.nom }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Numéro de lot *</label>
              <input 
                type="text" 
                v-model="newEntree.lot_number" 
                class="input-field w-full" 
                required
                placeholder="LOT-2025-001"
              >
            </div>
            
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Quantité *</label>
              <input 
                type="number" 
                v-model="newEntree.quantite" 
                class="input-field w-full" 
                required
                min="1"
              >
            </div>
            
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Prix d'achat unitaire (FCFA) *</label>
              <input 
                type="number" 
                v-model="newEntree.prix_achat" 
                class="input-field w-full" 
                required
                min="0"
                step="1"
              >
            </div>
            
            <div>
              <label class="block text-gray-700 text-sm font-medium mb-2">Date de péremption *</label>
              <input 
                type="date" 
                v-model="newEntree.date_peremption" 
                class="input-field w-full" 
                required
                :min="minDate"
              >
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="btn-secondary">Annuler</button>
            <button type="submit" :disabled="submitting" class="btn-primary">
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
    // Adapter selon la structure de l'API
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
    loadMedicaments() // Recharger pour mettre à jour les stocks
    alert('Entrée de stock enregistrée avec succès')
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de l\'enregistrement')
  } finally {
    submitting.value = false
  }
}

const closeModal = () => {
  showModal.value = false
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