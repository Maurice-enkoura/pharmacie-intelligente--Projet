import api from './api'

export const stockService = {
  async getAlertes() {
    const response = await api.get('/stock/alertes')
    return response.data
  },
  
  async entrees(data) {
    const response = await api.post('/stock/entrees', data)
    return response.data
  },
  
  async getHistorique(medicamentId) {
    const response = await api.get('/stock/historique', { params: { medicament_id: medicamentId } })
    return response.data
  }
}