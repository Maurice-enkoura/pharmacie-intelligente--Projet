<!-- src/views/shared/ClientsDetailView.vue -->
<template>
  <div class="client-detail">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Fiche client</h1>
      <router-link to="/clients" class="btn-secondary">Retour</router-link>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Infos client -->
      <div class="lg:col-span-1">
        <div class="card">
          <div class="text-center">
            <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 text-3xl mx-auto mb-4">
              {{ client.prenom?.charAt(0) }}{{ client.nom?.charAt(0) }}
            </div>
            <h2 class="text-xl font-bold">{{ client.prenom }} {{ client.nom }}</h2>
            <p class="text-gray-500">{{ client.telephone }}</p>
          </div>
          
          <div class="mt-6 space-y-3">
            <div v-if="client.email" class="flex items-center">
              <span class="w-8">📧</span>
              <span>{{ client.email }}</span>
            </div>
            <div v-if="client.adresse" class="flex items-center">
              <span class="w-8">📍</span>
              <span>{{ client.adresse }}</span>
            </div>
            <div v-if="client.medicaments_chroniques" class="flex items-start">
              <span class="w-8">💊</span>
              <div>
                <p class="font-medium text-orange-600">Traitements chroniques :</p>
                <p class="text-sm">{{ JSON.parse(client.medicaments_chroniques).join(', ') }}</p>
              </div>
            </div>
          </div>
          
          <div class="mt-6 pt-4 border-t flex justify-between">
            <router-link v-if="isAdmin || isPharmacien" :to="`/clients/${client.id}/edit`" class="btn-primary">
              Modifier
            </router-link>
            <router-link to="/ventes/create" :to="{ path: '/ventes/create', query: { client_id: client.id } }" class="btn-success">
              Nouvelle vente
            </router-link>
          </div>
        </div>
      </div>
      
      <!-- Historique achats -->
      <div class="lg:col-span-2">
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">Historique des achats</h3>
          
          <div v-if="client.ventes?.length === 0" class="text-center text-gray-500 py-8">
            Aucun historique d'achat
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="vente in client.ventes" :key="vente.id" class="border rounded-lg p-4">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-medium">{{ vente.numero_facture }}</p>
                  <p class="text-sm text-gray-500">{{ formatDate(vente.date_vente) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-lg font-bold text-primary-600">{{ formatPrice(vente.montant_total) }}</p>
                  <p class="text-sm text-gray-500">{{ getPaiementLabel(vente.mode_paiement) }}</p>
                </div>
              </div>
              
              <div class="mt-3 pt-3 border-t">
                <p class="text-sm font-medium mb-2">Médicaments :</p>
                <div class="flex flex-wrap gap-2">
                  <span v-for="ligne in vente.ligne_ventes" :key="ligne.id" class="bg-gray-100 px-2 py-1 rounded-full text-xs">
                    {{ ligne.medicament?.nom }} x{{ ligne.quantite }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'
import { clientService } from '@/services/client'

const route = useRoute()
const authStore = useAuthStore()
const { isAdmin, isPharmacien } = storeToRefs(authStore)

const client = ref({})
const loading = ref(true)

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

const loadClient = async () => {
  try {
    client.value = await clientService.getById(route.params.id)
  } catch (error) {
    console.error('Erreur chargement client:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadClient()
})
</script>