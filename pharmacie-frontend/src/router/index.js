// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import MainLayout from '@/layouts/MainLayout.vue'

// Auth Views
import LoginView from '@/views/auth/LoginView.vue'

// Shared Views
import DashboardView from '@/views/shared/DashboardView.vue'
import VentesListView from '@/views/shared/VentesListView.vue'
import VentesFormView from '@/views/shared/VentesFormView.vue'
import VentesDetailView from '@/views/shared/VentesDetailView.vue'
import ClientsListView from '@/views/shared/ClientsListView.vue'
import ClientsFormView from '@/views/shared/ClientsFormView.vue'
import ClientsDetailView from '@/views/shared/ClientsDetailView.vue'

// Pharmacien/Admin Views
import MedicamentsListView from '@/views/pharmacien/MedicamentsListView.vue'
import MedicamentsFormView from '@/views/pharmacien/MedicamentsFormView.vue'
import StockAlertesView from '@/views/pharmacien/StockAlertesView.vue'
import StockEntreesView from '@/views/pharmacien/StockEntreesView.vue'
import StockHistoriqueView from '@/views/pharmacien/StockHistoriqueView.vue'
import FournisseursListView from '@/views/pharmacien/FournisseursListView.vue'
import FournisseursFormView from '@/views/pharmacien/FournisseursFormView.vue'
import CommandesListView from '@/views/pharmacien/CommandesListView.vue'
import CommandesFormView from '@/views/pharmacien/CommandesFormView.vue'
import CommandesReceptionView from '@/views/pharmacien/CommandesReceptionView.vue'
import RapportsView from '@/views/pharmacien/RapportsView.vue'

// Admin Views
import UtilisateursListView from '@/views/admin/UtilisateursListView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth
    { path: '/login', name: 'Login', component: LoginView, meta: { guest: true } },
    
    // Layout principal
    {
      path: '/',
      component: MainLayout,
      meta: { requiresAuth: true },
      children: [
        // Dashboard (admin + pharmacien)
        { path: '', name: 'Dashboard', component: DashboardView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Médicaments
        { path: 'medicaments', name: 'MedicamentsList', component: MedicamentsListView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'medicaments/create', name: 'MedicamentsCreate', component: MedicamentsFormView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'medicaments/:id/edit', name: 'MedicamentsEdit', component: MedicamentsFormView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Stock
        { path: 'stock/alertes', name: 'StockAlertes', component: StockAlertesView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'stock/entrees', name: 'StockEntrees', component: StockEntreesView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'stock/historique', name: 'StockHistorique', component: StockHistoriqueView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Ventes (tous)
        { path: 'ventes', name: 'VentesList', component: VentesListView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        { path: 'ventes/create', name: 'VentesCreate', component: VentesFormView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        { path: 'ventes/:id', name: 'VentesDetail', component: VentesDetailView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        
        // Clients (tous)
        { path: 'clients', name: 'ClientsList', component: ClientsListView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        { path: 'clients/create', name: 'ClientsCreate', component: ClientsFormView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'clients/:id', name: 'ClientsDetail', component: ClientsDetailView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        
        // Fournisseurs
        { path: 'fournisseurs', name: 'FournisseursList', component: FournisseursListView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'fournisseurs/create', name: 'FournisseursCreate', component: FournisseursFormView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Commandes
        { path: 'commandes', name: 'CommandesList', component: CommandesListView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'commandes/create', name: 'CommandesCreate', component: CommandesFormView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'commandes/:id/reception', name: 'CommandesReception', component: CommandesReceptionView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Rapports
        { path: 'rapports', name: 'Rapports', component: RapportsView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Admin seulement
        { path: 'utilisateurs', name: 'UtilisateursList', component: UtilisateursListView, meta: { roles: ['admin'] } },
      ]
    },
    
    // 404
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('@/views/NotFoundView.vue') }
  ]
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated
  const userRole = authStore.userRole
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } 
  else if (to.meta.roles && !to.meta.roles.includes(userRole)) {
    next('/')
  }
  else if (to.meta.guest && isAuthenticated) {
    next('/')
  }
  else {
    next()
  }
})

export default router