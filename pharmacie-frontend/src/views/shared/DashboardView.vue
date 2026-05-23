<!-- src/views/shared/DashboardView.vue -->
<template>
  <div class="dashboard">
    <!-- Période selector -->
    <div class="mb-6 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
      <select v-model="periode" @change="loadStats" class="input-field">
        <option value="semaine">Cette semaine</option>
        <option value="mois">Ce mois</option>
        <option value="annee">Cette année</option>
      </select>
    </div>
    
    <!-- Cartes KPI -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
            <p class="text-2xl font-bold text-gray-800">{{ formatPrice(stats.chiffre_affaires || 0) }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <span class="text-2xl">💰</span>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Nombre de ventes</p>
            <p class="text-2xl font-bold text-gray-800">{{ stats.nombre_ventes || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
            <span class="text-2xl">📊</span>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Clients</p>
            <p class="text-2xl font-bold text-gray-800">{{ stats.nombre_clients || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
            <span class="text-2xl">👥</span>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Stock alerte</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.stock_alerte || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
            <span class="text-2xl">⚠️</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Top médicaments -->
      <div class="card">
        <h3 class="text-lg font-semibold mb-4">Top médicaments vendus</h3>
        <div v-if="stats.top_medicaments?.length" class="space-y-3">
          <div v-for="med in stats.top_medicaments" :key="med.nom" class="flex items-center">
            <span class="w-32 text-sm">{{ med.nom }}</span>
            <div class="flex-1 mx-2">
              <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-primary-500 rounded-full" 
                  :style="{ width: getMaxPercentage(med.total_vendus) + '%' }"
                ></div>
              </div>
            </div>
            <span class="text-sm font-medium">{{ med.total_vendus }} vendus</span>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 py-8">
          Aucune donnée
        </div>
      </div>
      
      <!-- Paiements par mode -->
      <div class="card">
        <h3 class="text-lg font-semibold mb-4">Mode de paiement</h3>
        <div v-if="stats.paiements?.length" class="space-y-3">
          <div v-for="paiement in stats.paiements" :key="paiement.mode_paiement" class="flex items-center">
            <span class="w-32 text-sm">{{ getPaiementLabel(paiement.mode_paiement) }}</span>
            <div class="flex-1 mx-2">
              <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-green-500 rounded-full" 
                  :style="{ width: getPaiementPercentage(paiement.total) + '%' }"
                ></div>
              </div>
            </div>
            <span class="text-sm font-medium">{{ formatPrice(paiement.total) }}</span>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 py-8">
          Aucune donnée
        </div>
      </div>
    </div>
    
    <!-- Dernières ventes -->
    <div class="card">
      <h3 class="text-lg font-semibold mb-4">Dernières ventes</h3>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-sm">Facture</th>
              <th class="px-4 py-2 text-left text-sm">Client</th>
              <th class="px-4 py-2 text-left text-sm">Date</th>
              <th class="px-4 py-2 text-right text-sm">Montant</th>
              <th class="px-4 py-2 text-center text-sm">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="vente in recentVentes" :key="vente.id" class="border-t">
              <td class="px-4 py-2 text-sm">{{ vente.numero_facture }}</td>
              <td class="px-4 py-2 text-sm">{{ vente.client?.prenom }} {{ vente.client?.nom }}</td>
              <td class="px-4 py-2 text-sm">{{ formatDate(vente.date_vente) }}</td>
              <td class="px-4 py-2 text-right text-sm font-medium">{{ formatPrice(vente.montant_total) }}</td>
              <td class="px-4 py-2 text-center">
                <router-link :to="`/ventes/${vente.id}`" class="text-primary-600 hover:underline text-sm">
                  Voir
                </router-link>
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
import { dashboardService } from '@/services/dashboard'
import { venteService } from '@/services/vente'

const periode = ref('mois')
const stats = ref({})
const recentVentes = ref([])

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const getPaiementLabel = (mode) => {
  const labels = {
    especes: 'Espèces',
    orange_money: 'Orange Money',
    wave: 'Wave',
    carte: 'Carte'
  }
  return labels[mode] || mode
}

const getMaxPercentage = (value) => {
  const max = Math.max(...(stats.value.top_medicaments?.map(m => m.total_vendus) || [1]))
  return (value / max) * 100
}

const getPaiementPercentage = (value) => {
  const total = stats.value.paiements?.reduce((sum, p) => sum + p.total, 0) || 1
  return (value / total) * 100
}

const loadStats = async () => {
  try {
    stats.value = await dashboardService.getStats(periode.value)
  } catch (error) {
    console.error('Erreur chargement stats:', error)
  }
}

const loadRecentVentes = async () => {
  try {
    const response = await venteService.getAll({ page: 1 })
    recentVentes.value = response.data?.slice(0, 5) || []
  } catch (error) {
    console.error('Erreur chargement ventes:', error)
  }
}

onMounted(() => {
  loadStats()
  loadRecentVentes()
})
</script>