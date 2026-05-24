<!-- src/views/pharmacien/StockAlertesView.vue -->
<template>
  <div class="stock-alertes">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Alertes Stock</h1>
      <button 
        @click="refreshAlertes" 
        class="btn-secondary text-sm flex items-center gap-1"
      >
        <span>🔄</span> Actualiser
      </button>
    </div>
    
    <!-- Stock bas -->
    <div class="card mb-6">
      <h2 class="text-lg font-semibold mb-4 flex items-center">
        <span class="text-red-600 mr-2">⚠️</span> 
        Stock bas 
        <span class="ml-2 text-sm text-gray-500">({{ stockBas.length }})</span>
      </h2>
      
      <div v-if="stockBas.length === 0" class="text-gray-500 py-4 text-center">
        ✅ Aucun médicament en stock bas
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold">Médicament</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Stock actuel</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Seuil alerte</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Statut</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="med in stockBas" :key="med.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 font-medium">{{ med.nom }}</td>
              <td class="px-4 py-3 text-center">
                <span class="text-red-600 font-bold">{{ med.stock_actuel }}</span>
              </td>
              <td class="px-4 py-3 text-center">{{ med.seuil_alerte }}</td>
              <td class="px-4 py-3 text-center">
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">
                  ⚠️ Rupture imminente
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button 
                  @click="goToCommande(med)" 
                  class="text-primary-600 hover:text-primary-800 text-sm font-medium"
                >
                  📦 Commander
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Péremption proche -->
    <div class="card">
      <h2 class="text-lg font-semibold mb-4 flex items-center">
        <span class="text-orange-600 mr-2">📅</span> 
        Péremption proche 
        <span class="ml-2 text-sm text-gray-500">({{ peremptionProche.length }})</span>
      </h2>
      
      <div v-if="peremptionProche.length === 0" class="text-gray-500 py-4 text-center">
        ✅ Aucun médicament avec péremption proche
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold">Médicament</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Lot</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Quantité</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Date péremption</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Jours restants</th>
              <th class="px-4 py-3 text-center text-sm font-semibold">Statut</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="lot in peremptionProche" :key="lot.id" class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 font-medium">{{ lot.medicament?.nom }}</td>
              <td class="px-4 py-3 text-sm">{{ lot.lot_number }}</td>
              <td class="px-4 py-3 text-center">{{ lot.quantite_restante }}</td>
              <td class="px-4 py-3 text-center">{{ formatDate(lot.date_peremption) }}</td>
              <td class="px-4 py-3 text-center" :class="getDaysClass(lot.date_peremption)">
                {{ getDaysLeft(lot.date_peremption) }} jours
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="getStatusClass(lot.date_peremption)" class="px-2 py-1 rounded-full text-xs">
                  {{ getStatus(lot.date_peremption) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Dernière mise à jour -->
    <div class="mt-4 text-right text-xs text-gray-400">
      Dernière mise à jour : {{ lastUpdate }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { stockService } from '@/services/stock'

const router = useRouter()
const stockBas = ref([])
const peremptionProche = ref([])
const lastUpdate = ref('')
let refreshInterval = null

// ========== FORMATAGE DATES ==========
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getDaysLeft = (date) => {
  const diff = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  return diff
}

const getDaysClass = (date) => {
  const days = getDaysLeft(date)
  if (days <= 7) return 'text-red-600 font-bold'
  if (days <= 15) return 'text-orange-600'
  return 'text-yellow-600'
}

const getStatus = (date) => {
  const days = getDaysLeft(date)
  if (days <= 7) return 'URGENT !'
  if (days <= 15) return 'Attention'
  return 'À surveiller'
}

const getStatusClass = (date) => {
  const days = getDaysLeft(date)
  if (days <= 7) return 'bg-red-100 text-red-700'
  if (days <= 15) return 'bg-orange-100 text-orange-700'
  return 'bg-yellow-100 text-yellow-700'
}

// ========== ACTIONS ==========
const goToCommande = (medicament) => {
  router.push(`/commandes/create?medicament_id=${medicament.id}`)
}

const refreshAlertes = () => {
  loadAlertes()
}

// ========== CHARGEMENT DES DONNÉES ==========
const loadAlertes = async () => {
  try {
    const data = await stockService.getAlertes()
    stockBas.value = data.stock_bas || []
    peremptionProche.value = data.peremption_proche || []
    lastUpdate.value = new Date().toLocaleTimeString('fr-FR')
    
    console.log(`📊 Alertes chargées - Stock bas: ${stockBas.value.length}, Péremption: ${peremptionProche.value.length}`)
    
  } catch (error) {
    console.error('Erreur chargement alertes:', error)
  }
}

// ========== AUTO-REFRESH ==========
const startAutoRefresh = () => {
  if (refreshInterval) clearInterval(refreshInterval)
  refreshInterval = setInterval(() => {
    loadAlertes()
  }, 30000) // Rafraîchir toutes les 30 secondes
}

// ========== LIFECYCLE ==========
onMounted(() => {
  loadAlertes()
  startAutoRefresh()
})

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>