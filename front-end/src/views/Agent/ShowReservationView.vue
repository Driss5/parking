<script setup>
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'

const role = ref(null)
const token = localStorage.getItem('token')
const reservations = ref([])
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
        startAutoFetch()
    } else {
        console.log('You are not an agent')
    }

  } catch (error) {
    console.log('Error fetching role', error.response?.data || error)
  }
}

const fetchReservations = async () => {
  try {
    const response = await axios.get(
      'http://127.0.0.1:8000/api/agent-reservations',
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )
    reservations.value = response.data.reservations
    console.log(reservations.value)
  } catch (error) {
    console.log('Error fetching reservations', error.response?.data || error)
  }
}

const approveReservation = async (reservationId) => {
    const status = 'completed'
    try {
        const response = await axios.put(
            `http://127.0.0.1:8000/api/update-reservation-status/${reservationId}/${status}`, {},
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
            )
            await fetchReservations()
        console.log(reservationId)
        console.log(response.data.message)
  } catch (error) {
    console.log('Error fetching reservations', error.response?.data || error)
  }
}

const rejectReservation = async (reservationId) => {
    const status = 'cancelled'
    try {
        const response = await axios.put(
            `http://127.0.0.1:8000/api/update-reservation-status/${reservationId}/${status}`, {},
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
            )
            await fetchReservations()
        console.log(reservationId)
        console.log(response.data.message)
  } catch (error) {
    console.log('Error fetching reservations', error.response?.data || error)
  }
}

const startAutoFetch = () => {
  if (intervalId) return

  intervalId = setInterval(() => {
    fetchReservations()
  }, 5000)
}

onMounted(() => {
  fetchRole()
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})

function handleReturnToDashboard() {
    window.location.href = '/AgentDashboard'
}


</script>

<template>
    <div class="container">
        <div class="header">
            <div>
                <h1>Agent Partie Reservations</h1>
            </div>
            <button class="btn-delete-all">Delete All</button>
        </div>
        <button class="btn-delete-all" @click="handleReturnToDashboard">Go To Dashboard</button>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>CIN</th>
                    <th>Phone</th>
                    <th>Car</th>
                    <th>Duration (min)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="reservation in reservations" :key="reservation.id">
                    <td>{{ reservation.name }}</td>
                    <td>{{ reservation.cin }}</td>
                    <td>{{ reservation.number_phone }}</td>
                    <td>{{ reservation.car }}</td>
                    <td>{{ reservation.duration_minutes }}</td>
                    <td><span :class="['status', reservation.status.toLowerCase()]">{{ reservation.status }}</span></td>
                    <td class="actions">
                        <button class="btn-approve" @click="approveReservation(reservation.id)">Approve</button>
                        <button class="btn-reject" @click="rejectReservation(reservation.id)">Reject</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: #f5f7fa;
        min-height: 100vh;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    h1 {
        color: #1a202c;
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        padding: 0;
    }

    .role {
        color: #718096;
        font-size: 14px;
        margin: 8px 0 0 0;
    }

    .btn-delete-all {
        padding: 10px 20px;
        background: #f56565;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-delete-all:hover {
        background: #e53e3e;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 101, 101, 0.3);
    }

    table {
        width: 100%;
        background: white;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        margin-top: 25px;
    }

    thead {
        background: #edf2f7;
        border-bottom: 2px solid #e2e8f0;
    }

    th {
        padding: 16px 12px;
        text-align: left;
        color: #2d3748;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 16px 12px;
        color: #4a5568;
        font-size: 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    tbody tr:hover {
        background: #f7fafc;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status.approved {
        background: #d1fae5;
        color: #065f46;
    }

    .status.rejected {
        background: #fee2e2;
        color: #7f1d1d;
    }

    .actions {
        display: flex;
        gap: 8px;
    }

    .btn-approve,
    .btn-reject {
        padding: 8px 14px;
        border: none;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-approve {
        background: #48bb78;
        color: white;
    }

    .btn-approve:hover {
        background: #38a169;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(72, 187, 120, 0.2);
    }

    .btn-reject {
        background: #fc8181;
        color: white;
    }

    .btn-reject:hover {
        background: #f56565;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(245, 101, 101, 0.2);
    }

    @media (max-width: 768px) {
        .container {
            padding: 16px;
        }

        .header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 12px 8px;
        }

        .actions {
            flex-direction: column;
        }

        .btn-approve,
        .btn-reject {
            width: 100%;
        }
    }
</style>
