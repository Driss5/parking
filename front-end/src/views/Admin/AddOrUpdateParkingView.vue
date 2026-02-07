<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const token = localStorage.getItem('token')

const role = ref(null)
const parkingId = ref(route.params.id || null)
const isEdit = ref(!!parkingId.value)

const parkingForm = ref({
  name: '',
  location: '',
  tariffs: '',
  capacity: '',
  description: '',
  number: '',
  available_spots: '',
  agent_id: '',
  image: '',
  status: 'open'
})

const fetchRole = async () => {
  if (!token) return

  try {
    const response = await axios.get(
      'http://127.0.0.1:8000/api/user',
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )

    role.value = response.data.user.role

    if (role.value !== 'admin') {
      alert('Access denied')
      router.push('/')
      return
    }

    if (isEdit.value) {
      fetchParking()
    }

  } catch (error) {
    console.log('Error fetching role', error.response?.data || error)
  }
}

const fetchParking = async () => {
  try {
    const response = await axios.get(
      `http://127.0.0.1:8000/api/parking-admin-show/${parkingId.value}`,
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    )

    parkingForm.value = response.data

  } catch (error) {
    console.log('Error fetching parking', error.response?.data || error)
  }
}

const submitParking = async () => {
  try {
    if (isEdit.value) {
      // UPDATE
      await axios.put(
        `http://127.0.0.1:8000/api/update-parking/${parkingId.value}`,
        parkingForm.value,
        {
          headers: { Authorization: `Bearer ${token}` }
        }
      )
      alert('Parking updated successfully')
    } else {
      // CREATE
      await axios.post(
        'http://127.0.0.1:8000/api/create-parking',
        parkingForm.value,
        {
          headers: { Authorization: `Bearer ${token}` }
        }
      )
      alert('Parking created successfully')
    }

    router.push('/parkings')

  } catch (error) {
    console.log('Error saving parking', error.response?.data || error)
  }
}


onMounted(() => {
  fetchRole()
})
</script>

<template>
  <div class="form-container">
    <div class="form-card">
      <header class="form-header">
        <h1>{{ isEdit ? 'Update Parking' : 'Create New Parking' }}</h1>
        <p class="subtitle">Enter the details below to manage your parking space</p>
      </header>

      <div v-if="role === 'admin'">
        <form @submit.prevent="submitParking" class="modern-form">
          <div class="grid-inputs">
            <div class="input-group">
              <label>Parking Name</label>
              <input v-model="parkingForm.name" placeholder="e.g. Blue Plaza" />
            </div>

            <div class="input-group">
              <label>Location</label>
              <input v-model="parkingForm.location" placeholder="e.g. Casablanca, Maarif" />
            </div>

            <div class="input-group">
              <label>Tariffs (DH/Hour)</label>
              <input v-model="parkingForm.tariffs" placeholder="10" />
            </div>

            <div class="input-group">
              <label>Total Capacity</label>
              <input v-model="parkingForm.capacity" placeholder="100" />
            </div>

            <div class="input-group">
              <label>Parking Number</label>
              <input v-model="parkingForm.number" placeholder="06164248" />
            </div>

            <div class="input-group">
              <label>Available Spots</label>
              <input v-model="parkingForm.available_spots" placeholder="85" />
            </div>

            <div class="input-group">
              <label>Status</label>
              <select v-model="parkingForm.status" class="modern-select">
                <option value="open">open</option>
                <option value="closed">Closed</option>
              </select>
            </div>

            <div class="input-group">
              <label>Agent ID</label>
              <input type="number" v-model="parkingForm.agent_id" placeholder="12" />
            </div>
          </div>

          <div class="input-group full-width">
            <label>Image URL</label>
            <input type="text" v-model="parkingForm.image" placeholder="https://..." />
          </div>

          <div class="input-group full-width">
            <label>Description</label>
            <textarea v-model="parkingForm.description" rows="4" placeholder="Brief details about the parking..."></textarea>
          </div>

          <button type="submit" class="submit-btn">
            {{ isEdit ? 'Update Parking' : 'Create Parking' }}
          </button>
        </form>
      </div>

      <div v-else class="unauthorized-msg">
        <div class="icon">🚫</div>
        <p>You do not have administrative privileges to access this form.</p>
      </div>
    </div>
  </div>
</template>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

.form-container {
  font-family: 'Inter', sans-serif;
  background-color: #f1f5f9;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}

.form-card {
  background: white;
  width: 100%;
  max-width: 800px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  padding: 40px;
}

.form-header {
  margin-bottom: 30px;
  text-align: center;
}

h1 {
  color: #1e293b;
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 8px;
}

.subtitle {
  color: #64748b;
  font-size: 0.95rem;
}

.grid-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 20px;
}

.full-width {
  grid-column: span 2;
}

label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #475569;
}

input, textarea, .modern-select {
  padding: 12px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8fafc;
}

input:focus, textarea:focus {
  outline: none;
  border-color: #2563eb;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.submit-btn {
  width: 100%;
  padding: 14px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
  margin-top: 10px;
}

.submit-btn:hover {
  background: #1d4ed8;
}

.unauthorized-msg {
  text-align: center;
  padding: 40px;
  background: #fff1f2;
  border-radius: 12px;
  color: #be123c;
}

.unauthorized-msg .icon {
  font-size: 3rem;
  margin-bottom: 10px;
}

@media (max-width: 600px) {
  .grid-inputs {
    grid-template-columns: 1fr;
  }
  .full-width {
    grid-column: span 1;
  }
}
</style>