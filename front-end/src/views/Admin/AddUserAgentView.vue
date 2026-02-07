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
  <div class="agent-page">
    <div class="glass-card">
      <header class="form-header">
        <div class="icon-circle">👤</div>
        <h1>Create New Agent</h1>
        <p>Register a new agent to manage parking operations</p>
      </header>

      <div v-if="role === 'admin'" class="form-wrapper">
        <form @submit.prevent="handleRegistrationAgent" class="agent-form">
          <div class="input-grid">
            <div class="form-control">
              <label>Full Name</label>
              <input v-model="myForm.name" placeholder="John Doe" required />
            </div>

            <div class="form-control">
              <label>Email Address</label>
              <input type="email" v-model="myForm.email" placeholder="john@example.com" required />
            </div>

            <div class="form-control">
              <label>Password</label>
              <input type="password" v-model="myForm.password" placeholder="••••••••" required />
            </div>

            <div class="form-control">
              <label>Confirm Password</label>
              <input type="password" v-model="myForm.password_confirmed" placeholder="••••••••" required />
            </div>

            <div class="form-control">
              <label>CIN</label>
              <input type="text" v-model="myForm.cin" placeholder="AB123456" required />
            </div>

            <div class="form-control">
              <label>City</label>
              <input type="text" v-model="myForm.city" placeholder="Casablanca" required />
            </div>

            <div class="form-control full-width">
              <label>Phone Number</label>
              <input type="text" v-model="myForm.number_phone" placeholder="+212 600 000 000" required />
            </div>
          </div>

          <div class="messages">
            <transition name="fade">
              <div v-if="errorMessage" class="alert error">
                <span>⚠️</span> {{ errorMessage }}
              </div>
            </transition>
            <transition name="fade">
              <div v-if="isRegistered" class="alert success">
                <span>✅</span> Agent Added successfully!
              </div>
            </transition>
          </div>

          <button type="submit" class="submit-btn">
            Register Agent
          </button>
        </form>
      </div>

      <div v-else class="unauthorized">
        <div class="lock-icon">🔒</div>
        <p>Access Denied. Admins only.</p>
        <span class="badge">Current Role: {{ role }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

.agent-page {
  font-family: 'Plus Jakarta Sans', sans-serif;
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
}

.glass-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  width: 100%;
  max-width: 700px;
  border-radius: 24px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
  padding: 40px;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.form-header {
  text-align: center;
  margin-bottom: 35px;
}

.icon-circle {
  width: 60px;
  height: 60px;
  background: #eff6ff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin: 0 auto 15px;
}

h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.form-header p {
  color: #64748b;
  margin-top: 8px;
}

.input-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-control {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.full-width {
  grid-column: span 2;
}

label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #475569;
  margin-left: 4px;
}

input {
  padding: 12px 16px;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  background: white;
}

input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.submit-btn {
  width: 100%;
  margin-top: 30px;
  padding: 14px;
  background: #0f172a;
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: transform 0.2s, background 0.2s;
}

.submit-btn:hover {
  background: #1e293b;
  transform: translateY(-1px);
}

.messages {
  margin-top: 20px;
}

.alert {
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.error { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

.unauthorized {
  text-align: center;
  padding: 40px 0;
}

.lock-icon { font-size: 3rem; margin-bottom: 15px; }

.badge {
  display: inline-block;
  margin-top: 15px;
  padding: 6px 12px;
  background: #f1f5f9;
  border-radius: 20px;
  font-size: 0.8rem;
  color: #64748b;
  font-weight: 600;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@media (max-width: 600px) {
  .input-grid { grid-template-columns: 1fr; }
  .full-width { grid-column: span 1; }
  .glass-card { padding: 25px; }
}
</style>