<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'

const myForm = ref({
    email: '',
    password: '',
})

const router = useRouter()
const errorMessage = ref('');
const isLogined = ref(false);
const isLoading = ref(false);

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  
  try {
    const apiEndpoint = 'http://127.0.0.1:8000/api/login'; 

    const response = await axios.post(apiEndpoint, {
        email: myForm.value.email,
        password: myForm.value.password,
    });

    if (response.status === 200) {
        localStorage.setItem('token', response.data.access_token)
        isLogined.value = true;
        router.push('/')
        console.log('Login successful:', response.data);
    }
  } catch (error) {
    errorMessage.value = 'Email or password incorrect.';
    console.error('Login error:', error);
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
        <h1>Welcome Back</h1>
        <p>Please enter your details to sign in</p>
      </div>

      <form @submit.prevent="handleLogin" class="card-body">
        <div class="input-field">
          <label>Email Address</label>
          <input 
            type="email" 
            v-model="myForm.email" 
            placeholder="name@example.com" 
            required 
          />
        </div>

        <div class="input-field">
          <label>Password</label>
          <input 
            type="password" 
            v-model="myForm.password" 
            placeholder="••••••••" 
            required 
          />
        </div>

        <button type="submit" :disabled="isLoading" class="login-btn">
          <span v-if="!isLoading">Log In</span>
          <div v-else class="spinner"></div>
        </button>
      </form>

      <div class="card-footer">
        <Transition name="fade">
          <p v-if="errorMessage" class="alert error">{{ errorMessage }}</p>
        </Transition>
        <Transition name="fade">
          <p v-if="isLogined" class="alert success">Login successful! Redirecting...</p>
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
  background-color: #ffffffff; /* خلفية هادئة */
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 20px;
}

.auth-card {
  width: 100%;
  max-width: 400px;
  background: #ffffff;
  border-radius: 24px;
  padding: 40px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.49), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  border: 1px solid #f1f5f9;
}

.card-header {
  text-align: center;
  margin-bottom: 32px;
}

/* الـ Logo الـ m-cadre اللي طلبتي */
.app-logo {
  width: 52px;
  height: 52px;
  margin: 0 auto 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #2e00adff; /* الكادر الأسود */
  border-radius: 12px;
  font-size: 26px;
  font-weight: 800;
  color: #2e00adff;
  transition: all 0.3s ease;
}

/* تأثير عند تمرير الفأرة على الكارط */
.auth-card:hover .app-logo {
  background-color: #2e00adff;
  color: #fff;
  transform: translateY(-2px);
}

.card-header h1 {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 8px;
}

.card-header p {
  font-size: 14px;
  color: #64748b;
}

.card-body {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.input-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.input-field label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}

input {
  padding: 12px 16px;
  border-radius: 12px;
  border: 1.5px solid #e2e8f0;
  font-size: 15px;
  transition: all 0.2s;
  background-color: #fcfcfd;
}

input:focus {
  outline: none;
  border-color: #000;
  box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.05);
  background-color: #fff;
}

.login-btn {
  background-color: #2e00adff;
  color: #fff;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 8px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.login-btn:hover:not(:disabled) {
  opacity: 0.85;
  transform: translateY(-1px);
}

.login-btn:active {
  transform: scale(0.98);
}

.login-btn:disabled {
  background-color: #64748b;
  cursor: not-allowed;
}

/* Spinner Animation */
.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.card-footer {
  margin-top: 24px;
  min-height: 45px;
}

.alert {
  padding: 12px;
  border-radius: 10px;
  font-size: 13px;
  text-align: center;
  font-weight: 500;
}

.error {
  background-color: #fef2f2;
  color: #dc2626;
  border: 1px solid #fee2e2;
}

.success {
  background-color: #f0fdf4;
  color: #16a34a;
  border: 1px solid #dcfce7;
}

/* Smooth Transition */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>