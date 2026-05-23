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
              <option value="">Sélectionner</option>
              <option value="Comprimé">Comprimé</option>
              <option value="Gélule">Gélule</option>
              <option value="Sirop">Sirop</option>
              <option value="Injectable">Injectable</option>
              <option value="Pommade">Pommade</option>
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
import { useMedicamentStore } from '@/stores/medicament'

const route = useRoute()
const router = useRouter()
const medicamentStore = useMedicamentStore()

const isEdit = ref(false)
const loading = ref(false)
const categories = ref([])

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
    const response = await fetch('http://127.0.0.1:8000/api/v1/categories', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
    const data = await response.json()
    categories.value = data.data || []
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

const loadMedicament = async (id) => {
  await medicamentStore.fetchById(id)
  const med = medicamentStore.currentMedicament
  if (med) {
    form.value = {
      nom: med.nom,
      dci: med.dci,
      forme: med.forme,
      dosage: med.dosage,
      categorie_id: med.categorie_id,
      prix_achat: med.prix_achat,
      prix_vente: med.prix_vente,
      seuil_alerte: med.seuil_alerte,
      stock_actuel: med.stock_actuel,
      ordonnance_requise: med.ordonnance_requise
    }
  }
}

const submitForm = async () => {
  loading.value = true
  try {
    if (isEdit.value) {
      await medicamentStore.update(route.params.id, form.value)
    } else {
      await medicamentStore.create(form.value)
    }
    router.push('/medicaments')
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de l\'enregistrement')
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
</script>