<!-- src/views/pharmacien/CommandesListView.vue -->
<template>
  <div class="commandes-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Commandes fournisseurs</h1>
      <router-link to="/commandes/create" class="btn-primary">
        + Nouvelle commande
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <select v-model="filters.statut" @change="search" class="input-field">
          <option :value="null">Tous les statuts</option>
          <option value="en_attente">En attente</option>
          <option value="envoyee">Envoyée</option>
          <option value="recue_partielle">Réception partielle</option>
          <option value="recue_complete">Réception complète</option>
        </select>
        <input type="date" v-model="filters.date_debut" @change="search" class="input-field">
        <input type="date" v-model="filters.date_fin" @change="search" class="input-field">
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <div v-else class="card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">N° Commande</th>
            <th class="px-4 py-3 text-left">Fournisseur</th>
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-right">Montant</th>
            <th class="px-4 py-3 text-center">Statut</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="commande in commandes" :key="commande.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-medium">{{ commande.numero_commande }}</td>
            <td class="px-4 py-3 text-sm">{{ commande.fournisseur?.nom }}</td>
            <td class="px-4 py-3 text-sm">{{ formatDate(commande.date_commande) }}</td>
            <td class="px-4 py-3 text-right font-medium">{{ formatPrice(commande.montant_total) }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="getStatutClass(commande.statut)" class="px-2 py-1 rounded-full text-xs">
                {{ getStatutLabel(commande.statut) }}
              </span>
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <router-link :to="`/commandes/${commande.id}/reception`" class="text-green-600 hover:text-green-800">
                  📦 Réception
                </router-link>
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
import { commandeService } from '@/services/commande'

const commandes = ref([])
const loading = ref(false)
const pagination = ref(null)

const filters = ref({
  statut: null,
  date_debut: '',
  date_fin: '',
  page: 1
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const getStatutLabel = (statut) => {
  const labels = {
    en_attente: 'En attente',
    envoyee: 'Envoyée',
    recue_partielle: 'Réception partielle',
    recue_complete: 'Réception complète'
  }
  return labels[statut] || statut
}

const getStatutClass = (statut) => {
  const classes = {
    en_attente: 'bg-yellow-100 text-yellow-700',
    envoyee: 'bg-blue-100 text-blue-700',
    recue_partielle: 'bg-orange-100 text-orange-700',
    recue_complete: 'bg-green-100 text-green-700'
  }
  return classes[statut] || 'bg-gray-100'
}

const search = () => {
  filters.value.page = 1
  loadCommandes()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.value.page = page
    loadCommandes()
  }
}

const loadCommandes = async () => {
  loading.value = true
  try {
    const response = await commandeService.getAll(filters.value)
    commandes.value = response.data || []
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      total: response.total
    }
  } catch (error) {
    console.error('Erreur chargement commandes:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCommandes()
})
</script>