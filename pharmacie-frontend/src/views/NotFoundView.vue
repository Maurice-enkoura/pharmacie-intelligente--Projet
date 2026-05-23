<!-- src/views/NotFoundView.vue -->
<template>
  <div class="not-found flex flex-col items-center justify-center min-h-[60vh]">
    <div class="text-center">
      <h1 class="text-9xl font-bold text-gray-200">404</h1>
      <div class="text-6xl my-4">🔍</div>
      <h2 class="text-2xl font-semibold text-gray-700 mb-2">Page non trouvée</h2>
      <p class="text-gray-500 mb-6">Désolé, la page que vous cherchez n'existe pas.</p>
      <router-link to="/" class="btn-primary">
        Retour à l'accueil
      </router-link>
    </div>
  </div>
</template>