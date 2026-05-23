<!-- src/views/pharmacien/RapportsView.vue -->
<template>
  <div class="rapports">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Rapports et statistiques</h1>
    
    <!-- Période selector -->
    <div class="card mb-6">
      <div class="flex flex-wrap gap-4 items-end">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Période</label>
          <select v-model="periode" @change="loadRapports" class="input-field">
            <option value="semaine">Cette semaine</option>
            <option value="mois">Ce mois</option>
            <option value="trimestre">Ce trimestre</option>
            <option value="annee">Cette année</option>
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Date début</label>
          <input type="date" v-model="dateDebut" class="input-field">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Date fin</label>
          <input type="date" v-model="dateFin" class="input-field">
        </div>
        <div>
          <button @click="loadRapports" class="btn-primary">Générer</button>
        </div>
        <div>
          <button @click="exportExcel" class="btn-success">📊 Export Excel</button>
        </div>
        <div>
          <button @click="exportPDF" class="btn-secondary">📄 Export PDF</button>
        </div>
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <div v-else>
      <!-- Cartes KPI -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
              <p class="text-2xl font-bold text-gray-800">{{ formatPrice(rapports.chiffre_affaires || 0) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">💰</div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Nombre de ventes</p>
              <p class="text-2xl font-bold text-gray-800">{{ rapports.nombre_ventes || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">📊</div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Panier moyen</p>
              <p class="text-2xl font-bold text-gray-800">{{ formatPrice(rapports.panier_moyen || 0) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-2xl">🛒</div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Clients actifs</p>
              <p class="text-2xl font-bold text-gray-800">{{ rapports.clients_actifs || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl">👥</div>
          </div>
        </div>
      </div>
      
      <!-- Ventes par jour -->
      <div class="card mb-8">
        <h3 class="text-lg font-semibold mb-4">Évolution des ventes</h3>
        <canvas ref="ventesChart"></canvas>
      </div>
      
      <!-- Top médicaments -->
      <div class="card mb-8">
        <h3 class="text-lg font-semibold mb-4">Top 10 médicaments vendus</h3>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">Médicament</th>
                <th class="px-4 py-2 text-center">Quantité vendue</th>
                <th class="px-4 py-2 text-right">Chiffre d'affaires</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="med in rapports.top_medicaments" :key="med.nom" class="border-t">
                <td class="px-4 py-3">{{ med.nom }}</td>
                <td class="px-4 py-3 text-center">{{ med.total_vendus }}</td>
                <td class="px-4 py-3 text-right">{{ formatPrice(med.ca) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Ventes par mode de paiement -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">Mode de paiement</h3>
          <canvas ref="paiementChart"></canvas>
        </div>
        
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">Ventes par caissier</h3>
          <div class="space-y-3">
            <div v-for="user in rapports.ventes_par_caissier" :key="user.name" class="flex items-center">
              <span class="w-32 text-sm">{{ user.name }}</span>
              <div class="flex-1 mx-2">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-primary-500 rounded-full" :style="{ width: getMaxVentesPourcentage(user.total_ventes) + '%' }"></div>
                </div>
              </div>
              <span class="text-sm font-medium">{{ user.total_ventes }} ventes</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

const periode = ref('mois')
const dateDebut = ref('')
const dateFin = ref('')
const loading = ref(false)
const rapports = ref({})

const ventesChart = ref(null)
const paiementChart = ref(null)
let ventesChartInstance = null
let paiementChartInstance = null

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const getMaxVentesPourcentage = (value) => {
  const max = Math.max(...(rapports.value.ventes_par_caissier?.map(u => u.total_ventes) || [1]))
  return (value / max) * 100
}

const loadRapports = async () => {
  loading.value = true
  try {
    const params = {
      periode: periode.value,
      date_debut: dateDebut.value,
      date_fin: dateFin.value
    }
    const response = await fetch('http://127.0.0.1:8000/api/v1/rapports', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
    rapports.value = await response.json()
    
    // Mettre à jour les graphiques
    updateCharts()
  } catch (error) {
    console.error('Erreur chargement rapports:', error)
  } finally {
    loading.value = false
  }
}

const updateCharts = () => {
  if (ventesChartInstance) {
    ventesChartInstance.destroy()
  }
  if (paiementChartInstance) {
    paiementChartInstance.destroy()
  }
  
  // Graphique des ventes
  if (ventesChart.value && rapports.value.ventes_par_jour) {
    ventesChartInstance = new Chart(ventesChart.value, {
      type: 'line',
      data: {
        labels: rapports.value.ventes_par_jour.map(v => v.date),
        datasets: [{
          label: 'Chiffre d\'affaires',
          data: rapports.value.ventes_par_jour.map(v => v.ca),
          borderColor: '#22c55e',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: {
          tooltip: {
            callbacks: {
              label: (context) => `${formatPrice(context.raw)}`
            }
          }
        }
      }
    })
  }
  
  // Graphique des paiements
  if (paiementChart.value && rapports.value.paiements) {
    paiementChartInstance = new Chart(paiementChart.value, {
      type: 'doughnut',
      data: {
        labels: rapports.value.paiements.map(p => getPaiementLabel(p.mode_paiement)),
        datasets: [{
          data: rapports.value.paiements.map(p => p.total),
          backgroundColor: ['#22c55e', '#f97316', '#3b82f6', '#a855f7']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          tooltip: {
            callbacks: {
              label: (context) => `${context.label}: ${formatPrice(context.raw)}`
            }
          }
        }
      }
    })
  }
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

const exportExcel = async () => {
  window.open('http://127.0.0.1:8000/api/v1/rapports/excel', '_blank')
}

const exportPDF = () => {
  window.print()
}

onMounted(() => {
  loadRapports()
})
</script>

<style scoped>
@media print {
  .btn-primary, .btn-success, .btn-secondary, .flex.gap-4 {
    display: none;
  }
}
</style>