<!-- src/views/shared/VentesFormView.vue -->
<template>
  <div class="vente-form">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Nouvelle vente</h1>
      <router-link to="/ventes" class="btn-secondary">Retour</router-link>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Panier -->
      <div class="lg:col-span-2 card">
        <h2 class="text-lg font-semibold mb-4">Panier</h2>
        
        <!-- Ajout médicament -->
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
              <select v-model="selectedMedicament" class="input-field w-full">
                <option :value="null">-- Choisir un médicament --</option>
                <option v-for="med in medicaments" :key="med.id" :value="med">
                  {{ med.nom }} - {{ med.dosage }} (Stock: {{ med.stock_actuel }})
                </option>
              </select>
            </div>
            <div>
              <input 
                type="number" 
                v-model="quantite" 
                min="1"
                :max="selectedMedicament?.stock_actuel"
                class="input-field w-full" 
                placeholder="Qté"
              >
            </div>
            <div>
              <button 
                @click="addLigne" 
                :disabled="!selectedMedicament || quantite < 1"
                class="btn-primary w-full"
              >
                Ajouter
              </button>
            </div>
          </div>
        </div>
        
        <!-- Tableau panier -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left">Médicament</th>
                <th class="px-4 py-2 text-center">Prix unitaire</th>
                <th class="px-4 py-2 text-center">Quantité</th>
                <th class="px-4 py-2 text-right">Sous-total</th>
                <th class="px-4 py-2 text-center"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(ligne, index) in panier" :key="index" class="border-t">
                <td class="px-4 py-3">
                  {{ ligne.medicament.nom }}
                  <span class="text-xs text-gray-500 block">{{ ligne.medicament.dosage }}</span>
                </td>
                <td class="px-4 py-3 text-center">{{ formatPrice(ligne.prix_unitaire) }}</td>
                <td class="px-4 py-3 text-center">
                  <input 
                    type="number" 
                    v-model="ligne.quantite" 
                    @change="updateLigne(ligne)"
                    min="1"
                    :max="ligne.medicament.stock_actuel"
                    class="w-20 text-center border rounded px-2 py-1"
                  >
                </td>
                <td class="px-4 py-3 text-right font-medium">{{ formatPrice(ligne.sous_total) }}</td>
                <td class="px-4 py-3 text-center">
                  <button @click="removeLigne(index)" class="text-red-500 hover:text-red-700">🗑️</button>
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 font-bold">
              <tr>
                <td colspan="3" class="px-4 py-3 text-right">Total :</td>
                <td class="px-4 py-3 text-right text-lg text-primary-600">{{ formatPrice(total) }}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
      <!-- Informations paiement -->
      <div class="card">
        <h2 class="text-lg font-semibold mb-4">Paiement</h2>
        
        <!-- Client -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Client *</label>
          <div class="flex space-x-2">
            <select v-model="form.client_id" class="input-field flex-1" required>
              <option :value="null">Sélectionner</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.prenom }} {{ client.nom }} - {{ client.telephone }}
              </option>
            </select>
            <button @click="showNewClientModal = true" class="btn-secondary">+</button>
          </div>
        </div>
        
        <!-- Type vente -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Type de vente *</label>
          <div class="flex space-x-4">
            <label class="flex items-center">
              <input type="radio" v-model="form.type_vente" value="sans_ordonnance" class="mr-2">
              Sans ordonnance
            </label>
            <label class="flex items-center">
              <input type="radio" v-model="form.type_vente" value="avec_ordonnance" class="mr-2">
              Avec ordonnance
            </label>
          </div>
        </div>
        
        <!-- Référence ordonnance -->
        <div v-if="form.type_vente === 'avec_ordonnance'" class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Référence ordonnance *</label>
          <input type="text" v-model="form.ordonnance_ref" class="input-field w-full">
        </div>
        
        <!-- Mode paiement -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Mode de paiement *</label>
          <select v-model="form.mode_paiement" class="input-field w-full">
            <option value="especes">Espèces</option>
            <option value="orange_money">Orange Money</option>
            <option value="wave">Wave</option>
            <option value="carte">Carte</option>
          </select>
        </div>
        
        <!-- Montant payé -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Montant payé (FCFA)</label>
          <input 
            type="number" 
            v-model="form.montant_paye" 
            @input="calculateMonnaie"
            class="input-field w-full"
            min="0"
          >
        </div>
        
        <!-- Rendu monnaie -->
        <div v-if="monnaie > 0" class="mb-4 p-3 bg-green-50 rounded-lg">
          <p class="text-sm text-green-700">Monnaie à rendre :</p>
          <p class="text-2xl font-bold text-green-600">{{ formatPrice(monnaie) }}</p>
        </div>
        
        <!-- Reste à payer -->
        <div v-if="reste > 0" class="mb-4 p-3 bg-red-50 rounded-lg">
          <p class="text-sm text-red-700">Reste à payer :</p>
          <p class="text-2xl font-bold text-red-600">{{ formatPrice(reste) }}</p>
        </div>
        
        <!-- Message erreur ordonnance -->
        <div v-if="ordonnanceError" class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-lg">
          {{ ordonnanceError }}
        </div>
        
        <!-- Bouton validation -->
        <button 
          @click="submitVente" 
          :disabled="!canSubmit || loading"
          class="btn-success w-full py-3 text-lg disabled:opacity-50"
        >
          <span v-if="loading">Traitement...</span>
          <span v-else>✅ Valider la vente</span>
        </button>
      </div>
    </div>
    
    <!-- Modal nouveau client -->
    <div v-if="showNewClientModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Nouveau client</h3>
        <div class="space-y-3">
          <input v-model="newClient.nom" placeholder="Nom" class="input-field w-full">
          <input v-model="newClient.prenom" placeholder="Prénom" class="input-field w-full">
          <input v-model="newClient.telephone" placeholder="Téléphone" class="input-field w-full">
          <input v-model="newClient.email" placeholder="Email" class="input-field w-full">
          <input v-model="newClient.adresse" placeholder="Adresse" class="input-field w-full">
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showNewClientModal = false" class="btn-secondary">Annuler</button>
          <button @click="saveNewClient" class="btn-primary">Enregistrer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { venteService } from '@/services/vente'
