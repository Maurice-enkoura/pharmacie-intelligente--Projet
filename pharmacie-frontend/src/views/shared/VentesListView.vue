<!-- src/views/shared/VentesListView.vue -->
<template>
  <div class="ventes-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Ventes</h1>
      <router-link to="/ventes/create" class="btn-primary">
        + Nouvelle vente
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="date" v-model="filters.date_debut" @change="search" class="input-field">
        <input type="date" v-model="filters.date_fin" @change="search" class="input-field">
        <select v-model="filters.client_id" @change="search" class="input-field">
          <option :value="null">Tous les clients</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">
            {{ client.prenom }} {{ client.nom }}
          </option>
        </select>
        <button @click="search" class="btn-secondary">Filtrer</button>
      </div>
    </div>
    
    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
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
            <th class="px-4 py-3 text-center">Ordonnance</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="vente in ventes" :key="vente.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-medium">{{ vente.numero_facture }}</td>
            <td class="px-4 py-3 text-sm">{{ vente.client?.prenom }} {{ vente.client?.nom }}</td>
            <td class="px-4 py-3 text-sm">{{ formatDate(vente.date_vente) }}</td>
            <td class="px-4 py-3 text-right font-medium">{{ formatPrice(vente.montant_total) }}</td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 rounded-full text-xs" :class="getPaiementClass(vente.mode_paiement)">
                {{ getPaiementLabel(vente.mode_paiement) }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <span v-if="vente.type_vente === 'avec_ordonnance'" class="text-orange-600 text-sm">
                Oui
              </span>
              <span v-else class="text-green-600 text-sm">Non</span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <router-link :to="`/ventes/${vente.id}`" class="text-primary-600 hover:text-primary-800">
                  📄
                </router-link>
                <button 
                  v-if="isAdmin || isPharmacien" 
                  @click="cancelVente(vente)" 
                  class="text-red-600 hover:text-red-800"
                >
                  ❌                </button>
              </div>
            </td>
          </tr>
        </tbody>
       </table>
    </div>
    
    <!-- Pagination -->
    <div v-if="pagination" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-500">
        Page {{ pagination.current_page }} sur {{ pagination.last_page }}
      </div>
      <div class="flex space-x-2">
        <button @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1" class="px-3 py-1 border rounded-lg disabled:opacity-50">
          Précédent
        </button>
        <button @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" class="px-3 py-1 border rounded-lg disabled:opacity-50">
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'
import { venteService } from '@/services/vente'
import { clientService } from '@/services/client'

const authStore = useAuthStore()
const { isAdmin, isPharmacien } = storeToRefs(authStore)

const ventes = ref([])
const clients = ref([])
const loading = ref(false)
const pagination = ref(null)

const filters = ref({
  date_debut: '',
  date_fin: '',
  client_id: null,
  page: 1
})

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
    const response = await venteService.getAll(filters.value)
    ventes.value = response.data || []
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      total: response.total
    }
  } catch (error) {
    console.error('Erreur chargement ventes:', error)
  } finally {
    loading.value = false
  }
}

const loadClients = async () => {
  try {
    const response = await clientService.getAll()
    clients.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement clients:', error)
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

const cancelVente = async (vente) => {
  if (confirm(`Annuler la vente ${vente.numero_facture} ?`)) {
    try {
      await venteService.cancel(vente.id)
      loadVentes()
    } catch (error) {
      alert('Erreur lors de l\'annulation')
    }
  }
}

onMounted(() => {
  loadVentes()
  loadClients()
})
</script>