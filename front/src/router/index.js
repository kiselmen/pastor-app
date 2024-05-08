import { createRouter, createWebHistory } from 'vue-router';
import WelcomeView from '@/views/WelcomeView.vue';
import { useUserStore } from '@/stores/userStore.js';

const guest = (to, from, next) => {
  if (!localStorage.getItem("authToken")) {
    return next();
  } else {
    return next("/");
  }
};

const auth = (to, from, next) => {
  const userStore = useUserStore();
  if (localStorage.getItem("authToken")) {
    return next();
  } else {
    return next("/login");
  }
};

const permition = (to, from, next) => {
  const userStore = useUserStore();
  if (localStorage.getItem("authToken")) {
    if (!userStore.user) {
      return next("/login");
    } else {
      if (!userStore.isPermition(0)) {
        return next("/login");
      } else {
        return next();
      }
    }
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
    {
      path: '/families',
      name: 'families',
      beforeEnter: auth,
      component: () => import('@/views/FamilyView.vue') 
    },
    {
      path: '/target-groups',
      name: 'target-groups',
      beforeEnter: permition,
      component: () => import('@/views/TargetGroupsView.vue') 
    },
    {
      path: '/services',
      name: 'services',
      beforeEnter: permition,
      component: () => import('@/views/ServicesView.vue') 
    },
    {
      path: '/levels',
      name: 'levels',
      beforeEnter: permition,
      component: () => import('@/views/LevelsView.vue') 
    },
    
  ]
})

export default router
