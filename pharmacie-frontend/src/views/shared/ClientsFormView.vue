<!-- src/views/shared/ClientsFormView.vue -->
<template>
  <div class="client-form">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">
        {{ isEdit ? 'Modifier le client' : 'Nouveau client' }}
      </h1>
      <router-link to="/clients" class="btn-secondary">Retour</router-link>
    </div>
    
    <div class="card max-w-2xl">
      <form @submit.prevent="submitForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Nom *</label>
            <input 
              type="text" 
              v-model="form.nom" 
              class="input-field w-full" 
              required
              placeholder="Diop"
            >
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Prénom *</label>
            <input 
              type="text" 
              v-model="form.prenom" 
              class="input-field w-full" 
              required
              placeholder="Aminata"
            >
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Téléphone *</label>
            <input 
              type="tel" 
              v-model="form.telephone" 
              class="input-field w-full" 
              required
              placeholder="77 123 45 67"
            >
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
            <input 
              type="email" 
              v-model="form.email" 
              class="input-field w-full"
              placeholder="client@email.com"
            >
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-gray-700 text-sm font-medium mb-2">Adresse</label>
            <textarea 
              v-model="form.adresse" 
              class="input-field w-full" 
              rows="2"
              placeholder="Dakar, Sicap Liberté"
            ></textarea>
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-gray-700 text-sm font-medium mb-2">Médicaments chroniques</label>
            <div class="space-y-2">
              <div class="flex flex-wrap gap-2 mb-2">
                <span 
                  v-for="(med, index) in medicamentsChroniques" 
                  :key="index" 
                  class="bg-gray-100 px-3 py-1 rounded-full text-sm flex items-center"
                >
                  {{ med }}
                  <button type="button" @click="removeMedicament(index)" class="ml-2 text-red-500 hover:text-red-700">
                    ✕
                  </button>
                </span>
              </div>
              <div class="flex gap-2">
                <input 
                  type="text" 
                  v-model="nouveauMedicament" 
                  class="input-field flex-1"
                  placeholder="Ajouter un médicament chronique"
                  @keyup.enter="addMedicament"
                >
                <button type="button" @click="addMedicament" class="btn-secondary">
                  Ajouter
                </button>
              </div>
              <p class="text-xs text-gray-500">
                Ex: Amlodipine 5mg, Metformine 500mg, Salbutamol inhalateur...
              </p>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
          <router-link to="/clients" class="btn-secondary">Annuler</router-link>
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
import { clientService } from '@/services/client'

const route = useRoute()
const router = useRouter()

const isEdit = ref(false)
const loading = ref(false)
const medicamentsChroniques = ref([])
const nouveauMedicament = ref('')

const form = ref({
  nom: '',
  prenom: '',
  telephone: '',
  email: '',
  adresse: '',
  medicaments_chroniques: null
})

const addMedicament = () => {
  if (nouveauMedicament.value.trim()) {
    medicamentsChroniques.value.push(nouveauMedicament.value.trim())
    nouveauMedicament.value = ''
    updateMedicamentsChroniques()
  }
}

const removeMedicament = (index) => {
  medicamentsChroniques.value.splice(index, 1)
  updateMedicamentsChroniques()
}

const updateMedicamentsChroniques = () => {
  if (medicamentsChroniques.value.length > 0) {
    form.value.medicaments_chroniques = JSON.stringify(medicamentsChroniques.value)
  } else {
    form.value.medicaments_chroniques = null
  }
}

const loadClient = async (id) => {
  try {
    const client = await clientService.getById(id)
    form.value = {
      nom: client.nom,
      prenom: client.prenom,
      telephone: client.telephone,
      email: client.email || '',
      adresse: client.adresse || '',
      medicaments_chroniques: client.medicaments_chroniques
    }
    
    // Charger les médicaments chroniques
    if (client.medicaments_chroniques) {
      try {
        medicamentsChroniques.value = JSON.parse(client.medicaments_chroniques)
      } catch {
        medicamentsChroniques.value = []
      }
    }
  } catch (error) {
    console.error('Erreur chargement client:', error)
  }
}

const submitForm = async () => {
  loading.value = true
  try {
    if (isEdit.value) {
      await clientService.update(route.params.id, form.value)
    } else {
      await clientService.create(form.value)
    }
    router.push('/clients')
  } catch (error) {
    console.error('Erreur:', error)
    let message = 'Erreur lors de l\'enregistrement'
    if (error.response?.data?.errors) {
      message = Object.values(error.response.data.errors).flat().join(', ')
    } else if (error.response?.data?.message) {
      message = error.response.data.message
    }
    alert(message)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true
    loadClient(route.params.id)
  }
})
</script>