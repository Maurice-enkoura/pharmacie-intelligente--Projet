<!-- src/views/pharmacien/DashboardView.vue -->
<template>
  <div class="dashboard">
    <!-- En-tête avec sélecteur de période -->
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
        <p class="text-sm text-gray-500 mt-1">Aperçu des performances de votre pharmacie</p>
      </div>
      <select v-model="periode" @change="loadStats" class="input-field w-48">
        <option value="semaine">Cette semaine</option>
        <option value="mois">Ce mois</option>
        <option value="trimestre">Ce trimestre</option>
        <option value="annee">Cette année</option>
      </select>
    </div>
    
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-green-600"></div>
      <p class="mt-4 text-gray-500">Chargement des données...</p>
    </div>
    
    <div v-else>
      <!-- Cartes KPI -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Chiffre d'affaires</p>
              <p class="text-2xl font-bold text-green-600">{{ formatPrice(stats.chiffre_affaires) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>
        
        <div class="card hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Nombre de ventes</p>
              <p class="text-2xl font-bold text-blue-600">{{ stats.nombre_ventes }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
          </div>
        </div>
        
        <div class="card hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Panier moyen</p>
              <p class="text-2xl font-bold text-purple-600">{{ formatPrice(stats.panier_moyen) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
            </div>
          </div>
        </div>
        
        <div class="card hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm font-medium">Clients actifs</p>
              <p class="text-2xl font-bold text-orange-600">{{ stats.clients_actifs }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Graphiques -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Évolution des ventes -->
        <div class="card">
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            <h3 class="text-lg font-semibold">Évolution des ventes</h3>
          </div>
          <canvas ref="ventesChartCanvas" style="max-height: 300px; width: 100%;"></canvas>
          <div v-if="!stats.ventes_par_jour?.length" class="text-center text-gray-500 py-8">
            Aucune donnée de vente sur cette période
          </div>
        </div>
        
        <!-- Top médicaments -->
        <div class="card">
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold">Top médicaments vendus</h3>
          </div>
          <div v-if="stats.top_medicaments?.length" class="space-y-3">
            <div v-for="med in stats.top_medicaments" :key="med.nom" class="flex items-center">
              <span class="w-32 text-sm truncate" :title="med.nom">{{ med.nom }}</span>
              <div class="flex-1 mx-2">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-green-500 rounded-full" :style="{ width: getMaxPercentage(med.total_vendus) + '%' }"></div>
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
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <h3 class="text-lg font-semibold">Mode de paiement</h3>
          </div>
          <canvas ref="paiementChartCanvas" style="max-height: 300px; width: 100%;"></canvas>
          <div v-if="!stats.paiements?.length" class="text-center text-gray-500 py-8">
            Aucune donnée de paiement
          </div>
        </div>
        
        <!-- Ventes par caissier -->
        <div class="card">
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="text-lg font-semibold">Performance des caissiers</h3>
          </div>
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
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-lg font-semibold">Alertes stock</h3>
          </div>
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
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
              <span>Stock bas ({{ alertesStock.stock_bas.length }})</span>
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
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span>Péremption proche ({{ alertesStock.peremption_proche.length }})</span>
            </p>
            <div class="flex flex-wrap gap-2">
              <span v-for="lot in alertesStock.peremption_proche" :key="lot.id" class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-sm">
                {{ lot.medicament?.nom }} - exp: {{ formatDate(lot.date_peremption) }}
              </span>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center text-gray-500 py-8">
          <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p>Aucune alerte stock</p>
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

const updateCharts = () => {
  if (ventesChart) { ventesChart.destroy(); ventesChart = null }
  if (paiementChart) { paiementChart.destroy(); paiementChart = null }
  
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

const startAutoRefresh = () => {
  if (refreshInterval) clearInterval(refreshInterval)
  refreshInterval = setInterval(() => {
    loadAlertesStock()
  }, 30000)
}

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