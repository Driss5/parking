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
    <h1>Agent Partie, Page Show Reservation</h1>
    {{ role }}
    <button @click="createParking">Create Parking</button>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Location</th>
            <th>tarrifs</th>
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
            <td>
                <button @click="editParking(parking.id)">Edit</button>
                <button @click="deleteParking(parking.id)">Delete</button>
            </td>
        </tr>
    </table>

    <button @click="router.push({ name: 'AddUserAgent' })">Add Agent</button>
</template>

<style scoped>
    h1 {
        color: red;
    }
</style>