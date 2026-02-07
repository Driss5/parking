<script setup>
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const role = ref(null)
const token = localStorage.getItem('token')
const parkings = ref([])
const router = useRouter()

const fetchRole = async () => {
  if (!token) {
    console.log('No token found')
    return
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

    role.value = response.data.user.role
    console.log('ROLE:', role.value)

    if (role.value == 'admin') {
        await fetchParkings()
        console.log('You are an admin')
    } else {
        console.log('You are not an admin')
    }

  } catch (error) {
    console.log('Error fetching role', error.response?.data || error)
  }
}

const fetchParkings = async () => {
  try {
    const response = await axios.get(
      'http://127.0.0.1:8000/api/parking-admin',
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )
    parkings.value = response.data
  } catch (error) {
    console.log('Error fetching parkings', error.response?.data || error)
  }
}

const deleteParking = async (parkingId) => {
    try {
        const response = await axios.delete(
            `http://127.0.0.1:8000/api/delete-parking/${parkingId}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
            )
            await fetchParkings()
            console.log(response.data.message)
  } catch (error) {
    console.log('Error deleting parking', error.response?.data || error)
  }
}

const editParking = (parkingId) => {
  router.push({ name: 'AddOrUpdateParking', params: { id: parkingId } })
}
const createParking = () => {
  router.push({ name: 'AddOrUpdateParking' })
}

onMounted(() => {
  fetchRole()
})
</script>

<template>
  <div class="dashboard-container">
    <header class="table-header">
      <div class="title-group">
        <h1>Parking Management</h1>
        <span class="role-badge">{{ role }}</span>
      </div>
      <div class="action-buttons">
        <button class="btn btn-primary" @click="createParking">
          <span class="icon">+</span> Create Parking
        </button>
        <button class="btn btn-secondary" @click="router.push({ name: 'AddUserAgent' })">
          Add Agent
        </button>
      </div>
    </header>

    <div class="table-wrapper">
      <table class="modern-table">
        <thead>
          <tr>
            <th>Info</th>
            <th>Location</th>
            <th>Pricing/Cap</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="parking in parkings" :key="parking.id">
            <td>
              <div class="parking-info">
                <div>
                  <div class="name">{{ parking.name }}</div>
                  <div class="subtext">#{{ parking.number }}</div>
                </div>
              </div>
            </td>
            <td>{{ parking.location }}</td>
            <td>
              <div class="pricing"><strong>{{ parking.tariffs }} DH</strong></div>
              <div class="spots">{{ parking.available_spots }} / {{ parking.capacity }} Spots</div>
            </td>
            <td class="desc-cell">{{ parking.description }}</td>
            <td>
              <span :class="['status-pill', parking.status]">
                {{ parking.status }}
              </span>
            </td>
            <td class="actions">
              <button class="btn-icon edit" @click="editParking(parking.id)" title="Edit">✏️</button>
              <button class="btn-icon delete" @click="deleteParking(parking.id)" title="Delete">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

.dashboard-container {
  font-family: 'Inter', sans-serif;
  padding: 2rem;
  background-color: #f8fafc;
  min-height: 80vh;
  color: #1e293b;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

h1 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
  color: #0f172a;
}

.role-badge {
  background: #e2e8f0;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  color: #64748b;
  text-transform: uppercase;
}

/* Buttons */
.btn {
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  margin-left: 10px;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-secondary {
  background: white;
  border: 1px solid #e2e8f0;
  color: #475569;
}

/* Table Card */
.table-wrapper {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.modern-table th {
  background: #f1f5f9;
  padding: 16px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
}

.modern-table td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.parking-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.parking-img img {
  width: 45px;
  height: 45px;
  border-radius: 8px;
  object-fit: cover;
}

.name {
  font-weight: 600;
  color: #1e293b;
}

.subtext {
  font-size: 0.75rem;
  color: #94a3b8;
}

.status-pill {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-pill.available { background: #dcfce7; color: #166534; }
.status-pill.full { background: #fee2e2; color: #991b1b; }

.btn-icon {
  background: none;
  border: none;
  font-size: 1.1rem;
  cursor: pointer;
  padding: 5px;
  transition: transform 0.1s;
}

.btn-icon:hover {
  transform: scale(1.2);
}

.desc-cell {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #64748b;
}
</style>