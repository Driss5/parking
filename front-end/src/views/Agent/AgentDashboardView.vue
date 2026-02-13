<script setup>
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'

const role = ref(null)
const token = localStorage.getItem('token')
let dashboardData = ref({})
let intervalId = null

const userId = ref(null)
const parkingTarif = ref(null)

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
    userId.value = response.data.user.id
    // console.log('ROLE:', role.value)
    // console.log('User:', userId.value)

    // if (role.value == 'agent') {
    //     await fetchParking()
    //     await fetchAgentDashboard()
    // }

    if (role.value == 'agent') {
        // await fetchReservations()
        await fetchParking()
        await fetchAgentDashboard()
        startDashboardRefresh()
    } else {
        console.log('You are not an agent')
    }

  } catch (error) {
    console.log('Error fetching role', error.response?.data || error)
  }
}

const fetchParking = async () => {
  try {
    const response = await axios.get(
      'http://127.0.0.1:8000/api/parkings',
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )
    const parkingFind = response.data.find((data)=> {
        return data.agent_id == userId.value
    })
    // dashboardData.value = response.data
    console.log('user : ', userId.value)
    console.log(parkingFind)
    parkingTarif.value = parkingFind.tariffs
    console.log('parking tarif', parkingTarif.value)
  } catch (error) {
    console.log('Error fetching agent dashboard data', error.response?.data || error)
  }
}

const fetchAgentDashboard = async () => {
  try {
    const response = await axios.get(
      'http://127.0.0.1:8000/api/agent-dashboard',
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )
    dashboardData.value = response.data
    console.log(dashboardData.value)
  } catch (error) {
    console.log('Error fetching agent dashboard data', error.response?.data || error)
  }
}

const startDashboardRefresh = () => {
  if (intervalId) return

  intervalId = setInterval(() => {
    fetchAgentDashboard()
  }, 10000)
}

onMounted(() => {
//     fetchParking()
//   fetchAgentDashboard()
//   startDashboardRefresh()
fetchRole()
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})

function handleReturnToReservation() {
    window.location.href = '/ShowReservationView'
}

// onMounted(() => {
//   fetchAgentDashboard()
// })

</script>

<template>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1 class="dashboard-title">Dashboard</h1>
            <span class="user-role">Agent</span>
        </header>

        <span class="user-role" @click="handleReturnToReservation">Return To Reservations</span>
        
        <div class="table-wrapper">
            <table class="metrics-table">
                <thead>
                    <tr>
                        <th>Total Reservations</th>
                        <th>Pending Reservations</th>
                        <th>Completed Reservations</th>
                        <th>Available Spots</th>
                        <th>Revenue Day</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ dashboardData.total_reservations }}</td>
                        <td>{{ dashboardData.pending_reservations }}</td>
                        <td>{{ dashboardData.completed_reservations }}</td>
                        <td>{{ dashboardData.available_spots }}</td>
                        <td>${{ dashboardData.completed_reservations * parkingTarif }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
    .dashboard-container {
        background: linear-gradient(135deg, #0f172a 0%, #1a1f35 100%);
        min-height: 90vh;
        padding: 2rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 3rem;
        padding: 0 1rem;
    }

    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .user-role {
        font-size: 1rem;
        color: #94a3b8;
        font-weight: 500;
        background: rgba(148, 163, 184, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        cursor: pointer;
    }

    .table-wrapper {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
        padding: 0;
        margin-top: 25px;
    }

    .metrics-table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
    }

    .metrics-table thead {
        background: rgba(30, 41, 59, 0.8);
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
    }

    .metrics-table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        color: #cbd5e1;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metrics-table tbody tr {
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        transition: background-color 0.2s ease;
    }

    .metrics-table tbody tr:hover {
        background-color: rgba(148, 163, 184, 0.05);
    }

    .metrics-table td {
        padding: 1.5rem;
        color: #e2e8f0;
        font-size: 1rem;
        font-weight: 500;
    }

    .metrics-table td:first-child {
        font-weight: 600;
        color: #60a5fa;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }

        .dashboard-header {
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: flex-start;
        }

        .dashboard-title {
            font-size: 2rem;
        }

        .metrics-table th,
        .metrics-table td {
            padding: 0.75rem;
            font-size: 0.875rem;
        }

        .table-wrapper {
            overflow-x: auto;
        }
    }
</style>