import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useMsgStore } from '@/stores/msgStore';
import { useViewStore } from '@/stores/viewStore';
import axios from 'axios';

export const usePeopleStore = defineStore('peopleStore', () => {

  const msgStore = useMsgStore();
  const viewStore = useViewStore();
  const peoples = ref([]);
  const onePersone = ref(null); 
  const personePermitions =ref([]);
  const loader = ref(false);
  const errors = ref({});

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const getAllPeople = async () => {
    loader.value = true;
    // console.log('viewStore.searchFilterMask ', viewStore.searchFilterMask);
    try {
      let mask = '';
      viewStore.allowFilterData.forEach(item => {
          const storeKey = item.name + 'FilterMask';
          const storeValue = viewStore[storeKey];
          // console.log('storeKey ', storeKey, ' storeValue ', storeValue);
          if (storeValue) mask = mask + '&_' + item.name + '=' + storeValue;
      });
      if (mask) mask = '?' + mask.substring(1);

      // console.log('mask ', mask);

      const response = await axios.get('api/peoples' + mask);
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

  const getOnePersone = async (id) => {
    loader.value = true;
    try {
      const response = await axios.get('api/peoples/' + id);
      onePersone.value = response.data;
    } catch (error) {
      if (error.response.status == 403) msgStore.addMessage({name: "Не достаточно прав доступа", icon: 'error'});
      else if (error.response.status == 404) msgStore.addMessage({name: "Не найдено", icon: 'error'});
      else {
        msgStore.addMessage({name: error.message, icon: 'error'});
        console.log(error);
      }  
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

  const editPersone = async (personeData, type) => {
    loader.value = true;
    try {
      errors.value = {};
      const id = Number(personeData.get('id'));
      const response = await axios.post('api/peoples/' + id + '/update', personeData);
      if (type) {
        const newPeoples = peoples.value.filter(item => item.id !== id);
        peoples.value = [...newPeoples, response.data].sort((a ,b) => a.id - b.id).map(item => {
          item.plevel.sort((a, b) => new Date(a.date) - new Date(b.date));
          return item;
        });
      } else {
        onePersone.value = response.data;
      }
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

  const updatePersoneServices = async (id, pservices, type) => {
    loader.value = true;
    const persone = type ? peoples.value.filter(item => item.id === id)[0]: onePersone.value;
    for (let i = 0; i < pservices.length; i++) {
      let isNotFind = true;
      for (let j = 0; j < persone.pservice.length; j++) {
        if(pservices[i].service_id === persone.pservice[j].service_id) isNotFind = false;
      };
      if (isNotFind) {
        try {
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

  const addPersonLevel = async (levelData, type) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/plevels', levelData);
      if (type) {
        const persone = peoples.value.filter(item => item.id == response.data.people_id)[0];
        persone.plevel = [...persone.plevel, response.data].sort((a, b) => new Date(a.date) - new Date(b.date));
      } else {
        onePersone.value.plevel = [...onePersone.value.plevel, response.data].sort((a, b) => new Date(a.date) - new Date(b.date));
      }
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

  const deletePersonLevels = async (itemsToDelete, personeID, type) => {
    loader.value = true;
    try {
      errors.value = {};
      await axios.post('api/plevels/delete', {ids: itemsToDelete});
      if (type) {
        const persone = peoples.value.filter(item => item.id == personeID)[0];
        let filteredLevels = [...persone.plevel];
        itemsToDelete.forEach(levelID => {
          filteredLevels = filteredLevels.filter(item => item.id != levelID);
        })
        persone.plevel = [...filteredLevels].sort((a, b) => new Date(a.date) - new Date(b.date));
      } else {
        let filteredLevels = [...onePersone.value.plevel];
        itemsToDelete.forEach(levelID => {
          filteredLevels = filteredLevels.filter(item => item.id != levelID);
        })
        onePersone.value.plevel = [...filteredLevels].sort((a, b) => new Date(a.date) - new Date(b.date));
      }
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

  const editPersonLevel = async (levelData, type) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/plevels/' + levelData.id + '/update', levelData);
      if (type) {
        const persone = peoples.value.filter(item => item.id == response.data.people_id)[0];
        const without = persone.plevel.filter(item => item.id !== response.data.id);
        persone.plevel = [...without, response.data].sort((a, b) => new Date(a.date) - new Date(b.date));
      } else {
        const without = onePersone.value.plevel.filter(item => item.id !== response.data.id);
        onePersone.value.plevel = [...without, response.data].sort((a, b) => new Date(a.date) - new Date(b.date));
      }
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
    onePersone,
    personePermitions,
    errors,
    totalCountErrors,
    getAllPeople,
    getOnePersone,
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