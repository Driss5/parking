<script setup>
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'

const role = ref(null)
const token = localStorage.getItem('token')
let dashboardData = ref({})
let intervalId = null

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

    if (role.value == 'agent') {
        await fetchReservations()
    } else {
        console.log('You are not an agent')
    }

  } catch (error) {
    console.log('Error fetching role', error.response?.data || error)
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
  fetchAgentDashboard()
  startDashboardRefresh()
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})

// onMounted(() => {
//   fetchAgentDashboard()
// })

</script>

<template>
    <h1>Agent Partie, Page Dashboard</h1>
    {{ role }}
    <table border="1">
        <tr>
            <th>Total Reservations</th>
            <th>Pending Reservations</th>
            <th>Completed reservations</th>
            <th>Available Spots</th>
            <th>Revenue Day</th>
        </tr>
        <tr>
            <td>{{ dashboardData.total_reservations }}</td>
            <td>{{ dashboardData.pending_reservations }}</td>
            <td>{{ dashboardData.completed_reservations }}</td>
            <td>{{ dashboardData.available_spots }}</td>
            <td>{{ dashboardData.completed_reservations * 5 }}</td>
        </tr>
    </table>
</template>

<style scoped>
    h1 {
        color: red;
    }
</style>