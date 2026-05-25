<!-- src/views/shared/VentesFormView.vue -->
<template>
  <div class="vente-form">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Nouvelle vente</h1>
        <p class="text-sm text-gray-500 mt-1">Enregistrement d'une transaction client</p>
      </div>
      <router-link to="/ventes" class="btn-secondary flex items-center gap-2">
        ← Retour
      </router-link>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- ==================== PANIER ==================== -->
      <div class="lg:col-span-2">
        <div class="card">
          <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
            🛒 Panier <span v-if="panier.length" class="text-sm text-gray-500">({{ panier.length }} produit(s))</span>
          </h2>
          
          <!-- 🔍 Barre de recherche intelligente -->
          <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <div class="relative">
              <div class="flex items-center gap-2">
                <div class="relative flex-1">
                  <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
                  <input 
                    type="text"
                    v-model="searchQuery"
                    @input="onSearchInput"
                    @keyup.enter="selectFirstResult"
                    placeholder="Rechercher un médicament (nom, DCI, catégorie)..."
                    class="input-field w-full pl-10"
                    :class="{ 'border-primary-500 ring-2 ring-primary-200': selectedMedicament }"
                    autocomplete="off"
                  >
                  <button 
                    v-if="searchQuery"
                    @click="clearSearch"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                  >
                    ✕
                  </button>
                </div>
                <div class="w-24">
                  <input 
                    type="number" 
                    v-model="quantite" 
                    min="1"
                    class="input-field w-full text-center" 
                    placeholder="Qté"
                  >
                </div>
                <button 
                  @click="addSelectedMedicament" 
                  :disabled="!selectedMedicament || quantite < 1"
                  class="btn-primary px-4 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Ajouter
                </button>
              </div>
              
              <!-- Résultats de recherche -->
              <div v-if="searchResults.length > 0 && searchQuery && !selectedMedicament" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-80 overflow-y-auto">
                <div 
                  v-for="med in searchResults" 
                  :key="med.id"
                  @click="selectMedicament(med)"
                  class="flex items-center justify-between p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition-colors"
                >
                  <div class="flex-1">
                    <div class="flex items-center flex-wrap gap-2">
                      <span class="font-medium text-gray-800">{{ med.nom }}</span>
                      <span class="text-xs text-gray-500">{{ med.dosage }}</span>
                      <span v-if="med.ordonnance_requise" class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                        🔒 Ordonnance
                      </span>
                      <span v-if="med.stock_actuel < med.seuil_alerte" class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
                        ⚠️ Stock bas
                      </span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                      {{ med.dci }} | {{ med.forme }} | {{ med.categorie?.nom || 'Non catégorisé' }}
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="font-semibold text-primary-600">{{ formatPrice(med.prix_vente) }}</div>
                    <div class="text-xs text-gray-500">Stock: {{ med.stock_actuel }}</div>
                  </div>
                </div>
              </div>
              
              <!-- Message aucun résultat -->
              <div v-if="!searching && searchQuery && searchResults.length === 0 && !selectedMedicament" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl p-4 text-center">
                <div class="flex flex-col items-center gap-2">
                  <span class="text-3xl">🔍</span>
                  <span class="text-gray-500">Aucun médicament trouvé pour "{{ searchQuery }}"</span>
                  <span class="text-xs text-gray-400">Vérifiez l'orthographe ou ajoutez un nouveau médicament</span>
                </div>
              </div>
            </div>
            
            <!-- Produit sélectionné -->
            <div v-if="selectedMedicament" class="mt-3 p-2 bg-primary-50 rounded-lg flex justify-between items-center">
              <div>
                <span class="text-xs text-gray-500">Produit sélectionné</span>
                <div class="font-medium text-primary-700">{{ selectedMedicament.nom }} - {{ selectedMedicament.dosage }}</div>
                <div class="text-xs text-gray-500">Prix: {{ formatPrice(selectedMedicament.prix_vente) }} | Stock: {{ selectedMedicament.stock_actuel }}</div>
              </div>
              <button @click="clearSelectedMedicament" class="text-gray-400 hover:text-gray-600 text-sm px-2 py-1 rounded hover:bg-white">
                ✕ Changer
              </button>
            </div>
            
            <!-- Indicateur de recherche -->
            <div v-if="searching" class="mt-2 text-center text-sm text-gray-500">
              <div class="inline-flex items-center gap-2">
                <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-primary-600"></div>
                Recherche en cours...
              </div>
            </div>
          </div>
          
          <!-- Tableau panier -->
          <!-- Tableau panier - version corrigée -->
