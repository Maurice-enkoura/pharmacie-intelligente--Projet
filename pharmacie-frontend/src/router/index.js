// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import MainLayout from '@/layouts/MainLayout.vue'

// Auth Views
import LoginView from '@/views/auth/LoginView.vue'

// Shared Views (tous rôles)
import VentesListView from '@/views/shared/VentesListView.vue'
import VentesFormView from '@/views/shared/VentesFormView.vue'
import VentesDetailView from '@/views/shared/VentesDetailView.vue'
import ClientsListView from '@/views/shared/ClientsListView.vue'
import ClientsDetailView from '@/views/shared/ClientsDetailView.vue'
import ClientsFormView from '@/views/shared/ClientsFormView.vue'

// Admin/Pharmacien Views
import DashboardView from '@/views/pharmacien/DashboardView.vue'
import MedicamentsListView from '@/views/pharmacien/MedicamentsListView.vue'
import MedicamentsFormView from '@/views/pharmacien/MedicamentsFormView.vue'
import StockAlertesView from '@/views/pharmacien/StockAlertesView.vue'
import StockEntreesView from '@/views/pharmacien/StockEntreesView.vue'
import StockHistoriqueView from '@/views/pharmacien/StockHistoriqueView.vue'

// Admin Only Views
import FournisseursListView from '@/views/pharmacien/FournisseursListView.vue'
import FournisseursFormView from '@/views/pharmacien/FournisseursFormView.vue'
import CommandesListView from '@/views/pharmacien/CommandesListView.vue'
import CommandesFormView from '@/views/pharmacien/CommandesFormView.vue'
import CommandesReceptionView from '@/views/pharmacien/CommandesReceptionView.vue'
import RapportsView from '@/views/pharmacien/RapportsView.vue'
import UtilisateursListView from '@/views/admin/UtilisateursListView.vue'

// Caissier Views
import CaissierVentesListView from '@/views/caissier/VentesListView.vue'
import CaissierVentesFormView from '@/views/caissier/VentesFormView.vue'
import CaissierClientsListView from '@/views/caissier/ClientsListView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/login', name: 'Login', component: LoginView, meta: { guest: true } },
    
    {
      path: '/',
      component: MainLayout,
      meta: { requiresAuth: true },
      children: [
        // Dashboard (admin seulement)
        { path: '', name: 'Dashboard', component: DashboardView, meta: { roles: ['admin'] } },
        
        // ========== ADMIN + PHARMACIEN ==========
        // Médicaments
        { path: 'medicaments', name: 'MedicamentsList', component: MedicamentsListView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'medicaments/create', name: 'MedicamentsCreate', component: MedicamentsFormView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'medicaments/:id/edit', name: 'MedicamentsEdit', component: MedicamentsFormView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Stock
        { path: 'stock/alertes', name: 'StockAlertes', component: StockAlertesView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'stock/entrees', name: 'StockEntrees', component: StockEntreesView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'stock/historique', name: 'StockHistorique', component: StockHistoriqueView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // Ventes (tous rôles)
        { path: 'ventes', name: 'VentesList', component: VentesListView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        { path: 'ventes/create', name: 'VentesCreate', component: VentesFormView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        { path: 'ventes/:id', name: 'VentesDetail', component: VentesDetailView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        
        // Clients (consultation tous, modification admin/pharmacien)
        { path: 'clients', name: 'ClientsList', component: ClientsListView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        { path: 'clients/create', name: 'ClientsCreate', component: ClientsFormView, meta: { roles: ['admin', 'pharmacien'] } },
        { path: 'clients/:id', name: 'ClientsDetail', component: ClientsDetailView, meta: { roles: ['admin', 'pharmacien', 'caissier'] } },
        { path: 'clients/:id/edit', name: 'ClientsEdit', component: ClientsFormView, meta: { roles: ['admin', 'pharmacien'] } },
        
        // ========== ADMIN SEULEMENT ==========
        // Fournisseurs
        { path: 'fournisseurs', name: 'FournisseursList', component: FournisseursListView, meta: { roles: ['admin'] } },
        { path: 'fournisseurs/create', name: 'FournisseursCreate', component: FournisseursFormView, meta: { roles: ['admin'] } },
        { path: 'fournisseurs/:id/edit', name: 'FournisseursEdit', component: FournisseursFormView, meta: { roles: ['admin'] } },
        
        // Commandes
        { path: 'commandes', name: 'CommandesList', component: CommandesListView, meta: { roles: ['admin'] } },
        { path: 'commandes/create', name: 'CommandesCreate', component: CommandesFormView, meta: { roles: ['admin'] } },
        { path: 'commandes/:id/reception', name: 'CommandesReception', component: CommandesReceptionView, meta: { roles: ['admin'] } },
        
        // Rapports
        { path: 'rapports', name: 'Rapports', component: RapportsView, meta: { roles: ['admin'] } },
        
        // Utilisateurs
        { path: 'utilisateurs', name: 'UtilisateursList', component: UtilisateursListView, meta: { roles: ['admin'] } },
        
        // ========== CAISSIER (routes spécifiques si besoin) ==========
        { path: 'caissier/ventes', redirect: '/ventes' },
        { path: 'caissier/ventes/create', redirect: '/ventes/create' },
        { path: 'caissier/clients', redirect: '/clients' },

        // router/index.js - Ajouter dans la section admin
{ path: 'import-export', name: 'ImportExport', component: () => import('@/views/admin/ImportExportMedicaments.vue'), meta: { roles: ['admin'] } },
      ]
    },
    
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('@/views/NotFoundView.vue') }
  ]
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const token = localStorage.getItem('token')
  
  // Récupérer l'utilisateur depuis le store ou localStorage
  let user = authStore.user
  if (!user && token) {
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
      user = JSON.parse(storedUser)
      authStore.user = user
      authStore.token = token
    }
  }
  
  const isAuthenticated = !!token
  
  // Vérifier l'accès aux pages qui nécessitent authentification
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } 
  // Vérifier les rôles
  else if (to.meta.roles && isAuthenticated) {
    const userRole = user?.role
    if (userRole && to.meta.roles.includes(userRole)) {
      next()
    } else {
      // Rediriger vers la page autorisée selon le rôle
      if (userRole === 'caissier') {
        next('/ventes')
      } else if (userRole === 'pharmacien') {
        next('/medicaments')
      } else {
        next('/')
      }
    }
  }
  // Rediriger si déjà connecté et essaie d'aller sur login
  else if (to.meta.guest && isAuthenticated) {
    const userRole = user?.role
    if (userRole === 'caissier') {
      next('/ventes')
    } else if (userRole === 'pharmacien') {
      next('/medicaments')
    } else {
      next('/')
    }
  }
  else {
    next()
  }
})

export default router