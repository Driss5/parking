<script setup>
import axios from 'axios'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useRouter } from 'vue-router'

const token = localStorage.getItem('token')
const user = ref(null)

const fetchUser = async () => {
    try {
        const response = await axios.get(
        'http://127.0.0.1:8000/api/user',
        {
            headers: {
            Authorization: `Bearer ${token}`
            }
        }
        )

        user.value = response.data.user

        console.log(user.value)
        
        myForm.value.name = user.value.name
        myForm.value.cin = user.value.cin
        myForm.value.number_phone = user.value.number_phone

    } catch (error) {
        console.log('Error fetching User', error.response?.data || error)
    }
}

onMounted(() => {
  fetchUser()
})

const route = useRoute()
const id = route.params.id
const showForm = ref(false) // Toggle state for the form
const router = useRouter()
const myForm = ref({
    name: '',
    cin: '',
    number_phone: '',
    car: '',
    duration_minutes: '',
})

const parking = ref(null)

onMounted(async () => {
  try {
      const response = await axios.get(
        `http://127.0.0.1:8000/api/parkings/${id}`,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        }
      )
      parking.value = response.data
  } catch (error) {
      console.error("Error fetching parking details:", error)
  }
})

const duration_minutes = ref(0)
const updateMinutes = () => {
  const now = new Date()
  duration_minutes.value = now.getMinutes()
}
let intervalId = null
onMounted(() => {
  updateMinutes()
  intervalId = setInterval(updateMinutes, 1000)
})
onUnmounted(() => {
  clearInterval(intervalId)
})

