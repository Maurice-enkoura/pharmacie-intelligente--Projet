<!-- src/views/pharmacien/MedicamentsFormView.vue -->
<template>
  <div class="medicament-form">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">
        {{ isEdit ? 'Modifier le médicament' : 'Nouveau médicament' }}
      </h1>
      <router-link to="/medicaments" class="btn-secondary">Retour</router-link>
    </div>
    
    <div class="card max-w-2xl">
      <form @submit.prevent="submitForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Nom *</label>
            <input type="text" v-model="form.nom" class="input-field w-full" required>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">DCI *</label>
            <input type="text" v-model="form.dci" class="input-field w-full" required>
          </div>
          
          <div>
  <label class="block text-gray-700 text-sm font-medium mb-2">Forme *</label>
  <select v-model="form.forme" class="input-field w-full" required>
    <option value="">Sélectionner une forme</option>
    <optgroup label="💊 Formes orales solides">
      <option v-for="forme in formesOraleSolide" :key="forme" :value="forme">{{ forme }}</option>
    </optgroup>
    <optgroup label="🧪 Formes orales liquides">
      <option v-for="forme in formesOraleLiquide" :key="forme" :value="forme">{{ forme }}</option>
    </optgroup>
    <optgroup label="💉 Formes injectables">
      <option v-for="forme in formesInjectable" :key="forme" :value="forme">{{ forme }}</option>
    </optgroup>
    <optgroup label="🧴 Formes topiques">
      <option v-for="forme in formesTopique" :key="forme" :value="forme">{{ forme }}</option>
    </optgroup>
    <optgroup label="👁️ Formes ophtalmiques/ORL">
      <option v-for="forme in formesOphtalmique" :key="forme" :value="forme">{{ forme }}</option>
    </optgroup>
    <optgroup label="💨 Formes inhalées">
      <option v-for="forme in formesInhalation" :key="forme" :value="forme">{{ forme }}</option>
    </optgroup>
  </select>
</div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Dosage *</label>
            <input type="text" v-model="form.dosage" class="input-field w-full" required>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Catégorie *</label>
            <select v-model="form.categorie_id" class="input-field w-full" required>
              <option :value="null">Sélectionner</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.nom }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Prix d'achat (FCFA) *</label>
            <input type="number" v-model="form.prix_achat" class="input-field w-full" required min="0">
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Prix de vente (FCFA) *</label>
            <input type="number" v-model="form.prix_vente" class="input-field w-full" required min="0">
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Seuil d'alerte</label>
            <input type="number" v-model="form.seuil_alerte" class="input-field w-full" min="0">
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Stock initial</label>
            <input type="number" v-model="form.stock_actuel" class="input-field w-full" min="0">
          </div>
          
          <div class="col-span-2">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.ordonnance_requise" class="w-4 h-4">
              <span class="text-gray-700">Nécessite une ordonnance</span>
            </label>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
          <router-link to="/medicaments" class="btn-secondary">Annuler</router-link>
          <button type="submit" :disabled="loading" class="btn-primary">
            {{ loading ? 'Enregistrement...' : (isEdit ? 'Modifier' : 'Créer') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()

const isEdit = ref(false)
const loading = ref(false)
const categories = ref([])
const errorMessage = ref('')

const form = ref({
  nom: '',
  dci: '',
  forme: '',
  dosage: '',
  categorie_id: null,
  prix_achat: 0,
  prix_vente: 0,
  seuil_alerte: 10,
  stock_actuel: 0,
  ordonnance_requise: false
})

const loadCategories = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://127.0.0.1:8000/api/v1/categories', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    const data = await response.json()
    categories.value = data.data || []
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

const loadMedicament = async (id) => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/v1/medicaments/${id}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    const med = await response.json()
    
    form.value = {
      nom: med.nom,
      dci: med.dci,
      forme: med.forme,
      dosage: med.dosage,
      categorie_id: med.categorie_id,
      prix_achat: parseFloat(med.prix_achat),
      prix_vente: parseFloat(med.prix_vente),
      seuil_alerte: med.seuil_alerte,
      stock_actuel: med.stock_actuel,
      ordonnance_requise: med.ordonnance_requise === 1 || med.ordonnance_requise === true
    }
  } catch (error) {
    console.error('Erreur chargement médicament:', error)
  }
}

const submitForm = async () => {
  loading.value = true
  errorMessage.value = ''
  
  try {
    const token = localStorage.getItem('token')
    const url = isEdit.value 
      ? `http://127.0.0.1:8000/api/v1/medicaments/${route.params.id}`
      : 'http://127.0.0.1:8000/api/v1/medicaments'
    
    const method = isEdit.value ? 'PUT' : 'POST'
    
    const response = await fetch(url, {
      method: method,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(form.value)
    })
    
    const data = await response.json()
    
    if (!response.ok) {
      if (data.errors) {
        const errors = Object.values(data.errors).flat().join(', ')
        throw new Error(errors)
      }
      throw new Error(data.message || 'Erreur lors de l\'enregistrement')
    }
    
    router.push('/medicaments')
  } catch (error) {
    console.error('Erreur:', error)
    errorMessage.value = error.message
    alert(errorMessage.value)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true
    loadMedicament(route.params.id)
  }
  loadCategories()
})



const formesOraleSolide = ['Comprimé', 'Comprimé effervescent', 'Comprimé à croquer', 'Gélule', 'Capsule', 'Gélule à libération prolongée', 'Poudre', 'Granulé', 'Sachet']
const formesOraleLiquide = ['Sirop', 'Solution buvable', 'Suspension buvable', 'Gouttes buvables', 'Ampoule buvable']
const formesInjectable = ['Injectable (ampoule)', 'Injectable (flacon)', 'Poudre pour injection', 'Solution pour perfusion']
const formesTopique = ['Pommade', 'Crème', 'Gel', 'Onguent', 'Patch transdermique', 'Savon', 'Shampoing', 'Dentifrice']
const formesOphtalmique = ['Collyre (gouttes oculaires)', 'Solution ophtalmique', 'Pommade ophtalmique', 'Spray nasal', 'Gouttes nasales']
const formesInhalation = ['Inhalateur', 'Aérosol', 'Nébuliseur', 'Poudre pour inhalation']
</script>