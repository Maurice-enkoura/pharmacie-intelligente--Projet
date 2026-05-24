<!-- src/views/caissier/VentesListView.vue -->
<template>
  <div class="ventes-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Mes ventes</h1>
      <router-link to="/ventes/create" class="btn-primary">
        + Nouvelle vente
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="date" v-model="filters.date_debut" @change="search" class="input-field">
        <input type="date" v-model="filters.date_fin" @change="search" class="input-field">
        <button @click="search" class="btn-secondary">Filtrer</button>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      <p class="mt-2 text-gray-500">Chargement des ventes...</p>
    </div>
    
    <!-- Tableau -->
    <div v-else class="card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">Facture</th>
            <th class="px-4 py-3 text-left">Client</th>
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-right">Montant</th>
            <th class="px-4 py-3 text-center">Paiement</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="vente in ventes" :key="vente.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-medium">{{ vente.numero_facture }}</td>
            <td class="px-4 py-3 text-sm">
              {{ vente.client?.prenom || '' }} {{ vente.client?.nom || '' }}
            </td>
            <td class="px-4 py-3 text-sm">{{ formatDate(vente.date_vente) }}</td>
            <td class="px-4 py-3 text-right font-medium">{{ formatPrice(parseFloat(vente.montant_total)) }}</td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 rounded-full text-xs" :class="getPaiementClass(vente.mode_paiement)">
                {{ getPaiementLabel(vente.mode_paiement) }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <router-link :to="`/ventes/${vente.id}`" class="text-primary-600 hover:text-primary-800">
                📄 Détail
              </router-link>
            </td>
          </tr>
          <tr v-if="ventes.length === 0 && !loading">
            <td colspan="6" class="text-center py-8 text-gray-500">
              Aucune vente enregistrée
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        Page {{ pagination.current_page }} sur {{ pagination.last_page }} ({{ pagination.total }} ventes)
      </div>
      <div class="flex space-x-2">
        <button 
          @click="goToPage(pagination.current_page - 1)" 
          :disabled="pagination.current_page <= 1"
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          Précédent
        </button>
        <button 
          @click="goToPage(pagination.current_page + 1)" 
          :disabled="pagination.current_page >= pagination.last_page"
          class="px-3 py-1 border rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const ventes = ref([])
const loading = ref(false)
const pagination = ref(null)

const filters = ref({
  date_debut: '',
  date_fin: '',
  page: 1
})

const formatPrice = (price) => {
  if (!price && price !== 0) return '0 FCFA'
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDate = (date) => {
  if (!date) return ''
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

const getPaiementClass = (mode) => {
  const classes = {
    especes: 'bg-gray-100 text-gray-700',
    orange_money: 'bg-orange-100 text-orange-700',
    wave: 'bg-blue-100 text-blue-700',
    carte: 'bg-purple-100 text-purple-700'
  }
  return classes[mode] || 'bg-gray-100'
}

const loadVentes = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const params = new URLSearchParams()
    
    if (filters.value.date_debut) params.append('date_debut', filters.value.date_debut)
    if (filters.value.date_fin) params.append('date_fin', filters.value.date_fin)
    if (filters.value.page) params.append('page', filters.value.page)
    
    const response = await fetch(`http://127.0.0.1:8000/api/v1/ventes?${params.toString()}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    console.log('Données reçues:', data) // Debug
    
    // Les données sont dans data.data
    ventes.value = data.data || []
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      total: data.total || 0
    }
    
    console.log('Ventes à afficher:', ventes.value.length) // Debug
  } catch (error) {
    console.error('Erreur chargement ventes:', error)
  } finally {
    loading.value = false
  }
}

const search = () => {
  filters.value.page = 1
  loadVentes()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadVentes()
  }
}

onMounted(() => {
  loadVentes()
})
</script>