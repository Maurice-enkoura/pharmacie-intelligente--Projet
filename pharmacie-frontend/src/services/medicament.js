import api from './api'

export const medicamentService = {
  async getAll(params = {}) {
    const response = await api.get('/medicaments', { params })
    return response.data
  },
  
  async getById(id) {
    const response = await api.get(`/medicaments/${id}`)
    return response.data
  },
  
  async create(data) {
    const response = await api.post('/medicaments', data)
    return response.data
  },
  
  async update(id, data) {
    const response = await api.put(`/medicaments/${id}`, data)
    return response.data
  },
  
  async delete(id) {
    const response = await api.delete(`/medicaments/${id}`)
    return response.data
  },
  
  async restore(id) {
    const response = await api.post(`/medicaments/${id}/restore`)
    return response.data
  }
}