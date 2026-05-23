<!-- src/App.vue -->
<template>
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  // Vérifier si l'utilisateur est déjà connecté
  if (authStore.token && !authStore.user) {
    // Recharger l'utilisateur si nécessaire
  }
})
</script>