// import './assets/base.scss';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from '@/App.vue';
import router from '@/router';
import axios from 'axios';

import clickOutside from '@/directives/clickOutside.js';

window.axios = axios;
axios.defaults.headers.common = {
  ['X-Requested-With']: 'XMLHttpRequest',
  // "Content-Type": "application/json",
  Accept: "application/json"
};
axios.defaults.baseURL = 'http://localhost:8000';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('authToken');
      router.push({ name: 'login' });
    }
    return Promise.reject(error);
  }
);

const app = createApp(App);

app.directive('click-outside', clickOutside);
app.use(createPinia());
app.use(router);

app.mount('#app');
