<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const user = ref(null)
const reservations = ref([])
let message = ref('');
let intervalId = null

const fetchReservations = async () => {

    const token = localStorage.getItem('token')

    if (!token) {
        console.log('siir a sa7bii tl3aab')
        message.value = 'siir a saa7bii tl3aab';
    } else {
        message.value = 'waaa siiirr dir chi reservation';
    }

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
    reservations.value = response.data.reservations

    console.log(user.value)
    console.log(reservations.value)

  } catch (error) {
    console.log('Error fetching reservations', error.response?.data || error)
  }
}

// onMounted(() => {
//   fetchReservations()
// })

onMounted(() => {
  fetchReservations()
  intervalId = setInterval(fetchReservations, 10000)
})

onUnmounted(() => {
  clearInterval(intervalId)
})
</script>

<template>
  <div class="profile-container">
    <div class="profile-header">
      <h1>Profile Page</h1>
    </div>
    
    <div v-if="user" class="user-card">
      <div class="avatar">
        <span>{{ user.name?.charAt(0)?.toUpperCase() || 'U' }}</span>
      </div>
      <div class="user-info">
        <h2>Welcome, {{ user.name }}</h2>
        <p class="cin-badge">
          <span class="label">Carte Nationale</span>
          <span class="value">{{ user.cin }}</span>
        </p>
      </div>
    </div>

    <div v-if="reservations.length" class="reservations-section">
      <h3>Your Reservations</h3>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Parking</th>
              <th>Car</th>
              <th>CIN</th>
              <th>Duration</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="reservation in reservations" :key="reservation.id">
              <td>{{ reservation.parking.name }}</td>
              <td>{{ reservation.car }}</td>
              <td>{{ reservation.cin }}</td>
              <td>{{ reservation.duration_minutes }} min</td>
              <td>
                <span :class="['status-badge', reservation.status?.toLowerCase()]">
                  {{ reservation.status }}
                </span>
              </td>
              <td>{{ reservation.created_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">🚗</div>
      <p>{{ message }}</p>
    </div>
  </div>
</template>

<style scoped>
.profile-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.profile-header h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 2rem;
  border-bottom: 3px solid #3b82f6;
  padding-bottom: 0.75rem;
  display: inline-block;
}

.user-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  padding: 2rem;
  border-radius: 16px;
  margin-bottom: 2rem;
  box-shadow: 0 10px 40px  rgba(16, 93, 218, 0.3);
}

.avatar {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  color: white;
  border: 3px solid rgba(255, 255, 255, 0.4);
}

.user-info h2 {
  color: white;
  font-size: 1.5rem;
  margin: 0 0 0.5rem 0;
}

.cin-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.15);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  margin: 0;
}

.cin-badge .label {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.85rem;
}

.cin-badge .value {
  color: white;
  font-weight: 600;
}

.reservations-section h3 {
  color: #1e293b;
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.table-wrapper {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f8fafc;
}

th {
  text-align: left;
  padding: 1rem;
  font-weight: 600;
  color: #64748b;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 2px solid #e2e8f0;
}

td {
  padding: 1rem;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
}

tbody tr:hover {
  background: #f8fafc;
}

tbody tr:last-child td {
  border-bottom: none;
}

.status-badge {
  display: inline-block;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.confirmed,
.status-badge.active {
  background: #d1fae5;
  color: #059669;
}

.status-badge.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge.completed {
  background: #e0e7ff;
  color: #2d2a6c;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: #f8fafc;
  border-radius: 16px;
  border: 2px dashed #cbd5e1;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state p {
  color: #64748b;
  font-size: 1.1rem;
  margin: 0;
}

@media (max-width: 768px) {
  .profile-container {
    padding: 1rem;
  }
  
  .user-card {
    flex-direction: column;
    text-align: center;
  }
  
  .table-wrapper {
    overflow-x: auto;
  }
  
  th, td {
    padding: 0.75rem 0.5rem;
    font-size: 0.9rem;
  }
}
</style>