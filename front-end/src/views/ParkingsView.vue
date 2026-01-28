<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const parkings = ref([])

const handleShowParking = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      console.log('No token found. Please log in first.')
      return
    }

    const response = await axios.get(
      'http://127.0.0.1:8000/api/parkings',
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    parkings.value = response.data
    console.log('this is data parkings:', parkings.value)

  } catch (error) {
    console.error('Error fetching parkings:', error.response?.data || error)
  }
}

onMounted(() => {
  handleShowParking()
})
</script>


<template>
  <h1>Parkings</h1>
  <table border="1">
    <tr>
        <th>Name</th>
        <th>location</th>
        <th>tariffs</th>
        <th>capacity</th>
        <th>available spots</th>
        <th>image</th>
        <th>description</th>
        <th>number</th>
        <th>status</th>
        <th>Actions</th>
    </tr>
    <tr v-for="parking in parkings" :key="parking.id">
        <td>{{ parking.name }}</td>
        <td>{{ parking.location }}</td>
        <td>{{ parking.tariffs }}</td>
        <td>{{ parking.capacity }}</td>
        <td>{{ parking.available_spots }}</td>
        <td>{{ parking.image }}</td>
        <td>{{ parking.description }}</td>
        <td>{{ parking.number }}</td>
        <td>{{ parking.status }}</td>
        <td><button>Reserver</button></td>
    </tr>
  </table>
</template>

<style scoped>
h1 {
  color: red;
}
</style>