import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { defineStore } from 'pinia';
import axios from 'axios';
import { useMenuStore } from '@/stores/menuStore';
import { useMsgStore } from '@/stores/msgStore';
import { usePeopleStore } from '@/stores/peopleStore';

export const useUserStore = defineStore('userStore', () => {

  const menuStore = useMenuStore();
  const msgStore = useMsgStore();
  const peopleStore = usePeopleStore();

  const user = ref(null);
  const authenticated = ref(false);
  const loader = ref(false);
  const errors = ref({});
  const router = useRouter();

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const signIn = async () => {
    try {
      const sigInResponse = await axios.get('api/user');
      user.value = sigInResponse.data;
      authenticated.value = true;
      localStorage.setItem('authToken', 'authenticated');
      menuStore.setContentClasses('content');
    } catch (error) {
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
      await signIn();
      router.push('/');
    } catch(error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
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
      msgStore.addMessage({name: 'Пользователь: "' + credintails.name + '", добавлен.', icon: 'done'});
      localStorage.setItem('token', response.data.token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
      await signIn();
      router.push('/');
    } catch(error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const registerPersonAsUser = async (credintails, type) => {
    console.log('credintails ', credintails);
    loader.value = true;
    setErrors({});
    try {
      const response = await axios.post('api/user', credintails);
      if (type) {
        peopleStore.peoples = [...peopleStore.peoples, response.data].sort((a ,b) => a.id - b.id);
      } else {
        peopleStore.onePersone = response.data;
      }
      msgStore.addMessage({name: 'Пользователь: "' + credintails.name + '", добавлен.', icon: 'done'});
    } catch(error) {
      if (error.response?.status === 422) {
        console.log(error);
        errors.value = error.response?.data?.errors;
      } else if (error.response?.status === 403) {
        msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const changeUserPassword = async (credintails) => {
    console.log('credintails ', credintails);
    loader.value = true;
    setErrors({});
    try {
      const response = await axios.post('api/user/change', credintails);
      msgStore.addMessage({name: 'Пароль для: "' + credintails.name + '", изменен.', icon: 'done'});
    } catch(error) {
      if (error.response?.status === 422) {
        console.log(error);
        errors.value = error.response?.data?.errors;
      } else if (error.response?.status === 403) {
        msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const updatePersonePermitions = async (id, permitions) => {
    // console.log('update permitions ', id, permitions);
    loader.value = true;
    const permitionsInArray = peopleStore.personePermitions;
    for (let i = 0; i < permitions.length; i++) {
      let isNotFind = true;
      for (let j = 0; j < permitionsInArray.length; j++) {
        if(permitions[i].type == permitionsInArray[j].type && permitions[i].source_id == permitionsInArray[j].source_id) isNotFind = false;
      };
      if (isNotFind) {
        try {
          // console.log('add permition ', permitions[i]);
          await axios.post('api/permition', permitions[i]);
          msgStore.addMessage({name: 'Доступ: "' + permitions[i].name + ' ' + permitions[i].source_name + '", добавлен.', icon: 'done'});
          peopleStore.addNewPermitionToArray(permitions[i]);
        } catch (error) {
          if (error.response?.status === 422) {
            errors.value = error.response?.data?.errors;
          } else if (error.response?.status === 403) {
            msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
          } else {
            msgStore.addMessage({name: error.message, icon: 'error'});
          }
        }
      }
    };
    
    const oldPermitions = [];
    let itemsToDelete = [];
    for (let i = 0; i < permitionsInArray.length; i++) {
      let isNotFind = true;
      for (let j = 0; j < permitions.length; j++) {
        if(permitionsInArray[i].type == permitions[j].type && permitionsInArray[i].source_id == permitions[j].source_id) isNotFind = false;
      };
      if (isNotFind) {
        itemsToDelete.push(permitionsInArray[i].id);
        oldPermitions.push(permitionsInArray[i]);
      }
    }
    if (itemsToDelete.length) {
      try {
        // console.log('delete permitions ', itemsToDelete);
        await axios.post('api/permition/delete', { ids: itemsToDelete });
        let filteredPermitions = [...permitionsInArray];
        oldPermitions.forEach(permition => {
          filteredPermitions = filteredPermitions.filter(item => !(item.type == permition.type && item.source_id == permition.source_id));
        });
        peopleStore.setPermitionToArray(filteredPermitions);
        msgStore.addMessage({name: 'Доступы удалены.', icon: 'done'});
      } catch (error) {
        if (error.response?.status === 403) {
          msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
        } else if (error.response?.status === 403) {
          msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
        } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
        }
      }
    }
  };

  const setErrors = (value) => {
    errors.value = value;
  };

  const clearLoginState = () => {
    user.value = null;
    authenticated.value = false;
    menuStore.setContentClasses('content content-off');
    // menuStore.authenticated.value = null;
    // menuStore.user.value = null;
  };
  
  const isPermition = (type) => {
    if (type === 0) {
      const permition = user.value.permition.filter(item => item.type === type);
      return Boolean(permition.length);
    } else if (type === 1) {
      const permition = user.value.permition.filter(item => item.type === type || item.type == 0);
      return Boolean(permition.length);
    } else if (type === 2) {
      return false;
    } else {
      return false;
    }
  };

  return { 
    user,
    authenticated,
    loader,
    errors,
    totalCountErrors,
    signIn,
    loginUser,
    logoutUser,
    createNewUser,
    registerPersonAsUser,
    changeUserPassword,
    updatePersonePermitions,
    setErrors,
    clearLoginState,
    isPermition,
  };
});