<div v-if="panier.length > 0" class="overflow-x-auto">
  <table class="w-full">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Médicament</th>
        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Prix unitaire</th>
        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Quantité</th>
        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Sous-total</th>
        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(ligne, index) in panier" :key="index" class="border-t hover:bg-gray-50">
        <td class="px-4 py-3">
          <div>
            <span class="font-medium text-gray-800">{{ ligne.medicament.nom }}</span>
            <div class="text-xs text-gray-400 mt-0.5">{{ ligne.medicament.dci }} - {{ ligne.medicament.dosage }}</div>
          </div>
        </td>
        <td class="px-4 py-3 text-center">{{ formatPrice(ligne.prix_unitaire) }}</td>
        <td class="px-4 py-3 text-center">
          <input 
            type="number" 
            v-model="ligne.quantite" 
            @change="updateLigne(ligne)"
            min="1"
            :max="ligne.medicament.stock_actuel"
            class="w-20 text-center border rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
        </td>
        <td class="px-4 py-3 text-right font-medium">{{ formatPrice(ligne.sous_total) }}</td>
        <td class="px-4 py-3 text-center">
          <button @click="removeLigne(index)" class="text-red-500 hover:text-red-700 transition-colors" title="Supprimer">
            🗑️
          </button>
        </td>
      </tr>
    </tbody>
    <tfoot class="bg-gray-50 border-t">
      <tr>
        <td colspan="3" class="px-4 py-3 text-right font-semibold">Total :</td>
        <td class="px-4 py-3 text-right text-xl font-bold text-primary-600">{{ formatPrice(total) }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</div>
          
          <div v-else class="text-center py-12">
            <div class="text-5xl mb-3">🛒</div>
            <p class="text-gray-500">Panier vide</p>
            <p class="text-sm text-gray-400">Recherchez et ajoutez des médicaments</p>
          </div>
        </div>
        
        <!-- ==================== TABLEAU DES VENTES DU CAISSIER ==================== -->
        <div class="card mt-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold flex items-center gap-2">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              Mes ventes aujourd'hui
            </h2>
            <button @click="loadMesVentes" class="text-sm text-primary-600 hover:underline flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Actualiser
            </button>
          </div>
          
          <div v-if="loadingVentes" class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
          </div>
          
          <div v-else-if="mesVentes.length === 0" class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>Aucune vente enregistrée aujourd'hui</p>
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left">Facture</th>
                  <th class="px-3 py-2 text-left">Client</th>
                  <th class="px-3 py-2 text-center">Heure</th>
                  <th class="px-3 py-2 text-right">Montant</th>
                  <th class="px-3 py-2 text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="vente in mesVentes" :key="vente.id" class="border-t hover:bg-gray-50">
                  <td class="px-3 py-2 font-medium">{{ vente.numero_facture }}</td>
                  <td class="px-3 py-2">{{ vente.client?.prenom }} {{ vente.client?.nom }}</td>
                  <td class="px-3 py-2 text-center">{{ formatTime(vente.created_at) }}</td>
                  <td class="px-3 py-2 text-right font-medium">{{ formatPrice(vente.montant_total) }}</td>
                  <td class="px-3 py-2 text-center">
                    <router-link :to="`/ventes/${vente.id}`" class="text-primary-600 hover:text-primary-800" title="Voir détails">
                      📄
                    </router-link>
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-gray-50 border-t">
  <tr>
    <td colspan="3" class="px-4 py-3 text-right font-semibold">Total :</td>
    <td class="px-4 py-3 text-right text-xl font-bold text-primary-600">{{ formatPrice(total) }}</td>
    <td></td>
  </tr>
</tfoot>
            </table>
          </div>
        </div>
      </div>
      
      <!-- ==================== PAIEMENT ==================== -->
      <div class="card">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">💳 Paiement</h2>
        
        <!-- Recherche client -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Client</label>
          <div class="relative">
            <input 
              type="text"
              v-model="clientSearchQuery"
              @input="onClientSearchInput"
              @focus="clientSearchFocused = true"
              placeholder="Rechercher par nom, prénom ou téléphone..."
              class="input-field w-full"
              autocomplete="off"
            >
            <div v-if="clientSearchFocused && clientSearchResults.length > 0" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-y-auto">
              <div 
                v-for="client in clientSearchResults" 
                :key="client.id"
                @click="selectClient(client)"
                class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition-colors"
              >
                <div class="font-medium">{{ client.prenom }} {{ client.nom }}</div>
                <div class="text-sm text-gray-500">{{ client.telephone }}</div>
                <div v-if="client.email" class="text-xs text-gray-400">{{ client.email }}</div>
              </div>
              <div v-if="clientSearchResults.length === 0 && clientSearchQuery" class="p-3 text-center text-gray-500">
                Aucun client trouvé
              </div>
            </div>
          </div>
          
          <button 
            @click="showNewClientModal = true" 
            class="text-primary-600 text-sm mt-2 hover:underline flex items-center gap-1"
          >
            ➕ Nouveau client
          </button>
        </div>
        
        <!-- Client sélectionné -->
        <div v-if="selectedClient" class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-xs text-green-600 font-medium">Client sélectionné</p>
              <p class="font-semibold text-gray-800">{{ selectedClient.prenom }} {{ selectedClient.nom }}</p>
              <p class="text-sm text-gray-600">{{ selectedClient.telephone }}</p>
              <p v-if="selectedClient.email" class="text-xs text-gray-500">{{ selectedClient.email }}</p>
            </div>
            <button @click="clearSelectedClient" class="text-gray-400 hover:text-gray-600" title="Changer de client">
              🔄
            </button>
          </div>
        </div>
        
        <!-- Type de vente -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Type de vente</label>
          <div class="flex gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" v-model="form.type_vente" value="sans_ordonnance" class="w-4 h-4 text-primary-600">
              <span>📄 Sans ordonnance</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" v-model="form.type_vente" value="avec_ordonnance" class="w-4 h-4 text-primary-600">
              <span>📋 Avec ordonnance</span>
            </label>
          </div>
        </div>
        
        <!-- Référence ordonnance (générée automatiquement) -->
        <div v-if="form.type_vente === 'avec_ordonnance'" class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Référence ordonnance</label>
          <div class="relative">
            <input 
              type="text" 
              v-model="form.ordonnance_ref" 
              class="input-field w-full bg-gray-50" 
              readonly
              disabled
            >
            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-1">✅ Référence générée automatiquement</p>
        </div>
        
        <!-- Mode de paiement -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Mode de paiement</label>
          <select v-model="form.mode_paiement" class="input-field w-full">
            <option value="especes">💰 Espèces</option>
            <option value="orange_money">📱 Orange Money</option>
            <option value="wave">🌊 Wave</option>
            <option value="carte">💳 Carte bancaire</option>
          </select>
        </div>
        
        <!-- Montant payé -->
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Montant payé (FCFA)</label>
          <input 
            type="number" 
            v-model="form.montant_paye" 
            class="input-field w-full"
            min="0"
            step="500"
            placeholder="0"
          >
        </div>
        
        <!-- Monnaie à rendre -->
        <div v-if="monnaie > 0" class="mb-4 p-3 bg-green-100 rounded-lg">
          <div class="flex justify-between items-center">
            <span class="text-green-800">💰 Monnaie à rendre</span>
            <span class="text-2xl font-bold text-green-700">{{ formatPrice(monnaie) }}</span>
          </div>
        </div>
        
        <!-- Reste à payer -->
        <div v-if="reste > 0" class="mb-4 p-3 bg-red-100 rounded-lg">
          <div class="flex justify-between items-center">
            <span class="text-red-800">⚠️ Reste à payer</span>
            <span class="text-2xl font-bold text-red-700">{{ formatPrice(reste) }}</span>
          </div>
        </div>
        
        <!-- Message erreur -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
          {{ errorMessage }}
        </div>
        
        <!-- Récapitulatif -->
        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
          <div class="flex justify-between text-sm mb-1">
            <span class="text-gray-600">Nombre d'articles :</span>
            <span class="font-medium">{{ panier.length }}</span>
          </div>
          <div class="flex justify-between text-sm mb-1">
            <span class="text-gray-600">Quantité totale :</span>
            <span class="font-medium">{{ quantiteTotale }}</span>
          </div>
          <div class="flex justify-between text-lg font-bold pt-2 border-t mt-2">
            <span>Total TTC :</span>
            <span class="text-primary-600">{{ formatPrice(total) }}</span>
          </div>
        </div>
        
        <!-- Bouton validation -->
        <button 
          @click="submitVente" 
          :disabled="!canSubmit || loading || panier.length === 0"
          class="btn-success w-full py-3 text-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-all"
        >
          <span v-if="loading" class="flex items-center justify-center gap-2">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
            Traitement en cours...
          </span>
          <span v-else class="flex items-center justify-center gap-2">
            ✅ Valider la vente
          </span>
        </button>
      </div>
    </div>
    
    <!-- ==================== MODAL NOUVEAU CLIENT ==================== -->
    <div v-if="showNewClientModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold text-gray-800">➕ Nouveau client</h3>
          <button @click="closeNewClientModal" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm text-gray-600 mb-1">Nom *</label>
              <input v-model="newClient.nom" placeholder="Diop" class="input-field w-full">
            </div>
            <div>
              <label class="block text-sm text-gray-600 mb-1">Prénom *</label>
              <input v-model="newClient.prenom" placeholder="Aminata" class="input-field w-full">
            </div>
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Téléphone *</label>
            <input v-model="newClient.telephone" placeholder="77 123 45 67" class="input-field w-full">
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input v-model="newClient.email" type="email" placeholder="client@email.com" class="input-field w-full">
          </div>
          
          <template v-if="isAdmin || isPharmacien">
            <div>
              <label class="block text-sm text-gray-600 mb-1">Adresse</label>
              <textarea v-model="newClient.adresse" rows="2" placeholder="Dakar, Sicap Liberté" class="input-field w-full"></textarea>
            </div>
          </template>
          
          <div v-if="isCaissier" class="text-xs text-gray-400 bg-gray-50 p-2 rounded">
            💡 L'email permet d'envoyer les factures électroniques au client.
          </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
          <button @click="closeNewClientModal" class="btn-secondary">Annuler</button>
          <button @click="saveNewClient" class="btn-primary">Enregistrer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { venteService } from '@/services/vente'
import { medicamentService } from '@/services/medicament'
import { clientService } from '@/services/client'

const router = useRouter()
const authStore = useAuthStore()

// Rôles
const isAdmin = computed(() => authStore.isAdmin)
const isPharmacien = computed(() => authStore.isPharmacien)
const isCaissier = computed(() => authStore.isCaissier)

// États recherche médicaments
const searchQuery = ref('')
const searchResults = ref([])
const searching = ref(false)
const selectedMedicament = ref(null)
let searchTimeout = null

// États panier
const quantite = ref(1)
const panier = ref([])

// États formulaire
const form = ref({
  client_id: null,
  type_vente: 'sans_ordonnance',
  ordonnance_ref: null,
  mode_paiement: 'especes',
  montant_paye: 0
})

// États recherche client
const clientSearchQuery = ref('')
const clientSearchResults = ref([])
const clientSearchFocused = ref(false)
const selectedClient = ref(null)
let clientSearchTimeout = null

// États modaux et erreurs
const loading = ref(false)
const showNewClientModal = ref(false)
const errorMessage = ref('')

// États ventes du caissier
const mesVentes = ref([])
const loadingVentes = ref(false)

// Nouveau client
const newClient = ref({
  nom: '',
  prenom: '',
  telephone: '',
  email: '',
  adresse: ''
})

// 🔥 GÉNÉRER UNE RÉFÉRENCE ORDONNANCE UNIQUE
const generateOrdonnanceRef = () => {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0')
  return `ORD-${year}${month}${day}-${random}`
}

// 🔥 SURVEILLER LE CHANGEMENT DE TYPE DE VENTE
watch(() => form.value.type_vente, (newValue) => {
  if (newValue === 'avec_ordonnance' && !form.value.ordonnance_ref) {
    form.value.ordonnance_ref = generateOrdonnanceRef()
  } else if (newValue === 'sans_ordonnance') {
    form.value.ordonnance_ref = null
  }
})

// ==================== COMPUTED ====================
const total = computed(() => {
  return panier.value.reduce((sum, ligne) => sum + (ligne.sous_total || 0), 0)
})

const reste = computed(() => {
  return Math.max(0, total.value - (form.value.montant_paye || 0))
})

const monnaie = computed(() => {
  return Math.max(0, (form.value.montant_paye || 0) - total.value)
})

const quantiteTotale = computed(() => {
  return panier.value.reduce((sum, ligne) => sum + (ligne.quantite || 0), 0)
})

const totalVentesJour = computed(() => {
  return mesVentes.value.reduce((sum, vente) => sum + parseFloat(vente.montant_total), 0)
})

const canSubmit = computed(() => {
  return form.value.client_id && 
         panier.value.length > 0 && 
         form.value.montant_paye >= total.value &&
         (form.value.type_vente !== 'avec_ordonnance' || (form.value.ordonnance_ref && form.value.ordonnance_ref.trim()))
})

// ==================== MÉTHODES ====================
const formatPrice = (price) => {
  if (!price && price !== 0) return '0 FCFA'
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

// Charger les ventes du caissier pour aujourd'hui
const loadMesVentes = async () => {
  loadingVentes.value = true
  try {
    const token = localStorage.getItem('token')
    const today = new Date().toISOString().split('T')[0]
    const response = await fetch(`http://127.0.0.1:8000/api/v1/ventes?date_debut=${today}&date_fin=${today}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    const data = await response.json()
    mesVentes.value = data.data || []
  } catch (error) {
    console.error('Erreur chargement ventes:', error)
  } finally {
    loadingVentes.value = false
  }
}

// Recherche médicaments avec debounce
const onSearchInput = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => searchMedicaments(), 300)
}

const searchMedicaments = async () => {
  const query = searchQuery.value.trim()
  if (!query) {
    searchResults.value = []
    searching.value = false
    return
  }
  
  searching.value = true
  try {
    const response = await medicamentService.getAll({ search: query, per_page: 20 })
    searchResults.value = response.data || []
  } catch (error) {
    console.error('Erreur recherche:', error)
    searchResults.value = []
  } finally {
    searching.value = false
  }
}

const selectMedicament = (medicament) => {
  selectedMedicament.value = medicament
  searchQuery.value = `${medicament.nom} - ${medicament.dosage}`
  searchResults.value = []
  searching.value = false
}

const selectFirstResult = () => {
  if (searchResults.value.length > 0) {
    selectMedicament(searchResults.value[0])
  }
}

const clearSearch = () => {
  searchQuery.value = ''
  searchResults.value = []
  searching.value = false
}

const clearSelectedMedicament = () => {
  selectedMedicament.value = null
  searchQuery.value = ''
  searchResults.value = []
  searching.value = false
}

const addSelectedMedicament = () => {
  if (!selectedMedicament.value || quantite.value < 1) return
  
  if (selectedMedicament.value.ordonnance_requise && form.value.type_vente === 'sans_ordonnance') {
    errorMessage.value = `⚠️ ${selectedMedicament.value.nom} nécessite une ordonnance`
    setTimeout(() => { errorMessage.value = '' }, 3000)
    return
  }
  
  if (selectedMedicament.value.stock_actuel < quantite.value) {
    errorMessage.value = `❌ Stock insuffisant pour ${selectedMedicament.value.nom}`
    setTimeout(() => { errorMessage.value = '' }, 3000)
    return
  }
  
  const existingIndex = panier.value.findIndex(l => l.medicament_id === selectedMedicament.value.id)
  
  if (existingIndex !== -1) {
    const newQuantite = panier.value[existingIndex].quantite + quantite.value
    if (selectedMedicament.value.stock_actuel >= newQuantite) {
      panier.value[existingIndex].quantite = newQuantite
      panier.value[existingIndex].sous_total = panier.value[existingIndex].prix_unitaire * newQuantite
    } else {
      errorMessage.value = `❌ Stock insuffisant pour ${selectedMedicament.value.nom}`
      setTimeout(() => { errorMessage.value = '' }, 3000)
    }
  } else {
    panier.value.push({
      medicament_id: selectedMedicament.value.id,
      medicament: selectedMedicament.value,
      quantite: quantite.value,
      prix_unitaire: selectedMedicament.value.prix_vente,
      sous_total: selectedMedicament.value.prix_vente * quantite.value
    })
  }
  
  clearSelectedMedicament()
  quantite.value = 1
  errorMessage.value = ''
}

const updateLigne = (ligne) => {
  if (ligne.medicament.stock_actuel >= ligne.quantite) {
    ligne.sous_total = ligne.prix_unitaire * ligne.quantite
  } else {
    errorMessage.value = `❌ Stock insuffisant pour ${ligne.medicament.nom}`
    ligne.quantite = ligne.medicament.stock_actuel
    ligne.sous_total = ligne.prix_unitaire * ligne.quantite
    setTimeout(() => { errorMessage.value = '' }, 3000)
  }
}

const removeLigne = (index) => {
  panier.value.splice(index, 1)
}

const onClientSearchInput = () => {
  if (clientSearchTimeout) clearTimeout(clientSearchTimeout)
  clientSearchTimeout = setTimeout(() => searchClients(), 300)
}

const searchClients = async () => {
  const query = clientSearchQuery.value.trim()
  if (!query) {
    clientSearchResults.value = []
    return
  }
  
  try {
    const response = await clientService.getAll({ search: query, per_page: 10 })
    clientSearchResults.value = response.data || []
  } catch (error) {
    console.error('Erreur recherche client:', error)
    clientSearchResults.value = []
  }
}

const selectClient = (client) => {
  form.value.client_id = client.id
  selectedClient.value = client
  clientSearchQuery.value = `${client.prenom} ${client.nom} - ${client.telephone}`
  clientSearchResults.value = []
  clientSearchFocused.value = false
}

const clearSelectedClient = () => {
  form.value.client_id = null
  selectedClient.value = null
  clientSearchQuery.value = ''
}

const closeNewClientModal = () => {
  showNewClientModal.value = false
  newClient.value = { nom: '', prenom: '', telephone: '', email: '', adresse: '' }
}

const saveNewClient = async () => {
  if (!newClient.value.nom || !newClient.value.prenom || !newClient.value.telephone) {
    alert('Veuillez remplir tous les champs obligatoires (*)')
    return
  }
  
  try {
    const response = await clientService.create(newClient.value)
    const createdClient = response.data || response
    selectClient(createdClient)
    closeNewClientModal()
    alert('✅ Client créé avec succès !')
  } catch (error) {
    console.error('Erreur création client:', error)
    alert('❌ Erreur lors de la création du client')
  }
}

const submitVente = async () => {
  if (!canSubmit.value) return
  
  loading.value = true
  errorMessage.value = ''
  
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
    await loadMesVentes()
    router.push('/ventes')
  } catch (error) {
    console.error('Erreur vente:', error)
    errorMessage.value = error.response?.data?.message || 'Erreur lors de l\'enregistrement de la vente'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadMesVentes()
})

onUnmounted(() => {
  if (searchTimeout) clearTimeout(searchTimeout)
  if (clientSearchTimeout) clearTimeout(clientSearchTimeout)
})
</script>