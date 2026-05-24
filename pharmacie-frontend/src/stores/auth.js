// src/stores/auth.js
import { defineStore } from 'pinia'
import api from '@/services/api'

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
        const response = await api.post('/login', { email, password })
        console.log('Login response:', response.data)
        
        if (response.data.token) {
          this.token = response.data.token
          this.user = response.data.user
          
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.user))
          
          // Configurer le header Authorization pour toutes les futures requêtes
          api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
        }
        
        return response.data
      } catch (error) {
        console.error('Login error:', error)
        throw error
      }
    },
    
    async logout() {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        delete api.defaults.headers.common['Authorization']
      }
    },
    
    // Initialiser depuis localStorage
    init() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      
      console.log('Init auth - token:', token ? 'present' : 'absent')
      console.log('Init auth - user:', user)
      
      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
      }
    }
  }
})