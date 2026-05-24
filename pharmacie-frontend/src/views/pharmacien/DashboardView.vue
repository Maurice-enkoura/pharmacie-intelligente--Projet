<!-- src/views/pharmacien/DashboardView.vue -->
<template>
  <div class="dashboard">
    <!-- En-tête avec sélecteur de période -->
    <div class="mb-6 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
      <select v-model="periode" @change="loadStats" class="input-field w-48">
        <option value="semaine">Cette semaine</option>
        <option value="mois">Ce mois</option>
        <option value="trimestre">Ce trimestre</option>
        <option value="annee">Cette année</option>
      </select>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      <p class="mt-2 text-gray-500">Chargement des données...</p>
    </div>
    
    <div v-else>
      <!-- Cartes KPI -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Chiffre d'affaires</p>
              <p class="text-2xl font-bold text-green-600">{{ formatPrice(stats.chiffre_affaires) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">💰</div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Nombre de ventes</p>
              <p class="text-2xl font-bold text-blue-600">{{ stats.nombre_ventes }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-2xl">📊</div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Panier moyen</p>
              <p class="text-2xl font-bold text-purple-600">{{ formatPrice(stats.panier_moyen) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-2xl">🛒</div>
          </div>
        </div>
        
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Clients actifs</p>
              <p class="text-2xl font-bold text-orange-600">{{ stats.clients_actifs }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl">👥</div>
          </div>
        </div>
      </div>
      
      <!-- Graphiques -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Évolution des ventes -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">📈 Évolution des ventes</h3>
          <canvas ref="ventesChartCanvas" style="max-height: 300px; width: 100%;"></canvas>
          <div v-if="!stats.ventes_par_jour?.length" class="text-center text-gray-500 py-8">
            Aucune donnée de vente sur cette période
          </div>
        </div>
        
        <!-- Top médicaments -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">🏆 Top médicaments vendus</h3>
          <div v-if="stats.top_medicaments?.length" class="space-y-3">
            <div v-for="med in stats.top_medicaments" :key="med.nom" class="flex items-center">
              <span class="w-32 text-sm truncate" :title="med.nom">{{ med.nom }}</span>
              <div class="flex-1 mx-2">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-primary-500 rounded-full" :style="{ width: getMaxPercentage(med.total_vendus) + '%' }"></div>
                </div>
              </div>
              <span class="text-sm font-medium min-w-[80px] text-right">{{ med.total_vendus }} vendus</span>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-8">
            Aucune donnée de vente
          </div>
        </div>
      </div>
      
      <!-- Deuxième ligne de graphiques -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Paiements par mode -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">💳 Mode de paiement</h3>
          <canvas ref="paiementChartCanvas" style="max-height: 300px; width: 100%;"></canvas>
          <div v-if="!stats.paiements?.length" class="text-center text-gray-500 py-8">
            Aucune donnée de paiement
          </div>
        </div>
        
        <!-- Ventes par caissier -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">👨‍💼 Performance des caissiers</h3>
          <div v-if="stats.ventes_par_caissier?.length" class="space-y-3">
            <div v-for="user in stats.ventes_par_caissier" :key="user.name" class="flex items-center">
              <span class="w-32 text-sm truncate">{{ user.name }}</span>
              <div class="flex-1 mx-2">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-green-500 rounded-full" :style="{ width: getVentesPercentage(user.total_ventes) + '%' }"></div>
                </div>
              </div>
              <span class="text-sm font-medium">{{ user.total_ventes }} ventes</span>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-8">
            Aucune donnée de vente
          </div>
        </div>
      </div>
      
      <!-- Alertes stock -->
      <div class="card">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold flex items-center">
            <span class="text-orange-600 mr-2">⚠️</span>
            Alertes stock
          </h3>
          <div v-if="hasAlertesStock" class="flex items-center gap-2">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
            <span class="text-xs text-red-500 animate-pulse">Alerte active !</span>
          </div>
        </div>
        
        <div v-if="hasAlertesStock" class="space-y-4">
          <!-- Stock bas -->
          <div v-if="alertesStock.stock_bas?.length" class="p-3 bg-red-50 rounded-lg border border-red-200">
            <p class="font-medium text-red-700 mb-2 flex items-center gap-2">
              <span>📦 Stock bas ({{ alertesStock.stock_bas.length }})</span>
            </p>
            <div class="flex flex-wrap gap-2">
              <span v-for="med in alertesStock.stock_bas" :key="med.id" class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-sm">
                {{ med.nom }} (stock: {{ med.stock_actuel }})
              </span>
            </div>
          </div>
          
          <!-- Péremption proche -->
          <div v-if="alertesStock.peremption_proche?.length" class="p-3 bg-orange-50 rounded-lg border border-orange-200">
            <p class="font-medium text-orange-700 mb-2 flex items-center gap-2">
              <span>📅 Péremption proche ({{ alertesStock.peremption_proche.length }})</span>
            </p>
            <div class="flex flex-wrap gap-2">
              <span v-for="lot in alertesStock.peremption_proche" :key="lot.id" class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-sm">
                {{ lot.medicament?.nom }} - exp: {{ formatDate(lot.date_peremption) }}
              </span>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center text-gray-500 py-8">
          ✅ Aucune alerte stock
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick, computed } from 'vue'
import Chart from 'chart.js/auto'

const periode = ref('mois')
const loading = ref(true)
let refreshInterval = null

const stats = ref({
  chiffre_affaires: 0,
  nombre_ventes: 0,
  panier_moyen: 0,
  clients_actifs: 0,
  top_medicaments: [],
  ventes_par_jour: [],
  paiements: [],
  ventes_par_caissier: []
})

const alertesStock = ref({
  stock_bas: [],
  peremption_proche: []
})

const hasAlertesStock = computed(() => {
  return (alertesStock.value.stock_bas?.length > 0) || 
         (alertesStock.value.peremption_proche?.length > 0)
})

let ventesChart = null
let paiementChart = null
const ventesChartCanvas = ref(null)
const paiementChartCanvas = ref(null)

// ========== FORMATAGE ==========
const formatPrice = (price) => {
  if (!price && price !== 0) return '0 FCFA'
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const getMaxPercentage = (value) => {
  const max = Math.max(...(stats.value.top_medicaments?.map(m => parseInt(m.total_vendus)) || [1]))
  return max > 0 ? (parseInt(value) / max) * 100 : 0
}

const getVentesPercentage = (value) => {
  const max = Math.max(...(stats.value.ventes_par_caissier?.map(u => u.total_ventes) || [1]))
  return max > 0 ? (value / max) * 100 : 0
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

// ========== CHARGEMENT DES DONNÉES ==========
const loadStats = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/v1/rapports?periode=${periode.value}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    
    const data = await response.json()
    stats.value = {
      chiffre_affaires: data.chiffre_affaires || 0,
      nombre_ventes: data.nombre_ventes || 0,
      panier_moyen: data.panier_moyen || 0,
      clients_actifs: data.clients_actifs || 0,
      top_medicaments: data.top_medicaments || [],
      ventes_par_jour: data.ventes_par_jour || [],
      paiements: data.paiements || [],
      ventes_par_caissier: data.ventes_par_caissier || []
    }
    
    await nextTick()
    updateCharts()
    
  } catch (error) {
    console.error('Erreur chargement statistiques:', error)
  } finally {
    loading.value = false
  }
}

const loadAlertesStock = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://127.0.0.1:8000/api/v1/stock/alertes', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    
    const data = await response.json()
    alertesStock.value = {
      stock_bas: data.stock_bas || [],
      peremption_proche: data.peremption_proche || []
    }
    
  } catch (error) {
    console.error('Erreur chargement alertes stock:', error)
  }
}

// ========== GRAPHIQUES ==========
const updateCharts = () => {
  if (ventesChart) { ventesChart.destroy(); ventesChart = null }
  if (paiementChart) { paiementChart.destroy(); paiementChart = null }
  
  // Graphique des ventes (évolution)
  if (ventesChartCanvas.value && stats.value.ventes_par_jour?.length > 0) {
    const ctx = ventesChartCanvas.value.getContext('2d')
    const labels = stats.value.ventes_par_jour.map(v => {
      const date = new Date(v.date)
      return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
    })
    const data = stats.value.ventes_par_jour.map(v => parseFloat(v.ca))
    
    ventesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Chiffre d\'affaires (FCFA)',
          data: data,
          borderColor: '#22c55e',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.3,
          pointBackgroundColor: '#22c55e',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          tooltip: { callbacks: { label: (ctx) => formatPrice(ctx.raw) } },
          legend: { position: 'top' }
        },
        scales: { y: { beginAtZero: true, ticks: { callback: (val) => formatPrice(val) } } }
      }
    })
  }
  
  // Graphique des paiements (camembert)
  if (paiementChartCanvas.value && stats.value.paiements?.length > 0) {
    const ctx = paiementChartCanvas.value.getContext('2d')
    const labels = stats.value.paiements.map(p => getPaiementLabel(p.mode_paiement))
    const data = stats.value.paiements.map(p => parseFloat(p.total))
    const colors = ['#22c55e', '#f97316', '#3b82f6', '#a855f7', '#ef4444', '#8b5cf6']
    
    paiementChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors.slice(0, data.length),
          borderWidth: 0,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          tooltip: { 
            callbacks: { 
              label: (ctx) => {
                const total = data.reduce((a, b) => a + b, 0)
                const pct = ((ctx.raw / total) * 100).toFixed(1)
                return `${ctx.label}: ${formatPrice(ctx.raw)} (${pct}%)`
              }
            }
          },
          legend: { position: 'bottom' }
        }
      }
    })
  }
}

// ========== AUTO-REFRESH ==========
const startAutoRefresh = () => {
  if (refreshInterval) clearInterval(refreshInterval)
  refreshInterval = setInterval(() => {
    loadAlertesStock()
  }, 30000) // Rafraîchir toutes les 30 secondes
}

// ========== WATCHERS & LIFECYCLE ==========
watch(periode, () => { loadStats() })

onMounted(() => {
  loadStats()
  loadAlertesStock()
  startAutoRefresh()
})

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval)
  if (ventesChart) ventesChart.destroy()
  if (paiementChart) paiementChart.destroy()
})
</script>