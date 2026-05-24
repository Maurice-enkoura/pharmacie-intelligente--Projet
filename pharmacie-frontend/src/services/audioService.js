// src/services/audioService.js

// Sons disponibles
const SOUNDS = {
  STOCK_BAS: '/sounds/alert.mp3',
  PEREMPTION: '/sounds/warning.mp3',
  SUCCESS: '/sounds/success.mp3'
}

// État des sons
let audioEnabled = false
let audioContext = null
let audioElements = {}

// Initialiser les éléments audio
const initAudioElements = () => {
  Object.keys(SOUNDS).forEach(key => {
    const audio = new Audio(SOUNDS[key])
    audio.preload = 'auto'
    audio.volume = 0.7
    audioElements[key] = audio
  })
}

// Activer les sons (à appeler après interaction utilisateur)
export const enableAudio = () => {
  audioEnabled = true
  
  // Initialiser les éléments audio
  if (Object.keys(audioElements).length === 0) {
    initAudioElements()
  }
  
  // Initialiser AudioContext pour certains navigateurs
  if (!audioContext && window.AudioContext) {
    audioContext = new AudioContext()
    audioContext.resume()
  }
  
  console.log('🔊 Sons activés')
}

// Désactiver les sons
export const disableAudio = () => {
  audioEnabled = false
  console.log('🔇 Sons désactivés')
}

// Jouer un son
export const playSound = (soundName) => {
  if (!audioEnabled) {
    console.log('Sons désactivés, lecture ignorée')
    return
  }
  
  try {
    const audio = audioElements[soundName]
    if (audio) {
      // Réinitialiser le son s'il est déjà en cours
      audio.currentTime = 0
      audio.play().catch(e => console.log('Erreur lecture son:', e))
    } else {
      // Fallback: créer un nouvel élément audio
      const fallbackAudio = new Audio(SOUNDS[soundName])
      fallbackAudio.volume = 0.7
      fallbackAudio.play().catch(e => console.log('Erreur lecture son (fallback):', e))
    }
  } catch (error) {
    console.error('Erreur lecture son:', error)
  }
}

// Précharger tous les sons
export const preloadSounds = () => {
  if (!audioEnabled) return
  
  Object.values(SOUNDS).forEach(soundUrl => {
    const audio = new Audio()
    audio.src = soundUrl
    audio.load()
  })
}

// Tester un son
export const testSound = (soundName = 'STOCK_BAS') => {
  if (audioEnabled) {
    playSound(soundName)
    return true
  } else {
    console.warn('Sons désactivés, activez-les d\'abord')
    return false
  }
}

// Récupérer l'état des sons
export const isAudioEnabled = () => audioEnabled