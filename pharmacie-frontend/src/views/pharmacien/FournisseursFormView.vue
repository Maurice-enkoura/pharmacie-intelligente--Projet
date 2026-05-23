<!-- src/views/pharmacien/FournisseursFormView.vue -->
<template>
  <div class="fournisseur-form">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">
        {{ isEdit ? 'Modifier le fournisseur' : 'Nouveau fournisseur' }}
      </h1>
      <router-link to="/fournisseurs" class="btn-secondary">Retour</router-link>
    </div>
    
    <div class="card max-w-2xl">
      <form @submit.prevent="submitForm">
        <div class="space-y-4">
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Nom *</label>
            <input type="text" v-model="form.nom" class="input-field w-full" required>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Personne de contact *</label>
            <input type="text" v-model="form.contact" class="input-field w-full" required>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Téléphone *</label>
            <input type="tel" v-model="form.telephone" class="input-field w-full" required>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Email *</label>
            <input type="email" v-model="form.email" class="input-field w-full" required>
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Adresse *</label>
            <textarea v-model="form.adresse" class="input-field w-full" rows="2" required></textarea>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
          <router-link to="/fournisseurs" class="btn-secondary">Annuler</router-link>
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
import { fournisseurService } from '@/services/fournisseur'

const route = useRoute()
const router = useRouter()

const isEdit = ref(false)
const loading = ref(false)

const form = ref({
  nom: '',
  contact: '',
  telephone: '',
  email: '',
  adresse: ''
})

const loadFournisseur = async (id) => {
  try {
    const fournisseur = await fournisseurService.getById(id)
    form.value = {
      nom: fournisseur.nom,
      contact: fournisseur.contact,
      telephone: fournisseur.telephone,
      email: fournisseur.email,
      adresse: fournisseur.adresse
    }
  } catch (error) {
    console.error('Erreur chargement fournisseur:', error)
  }
}

const submitForm = async () => {
  loading.value = true
  try {
    if (isEdit.value) {
      await fournisseurService.update(route.params.id, form.value)
    } else {
      await fournisseurService.create(form.value)
    }
    router.push('/fournisseurs')
  } catch (error) {
    alert('Erreur lors de l\'enregistrement')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true
    loadFournisseur(route.params.id)
  }
})
</script>