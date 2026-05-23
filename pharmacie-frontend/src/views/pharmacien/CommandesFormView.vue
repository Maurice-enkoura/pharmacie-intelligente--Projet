<!-- src/views/pharmacien/CommandesFormView.vue -->
<template>
  <div class="commande-form">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Nouvelle commande</h1>
      <router-link to="/commandes" class="btn-secondary">Retour</router-link>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Panier commande -->
      <div class="lg:col-span-2 card">
        <h2 class="text-lg font-semibold mb-4">Produits à commander</h2>
        
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
                class="input-field w-full" 
                placeholder="Quantité"
              >
            </div>
            <div>
              <input 
                type="number" 
                v-model="prixUnitaire" 
                min="0"
                step="1"
                class="input-field w-full" 
                placeholder="Prix unitaire"
              >
            </div>
            <div>
              <button 
                @click="addLigne" 
                :disabled="!selectedMedicament || quantite < 1 || prixUnitaire <= 0"
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
                <th class="px-4 py-2 text-center">Quantité</th>
                <th class="px-4 py-2 text-right">Prix unitaire</th>
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
                <td class="px-4 py-3 text-center">
                  <input 
                    type="number" 
                    v-model="ligne.quantite" 
                    @change="updateLigne(ligne)"
                    min="1"
                    class="w-20 text-center border rounded px-2 py-1"
                  >
                </td>
                <td class="px-4 py-3 text-right">
                  <input 
                    type="number" 
                    v-model="ligne.prix_unitaire" 
                    @change="updateLigne(ligne)"
                    min="0"
                    step="1"
                    class="w-28 text-right border rounded px-2 py-1"
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
      
      <!-- Informations commande -->
      <div class="card">
        <h2 class="text-lg font-semibold mb-4">Informations commande</h2>
        
        <!-- Fournisseur -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Fournisseur *</label>
          <div class="flex space-x-2">
            <select v-model="form.fournisseur_id" class="input-field flex-1" required>
              <option :value="null">Sélectionner</option>
              <option v-for="four in fournisseurs" :key="four.id" :value="four.id">
                {{ four.nom }}
              </option>
            </select>
            <button @click="showNewFournisseurModal = true" class="btn-secondary">+</button>
          </div>
        </div>
        
        <!-- Date commande -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Date commande *</label>
          <input type="date" v-model="form.date_commande" class="input-field w-full" required>
        </div>
        
        <!-- Date livraison prévue -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Date livraison prévue</label>
          <input type="date" v-model="form.date_livraison_prevue" class="input-field w-full">
        </div>
        
        <!-- Notes -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Notes</label>
          <textarea v-model="form.notes" class="input-field w-full" rows="3" placeholder="Instructions particulières..."></textarea>
        </div>
        
        <!-- Résumé -->
        <div class="bg-gray-50 p-3 rounded-lg mb-4">
          <div class="flex justify-between text-sm mb-2">
            <span>Nombre de produits :</span>
            <span class="font-medium">{{ panier.length }}</span>
          </div>
          <div class="flex justify-between text-sm mb-2">
            <span>Quantité totale :</span>
            <span class="font-medium">{{ quantiteTotale }}</span>
          </div>
          <div class="flex justify-between text-lg font-bold pt-2 border-t">
            <span>Montant total :</span>
            <span class="text-primary-600">{{ formatPrice(total) }}</span>
          </div>
        </div>
        
        <!-- Bouton validation -->
        <button 
          @click="submitCommande" 
          :disabled="!canSubmit || loading"
          class="btn-success w-full py-3 text-lg disabled:opacity-50"
        >
          <span v-if="loading">Traitement...</span>
          <span v-else>✅ Valider la commande</span>
        </button>
      </div>
    </div>
    
    <!-- Modal nouveau fournisseur -->
    <div v-if="showNewFournisseurModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Nouveau fournisseur</h3>
        <div class="space-y-3">
          <input v-model="newFournisseur.nom" placeholder="Nom" class="input-field w-full">
          <input v-model="newFournisseur.contact" placeholder="Personne de contact" class="input-field w-full">
          <input v-model="newFournisseur.telephone" placeholder="Téléphone" class="input-field w-full">
          <input v-model="newFournisseur.email" placeholder="Email" class="input-field w-full">
          <input v-model="newFournisseur.adresse" placeholder="Adresse" class="input-field w-full">
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showNewFournisseurModal = false" class="btn-secondary">Annuler</button>
          <button @click="saveNewFournisseur" class="btn-primary">Enregistrer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { commandeService } from '@/services/commande'