const handleReserve = async () => {
  try {
    const apiEndpoint = 'http://127.0.0.1:8000/api/reserver-parking';

    const token = localStorage.getItem('token')
    if (!token) {
      alert('Vous devez être connecté pour réserver.')
      return
    }

    const response = await axios.post(apiEndpoint, {
        name: myForm.value.name,
        cin: myForm.value.cin,
        number_phone: myForm.value.number_phone,
        car: myForm.value.car,
        duration_minutes: duration_minutes.value,
        parking_id: parking.value.id,
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    if (response.status === 201) {
        alert('Réservation réussie !')
        router.push('/profile')
        showForm.value = false // Hide form after success
        // Reset form or redirect if needed
    }
  } catch (error) {
    console.error('Reservation error', error)
    alert('Erreur lors de la réservation. Veuillez réessayer.')
  }
};

const toggleForm = () => {
    showForm.value = !showForm.value;
    // Scroll to form if showing
    if (showForm.value) {
        setTimeout(() => {
            const formElement = document.getElementById('reservation-form');
            if(formElement) formElement.scrollIntoView({ behavior: 'smooth' });
        }, 100);
    }
}
</script>


<template>
  <div class="page-container" v-if="parking">
    <div class="content-wrapper">
      
      <!-- Main Card -->
      <div class="main-card">
        <!-- Image Header -->
        <div class="image-header">
             <img :src="`/images/${parking.image}`" @error="(e) => e.target.src = 'https://via.placeholder.com/800x400?text=Premium+Parking'" alt="Parking Image" />
             <div class="overlay-gradient"></div>
             <div class="status-badge" :class="parking.status ? parking.status.toLowerCase() : ''">
                <span class="status-dot"></span>
                {{ parking.status || 'OUVERT' }}
             </div>
             <div class="header-content">
                 <h1 class="parking-title">{{ parking.name }}</h1>
                 <p class="parking-location">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon-loc">
                      <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    {{ parking.location }}
                 </p>
             </div>
        </div>

        <div class="card-body">
            <div class="description-box">
                <h3>À propos</h3>
                <p>{{ parking.description }}</p>
            </div>

            <div class="stats-container">
                <div class="stat-card price">
                    <div class="stat-icon">
                        <span class="currency">DH</span>
                    </div>
                    <div class="stat-info">
                        <span class="label">Tarif Horaire</span>
                        <span class="value">{{ parking.tariffs }} <small>DH/h</small></span>
                    </div>
                </div>
                
                <div class="stat-card capacity">
                     <div class="stat-icon">🚗</div>
                     <div class="stat-info">
                        <span class="label">Capacité Totale</span>
                        <span class="value">{{ parking.capacity }} <small>Places</small></span>
                     </div>
                </div>

                <div class="stat-card available">
                     <div class="stat-icon">✅</div>
                     <div class="stat-info">
                        <span class="label">Disponibilité</span>
                        <span class="value">{{ parking.available_spots }} <small>Libres</small></span>
                     </div>
                </div>
            </div>

            <div class="action-area">
                <button @click="toggleForm" class="toggle-btn" :class="{ 'active': showForm }">
                    <span class="btn-text">{{ showForm ? 'Annuler la réservation' : 'Réserver une place' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="btn-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" v-if="!showForm" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" v-else />
                    </svg>
                </button>
            </div>
        </div>
      </div>

      <!-- Reservation Form Section (Conditional) -->
      <transition name="expand">
        <div v-if="showForm" id="reservation-form" class="form-wrapper">
            <div class="form-container">
                <div class="form-header-fancy">
                    <h2>Confirmation de Réservation</h2>
                    <p>Complétez vos informations pour garantir votre place.</p>
                </div>
                
                <form @submit.prevent="handleReserve" class="modern-form">
                    <div class="input-group full">
                        <label>Nom complet</label>
                        <div class="input-wrapper">
                            <input type="text" v-model="myForm.name" readonly placeholder="Ex: Jean Dupont" required />
                            <span class="input-icon">👤</span>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="input-group">
                            <label>CIN</label>
                            <div class="input-wrapper">
                                <input type="text" v-model="myForm.cin" readonly placeholder="Ex: AB123456" required />
                                <span class="input-icon">🪪</span>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Téléphone</label>
                            <div class="input-wrapper">
                                <input type="text" v-model="myForm.number_phone" readonly placeholder="Ex: 06 12 34 56 78" required />
                                <span class="input-icon">📱</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="input-group">
                            <label>Matricule</label>
                            <div class="input-wrapper">
                                <input type="text" v-model="myForm.car" placeholder="Ex: 1234-A-50" required />
                                <span class="input-icon">🚘</span>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Durée estimée</label>
                            <div class="input-wrapper locked">
                                <input type="number" v-model="duration_minutes" required readonly />
                                <span class="unit">min</span>
                                <span class="input-icon">⏱️</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn-fancy">
                        <span class="btn-background"></span>
                        <span class="btn-content">Confirmer et Payer</span>
                    </button>
                </form>
            </div>
        </div>
      </transition>

    </div>
  </div>
  <div v-else class="loading-overlay">
      <div class="spinner"></div>
      <p>Chargement du parking...</p>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

/* GLOBAL RESET & FONT */
/* GLOBAL RESET & FONT */
.page-container {
  position: relative;
  min-height: 100vh;
  padding: 60px 20px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  overflow: hidden;
}

/* Background image */
.page-container::before {
  content: "";
  position: absolute;
  inset: 0;
  background: url("@/assets/car.jpeg") center/cover no-repeat;
  filter: blur(8px);
  transform: scale(1.1); /* bach ma ybanch l edges */
  z-index: -1;
}
.content-wrapper {
  max-width: 850px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 40px;
}

/* --- MAIN CARD --- */
.main-card {
  background: rgba(255, 255, 255, 0.39);
  border-radius: 30px;
  overflow: hidden;
  box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.15);
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease;
}

/* Image Header */
.image-header {
  height: 320px;
  position: relative;
  overflow: hidden;
}

.image-header img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.8s ease;
}

.main-card:hover .image-header img {
    transform: scale(1.05);
}

.overlay-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 50%, transparent 100%);
}

.header-content {
    position: absolute;
    bottom: 30px;
    left: 30px;
    right: 30px;
    z-index: 2;
    color: white;
}

.parking-title {
    font-size: 38px;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 8px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.parking-location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 16px;
    font-weight: 500;
    opacity: 0.9;
}

