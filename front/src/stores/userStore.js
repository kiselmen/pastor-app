import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { defineStore } from 'pinia';
import axios from 'axios';
import { useMenuStore } from '@/stores/menuStore';


export const useUserStore = defineStore('userStore', () => {

  const menuStore = useMenuStore();

  const user = ref(null);
  const authenticated = ref(false);
  const loader = ref(false);
  const errors = ref({});
  const router = useRouter();

  const signIn = async () => {
    try {
      const sigInResponse = await axios.get('/api/user');
      // console.log('sigInResponse ', sigInResponse);
      user.value = sigInResponse.data;
      authenticated.value = true;
      localStorage.setItem('authToken', 'authenticated');
      menuStore.setContentClasses('content');
    } catch (error) {
      // console.log(error);
      user.value = null;
      authenticated.value = false;
      localStorage.removeItem('authToken');
      router.push('login');
    }
  }

  const loginUser = async (credintails) => {
    loader.value = true;
    setErrors({});
    try {
      await axios.get('/sanctum/csrf-cookie');
      const loginResponse = await axios.post('login', credintails);
      // console.log('loginResponse ', loginResponse);
      await signIn();
      router.push('home');
    } catch(error) {
      // console.log(error);
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  };

  const logoutUser = async () => {
    loader.value = true;
    setErrors({});
    try {
      await axios.post('/logout');
    } catch(error) {
      console.log(error);
    }  
    user.value = null;
    authenticated.value = false;
    localStorage.removeItem('authToken');
    menuStore.setContentClasses('content')
    router.push('/login');
    loader.value = false;
  };

  const createNewUser = async (credintails) => {
    loader.value = true;
    setErrors({});
    try {
      const response = await axios.post('register', credintails);
      localStorage.setItem('token', response.data.token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
      await signIn();
      router.push('home');
    } catch(error) {
      // console.log(error);
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  };

  const setErrors = (value) => {
    errors.value = value;
  };

  const clearLoginState = () => {
    user.value = null;
    authenticated.value = false;
    menuStore.setContentClasses('content content-off')
    menuStore.authenticated.value = null;
    menuStore.user.value = null;
  }
  
  const isPermition = (type) => {
    if (type === 0) {
      const permition = user.value.permition.filter(item => item.type === type);
      return Boolean(permition.length);
    } else if (type === 1) {
      return false;
    } else if (type === 2) {
      return false;
    } else {
      return false;
    }
  }

  return { 
    user,
    authenticated,
    loader,
    errors,
    signIn,
    loginUser,
    logoutUser,
    createNewUser,
    setErrors,
    clearLoginState,
    isPermition,
  };
});
