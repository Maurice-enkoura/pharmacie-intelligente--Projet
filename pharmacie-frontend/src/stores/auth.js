// src/stores/auth.js
import { defineStore } from 'pinia'
import { authService } from '@/services/auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isPharmacien: (state) => state.user?.role === 'pharmacien',
    isCaissier: (state) => state.user?.role === 'caissier',
    userRole: (state) => state.user?.role,
    userName: (state) => state.user?.name
  },
  
  actions: {
    async login(email, password) {
      try {
        const data = await authService.login(email, password)
        this.user = data.user
        this.token = data.token
        return data
      } catch (error) {
        console.error('Erreur login:', error)
        throw error
      }
    },
    
    async logout() {
      try {
        await authService.logout()
      } catch (error) {
        console.error('Erreur logout API:', error)
      } finally {
        this.user = null
        this.token = null
      }
    },
    
    // Initialiser le store depuis localStorage
    init() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      
      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
      }
    }
  }
})