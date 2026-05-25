<!-- src/views/shared/VentesDetailView.vue -->
<template>
  <div class="vente-detail">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Détail de la vente</h1>
        <p class="text-sm text-gray-500 mt-1">Consultez les informations complètes de la transaction</p>
      </div>
      <div class="flex gap-3">
        <!-- Bouton Envoyer par email -->
        <button 
          @click="sendEmail" 
          :disabled="sending || !vente.client?.email"
          class="btn-secondary flex items-center gap-2"
          :title="vente.client?.email ? 'Envoyer la facture par email' : 'Client sans adresse email'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <span v-if="sending">Envoi...</span>
          <span v-else>Email</span>
        </button>
        
        <!-- Bouton Télécharger PDF -->
        <button 
          @click="downloadPDF" 
          :disabled="loadingPDF"
          class="btn-primary flex items-center gap-2"
          title="Télécharger la facture en PDF"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
          </svg>
          <span v-if="loadingPDF">Génération...</span>
          <span v-else>Télécharger PDF</span>
        </button>
        
        <!-- Bouton Imprimer -->
        <button 
          @click="printInvoice" 
          class="btn-secondary flex items-center gap-2"
          title="Imprimer la facture"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          Imprimer
        </button>
        
        <!-- Bouton Retour -->
        <router-link to="/ventes" class="btn-secondary flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Retour
        </router-link>
      </div>
    </div>
    
    <div v-if="loading && !vente.id" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      <p class="mt-4 text-gray-500">Chargement des détails de la vente...</p>
    </div>
    
    <div v-else class="space-y-6">
      <!-- En-tête de la facture -->
      <div class="card bg-gradient-to-r from-primary-50 to-white">
        <div class="flex justify-between items-start">
          <div>
            <div class="flex items-center gap-2 mb-2">
              <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                <span class="text-xl">🏥</span>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-800">Pharmacie Intelligente</h2>
                <p class="text-sm text-gray-500">Dakar, Sénégal</p>
              </div>
            </div>
          </div>
          <div class="text-right">
            <p class="text-sm text-gray-500">Facture N°</p>
            <p class="text-xl font-bold text-primary-600">{{ vente.numero_facture }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ formatDateTime(vente.created_at) }}</p>
          </div>
        </div>
      </div>
      
      <!-- Informations client et vente -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Client -->
        <div class="card">
          <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            INFORMATIONS CLIENT
          </h3>
          <div class="space-y-2">
            <p class="text-lg font-semibold text-gray-800">
              {{ vente.client?.prenom }} {{ vente.client?.nom }}
            </p>
            <p class="text-sm text-gray-600 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              {{ vente.client?.telephone }}
            </p>
            <p v-if="vente.client?.email" class="text-sm text-gray-600 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              {{ vente.client?.email }}
            </p>
            <p v-else class="text-sm text-orange-600 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              Aucun email renseigné
            </p>
            <p v-if="vente.client?.adresse" class="text-sm text-gray-600 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ vente.client?.adresse }}
            </p>
          </div>
        </div>
        
        <!-- Informations vente -->
        <div class="card">
          <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            INFORMATIONS VENTE
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b">
              <span class="text-gray-600">Date</span>
              <span class="font-medium">{{ formatDate(vente.created_at) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
              <span class="text-gray-600">Heure</span>
              <span class="font-medium">{{ formatTime(vente.created_at) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
              <span class="text-gray-600">Caissier</span>
              <span class="font-medium">{{ vente.user?.name }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
              <span class="text-gray-600">Type de vente</span>
              <span :class="vente.type_vente === 'avec_ordonnance' ? 'text-orange-600' : 'text-green-600'" class="font-medium">
                {{ vente.type_vente === 'avec_ordonnance' ? '📋 Avec ordonnance' : '📄 Sans ordonnance' }}
              </span>
            </div>
            <div v-if="vente.ordonnance_ref" class="flex justify-between items-center py-2 border-b">
              <span class="text-gray-600">Réf. ordonnance</span>
              <span class="font-medium text-orange-600">{{ vente.ordonnance_ref }}</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Détails des médicaments -->
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          DÉTAILS DES PRODUITS
        </h3>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Médicament</th>
                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Dosage</th>
                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Quantité</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Prix unitaire</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ligne in vente.ligne_ventes" :key="ligne.id" class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ ligne.medicament?.nom }}</td>
                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ ligne.medicament?.dosage }}</td>
                <td class="px-4 py-3 text-center text-sm">{{ ligne.quantite }}</td>
                <td class="px-4 py-3 text-right text-sm">{{ formatPrice(ligne.prix_unitaire) }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ formatPrice(ligne.sous_total) }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50">
              <tr class="border-t">
                <td colspan="4" class="px-4 py-3 text-right font-semibold">Total TTC :</td>
                <td class="px-4 py-3 text-right text-xl font-bold text-primary-600">{{ formatPrice(vente.montant_total) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
      <!-- Récapitulatif paiement -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card bg-gray-50">
          <p class="text-sm text-gray-500 mb-1">💰 Montant payé</p>
          <p class="text-2xl font-bold text-green-600">{{ formatPrice(vente.montant_paye) }}</p>
        </div>
        <div class="card bg-gray-50">
          <p class="text-sm text-gray-500 mb-1">🔄 Monnaie rendue</p>
          <p class="text-2xl font-bold text-blue-600">{{ formatPrice(vente.monnaie_rendue) }}</p>
        </div>
        <div class="card bg-gray-50">
          <p class="text-sm text-gray-500 mb-1">💳 Mode de paiement</p>
          <p class="text-2xl font-bold text-purple-600">{{ getPaiementLabel(vente.mode_paiement) }}</p>
        </div>
      </div>
      
      <!-- Message de remerciement -->
      <div class="text-center py-6 border-t border-gray-200">
        <p class="text-gray-500 text-sm">Merci de votre confiance ! À bientôt à la Pharmacie Intelligente</p>
        <p class="text-gray-400 text-xs mt-1">Horaires : Lun-Sam 8h-21h | Dimanche 9h-13h</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { venteService } from '@/services/vente'

const route = useRoute()
const vente = ref({})
const loading = ref(false)
const loadingPDF = ref(false)
const sending = ref(false)

const formatPrice = (price) => {
  if (!price && price !== 0) return '0 FCFA'
  return new Intl.NumberFormat('fr-SN', { style: 'currency', currency: 'XOF' }).format(price)
}

const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString('fr-FR')
}

const formatTime = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

const formatDateTime = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const getPaiementLabel = (mode) => {
  const labels = {
    especes: 'Espèces',
    orange_money: 'Orange Money',
    wave: 'Wave',
    carte: 'Carte bancaire'
  }
  return labels[mode] || mode
}

const loadVente = async () => {
  loading.value = true
  try {
    vente.value = await venteService.getById(route.params.id)
    console.log('Vente chargée:', vente.value)
  } catch (error) {
    console.error('Erreur chargement vente:', error)
    alert('Erreur lors du chargement des détails de la vente')
  } finally {
    loading.value = false
  }
}

const downloadPDF = async () => {
  loadingPDF.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/v1/ventes/${vente.value.id}/pdf`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/pdf'
      }
    })
    
    if (!response.ok) {
      const error = await response.json()
      throw new Error(error.message || 'Erreur lors de la génération du PDF')
    }
    
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `facture_${vente.value.numero_facture}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
  } catch (error) {
    console.error('Erreur PDF:', error)
    alert('Erreur lors de la génération du PDF: ' + error.message)
  } finally {
    loadingPDF.value = false
  }
}

const sendEmail = async () => {
  if (!vente.value.client?.email) {
    alert('Ce client n\'a pas d\'adresse email')
    return
  }
  
  if (!confirm(`Envoyer la facture à ${vente.value.client.email} ?`)) return
  
  sending.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await fetch(`http://127.0.0.1:8000/api/v1/ventes/${vente.value.id}/send-email`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    
    const data = await response.json()
    
    if (response.ok) {
      alert('✅ ' + data.message)
    } else {
      alert('❌ ' + data.message)
    }
  } catch (error) {
    console.error('Erreur envoi email:', error)
    alert('Erreur lors de l\'envoi de l\'email')
  } finally {
    sending.value = false
  }
}

const printInvoice = () => {
  window.print()
}

onMounted(() => {
  loadVente()
})
</script>

<style scoped>
@media print {
  .btn-primary, .btn-secondary, .router-link, header, .sidebar {
    display: none !important;
  }
  body {
    background: white;
  }
  .card {
    box-shadow: none;
    border: 1px solid #ddd;
  }
}
</style>