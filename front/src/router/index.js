import { createRouter, createWebHistory } from 'vue-router';
// import WelcomeView from '@/views/WelcomeView.vue';
import { useUserStore } from '@/stores/userStore.js';

const guest = (to, from, next) => {
  if (!localStorage.getItem("authToken")) {
    console.log('No token');
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
      // component: WelcomeView
      component: () => import('@/views/WelcomeView.vue') 
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
      component: () => import('@/views/RegisterView.vue')
    },
    // {
    //   path: '/home',
    //   name: 'home',
    //   beforeEnter: auth,
    //   component: () => import('@/views/HomeView.vue') 
    // },
    {
      path: '/peoples',
      name: 'peoples',
      beforeEnter: auth,
      component: () => import('@/views/PeoplesListView.vue') 
    },
    {
      path: '/people/:id',
      name: 'people',
      props: true,
      beforeEnter: auth,
      component: () => import('@/views/PeopleItemView.vue') 
    },
    {
      path: '/families',
      name: 'families',
      beforeEnter: auth,
      component: () => import('@/views/FamiliesListView.vue') 
    },
    {
      path: '/family/:id',
      name: 'family',
      props: true,
      beforeEnter: auth,
      component: () => import('@/views/FamilyItemView.vue') 
    },
    {
      path: '/prihods',
      name: 'prihods',
      beforeEnter: auth,
      component: () => import('@/views/PrihodsListView.vue') 
    },
    {
      path: '/prihod/:id',
      name: 'prihod',
      props: true,
      beforeEnter: auth,
      component: () => import('@/views/PrihodItemView.vue') 
    },
    {
      path: '/target-groups',
      name: 'target-groups',
      beforeEnter: permition,
      component: () => import('@/views/TargetGroupsListView.vue') 
    },
    {
      path: '/services',
      name: 'services',
      beforeEnter: permition,
      component: () => import('@/views/ServicesListView.vue') 
    },
    {
      path: '/levels',
      name: 'levels',
      beforeEnter: permition,
      component: () => import('@/views/LevelsListView.vue') 
    },
    {
      path: '/relations',
      name: 'relations',
      beforeEnter: permition,
      component: () => import('@/views/RelationsListView.vue') 
    },
    {
      path: '/report-borns',
      name: 'borns',
      beforeEnter: permition,
      component: () => import('@/views/ReportBornView.vue') 
    },    
  ]
})

export default router
