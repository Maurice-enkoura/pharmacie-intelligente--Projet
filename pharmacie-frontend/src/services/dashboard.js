import api from './api'

export const dashboardService = {
  async getStats(periode = 'mois') {
    const response = await api.get('/dashboard', { params: { periode } })
    return response.data
  }
}