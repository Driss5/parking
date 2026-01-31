import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/AuthView/RegisterView.vue'
import LoginView from '../views/AuthView/LoginView.vue'
import ParkingsView from '@/views/ParkingsView.vue'
import ParkingDetailsView from '@/views/ParkingDetailsView.vue'
import ProfileView from '@/views/ProfileView.vue'
import ShowReservationView from '@/views/Agent/ShowReservationView.vue'
import AgentDashboardView from '@/views/Agent/AgentDashboardView.vue'
import ParkingsAdminView from '@/views/Admin/ParkingsAdminView.vue'
import AddOrUpdateParkingView from '@/views/Admin/AddOrUpdateParkingView.vue'
import AddUserAgentView from '@/views/Admin/AddUserAgentView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/parkings',
      name: 'parkings',
      component: ParkingsView,
    },
    {
      path: '/ParkingDetails/:id',
      name: 'ParkingDetails',
      component: ParkingDetailsView,
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView,
    },
    {
      path: '/ShowReservationView',
      name: 'ShowReservationView',
      component: ShowReservationView,
    },
    {
      path: '/AgentDashboard',
      name: 'AgentDashboard',
      component: AgentDashboardView,
    },
    {
      path: '/ParkingsAdmin',
      name: 'ParkingsAdmin',
      component: ParkingsAdminView,
    },
    {
      path: '/AddOrUpdateParking/:id?',
      name: 'AddOrUpdateParking',
      component: AddOrUpdateParkingView,
    },
    {
      path: '/AddUserAgent',
      name: 'AddUserAgent',
      component: AddUserAgentView,
    },
  ],
})

export default router
