import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useMsgStore } from '@/stores/msgStore';
import axios from 'axios';

export const usePrihodStore = defineStore('prihodStore', () => {

  const msgStore = useMsgStore();
  const prihods = ref([]);
  const loader = ref(false);
  const errors = ref({});

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const getPrihods = async () => {
    loader.value = true;
    try {
      const response = await axios.get('api/prihods');
      prihods.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
      if (error.response?.status !== 401) {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }  
    }
    loader.value = false;
  }; // with permitions

  const addNewPrihod = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/prihods', data);
      prihods.value = [...prihods.value, response.data].sort((a ,b) => a.id - b.id);
      msgStore.addMessage({name: 'Участок: "' + response.data.name + '", добавлен.', icon: 'done'});
    } catch (error) {
      console.log(error);
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else if (error.response?.status === 403) {
        msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else if (error.response?.status === 401) {
        // msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  }; // with permitions

  const editPrihod = async (prihodData) => {
    console.log('editPrihod');
    loader.value = true;
    try {
      errors.value = {};
      const id = Number(prihodData.get('id'));
      const response = await axios.post('api/prihods/' + id + '/update', prihodData);
      const newPersons = prihods.value.filter(item => item.id !== id);
      prihods.value = [...newPersons, response.data].sort((a ,b) => a.id - b.id)
      msgStore.addMessage({name: 'Участок: "' + response.data.name + '", изменен.', icon: 'done'});
    } catch (error) {
      console.log(error);
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else if (error.response?.status === 403) {
        msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else if (error.response?.status === 401) {
        // msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  }; // with permitions

  const clearErrorsState = () => {
    errors.value = {}
  };

  // const checkIsPresentPrihod = (prihodID) => {
  //   console.log('prihods.value ', prihods.value);
  //   return prihods.value.filter(item => item.id == prihodID).length;
  // }

  return { 
    prihods,
    errors,
    totalCountErrors,
    getPrihods,
    addNewPrihod,
    editPrihod,
    clearErrorsState,
    // checkIsPresentPrihod,
  };
})