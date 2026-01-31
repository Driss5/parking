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
  <h1>{{ isEdit ? 'Update Parking' : 'Create Parking' }}</h1>

  <div v-if="role === 'admin'">
    <form @submit.prevent="submitParking">
      <input v-model="parkingForm.name" placeholder="Name" />
      <input v-model="parkingForm.location" placeholder="Location" />
      <input v-model="parkingForm.tariffs" placeholder="Tariffs" />
      <input v-model="parkingForm.capacity" placeholder="Capacity" />
      <input v-model="parkingForm.number" placeholder="Number" />
      <input v-model="parkingForm.available_spots" placeholder="Availabele spots" />
      <input v-model="parkingForm.status" placeholder="Status" />
      <input type="number" v-model="parkingForm.agent_id" placeholder="Agent ID" />
      <input type="text" v-model="parkingForm.image" placeholder="image" />
      <textarea v-model="parkingForm.description" placeholder="Description"></textarea>

      <button type="submit">
        {{ isEdit ? 'Update' : 'Create' }}
      </button>
    </form>
  </div>

  <div v-else>
    <p>You are not admin</p>
  </div>
</template>


<style scoped>
    h1 {
        color: red;
    }
</style>