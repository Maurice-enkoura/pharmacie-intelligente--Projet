import api from './api'

export const venteService = {
  async getAll(params = {}) {
    const response = await api.get('/ventes', { params })
    return response.data
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