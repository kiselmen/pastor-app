import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import axios from 'axios';

window.axios = axios;
axios.defaults.headers.common = {
  ['X-Requested-With']: 'XMLHttpRequest',
  "Content-Type": "application/json",
  Accept: "application/json"
};
axios.defaults.baseURL = 'http://pastor.by/';
// axios.defaults.baseURL = 'http://127.0.0.1:8000';

if (localStorage.getItem('token')) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
}
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            axios.defaults.headers.common['Authorization'] = 'Bearer';
            router.push({ name: 'login' });
        }
        return Promise.reject(error);
    }
);

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
