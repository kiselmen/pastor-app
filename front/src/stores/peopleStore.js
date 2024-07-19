import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useMsgStore } from '@/stores/msgStore';
import { useViewStore } from '@/stores/viewStore';
import axios from 'axios';

export const usePeopleStore = defineStore('peopleStore', () => {

  const msgStore = useMsgStore();
  const viewStore = useViewStore();
  const peoples = ref([]);
  const personePermitions =ref([]);
  const loader = ref(false);
  const errors = ref({});

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const getAllPeople = async () => {
    loader.value = true;
    console.log('store prihodFilerMask ', viewStore.prihodFilerMask);
    try {
      const response = await axios.get('api/peoples?_prihod=' + viewStore.prihodFilerMask);
      peoples.value = response.data.sort((a ,b) => a.id - b.id).map(item => {
        item.plevel.sort((a, b) => new Date(a.date) - new Date(b.date));
        return item;
      });
    } catch (error) {
      console.log(error);
      msgStore.addMessage({name: error.message, icon: 'error'});
    }
    loader.value = false;
  }; // with permitions

  const addNewPersone = async (personeData) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/peoples', personeData);
      peoples.value = [...peoples.value, response.data].sort((a ,b) => a.id - b.id).map(item => {
        item.plevel.sort((a, b) => new Date(a.date) - new Date(b.date));
        return item;
      });
      msgStore.addMessage({name: 'Прихожанин: "' + response.data.name + ' ' + response.data.first_name + '", добавлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else if (error.response?.status === 403) {
        msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else {
        console.log(error);
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  }; // with permitions

  const editPersone = async (personeData) => {
    loader.value = true;
    try {
      errors.value = {};
      const id = Number(personeData.get('id'));
      const response = await axios.post('api/peoples/' + id + '/update', personeData);
      const newPeoples = peoples.value.filter(item => item.id !== id);
      peoples.value = [...newPeoples, response.data].sort((a ,b) => a.id - b.id).map(item => {
        item.plevel.sort((a, b) => new Date(a.date) - new Date(b.date));
        return item;
      });
      msgStore.addMessage({name: 'Прихожанин: "' + response.data.name + ' ' + response.data.first_name + '", обновлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else if (error.response?.status === 403) {
        msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  }; // with permitions

  const updatePersoneServices = async (id, pservices) => {
    loader.value = true;
    const persone = peoples.value.filter(item => item.id === id)[0];
    for (let i = 0; i < pservices.length; i++) {
      let isNotFind = true;
      for (let j = 0; j < persone.pservice.length; j++) {
        if(pservices[i].service_id === persone.pservice[j].service_id) isNotFind = false;
      };
      if (isNotFind) {
        try {
          // console.log('add service ', pservices[i]);
          const response = await axios.post('api/pservices', pservices[i]);
          msgStore.addMessage({name: 'Вид служения: "' + response.data.ServiceName + '", добавлен.', icon: 'done'});
          persone.pservice = [...persone.pservice, response.data];
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
        // console.log('delete services ', itemsToDelete);
        await axios.post('api/pservices/delete', { ids: itemsToDelete });
        let filteredServices = [...persone.pservice];
        oldServices.forEach(service => {
          filteredServices = filteredServices.filter(item => item.service_id !== service.service_id);
        })
        persone.pservice = [...filteredServices];
        msgStore.addMessage({name: 'Виды служения удалены.', icon: 'done'});
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

  }; // with permitions

  const addPersonLevel = async (levelData) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/plevels', levelData);
      const persone = peoples.value.filter(item => item.id == response.data.people_id)[0];
      persone.plevel = [...persone.plevel, response.data].sort((a, b) => new Date(a.date) - new Date(b.date));
      msgStore.addMessage({name: 'Дисциплина: "' + response.data.LevelName + '", добавлена.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else if (error.response?.status === 403) {
        msgStore.addMessage({name: error.response?.data?.message, icon: 'error'});
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  }; // with permitions

  const deletePersonLevels = async (itemsToDelete, personeID) => {
    loader.value = true;
    try {
      errors.value = {};
      await axios.post('api/plevels/delete', {ids: itemsToDelete});
      const persone = peoples.value.filter(item => item.id == personeID)[0];
      let filteredLevels = [...persone.plevel];
      itemsToDelete.forEach(levelID => {
        filteredLevels = filteredLevels.filter(item => item.id != levelID);
      })
      persone.plevel = [...filteredLevels].sort((a, b) => new Date(a.date) - new Date(b.date));
      msgStore.addMessage({name: 'Дисциплина удалена.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const editPersonLevel = async (levelData) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/plevels/' + levelData.id + '/update', levelData);
      const persone = peoples.value.filter(item => item.id == response.data.people_id)[0];
      const without = persone.plevel.filter(item => item.id !== response.data.id);
      persone.plevel = [...without, response.data].sort((a, b) => new Date(a.date) - new Date(b.date));

      msgStore.addMessage({name: 'Дисциплина: "' + response.data.LevelName + '", изменена.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const getPersonePermitions = async (user_id) => {
    loader.value = true;
    try {
      const response = await axios.get('api/user/' + user_id + '/permitions');
      personePermitions.value = response.data;
    } catch (error) {
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
  }; // with permitions

  const addNewPermitionToArray = (permition) => {
    personePermitions.value = [...personePermitions.value, {...permition}];
  };  // with permitions

  const setPermitionToArray = (permitions) => {
    personePermitions.value = [...permitions];
  }; // with permitions
 
  const clearErrorsState = () => {
    errors.value = {}
  };

  return { 
    peoples,
    personePermitions,
    errors,
    totalCountErrors,
    getAllPeople,
    addNewPersone,
    editPersone,
    updatePersoneServices,
    addPersonLevel,
    deletePersonLevels,
    editPersonLevel,
    getPersonePermitions,
    addNewPermitionToArray,
    setPermitionToArray,
    clearErrorsState,
  }  
})