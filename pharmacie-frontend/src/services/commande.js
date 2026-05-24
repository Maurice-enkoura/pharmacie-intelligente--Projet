// src/services/commande.js
import api from './api'

export const commandeService = {
  async getAll(params = {}) {
    try {
      const response = await api.get('/commandes', { params })
      return {
        success: true,
        data: response.data.data || [],
        current_page: response.data.current_page || 1,
        last_page: response.data.last_page || 1,
        total: response.data.total || 0
      }
    } catch (error) {
      console.error('Erreur chargement commandes:', error)
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
    const response = await api.get(`/commandes/${id}`)
    return response.data
  },
  
  async create(data) {
    const response = await api.post('/commandes', data)
    return response.data
  },
  
  async reception(id, data) {
    const response = await api.put(`/commandes/${id}/reception`, data)
    return response.data
  }
}