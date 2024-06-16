import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useMsgStore } from '@/stores/msgStore';
import axios from 'axios';

export const usePeopleStore = defineStore('peopleStore', () => {

  const msgStore = useMsgStore();
  const peoples = ref([]);
  const loader = ref(false);
  const errors = ref({});

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const getAllPeople = async () => {
    loader.value = true;
    try {
      const response = await axios.get('/api/peoples');
      peoples.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
      msgStore.addMessage({name: error.message, icon: 'error'});
    }
    loader.value = false;
  };

  const addNewPersone = async (personeData) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('/api/peoples', personeData);
      peoples.value = [...peoples.value, response.data].sort((a ,b) => a.id - b.id);
      msgStore.addMessage({name: 'Прихожанин: "' + response.data.name + ' ' + response.data.first_name + '", добавлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const editPersone = async (personeData) => {
    loader.value = true;
    try {
      errors.value = {};
      const id = Number(personeData.get('id'));
      const response = await axios.post('/api/peoples/' + id + '/update', personeData);
      const newPeoples = peoples.value.filter(item => item.id !== id);
      peoples.value = [...newPeoples, response.data].sort((a ,b) => a.id - b.id)
      msgStore.addMessage({name: 'Прихожанин: "' + response.data.name + ' ' + response.data.first_name + '", обновлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const updateServices = async (id, pservices) => {
    loader.value = true;
    const persone = peoples.value.filter(item => item.id === id)[0];
    for (let i = 0; i < pservices.length; i++) {
      let isNotFind = true;
      for (let j = 0; j < persone.pservice.length; j++) {
        if(pservices[i].service_id === persone.pservice[j].service_id) isNotFind = false;
      };
      if (isNotFind) {
        try {
          console.log('add service ', pservices[i]);
          const response = await axios.post('/api/pservices/', pservices[i]);
          msgStore.addMessage({name: 'Вид служения: "' + response.data.name + ' ' + response.data.first_name + '", добавлен.', icon: 'done'});
          persone.pservice = [...persone.pservice, response.data];
        } catch (error) {
          console.log(error);
          msgStore.addMessage({name: error.message, icon: 'error'});
        }
      }
    };
    
    const oldServices = [];
    let itemsToDelete = [];
    for (let i = 0; i < persone.pservice.length; i++) {
      let isNotFind = true;
      for (let j = 0; j < pservices.length; j++) {
        if(persone.pservice[i].service_id === pservices[j].service_id) isNotFind = false;
      };
      if (isNotFind) {
        itemsToDelete.push(persone.pservice[i].id);
        oldServices.push(persone.pservice[i]);
      }
    }
    if (itemsToDelete.length) {
      try {
        console.log('delete services ', itemsToDelete);
        await axios.post('/api/pservices/delete', {ids: itemsToDelete});
        let filteredServices = [...persone.pservice];
        oldServices.forEach(service => {
          filteredServices = filteredServices.filter(item => item.service_id !== service.service_id);
        })
        persone.pservice = [...filteredServices];
        msgStore.addMessage({name: 'Виды служения удалены.', icon: 'done'});
      } catch (error) {
        console.log(error);
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }

  };

  const clearErrorsState = () => {
    errors.value = {}
  };

  return { 
    peoples,
    errors,
    totalCountErrors,
    getAllPeople,
    addNewPersone,
    editPersone,
    updateServices,
    clearErrorsState,
  }  
})