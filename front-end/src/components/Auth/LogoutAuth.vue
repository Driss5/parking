<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const logout = async () => {
  try {
    await axios.post('http://localhost:8000/api/logout', {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })

    localStorage.removeItem('token')

    router.push('/login')

    console.log('Logout success')
  } catch (error) {
    console.log('Logout error', error)
  }
}
</script>

<template>
  <button @click="logout">
    Logout
  </button>
</template>
