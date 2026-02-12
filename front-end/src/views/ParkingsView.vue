<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import axios from 'axios'
import router from '@/router'

// --- Logic ---
const parkings = ref([])
const searchQuery = ref('')
const token = localStorage.getItem('token')

if (!token) {
  router.push({ name: 'login' })
}

const handleShowParking = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/parkings', {
      headers: { Authorization: `Bearer ${token}` }
    })
    parkings.value = response.data || []
  } catch (error) {
    console.error('Error fetching parkings:', error)
  }
}

const filteredParkings = computed(() => {
  return parkings.value.filter(p => 
    p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    p.location.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

let intervalId = null
onMounted(() => {
  handleShowParking()
  intervalId = setInterval(handleShowParking, 3000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})

function handleShowOneParking(id) {
  router.push({ name: 'ParkingDetails', params: { id } })
}
</script>

<template>
  <div class="main-wrapper">
    <div class="background-overlay"></div>

    <div class="content-container">
      <header class="header-section">
        <h1>Parkings Disponibles</h1>
        <p>Gérez vos réservations en toute simplicité</p>
      </header>

      <div class="filter-card glass-panel">
        <div class="search-wrapper">
          <div class="input-group-pro">
            <span class="search-icon">🔍</span>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Trouvez votre parking idéal (nom, ville...)" 
            />
          </div>
          
          <div class="vertical-divider"></div>

          <div class="filters-container">
            <div class="pro-filter-group">
              <span class="mini-label">Capacité</span>
              <div class="chips-wrapper">
                <button class="pro-chip active">Petite</button>
                <button class="pro-chip">Grande</button>
              </div>
            </div>

            <div class="pro-filter-group">
              <span class="mini-label">Statut</span>
              <div class="chips-wrapper">
                <button class="pro-chip green">Disponible</button>
                <button class="pro-chip">Complet</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredParkings.length > 0" class="cards-grid">
        <div v-for="parking in filteredParkings" :key="parking.id" class="parking-card glass-panel">
          <div class="card-header-row">
            <div class="title-area">
              <h3>{{ parking.name }}</h3>
              <div class="mini-info">
                <span>Cap: {{ parking.capacity }}</span>
                <span class="sep">|</span>
                <span class="avail">Avail: {{ parking.available_spots }}</span>
              </div>
            </div>
            <button class="reserve-btn" @click="handleShowOneParking(parking.id)">Réserver</button>
          </div>

          <div class="image-box">
            <img :src="`/images/${parking.image}`" @error="(e) => e.target.src = 'https://via.placeholder.com/400x200?text=Parking'" />
          </div>

          <div class="card-body-content">
            <div class="meta-row">
              <span class="loc">📍 {{ parking.location }}</span>
              <span class="price">{{ parking.tariffs }} DH/h</span>
            </div>
            <p class="desc">{{ parking.description }}</p>
            <div class="footer-row">
              <span :class="['pill', parking.status]">{{ parking.status }}</span>
              <span class="tel">📞 {{ parking.number }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="no-data glass-panel">
        <p>Aucun parking trouvé pour le moment.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.main-wrapper {
  min-height: 100vh;
  position: relative;
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 30px 20px;
  color: white;
}

.background-overlay {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background-image: url('../assets/car.jpeg'); 
  background-size: cover;
  background-position: center;
  filter: blur(2px) brightness(0.5);
  z-index: -1;
  transform: scale(1.02);
}

.content-container { max-width: 1200px; margin: 0 auto; }
.header-section { text-align: center; margin-bottom: 30px; }
.header-section h1 { font-size: 36px; font-weight: 800; }

/* --- Glassmorphism --- */
.glass-panel {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(15px) saturate(180%);
  -webkit-backdrop-filter: blur(15px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 24px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
}

/* --- Search Bar PRO Section --- */
.filter-card {
  padding: 20px 30px;
  margin-bottom: 40px;
}

.search-wrapper {
  display: flex;
  align-items: center;
  gap: 25px;
  flex-wrap: wrap;
}

.input-group-pro {
  flex: 1;
  min-width: 300px;
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.95);
  padding: 12px 20px;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: 0.3s ease;
}

.input-group-pro:focus-within {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(46, 0, 173, 0.2);
}

.input-group-pro input {
  border: none; outline: none; width: 100%; margin-left: 12px;
  font-size: 15px; font-weight: 600; color: #1e293b; background: transparent;
}

.vertical-divider {
  width: 1px; height: 40px; background: rgba(255, 255, 255, 0.2);
}

.filters-container { display: flex; gap: 25px; align-items: center; }
.pro-filter-group { display: flex; flex-direction: column; gap: 5px; }
.mini-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: rgba(255, 255, 255, 0.6); margin-left: 5px; }

.chips-wrapper { display: flex; gap: 8px; }
.pro-chip {
  padding: 6px 16px; border-radius: 10px; font-size: 12px; font-weight: 700;
  border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.1);
  color: white; cursor: pointer; transition: 0.3s;
}
.pro-chip.active { background: #2e00ad; border-color: #2e00ad; }
.pro-chip.green { background: rgba(22, 163, 74, 0.8); }

/* --- Grid System --- */
.cards-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; }
.parking-card { padding: 24px; transition: 0.4s; }
.parking-card:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.12); }

.card-header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
.title-area h3 { font-size: 20px; font-weight: 800; margin: 0; }
.mini-info { font-size: 12px; opacity: 0.7; }
.avail { color: #4ade80; font-weight: 700; }
.sep { margin: 0 10px; }

.reserve-btn { background: #2e00ad; color: white; border: none; padding: 10px 24px; border-radius: 12px; font-weight: 800; cursor: pointer; }

.image-box { width: 100%; height: 210px; border-radius: 18px; overflow: hidden; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); }
.image-box img { width: 100%; height: 100%; object-fit: cover; }

.meta-row { display: flex; justify-content: space-between; font-weight: 800; margin-bottom: 12px; }
.price { color: #4ade80; }

.desc { font-size: 14px; opacity: 0.85; line-height: 1.6; margin-bottom: 20px; min-height: 45px; }

.footer-row { border-top: 1px solid rgba(255, 255, 255, 0.15); padding-top: 15px; display: flex; justify-content: space-between; align-items: center; }
.pill { padding: 4px 14px; border-radius: 8px; font-size: 11px; font-weight: 800; }
.pill.open { background: rgba(74, 222, 128, 0.2); color: #4ade80; }
.pill.full { background: rgba(248, 113, 113, 0.2); color: #f87171; }

@media (max-width: 900px) {
  .cards-grid { grid-template-columns: 1fr; }
  .search-wrapper { flex-direction: column; align-items: stretch; }
  .vertical-divider { display: none; }
}
</style>