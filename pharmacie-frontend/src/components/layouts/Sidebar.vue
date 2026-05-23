<template>
  <aside class="w-64 bg-primary-800 text-white flex flex-col">
    <!-- Logo -->
    <div class="p-6 border-b border-primary-700">
      <h1 class="text-2xl font-bold">Pharmacie</h1>
      <p class="text-sm text-primary-300">Intelligente</p>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 py-4">
      <router-link 
        v-if="isAdmin || isPharmacien"
        to="/" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path === '/' }"
      >
        <span class="mr-3">📊</span>
        Dashboard
      </router-link>
      
      <router-link 
        v-if="isAdmin || isPharmacien"
        to="/medicaments" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/medicaments') }"
      >
        <span class="mr-3">💊</span>
        Médicaments
      </router-link>
      
      <router-link 
        v-if="isAdmin || isPharmacien"
        to="/stock/alertes" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/stock') }"
      >
        <span class="mr-3">📦</span>
        Stock & Alertes
      </router-link>
      
      <div class="my-2 border-t border-primary-700"></div>
      
      <router-link 
        to="/ventes" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/ventes') }"
      >
        <span class="mr-3">💰</span>
        Ventes
      </router-link>
      
      <router-link 
        to="/ventes/create" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
      >
        <span class="mr-3">➕</span>
        Nouvelle vente
      </router-link>
      
      <div class="my-2 border-t border-primary-700"></div>
      
      <router-link 
        to="/clients" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/clients') }"
      >
        <span class="mr-3">👥</span>
        Clients
      </router-link>
      
      <router-link 
        v-if="isAdmin || isPharmacien"
        to="/fournisseurs" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/fournisseurs') }"
      >
        <span class="mr-3">🏭</span>
        Fournisseurs
      </router-link>
      
      <router-link 
        v-if="isAdmin || isPharmacien"
        to="/commandes" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/commandes') }"
      >
        <span class="mr-3">📋</span>
        Commandes
      </router-link>
      
      <router-link 
        v-if="isAdmin || isPharmacien"
        to="/rapports" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/rapports') }"
      >
        <span class="mr-3">📈</span>
        Rapports
      </router-link>
      
      <router-link 
        v-if="isAdmin"
        to="/utilisateurs" 
        class="flex items-center px-6 py-3 hover:bg-primary-700 transition-colors"
        :class="{ 'bg-primary-700': $route.path.startsWith('/utilisateurs') }"
      >
        <span class="mr-3">👤</span>
        Utilisateurs
      </router-link>
    </nav>
    
    <!-- Footer -->
    <div class="p-4 border-t border-primary-700 text-center text-sm text-primary-300">
      v1.0.0
    </div>
  </aside>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const authStore = useAuthStore()
const { isAdmin, isPharmacien } = storeToRefs(authStore)
</script>