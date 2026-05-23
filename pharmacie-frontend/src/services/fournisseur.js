import api from './api'

export const fournisseurService = {
  async getAll(params = {}) {
    const response = await api.get('/fournisseurs', { params })
    return response.data
  },
  
  async getById(id) {
    const response = await api.get(`/fournisseurs/${id}`)
    return response.data
  },
  
  async create(data) {
    const response = await api.post('/fournisseurs', data)
    return response.data
  },
  
  async update(id, data) {
    const response = await api.put(`/fournisseurs/${id}`, data)
    return response.data
  },
  
  async delete(id) {
    const response = await api.delete(`/fournisseurs/${id}`)
    return response.data
  }
}