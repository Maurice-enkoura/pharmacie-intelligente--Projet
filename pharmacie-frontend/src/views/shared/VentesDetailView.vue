<!-- src/views/shared/VentesDetailView.vue -->
<template>
  <div class="vente-detail">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Détail de la vente</h1>
      <div class="space-x-3">
        <button @click="print" class="btn-secondary">🖨️ Imprimer</button>
        <router-link to="/ventes" class="btn-secondary">Retour</router-link>
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <div v-else class="card">
      <!-- En-tête -->
      <div class="text-center mb-6 pb-4 border-b">
        <h2 class="text-xl font-bold">Pharmacie Intelligente</h2>
        <p class="text-gray-500">Facture N° {{ vente.numero_facture }}</p>
        <p class="text-gray-500">Date: {{ formatDateTime(vente.date_vente) }}</p>
      </div>
      
      <!-- Infos client -->
      <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm text-gray-500">Client</p>
          <p class="font-medium">{{ vente.client?.prenom }} {{ vente.client?.nom }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Téléphone</p>
          <p>{{ vente.client?.telephone }}</p>
        </div>
      </div>
      
      <!-- Détails médicaments -->
      <div class="overflow-x-auto mb-6">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Médicament</th>
              <th class="px-4 py-2 text-center">Prix unitaire</th>
              <th class="px-4 py-2 text-center">Quantité</th>
              <th class="px-4 py-2 text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ligne in vente.ligne_ventes" :key="ligne.id" class="border-t">
              <td class="px-4 py-3">{{ ligne.medicament?.nom }} ({{ ligne.medicament?.dosage }})</td>
              <td class="px-4 py-3 text-center">{{ formatPrice(ligne.prix_unitaire) }}</td>
              <td class="px-4 py-3 text-center">{{ ligne.quantite }}</td>
              <td class="px-4 py-3 text-right font-medium">{{ formatPrice(ligne.sous_total) }}</td>
            </tr>
          </tbody>
          <tfoot class="bg-gray-50">
            <tr>
              <td colspan="3" class="px-4 py-3 text-right font-bold">Total :</td>
              <td class="px-4 py-3 text-right text-lg text-primary-600 font-bold">{{ formatPrice(vente.montant_total) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      
      <!-- Infos paiement -->
      <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm text-gray-500">Mode de paiement</p>
          <p>{{ getPaiementLabel(vente.mode_paiement) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Montant payé</p>
          <p>{{ formatPrice(vente.montant_paye) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Monnaie rendue</p>
          <p>{{ formatPrice(vente.monnaie_rendue) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Caissier</p>
          <p>{{ vente.user?.name }}</p>
        </div>
        <div v-if="vente.type_vente === 'avec_ordonnance'" class="col-span-2">
          <p class="text-sm text-gray-500">Référence ordonnance</p>
          <p class="text-orange-600">{{ vente.ordonnance_ref }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { venteService } from '@/services/vente'

const route = useRoute()
const vente = ref({})
const loading = ref(true)

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('fr-FR')
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

const print = () => {
  window.print()
}

const loadVente = async () => {
  try {
    vente.value = await venteService.getById(route.params.id)
  } catch (error) {
    console.error('Erreur chargement vente:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadVente()
})
</script>

<style scoped>
@media print {
  .btn-secondary, .btn-primary, .space-x-3 {
    display: none;
  }
  .card {
    box-shadow: none;
    padding: 0;
  }
}
</style>