.icon-loc { width: 18px; height: 18px; color: #3b82f6; }

/* Status Badge */
.status-badge {
    position: absolute;
    top: 25px;
    right: 25px;
    padding: 8px 16px;
    border-radius: 50px;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(8px);
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: #0f172a;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 6px;
    z-index: 5;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Card Body */
.card-body {
    padding: 40px;
}

.description-box h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
}

.description-box p {
    color: #000000;
    line-height: 1.7;
    font-size: 15px;
}

/* Stats Container */
.stats-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin: 40px 0;
}

@media (max-width: 640px) {
    .stats-container { grid-template-columns: 1fr; }
}

.stat-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.2s;
     
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: #cbd5e1;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.stat-card.price .stat-icon { color: #10b981; background: #ecfdf5; }
.stat-card.capacity .stat-icon { color: #3b82f6; background: #eff6ff; }
.stat-card.available .stat-icon { color: #f59e0b; background: #fffbeb; }

.stat-info { display: flex; flex-direction: column; }
.stat-info .label { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; }
.stat-info .value { font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px; }
.stat-info .value small { font-size: 12px; color: #64748b; font-weight: 600; margin-left: 2px; }

/* Toggle Button & Action Area */
.action-area {
    margin-top: 10px;
}

.toggle-btn {
    width: 100%;
    padding: 18px;
    background: #0f172a;
    color: white;
    border: none;
    border-radius: 18px;
    font-size: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
}

.toggle-btn:hover {
    background: #1e293b;
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3);
}

.toggle-btn.active {
    background: #fff;
    color: #ef4444;
    border: 2px solid #fee2e2;
    box-shadow: none;
}

.btn-icon { width: 24px; height: 24px; transition: transform 0.3s; }
.toggle-btn:hover .btn-icon { transform: translateX(5px); }
.toggle-btn.active .btn-icon { transform: rotate(180deg); }


/* --- RESERVATION FORM --- */
.form-wrapper {
    overflow: hidden;
}

.form-container {
    background: rgba(255, 255, 255, 0.37);
    padding: 40px;
    border-radius: 30px;
    box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.15);
    position: relative;
    border: 1px solid rgba(255,255,255,0.8);
}

.form-header-fancy {
    text-align: center;
    margin-bottom: 35px;
}

.form-header-fancy h2 {
    font-size: 24px;
    font-weight: 800;
    color: #000000;
    background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 8px;
}

.form-header-fancy p { color: #64748b; font-size: 15px; }

.modern-form { display: flex; flex-direction: column; gap: 24px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.input-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 700;
    color: #000000;
    margin-left: 5px;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper input {
    width: 100%;
    padding: 16px 20px 16px 45px;
    border-radius: 16px;
    border: 2px solid #f1f5f9;
    background: #f8fafc;
    font-size: 15px;
    color: #0f172a;
    font-weight: 500;
    transition: all 0.2s ease;
    outline: none;
}

.input-wrapper:hover input { border-color: #cbd5e1; }
.input-wrapper input:focus {
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.input-icon {
    position: absolute;
    left: 16px;
    font-size: 18px;
    color: #94a3b8;
    pointer-events: none;
}

.input-wrapper.locked { background: #f1f5f9; border-radius: 16px; }
.input-wrapper.locked input { background: transparent; border: none; padding-right: 50px; }
.unit { position: absolute; right: 20px; font-weight: 700; font-size: 12px; color: #94a3b8; text-transform: uppercase; }

.submit-btn-fancy {
    position: relative;
    margin-top: 15px;
    padding: 20px;
    border: none;
    background: none;
    cursor: pointer;
    border-radius: 18px;
    overflow: hidden;
    transition: transform 0.2s;
}

.submit-btn-fancy .btn-background {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transition: all 0.3s;
}

.submit-btn-fancy:hover .btn-background {
    transform: scale(1.05);
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.submit-btn-fancy .btn-content {
    position: relative;
    z-index: 1;
    color: white;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.submit-btn-fancy:active { transform: scale(0.98); }

/* --- TRANSITIONS --- */
.expand-enter-active,
.expand-leave-active {
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
  max-height: 800px;
  opacity: 1;
  transform: translateY(0);
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-20px);
}

/* Loading */
.loading-overlay {
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 15px;
    color: #64748b;
}

.spinner {
    width: 40px; height: 40px;
    border: 4px solid #e2e8f0;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>