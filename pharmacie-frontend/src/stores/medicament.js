import { defineStore } from 'pinia'
import { medicamentService } from '@/services/medicament'

export const useMedicamentStore = defineStore('medicament', {
  state: () => ({
    medicaments: [],
    currentMedicament: null,
    loading: false,
    pagination: null
  }),
  
  actions: {
    async fetchAll(params = {}) {
      this.loading = true
      try {
        const response = await medicamentService.getAll(params)
        this.medicaments = response.data
        this.pagination = {
          current_page: response.current_page,
          last_page: response.last_page,
          total: response.total,
          per_page: response.per_page
        }
        return response
      } finally {
        this.loading = false
      }
    },
    
    async fetchById(id) {
      this.loading = true
      try {
        this.currentMedicament = await medicamentService.getById(id)
        return this.currentMedicament
      } finally {
        this.loading = false
      }
    },
    
    async create(data) {
      const medicament = await medicamentService.create(data)
      await this.fetchAll()
      return medicament
    },
    
    async update(id, data) {
      const medicament = await medicamentService.update(id, data)
      await this.fetchAll()
      return medicament
    },
    
    async delete(id) {
      await medicamentService.delete(id)
      await this.fetchAll()
    }
  }
})