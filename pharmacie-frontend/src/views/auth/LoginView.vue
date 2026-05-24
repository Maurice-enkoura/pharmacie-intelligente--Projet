<!-- src/views/auth/LoginView.vue -->
<template>
  <div class="min-h-screen flex">
    
    <!-- ==================== PARTIE GAUCHE - IMAGE ==================== -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
      <!-- Image de fond pharmacie -->
      <div 
        class="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80')"
      >
        <!-- Overlay sombre pour rendre le texte lisible -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/90 to-primary-800/80"></div>
      </div>
      
      <!-- Contenu overlay sur l'image -->
      <div class="relative z-10 flex flex-col justify-between h-full p-10 text-white">
        <div>
          <div class="flex items-center gap-3 mb-8">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
              <span class="text-2xl">💊</span>
            </div>
            <div>
              <h2 class="font-bold text-xl">PharmaCare</h2>
              <p class="text-white/70 text-xs">Système de gestion intelligent</p>
            </div>
          </div>
        </div>
        
        <div class="text-center">
          <div class="mb-6">
            <div class="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center mx-auto backdrop-blur-sm">
              <span class="text-5xl">🏥</span>
            </div>
          </div>
          <h1 class="text-3xl font-bold mb-2">Gestion de Pharmacie</h1>
          <p class="text-white/80 text-sm max-w-sm mx-auto">
            Solution complète pour la gestion de votre officine : ventes, stocks, ordonnances et reporting
          </p>
        </div>
        
        <div class="text-center text-white/50 text-xs">
          <p>© 2025 PharmaCare - Tous droits réservés</p>
        </div>
      </div>
    </div>
    
    <!-- ==================== PARTIE DROITE - FORMULAIRE ==================== -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-gray-50">
      <div class="w-full max-w-md">
        <!-- Logo mobile (visible uniquement sur petits écrans) -->
        <div class="text-center mb-8 lg:hidden">
          <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">💊</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-800">Pharmacie Intelligente</h1>
          <p class="text-gray-500 mt-1">Connectez-vous à votre compte</p>
        </div>
        
        <!-- Header formulaire -->
        <div class="mb-8 text-center lg:text-left">
          <h2 class="text-2xl font-bold text-gray-800">Bienvenue</h2>
          <p class="text-gray-500 text-sm mt-1">Connectez-vous pour accéder à votre espace de travail</p>
        </div>
        
        <!-- Formulaire -->
        <form @submit.prevent="handleLogin" class="space-y-5">
          <!-- Champ Email -->
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              Adresse email
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </span>
              <input 
                type="email" 
                v-model="form.email"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                placeholder="exemple@pharmacie.com"
                required
              >
            </div>
          </div>
          
          <!-- Champ Mot de passe -->
          <div>
            <div class="flex justify-between items-center mb-2">
              <label class="text-gray-700 text-sm font-semibold">
                Mot de passe
              </label>
              <a href="#" class="text-xs text-primary-600 hover:underline">Mot de passe oublié ?</a>
            </div>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </span>
              <input 
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password"
                class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                placeholder="••••••••"
                required
              >
              <button 
                type="button" 
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Option se souvenir de moi -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
              <span class="text-sm text-gray-600">Se souvenir de moi</span>
            </label>
          </div>
          
          <!-- Bouton de connexion -->
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="flex items-center justify-center gap-2">
              <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
              Connexion en cours...
            </span>
            <span v-else class="flex items-center justify-center gap-2">
              Se connecter
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </span>
          </button>
        </form>
        
        <!-- Message d'erreur -->
        <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm flex items-start gap-2">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ error }}</span>
        </div>
        
        <!-- Comptes de démonstration -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <p class="text-center text-xs text-gray-500 mb-3">Comptes de démonstration</p>
          <div class="grid grid-cols-3 gap-2">
            <button 
              type="button"
              @click="fillCredentials('admin@pharmacie.com', 'password')"
              class="p-2 bg-gray-100 rounded-lg text-center hover:bg-gray-200 transition-colors group"
            >
              <div class="font-semibold text-primary-600 text-sm">👑 Admin</div>
              <div class="text-gray-500 text-xs truncate">admin@pharmacie.com</div>
            </button>
            <button 
              type="button"
              @click="fillCredentials('pharmacien@pharmacie.com', 'password')"
              class="p-2 bg-gray-100 rounded-lg text-center hover:bg-gray-200 transition-colors group"
            >
              <div class="font-semibold text-blue-600 text-sm">💊 Pharmacien</div>
              <div class="text-gray-500 text-xs truncate">pharmacien@pharmacie.com</div>
            </button>
            <button 
              type="button"
              @click="fillCredentials('caissier@pharmacie.com', 'password')"
              class="p-2 bg-gray-100 rounded-lg text-center hover:bg-gray-200 transition-colors group"
            >
              <div class="font-semibold text-green-600 text-sm">🏪 Caissier</div>
              <div class="text-gray-500 text-xs truncate">caissier@pharmacie.com</div>
            </button>
          </div>
          <p class="text-center text-xs text-gray-400 mt-3">
            Mot de passe : <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">password</span>
          </p>
        </div>
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

const fillCredentials = (email, password) => {
  form.value.email = email
  form.value.password = password
}
</script>