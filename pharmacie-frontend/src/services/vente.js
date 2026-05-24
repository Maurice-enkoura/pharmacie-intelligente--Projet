// src/services/vente.js
import api from './api'

export const venteService = {
  async getAll(params = {}) {
    try {
      const response = await api.get('/ventes', { params })
      console.log('API Response brute:', response.data) // Debug
      
      // La réponse de Laravel paginate est directement dans response.data
      return {
        success: true,
        data: response.data.data || [],
        current_page: response.data.current_page || 1,
        last_page: response.data.last_page || 1,
        total: response.data.total || 0
      }
    } catch (error) {
      console.error('Erreur API ventes:', error)
      return {
        success: false,
        data: [],
        current_page: 1,
        last_page: 1,
        total: 0
      }
    }
  },
  
  async getById(id) {
    const response = await api.get(`/ventes/${id}`)
    return response.data
  },
  
  async create(data) {
    const response = await api.post('/ventes', data)
    return response.data
  },
  
  async cancel(id) {
    const response = await api.delete(`/ventes/${id}/cancel`)
    return response.data
  }
}