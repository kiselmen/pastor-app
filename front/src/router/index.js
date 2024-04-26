import { createRouter, createWebHistory } from 'vue-router'
import WelcomeView from '@/views/WelcomeView.vue'

const guest = (to, from, next) => {
  if (!localStorage.getItem("authToken")) {
    return next();
  } else {
    return next("/");
  }
};

const auth = (to, from, next) => {
  if (localStorage.getItem("authToken")) {
    return next();
  } else {
    return next("/login");
  }
};

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'welcome',
      beforeEnter: auth,
      component: WelcomeView
    },
    {
      path: '/login',
      name: 'login',
      beforeEnter: guest,
      component: () => import('@/views/LoginView.vue') 
    },
    {
      path: "/register",
      name: "Register",
      beforeEnter: guest,
      component: () => import("@/views/RegisterView.vue")
    },
    {
      path: '/home',
      name: 'home',
      beforeEnter: auth,
      component: () => import('@/views/HomeView.vue') 
    },
    {
      path: '/peoples',
      name: 'peoples',
      beforeEnter: auth,
      component: () => import('@/views/PeopleView.vue') 
    },
  ]
})

export default router
