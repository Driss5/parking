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
        color: red;
    }
</style>