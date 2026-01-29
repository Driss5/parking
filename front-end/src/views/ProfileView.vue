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
    <h1>Profile Page</h1>
      <div v-if="user">
    <h2>Welcome {{ user.name }}</h2>
    <p>Carte Nationale {{ user.cin }}</p>
  </div>

  <table v-if="reservations.length" border="1">
    <thead>
      <tr>
        <th>Parking</th>
        <th>Car</th>
        <th>Duration (minutes)</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="reservation in reservations" :key="reservation.id">
        <td>{{ reservation.parking.name }}</td>
        <td>{{ reservation.car }}</td>
        <td>{{ reservation.duration_minutes }}</td>
        <td>{{ reservation.status }}</td>
        <td>{{ reservation.created_at }}</td>
      </tr>
    </tbody>
  </table>

  <p v-else>{{ message }}</p>
</template>

<style scoped>
    h1 {
        color: red;
    }
</style>