import { medicamentService } from '@/services/medicament'
import { clientService } from '@/services/client'

const router = useRouter()

const medicaments = ref([])
const clients = ref([])
const selectedMedicament = ref(null)
const quantite = ref(1)
const panier = ref([])
const loading = ref(false)
const showNewClientModal = ref(false)
const ordonnanceError = ref('')

const form = ref({
  client_id: null,
  type_vente: 'sans_ordonnance',
  ordonnance_ref: null,
  mode_paiement: 'especes',
  montant_paye: 0
})

const newClient = ref({
  nom: '',
  prenom: '',
  telephone: '',
  email: '',
  adresse: ''
})

const total = computed(() => {
  return panier.value.reduce((sum, ligne) => sum + ligne.sous_total, 0)
})

const reste = computed(() => {
  return total.value - form.value.montant_paye
})

const monnaie = computed(() => {
  if (form.value.montant_paye > total.value) {
    return form.value.montant_paye - total.value
  }
  return 0
})

const canSubmit = computed(() => {
  if (!form.value.client_id) return false
  if (panier.value.length === 0) return false
  if (form.value.montant_paye < total.value) return false
  if (form.value.type_vente === 'avec_ordonnance' && !form.value.ordonnance_ref) return false
  return true
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const addLigne = () => {
  if (!selectedMedicament.value || quantite.value < 1) return
  
  // Vérifier ordonnance
  if (selectedMedicament.value.ordonnance_requise && form.value.type_vente === 'sans_ordonnance') {
    ordonnanceError.value = `⚠️ ${selectedMedicament.value.nom} nécessite une ordonnance`
    setTimeout(() => { ordonnanceError.value = '' }, 3000)
    return
  }
  
  // Vérifier stock
  if (selectedMedicament.value.stock_actuel < quantite.value) {
    alert(`Stock insuffisant pour ${selectedMedicament.value.nom}`)
    return
  }
  
  const existingIndex = panier.value.findIndex(l => l.medicament_id === selectedMedicament.value.id)
  
  if (existingIndex !== -1) {
    // Mettre à jour quantité existante
    const newQuantite = panier.value[existingIndex].quantite + quantite.value
    if (selectedMedicament.value.stock_actuel >= newQuantite) {
      panier.value[existingIndex].quantite = newQuantite
      panier.value[existingIndex].sous_total = panier.value[existingIndex].prix_unitaire * newQuantite
    } else {
      alert(`Stock insuffisant`)
    }
  } else {
    // Ajouter nouvelle ligne
    panier.value.push({
      medicament_id: selectedMedicament.value.id,
      medicament: selectedMedicament.value,
      quantite: quantite.value,
      prix_unitaire: selectedMedicament.value.prix_vente,
      sous_total: selectedMedicament.value.prix_vente * quantite.value
    })
  }
  
  selectedMedicament.value = null
  quantite.value = 1
}

const updateLigne = (ligne) => {
  if (ligne.medicament.stock_actuel >= ligne.quantite) {
    ligne.sous_total = ligne.prix_unitaire * ligne.quantite
  } else {
    alert(`Stock insuffisant`)
    ligne.quantite = ligne.medicament.stock_actuel
    ligne.sous_total = ligne.prix_unitaire * ligne.quantite
  }
}

const removeLigne = (index) => {
  panier.value.splice(index, 1)
}

const calculateMonnaie = () => {
  // Computed gère automatiquement
}

const saveNewClient = async () => {
  try {
    const response = await clientService.create(newClient.value)
    clients.value.push(response)
    form.value.client_id = response.id
    showNewClientModal.value = false
    newClient.value = { nom: '', prenom: '', telephone: '', email: '', adresse: '' }
    await loadClients()
  } catch (error) {
    alert('Erreur lors de la création du client')
  }
}

const submitVente = async () => {
  loading.value = true
  try {
    const data = {
      client_id: form.value.client_id,
      mode_paiement: form.value.mode_paiement,
      montant_paye: form.value.montant_paye,
      type_vente: form.value.type_vente,
      ordonnance_ref: form.value.ordonnance_ref,
      lignes: panier.value.map(l => ({
        medicament_id: l.medicament_id,
        quantite: l.quantite
      }))
    }
    
    await venteService.create(data)
    router.push('/ventes')
  } catch (error) {
    alert('Erreur: ' + (error.response?.data?.message || 'Erreur lors de l\'enregistrement'))
  } finally {
    loading.value = false
  }
}

const loadMedicaments = async () => {
  try {
    const response = await medicamentService.getAll({ page: 1 })
    medicaments.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement médicaments:', error)
  }
}

const loadClients = async () => {
  try {
    const response = await clientService.getAll({ page: 1 })
    clients.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement clients:', error)
  }
}

onMounted(() => {
  loadMedicaments()
  loadClients()
})
</script>