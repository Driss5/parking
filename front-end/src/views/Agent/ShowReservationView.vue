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


</script>

<template>
    <h1>Agent Partie, Page Show Reservation</h1>
    {{ role }}
    <table border="1">
        <tr>
            <th>name</th>
            <th>cin</th>
            <th>number phone</th>
            <th>car</th>
            <th>duration minute</th>
            <th>status</th>
            <th>Actions</th>
        </tr>
        <tr v-for="reservation in reservations" :key="reservation.id">
            <td>{{ reservation.name }}</td>
            <td>{{ reservation.cin }}</td>
            <td>{{ reservation.number_phone }}</td>
            <td>{{ reservation.car }}</td>
            <td>{{ reservation.duration_minutes }}</td>
            <td>{{ reservation.status }}</td>
            <td>
                <button @click="approveReservation(reservation.id)">Approve</button>
                <button @click="rejectReservation(reservation.id)">Reject</button>
            </td>
        </tr>
    </table>
</template>

<style scoped>
    h1 {
        color: red;
    }
</style>