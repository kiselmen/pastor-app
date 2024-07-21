import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { useMsgStore } from '@/stores/msgStore';
import { useUserStore } from '@/stores/userStore';
import axios from 'axios';

export const useNsiStore = defineStore('nsiStore', () => {

  const msgStore = useMsgStore();
  const userStore = useUserStore();
  const statuses = ref([]);
  const targets = ref([]);
  const services = ref([]);
  const levels = ref([]);
  const loader = ref(false);
  const errors = ref({});

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const availableServices = computed(() => {
    const isSuperAdmin = userStore.user.permition.filter(item => item.type === 0).length;
    if (isSuperAdmin) return services.value;
    const adminServices = services.value.filter(service => {
      let isPresentInPermitions = false;
      userStore.user.permition.forEach(item => {
        if (item.type === 2) {
          if (item.source_id == service.id) isPresentInPermitions = true
        }
      });
      return isPresentInPermitions;
    });
    return adminServices;
  });

  const getStatuses = async () => {
    loader.value = true;
    try {
      const response = await axios.get('api/statuses');
      statuses.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
      msgStore.addMessage({name: error.message, icon: 'error'});
    }
    loader.value = false;
  };

  const getTargets = async () => {
    loader.value = true;
    try {
      const response = await axios.get('api/targets');
      targets.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
      msgStore.addMessage({name: error.message, icon: 'error'});
    }
    loader.value = false;
  };

  const addNewTarget = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/targets', data);
      targets.value = [...targets.value, response.data].sort((a ,b) => a.id - b.id);
      msgStore.addMessage({name: 'Целевая группа: "' + response.data.name + '", добавлена.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  }

  const getServices = async () => {
    loader.value = true;
    try {
      const response = await axios.get('api/services');
      services.value = response.data.sort((a ,b) => a.id - b.id);
      services.value.forEach( item => {
        item.selected = false;
      });
    } catch (error) {
      console.log(error);
      msgStore.addMessage({name: error.message, icon: 'error'});
    }
    loader.value = false;
  };

  const addNewService = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/services', data);
      services.value = [...services.value, {...response.data, selected: false}].sort((a ,b) => a.id - b.id);
      msgStore.addMessage({name: 'Вид служения: "' + response.data.name + '", добавлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const editService = async (data, id) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/services/' + id + '/update', data);
      const newServices = services.value.filter(item => item.id !== id);
      services.value = [...newServices, {...response.data, selected: false}].sort((a ,b) => a.id - b.id);
      msgStore.addMessage({name: 'Вид служения: "' + response.data.name + '", обновлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const deleteServices = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/services/delete', {ids: data});
      services.value = response.data.sort((a ,b) => a.id - b.id);
      services.value.forEach( item => {
        item.selected = false;
      });
      msgStore.addMessage({name: 'Виды служения удалены.', icon: 'done'});
    } catch (error) {
      console.log('error ', error);
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const updateServiceAttribute = (id, attrName, attrValue) => {
    const filtered = services.value.filter( item => item.id === id)[0];
    filtered[attrName] = attrValue;
  };

  const updateServiceAttributeAllRows = (attrName, attrValue) => {
    services.value.forEach( item => item[attrName] = attrValue);
  };

  const getLevels = async () => {
    loader.value = true;
    try {
      const response = await axios.get('api/levels');
      levels.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
      msgStore.addMessage({name: error.message, icon: 'error'});
    }
    loader.value = false;
  };

  const addNewLevel = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/levels', data);
      levels.value = [...levels.value, response.data].sort((a ,b) => a.id - b.id);
      msgStore.addMessage({name: 'Уровень дисциплины: "' + response.data.name + '", добавлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const editLevel = async (data, id) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/levels/' + id + '/update', data);

      const newLevels = levels.value.filter(item => item.id !== id);
      levels.value = [...newLevels, response.data].sort((a ,b) => a.id - b.id);
      msgStore.addMessage({name: 'Уровень дисциплины: "' + response.data.name + '", обновлен.', icon: 'done'});
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      } else {
        msgStore.addMessage({name: error.message, icon: 'error'});
      }
    }
    loader.value = false;
  };

  const clearErrorsState = () => {
    errors.value = {}
  };

  return {
    totalCountErrors,
    availableServices,
    errors,
    statuses,
    targets,
    services,
    levels,
    loader,
    getStatuses,
    getTargets,
    addNewTarget,
    getServices,
    addNewService,
    editService,
    // deleteService,
    deleteServices,
    updateServiceAttribute,
    updateServiceAttributeAllRows,
    getLevels,
    addNewLevel,
    editLevel,
    clearErrorsState,
  }
});