import { medicamentService } from '@/services/medicament'
import { fournisseurService } from '@/services/fournisseur'

const router = useRouter()

const medicaments = ref([])
const fournisseurs = ref([])
const selectedMedicament = ref(null)
const quantite = ref(1)
const prixUnitaire = ref(0)
const panier = ref([])
const loading = ref(false)
const showNewFournisseurModal = ref(false)

const form = ref({
  fournisseur_id: null,
  date_commande: new Date().toISOString().split('T')[0],
  date_livraison_prevue: '',
  notes: ''
})

const newFournisseur = ref({
  nom: '',
  contact: '',
  telephone: '',
  email: '',
  adresse: ''
})

const total = computed(() => {
  return panier.value.reduce((sum, ligne) => sum + ligne.sous_total, 0)
})

const quantiteTotale = computed(() => {
  return panier.value.reduce((sum, ligne) => sum + ligne.quantite, 0)
})

const canSubmit = computed(() => {
  return form.value.fournisseur_id && panier.value.length > 0
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const addLigne = () => {
  if (!selectedMedicament.value || quantite.value < 1 || prixUnitaire.value <= 0) return
  
  const existingIndex = panier.value.findIndex(l => l.medicament_id === selectedMedicament.value.id)
  
  if (existingIndex !== -1) {
    panier.value[existingIndex].quantite += quantite.value
    panier.value[existingIndex].sous_total = panier.value[existingIndex].quantite * panier.value[existingIndex].prix_unitaire
  } else {
    panier.value.push({
      medicament_id: selectedMedicament.value.id,
      medicament: selectedMedicament.value,
      quantite: quantite.value,
      prix_unitaire: prixUnitaire.value,
      sous_total: quantite.value * prixUnitaire.value
    })
  }
  
  selectedMedicament.value = null
  quantite.value = 1
  prixUnitaire.value = 0
}

const updateLigne = (ligne) => {
  ligne.sous_total = ligne.quantite * ligne.prix_unitaire
}

const removeLigne = (index) => {
  panier.value.splice(index, 1)
}

const saveNewFournisseur = async () => {
  try {
    const response = await fournisseurService.create(newFournisseur.value)
    fournisseurs.value.push(response)
    form.value.fournisseur_id = response.id
    showNewFournisseurModal.value = false
    newFournisseur.value = { nom: '', contact: '', telephone: '', email: '', adresse: '' }
    await loadFournisseurs()
  } catch (error) {
    alert('Erreur lors de la création du fournisseur')
  }
}

const submitCommande = async () => {
  loading.value = true
  try {
    const data = {
      fournisseur_id: form.value.fournisseur_id,
      date_commande: form.value.date_commande,
      lignes: panier.value.map(l => ({
        medicament_id: l.medicament_id,
        quantite: l.quantite,
        prix_unitaire: l.prix_unitaire
      }))
    }
    
    await commandeService.create(data)
    router.push('/commandes')
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la création de la commande')
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

const loadFournisseurs = async () => {
  try {
    const response = await fournisseurService.getAll({ page: 1 })
    fournisseurs.value = response.data || []
  } catch (error) {
    console.error('Erreur chargement fournisseurs:', error)
  }
}

onMounted(() => {
  loadMedicaments()
  loadFournisseurs()
})
</script>