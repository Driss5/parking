<script setup>
import { ref } from 'vue';
import axios from 'axios';

const myForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmed: '',
    cin: '',
    city: '',
    number_phone: ''
})

const errorMessage = ref('');
const isRegistered = ref(false);

const handleRegistration = async () => {

  try {
    const apiEndpoint = 'http://127.0.0.1:8000/api/register'; 

    const response = await axios.post(apiEndpoint, {
        name: myForm.value.name,
        email: myForm.value.email,
        password: myForm.value.password,
        password_confirmation: myForm.value.password_confirmed,
        cin: myForm.value.cin,
        city: myForm.value.city,
        number_phone: myForm.value.number_phone
    });

    if (response.status === 201) {
        localStorage.setItem('token', response.data.access_token)
        isRegistered.value = true;
        console.log('Registration successful:', response.data);
    }
  } catch (error) {
    errorMessage.value = 'Registration failed: ';
    console.error('Registration error:', error);
  }
};

const token = localStorage.getItem('token')
console.log(token)

</script>

<template>
    <h1>Register Page</h1>
    <div>
        <form @submit.prevent="handleRegistration">
            <div>
                <label>Name</label>
                <input v-model="myForm.name" required/>
            </div>
            <div>
                <label>email</label>
                <input type="email" v-model="myForm.email" required />
            </div>
            <div>
                <label>password</label>
                <input type="password" v-model="myForm.password" required/>
            </div>
            <div>
                <label>confirm password</label>
                <input type="password" v-model="myForm.password_confirmed" required />
            </div>
            <div>
                <label>cin</label>
                <input type="text" v-model="myForm.cin" required/>
            </div>
            <div>
                <label>city</label>
                <input type="text" v-model="myForm.city" required />
            </div>
            <div>
                <label>number phone</label>
                <input type="text" v-model="myForm.number_phone" required/>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>

    <div>
        <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
        <p v-if="isRegistered" style="color: green;">Registration successful!</p>
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
} 
</style>