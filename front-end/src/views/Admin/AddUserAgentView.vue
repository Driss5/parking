<script setup>
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const role = ref(null)
const token = localStorage.getItem('token')
const errorMessage = ref('');
const isRegistered = ref(false);

const myForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmed: '',
    cin: '',
    city: '',
    number_phone: ''
})

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
        console.log('You are an admin')
    } else {
        console.log('You are not an admin')
    }

  } catch (error) {
    console.log('Error fetching role', error.response?.data || error)
  }
}

const handleRegistrationAgent = async () => {

  try {
    const apiEndpoint = 'http://127.0.0.1:8000/api/create-user'; 

    const response = await axios.post(apiEndpoint, {
        name: myForm.value.name,
        email: myForm.value.email,
        password: myForm.value.password,
        password_confirmation: myForm.value.password_confirmed,
        cin: myForm.value.cin,
        city: myForm.value.city,
        number_phone: myForm.value.number_phone
    },
    {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
);

    if (response.status === 201) {
        isRegistered.value = true;
        console.log('Agent Added:', response.data);
    }
  } catch (error) {
    errorMessage.value = 'Registration failed: ';
    console.error('Registration error:', error);
  }
};


onMounted(() => {
  fetchRole()
})
</script>

<template>
    <h1>Create Agent</h1>
        <div v-if="role === 'admin'">
            <form @submit.prevent="handleRegistrationAgent">
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
                <button type="submit">Add Agent</button>
            </form>
        </div>
        <div v-else>
            <p>You are not admin</p>
        </div>
            <div>
                <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
                <p v-if="isRegistered" style="color: green;">Agent Added successful!</p>
            </div>
    {{ role }}
</template>

<style scoped>
    h1 {
        color: red;
    }
</style>