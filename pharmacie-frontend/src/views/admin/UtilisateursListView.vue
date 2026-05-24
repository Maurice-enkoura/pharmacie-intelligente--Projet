<!-- src/views/admin/UtilisateursListView.vue -->
<template>
  <div class="utilisateurs-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Utilisateurs</h1>
      <button @click="showCreateModal = true" class="btn-primary">
        + Nouvel utilisateur
      </button>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      <p class="mt-2 text-gray-500">Chargement...</p>
    </div>
    
    <div v-else class="card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">Nom</th>
            <th class="px-4 py-3 text-left">Email</th>
            <th class="px-4 py-3 text-center">Rôle</th>
            <th class="px-4 py-3 text-center">Date création</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in utilisateurs" :key="user.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-3 font-medium">{{ user.name }}</td>
            <td class="px-4 py-3">{{ user.email }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="getRoleClass(user.role)" class="px-2 py-1 rounded-full text-xs">
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td class="px-4 py-3 text-center text-sm">{{ formatDate(user.created_at) }}</td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center space-x-2">
                <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800" title="Modifier">
                  ✏️
                </button>
                <button 
                  v-if="user.id !== currentUserId"
                  @click="confirmDelete(user)" 
                  class="text-red-600 hover:text-red-800" 
                  title="Supprimer"
                >
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Modal création/modification -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">
          {{ showEditModal ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Nom *</label>
            <input type="text" v-model="userForm.name" class="input-field w-full" required>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email *</label>
            <input type="email" v-model="userForm.email" class="input-field w-full" required>
          </div>
          <div v-if="!showEditModal">
            <label class="block text-sm text-gray-600 mb-1">Mot de passe *</label>
            <input type="password" v-model="userForm.password" class="input-field w-full" required>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Rôle *</label>
            <select v-model="userForm.role" class="input-field w-full">
              <option value="caissier">👤 Caissier</option>
              <option value="pharmacien">💊 Pharmacien</option>
              <option value="admin">👑 Administrateur</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="closeModals" class="btn-secondary">Annuler</button>
          <button @click="saveUser" :disabled="saving" class="btn-primary">
            {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </div>
    </div>
    
    <!-- Modal confirmation suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Confirmer la suppression</h3>
        <p>Voulez-vous vraiment supprimer l'utilisateur <strong>{{ userToDelete?.name }}</strong> ?</p>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showDeleteModal = false" class="btn-secondary">Annuler</button>
          <button @click="deleteUser" class="btn-danger">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const authStore = useAuthStore()
const currentUserId = ref(authStore.user?.id)

const utilisateurs = ref([])
const loading = ref(false)
const saving = ref(false)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const userToDelete = ref(null)
const userToEdit = ref(null)
const errorMessage = ref('')

const userForm = ref({
  name: '',
  email: '',
  password: '',
  role: 'caissier'
})

const getRoleLabel = (role) => {
  const labels = {
    admin: '👑 Administrateur',
    pharmacien: '💊 Pharmacien',
    caissier: '👤 Caissier'
  }
  return labels[role] || role
}

const getRoleClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-700',
    pharmacien: 'bg-blue-100 text-blue-700',
    caissier: 'bg-gray-100 text-gray-700'
  }
  return classes[role] || 'bg-gray-100'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}

const loadUtilisateurs = async () => {
  loading.value = true
  try {
    const response = await api.get('/utilisateurs')
    utilisateurs.value = response.data
  } catch (error) {
    console.error('Erreur chargement utilisateurs:', error)
    if (error.response?.status === 403) {
      alert('Vous n\'avez pas les droits pour voir les utilisateurs')
    }
  } finally {
    loading.value = false
  }
}

const editUser = (user) => {
  userToEdit.value = user
  userForm.value = {
    name: user.name,
    email: user.email,
    password: '',
    role: user.role
  }
  showEditModal.value = true
}

const saveUser = async () => {
  saving.value = true
  errorMessage.value = ''
  
  try {
    if (showEditModal.value) {
      // Mise à jour d'un utilisateur
      const response = await api.put(`/utilisateurs/${userToEdit.value.id}`, {
        name: userForm.value.name,
        email: userForm.value.email,
        role: userForm.value.role,
        password: userForm.value.password || undefined
      })
      
      if (response.data.success === false) {
        throw new Error(response.data.message)
      }
      
      alert('✅ Utilisateur modifié avec succès')
      
    } else {
      // Création d'un nouvel utilisateur
      const response = await api.post('/utilisateurs', {
        name: userForm.value.name,
        email: userForm.value.email,
        password: userForm.value.password,
        role: userForm.value.role
      })
      
      if (response.data.success === false) {
        throw new Error(response.data.message)
      }
      
      alert('✅ Utilisateur créé avec succès')
    }
    
    closeModals()
    loadUtilisateurs()
    
  } catch (error) {
    console.error('Erreur:', error)
    const message = error.response?.data?.message || error.message || 'Erreur lors de l\'enregistrement'
    alert('❌ ' + message)
  } finally {
    saving.value = false
  }
}

const confirmDelete = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const deleteUser = async () => {
  if (!userToDelete.value) return
  
  try {
    const response = await api.delete(`/utilisateurs/${userToDelete.value.id}`)
    
    if (response.data.success === false) {
      throw new Error(response.data.message)
    }
    
    alert('✅ Utilisateur supprimé avec succès')
    showDeleteModal.value = false
    userToDelete.value = null
    loadUtilisateurs()
    
  } catch (error) {
    console.error('Erreur:', error)
    const message = error.response?.data?.message || 'Erreur lors de la suppression'
    alert('❌ ' + message)
  }
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  userForm.value = { name: '', email: '', password: '', role: 'caissier' }
  userToEdit.value = null
  errorMessage.value = ''
}

onMounted(() => {
  loadUtilisateurs()
})
</script>