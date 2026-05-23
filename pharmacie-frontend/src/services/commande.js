import api from './api'

export const commandeService = {
  async getAll(params = {}) {
    const response = await api.get('/commandes', { params })
    return response.data
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
  },
  
  async cancel(id) {
    const response = await api.delete(`/commandes/${id}/cancel`)
    return response.data
  }
}