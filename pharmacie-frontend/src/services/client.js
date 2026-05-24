// src/services/client.js
import api from './api'

export const clientService = {
  async getAll(params = {}) {
    const response = await api.get('/clients', { params })
    return {
      data: response.data.data || [],
      current_page: response.data.current_page || 1,
      last_page: response.data.last_page || 1,
      total: response.data.total || 0
    }
  },
  
  async getById(id) {
    const response = await api.get(`/clients/${id}`)
    return response.data
  },
  
  async create(data) {
    const response = await api.post('/clients', data)
    return response.data.data || response.data
  },
  
  async update(id, data) {
    const response = await api.put(`/clients/${id}`, data)
    return response.data.data || response.data
  },
  
  async delete(id) {
    const response = await api.delete(`/clients/${id}`)
    return response.data
  }
}