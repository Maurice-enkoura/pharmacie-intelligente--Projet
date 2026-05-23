<!-- src/views/pharmacien/StockAlertesView.vue -->
<template>
  <div class="stock-alertes">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Alertes Stock</h1>
    
    <!-- Stock bas -->
    <div class="card mb-6">
      <h2 class="text-lg font-semibold mb-4 flex items-center">
        <span class="text-red-600 mr-2">⚠️</span> Stock bas ({{ stockBas.length }})
      </h2>
      <div v-if="stockBas.length === 0" class="text-gray-500 py-4 text-center">
        Aucun médicament en stock bas
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Médicament</th>
              <th class="px-4 py-2 text-center">Stock actuel</th>
              <th class="px-4 py-2 text-center">Seuil alerte</th>
              <th class="px-4 py-2 text-center">Statut</th>
              <th class="px-4 py-2 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="med in stockBas" :key="med.id" class="border-t">
              <td class="px-4 py-3 font-medium">{{ med.nom }}</td>
              <td class="px-4 py-3 text-center text-red-600 font-bold">{{ med.stock_actuel }}</td>
              <td class="px-4 py-3 text-center">{{ med.seuil_alerte }}</td>
              <td class="px-4 py-3 text-center">
                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Rupture imminente</span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="goToCommande(med)" class="text-primary-600 hover:underline text-sm">
                  Passer commande
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
        <span class="text-orange-600 mr-2">📅</span> Péremption proche ({{ peremptionProche.length }})
      </h2>
      <div v-if="peremptionProche.length === 0" class="text-gray-500 py-4 text-center">
        Aucun médicament avec péremption proche
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Médicament</th>
              <th class="px-4 py-2 text-left">Lot</th>
              <th class="px-4 py-2 text-center">Quantité</th>
              <th class="px-4 py-2 text-center">Date péremption</th>
              <th class="px-4 py-2 text-center">Jours restants</th>
              <th class="px-4 py-2 text-center">Statut</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="lot in peremptionProche" :key="lot.id" class="border-t">
              <td class="px-4 py-3 font-medium">{{ lot.medicament?.nom }}</td>
              <td class="px-4 py-3">{{ lot.lot_number }}</td>
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { stockService } from '@/services/stock'

const router = useRouter()
const stockBas = ref([])
const peremptionProche = ref([])

const formatDate = (date) => {
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
  if (days <= 7) return 'Urgent'
  if (days <= 15) return 'Attention'
  return 'À surveiller'
}

const getStatusClass = (date) => {
  const days = getDaysLeft(date)
  if (days <= 7) return 'bg-red-100 text-red-700'
  if (days <= 15) return 'bg-orange-100 text-orange-700'
  return 'bg-yellow-100 text-yellow-700'
}

const goToCommande = (medicament) => {
  router.push(`/commandes/create?medicament_id=${medicament.id}`)
}

const loadAlertes = async () => {
  try {
    const data = await stockService.getAlertes()
    stockBas.value = data.stock_bas || []
    peremptionProche.value = data.peremption_proche || []
  } catch (error) {
    console.error('Erreur chargement alertes:', error)
  }
}

onMounted(() => {
  loadAlertes()
})
</script>