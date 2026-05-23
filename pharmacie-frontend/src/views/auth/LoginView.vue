<!-- src/views/auth/LoginView.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-primary-600 to-primary-800">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-4xl">💊</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Pharmacie Intelligente</h1>
        <p class="text-gray-500 mt-2">Connectez-vous à votre compte</p>
      </div>
      
      <!-- Form -->
      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
          <input 
            type="email" 
            v-model="form.email"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
            placeholder="exemple@email.com"
            required
          >
        </div>
        
        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-medium mb-2">Mot de passe</label>
          <input 
            :type="showPassword ? 'text' : 'password'"
            v-model="form.password"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
            placeholder="••••••••"
            required
          >
          <button type="button" @click="showPassword = !showPassword" class="text-sm text-primary-600 mt-1">
            {{ showPassword ? 'Masquer' : 'Afficher' }}
          </button>
        </div>
        
        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50"
        >
          <span v-if="loading">Connexion...</span>
          <span v-else>Se connecter</span>
        </button>
      </form>
      
      <!-- Error Message -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
        {{ error }}
      </div>
      
      <!-- Footer -->
      <div class="mt-6 text-center text-sm text-gray-500">
        Comptes de démo :<br>
        admin@pharmacie.com / password<br>
        pharmacien@pharmacie.com / password<br>
        caissier@pharmacie.com / password
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})
const loading = ref(false)
const error = ref('')
const showPassword = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    await authStore.login(form.value.email, form.value.password)
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Email ou mot de passe incorrect'
  } finally {
    loading.value = false
  }
}
</script>