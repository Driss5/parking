<script setup>
import { ref, onMounted, onUnmounted , computed } from 'vue'
import axios from 'axios'
import router from '@/router'

// 1. Data State
const parkings = ref([])
const searchQuery = ref('')
const token = localStorage.getItem('token')
const selectedCapacity = ref(null)

if (!token) {
  router.push({ name: 'login' })
}

// 2. Fetch Data mn l-Backend
const handleShowParking = async () => {
  try {
    // const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/parkings', {
      headers: { Authorization: `Bearer ${token}` }
    })
    parkings.value = response.data
  } catch (error) {
    console.error('Error fetching parkings:', error)
  }
}

// 3. Logic dyal l-Search (Filtering)
const filteredParkings = computed(() => {
  return parkings.value.filter(p => 
    p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    p.location.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

let intervalId = null

onMounted(() => {
  handleShowParking()

  intervalId = setInterval(() => {
    handleShowParking()
  }, 2000)
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})


function handleShowOneParking(id) {
  router.push({ name: 'ParkingDetails', params: { id } })
}

// function handleAvailable

</script>

<template>
  <div class="main-wrapper">
    <div class="header-container">
      <div class="header-text">
        <h1>Parkings Disponibles</h1>
        <p>Gérez vos réservations en toute simplicité</p>
      </div>

      <div class="filter-card">
        <div class="search-box">
          <span class="icon">🔍</span>
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Rechercher par nom ou ville..." 
          />
        </div>
        
        <div class="quick-filters">
          <div class="filter-group">
            <span class="label">Capacity:</span>
            <button class="chip active">Small</button>
            <button class="chip outline">Large</button>
          </div>
          <div class="filter-group">
            <span class="label">Availability:</span>
            <button class="chip green" @click="handleAvFilter('available')">Available</button>
            <button class="chip outline" @click="handleAvFilter('full')">Full</button>
          </div>
        </div>
      </div>
    </div>

    <div class="cards-container">
      <div v-if="filteredParkings.length > 0" class="cards-grid">
        <div v-for="parking in filteredParkings" :key="parking.id" class="parking-card">
          
          <div class="card-top">
            <div class="card-info-header">
              <h3>{{ parking.name }}</h3>
              <div class="mini-stats">
                <span>Cap: <strong>{{ parking.capacity }}</strong></span>
                <span class="divider">|</span>
                <span class="avail">Avail: <strong>{{ parking.available_spots }}</strong></span>
              </div>
            </div>
            <button class="reserve-btn" @click="handleShowOneParking(parking.id)">Réserver</button>
          </div>

          <div class="image-wrapper">
            <img :src="`/images/${parking.image}`" @error="(e) => e.target.src = 'https://via.placeholder.com/400x200?text=Parking+View'" />
          </div>

          <div class="card-details">
            <div class="loc-price-row">
              <span class="location">📍 {{ parking.location }}</span>
              <span class="price-tag">{{ parking.tariffs }} DH/h</span>
            </div>
            
            <p class="description">{{ parking.description }}</p>

            <div class="card-footer">
              <span :class="['status-pill', parking.status]">{{ parking.status }}</span>
              <span class="phone-number">📞 {{ parking.number }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="no-results">
        <p>Aucun parking ne correspond à votre recherche.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Reset & Base */
.main-wrapper {
  background-color: #f4f7fa;
  min-height: 100vh;
  padding: 40px 20px;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* Header & Filter Card */
.header-container {
  max-width: 600px;
  margin: 0 auto 35px auto;
}

.header-text h1 {
  font-size: 28px;
  color: #1e293b;
  font-weight: 800;
  margin-bottom: 8px;
}

.header-text p {
  color: #64748b;
  margin-bottom: 25px;
}

.filter-card {
  background: white;
  padding: 20px;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.search-box {
  display: flex;
  align-items: center;
  background: #f1f5f9;
  padding: 12px 18px;
  border-radius: 14px;
  margin-bottom: 15px;
}

.search-box input {
  border: none;
  background: transparent;
  width: 100%;
  margin-left: 10px;
  outline: none;
  font-size: 14px;
}

.quick-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.label { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

.chip {
  padding: 5px 12px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 600;
  border: 1px solid transparent;
  cursor: pointer;
  transition: 0.2s;
}

.chip.outline { background: transparent; border-color: #e2e8f0; color: #64748b; }
.chip.active { background: #3b82f6; color: white; }
.chip.green { background: #dcfce7; color: #166534; }

/* Cards Styling */
.cards-container {
  max-width: 600px;
  margin: 0 auto;
}

.parking-card {
  background: white;
  border-radius: 24px;
  padding: 20px;
  margin-bottom: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
  border: 1px solid #f1f5f9;
}

.card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
}

.card-info-header h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  text-transform: capitalize;
}

.mini-stats {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 4px;
}

.avail { color: #3b82f6; }
.divider { margin: 0 8px; }

.reserve-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.reserve-btn:hover { background: #2563eb; transform: translateY(-2px); }

.image-wrapper {
  width: 100%;
  height: 220px;
  border-radius: 18px;
  overflow: hidden;
  margin-bottom: 15px;
}

.image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.loc-price-row {
  display: flex;
  justify-content: space-between;
  font-weight: 700;
  font-size: 14px;
  margin-bottom: 10px;
}

.location { color: #475569; }
.price-tag { color: #10b981; }

.description {
  font-size: 13px;
  color: #64748b;
  line-height: 1.5;
  margin-bottom: 15px;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #f1f5f9;
  padding-top: 12px;
}

.status-pill {
  padding: 4px 12px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
}

.status-pill.open { background: #ecfdf5; color: #059669; }
.status-pill.full { background: #fef2f2; color: #dc2626; }

.phone-number { font-size: 12px; color: #94a3b8; }

.no-results { text-align: center; color: #64748b; padding: 40px; }
</style>