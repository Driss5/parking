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
                <label>Email</label>
                <input type="email" v-model="myForm.email" required />
            </div>
            <div>
                <label>Password</label>
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
  text-align: center;
  margin-bottom: 20px;
  color: #1e3a5f;
}

div {
  display: flex;
  justify-content: center;
}

form {
  background: #f5f7fa;
  padding: 30px;
  border-radius: 12px;
  width: 100%;
  max-width: 380px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

form > div {
  display: flex;
  flex-direction: column;
  margin-bottom: 15px;
}

label {
  font-size: 14px;
  margin-bottom: 6px;
  color: #555;
}

input {
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  transition: 0.3s;
}

input:focus {
  outline: none;
  border-color: #1e88e5;
  box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.2);
}

button {
  margin-top: 10px;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: #1e88e5;
  color: white;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  background: #156
} </style>
