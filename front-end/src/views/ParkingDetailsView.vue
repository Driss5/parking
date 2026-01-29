<script setup>
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const id = route.params.id
console.log(id)

const myForm = ref({
    name: '',
    cin: '',
    number_phone: '',
    car: '',
    duration_minutes: '',
})

const parking = ref(null)

onMounted(async () => {
  const response = await axios.get(
    `http://127.0.0.1:8000/api/parkings/${id}`,
    {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    }
  )

  console.log(response.data);

  parking.value = response.data
})


const duration_minutes = ref(0)
const updateMinutes = () => {
  const now = new Date()
  duration_minutes.value = now.getMinutes()
}
let intervalId = null
onMounted(() => {
  updateMinutes()
  intervalId = setInterval(updateMinutes, 1000)
})
onUnmounted(() => {
  clearInterval(intervalId)
})

const handleReserve = async () => {
  try {
    const apiEndpoint = 'http://127.0.0.1:8000/api/reserver-parking';

    const token = localStorage.getItem('token')
    if (!token) {
      console.log('No token found')
      return
    }

    const response = await axios.post(apiEndpoint, {
        name: myForm.value.name,
        cin: myForm.value.cin,
        number_phone: myForm.value.number_phone,
        car: myForm.value.car,
        duration_minutes: duration_minutes.value,
        parking_id: parking.value.id,
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    if (response.status === 201) {
        console.log('reservation successful')
    }
  } catch (error) {
    console.log('Reservation error')
  }
};

</script>


<template>
  <h1>One Parking</h1>

    <div v-if="parking">
            <h2>{{ parking.name }}</h2>
            <p>{{ parking.description }}</p>
            <button>Show Formulaire</button>
        <div>
            <h2>Reserver Now</h2>
            <form @submit.prevent="handleReserve">
                <input type="hidden" v-model="parking.id" />
                <div>
                    <label>Name</label>
                    <input type="text" v-model="myForm.name" required />
                </div>
                <div>
                    <label>Cin</label>
                    <input type="text" v-model="myForm.cin" required />
                </div>
                <div>
                    <label>number_phone</label>
                    <input type="text" v-model="myForm.number_phone" required />
                </div>
                <div>
                    <label>car</label>
                    <input type="text" v-model="myForm.car" required />
                </div>
                <div>
                    <label>duration_minutes</label>
                    <input type="text" v-model="duration_minutes" required />
                </div>
                <button type="submit">Reserver Now</button>
            </form>
        </div>
    </div>

</template>

<style scoped>
h1 {
  color: red;
}
</style>