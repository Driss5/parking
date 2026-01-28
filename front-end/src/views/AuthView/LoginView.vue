<script setup>
import { ref } from 'vue';
import axios from 'axios';

const myForm = ref({
    email: '',
    password: '',
})

const errorMessage = ref('');
const isLogined = ref(false);

const handleLogin = async () => {
  try {
    const apiEndpoint = 'http://127.0.0.1:8000/api/login'; 

    const response = await axios.post(apiEndpoint, {
        email: myForm.value.email,
        password: myForm.value.password,
    });

    if (response.status === 200) {
        localStorage.setItem('token', response.data.access_token)
        isLogined.value = true;
        console.log('Login successful:', response.data);
    }
  } catch (error) {
    errorMessage.value = 'Login failed: ';
    console.error('Login error:', error);
  }
};

const token = localStorage.getItem('token')
console.log(token)

</script>

<template>
    <h1>Login Page</h1>
    <div>
        <form @submit.prevent="handleLogin">
            <div>
                <label>email</label>
                <input type="email" v-model="myForm.email" required />
            </div>
            <div>
                <label>password</label>
                <input type="password" v-model="myForm.password" required />
            </div>
            <button type="submit">Login</button>
        </form>
    </div>

    <div>
        <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
        <p v-if="isLogined" style="color: green;">Login successful!</p>
    </div>
</template>

<style scoped>
    h1 {
        color: red;
    }
</style>