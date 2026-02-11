<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'

const myForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmed: '',
    cin: '',
    city: '',
    number_phone: ''
})

const router = useRouter()
const errorMessage = ref('');
const isRegistered = ref(false);
const isLoading = ref(false);

const handleRegistration = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  
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
        router.push('/parkings')
        console.log('Registration successful:', response.data);
    }
  } catch (error) {
    errorMessage.value = 'Registration failed. Please check your data.';
    console.error('Registration error:', error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="page-wrapper">
    <div class="auth-card">
      
      <div class="card-header">
        <div class="app-logo">P</div>
        <h1>Create Account</h1>
        <p>Join us by filling out the form below</p>
      </div>

      <form @submit.prevent="handleRegistration" class="card-body">
        
        <div class="form-grid">
          <div class="input-field">
            <label>Full Name</label>
            <input v-model="myForm.name" placeholder="John Doe" required/>
          </div>

          <div class="input-field">
            <label>Email Address</label>
            <input type="email" v-model="myForm.email" placeholder="john@example.com" required />
          </div>

          <div class="input-field">
            <label>Password</label>
            <input type="password" v-model="myForm.password" placeholder="••••••••" required/>
          </div>

          <div class="input-field">
            <label>Confirm Password</label>
            <input type="password" v-model="myForm.password_confirmed" placeholder="••••••••" required />
          </div>

          <div class="input-field">
            <label>CIN</label>
            <input type="text" v-model="myForm.cin" placeholder="AB123456" required/>
          </div>

          <div class="input-field">
            <label>City</label>
            <input type="text" v-model="myForm.city" placeholder="Oujda" required />
          </div>

          <div class="input-field full-width">
            <label>Phone Number</label>
            <input type="text" v-model="myForm.number_phone" placeholder="+212 600-000000" required/>
          </div>
        </div>

        <button type="submit" :disabled="isLoading" class="login-btn">
          <span v-if="!isLoading">Create Account</span>
          <div v-else class="spinner"></div>
        </button>
      </form>

      <div class="card-footer">
        <Transition name="fade">
          <p v-if="errorMessage" class="error-msg">{{ errorMessage }}</p>
        </Transition>
        <Transition name="fade">
          <p v-if="isRegistered" class="success-msg">Account created! Welcome.</p>
        </Transition>
      </div>
      
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

.page-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #2e00adff;
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 40px 20px;
}

.auth-card {
  width: 100%;
  max-width: 550px; /* كبرنا العرض شوية حيت كاين بزاف ديال المعلومات */
  background: #ffffff;
  border-radius: 24px;
  padding: 40px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.44);
  border: 1px solid #f1f5f9;
}

.card-header {
  text-align: center;
  margin-bottom: 32px;
}

.app-logo {
  width: 52px;
  height: 52px;
  margin: 0 auto 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #2e00adff;
  border-radius: 12px;
  font-size: 26px;
  font-weight: 800;
  color: #2e00adff;
  transition: all 0.3s ease;
}

.auth-card:hover .app-logo {
  background-color: #2e00adff;
  color: #fff;
}

.card-header h1 {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.card-header p {
  font-size: 14px;
  color: #64748b;
  margin-top: 8px;
}

/* تقسيم الخانات لـ 2 Columns */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.input-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

/* جعل رقم الهاتف يأخذ العرض كاملا */
.full-width {
  grid-column: span 2;
}

.input-field label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

input {
  padding: 10px 14px;
  border-radius: 10px;
  border: 1.5px solid #e2e8f0;
  font-size: 14px;
  transition: all 0.2s;
  background-color: #fcfcfd;
}

input:focus {
  outline: none;
  border-color: #2e00adff;
  background-color: #fff;
  box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.05);
}

.login-btn {
  width: 100%;
  background-color: #2e00adff;
  color: #fff;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 24px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.login-btn:hover:not(:disabled) {
  opacity: 0.85;
}

.login-btn:disabled {
  background-color: #94a3b8;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.card-footer {
  margin-top: 20px;
  min-height: 40px;
}

.error-msg, .success-msg {
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  text-align: center;
}

.error-msg { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
.success-msg { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Responsive: يرجع عمود واحد في التلفونات */
@media (max-width: 500px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  .full-width {
    grid-column: span 1;
  }
}
</style>