<!-- src/views/pharmacien/CommandesReceptionView.vue -->
<template>
  <div class="commande-reception">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Réception de commande</h1>
      <router-link to="/commandes" class="btn-secondary">Retour</router-link>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Infos commande -->
      <div class="card">
        <h2 class="text-lg font-semibold mb-4">Informations commande</h2>
        <div class="space-y-2">
          <p><strong>N° commande :</strong> {{ commande.numero_commande }}</p>
          <p><strong>Fournisseur :</strong> {{ commande.fournisseur?.nom }}</p>
          <p><strong>Date commande :</strong> {{ formatDate(commande.date_commande) }}</p>
          <p><strong>Statut :</strong> 
            <span :class="getStatutClass(commande.statut)" class="px-2 py-1 rounded-full text-xs">
              {{ getStatutLabel(commande.statut) }}
            </span>
          </p>
        </div>
      </div>
      
      <!-- Réception -->
      <div class="card">
        <h2 class="text-lg font-semibold mb-4">Réception des produits</h2>
        
        <div class="space-y-4">
          <div v-for="ligne in commande.ligne_commandes" :key="ligne.id" class="border rounded-lg p-3">
            <div class="flex justify-between items-start">
              <div>
                <p class="font-medium">{{ ligne.medicament?.nom }}</p>
                <p class="text-sm text-gray-500">Commandé: {{ ligne.quantite_commandee }}</p>
                <p class="text-sm text-gray-500">Déjà reçu: {{ ligne.quantite_recue }}</p>
              </div>
              <div class="text-right">
                <label class="block text-sm text-gray-600">À recevoir</label>
                <input 
                  type="number" 
                  v-model="receptions[ligne.id]"
                  :max="ligne.quantite_commandee - ligne.quantite_recue"
                  min="0"
                  class="input-field w-24 text-center"
                >
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end">
          <button 
            @click="submitReception" 
            :disabled="!hasChanges"
            class="btn-primary"
          >
            Valider la réception
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { commandeService } from '@/services/commande'

const route = useRoute()
const router = useRouter()

const commande = ref({})
const loading = ref(true)
const receptions = ref({})

const hasChanges = computed(() => {
  return Object.values(receptions.value).some(q => q > 0)
})

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

const submitReception = async () => {
  const lignes = Object.entries(receptions.value)
    .filter(([_, quantite]) => quantite > 0)
    .map(([id, quantite]) => ({
      ligne_commande_id: parseInt(id),
      quantite_recue: quantite
    }))
  
  if (lignes.length === 0) return
  
  try {
    await commandeService.reception(route.params.id, { lignes })
    router.push('/commandes')
  } catch (error) {
    alert('Erreur lors de la réception')
  }
}

const loadCommande = async () => {
  try {
    commande.value = await commandeService.getById(route.params.id)
    // Initialiser les réceptions à 0
    commande.value.ligne_commandes?.forEach(ligne => {
      receptions.value[ligne.id] = 0
    })
  } catch (error) {
    console.error('Erreur chargement commande:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCommande()
})
</script>