import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import axios from 'axios';

export const useNsiStore = defineStore('nsiStore', () => {
  const statuses = ref([]);
  const targets = ref([]);
  const services = ref([]);
  const levels = ref([]);
  const loader = ref(false);
  const errors = ref({})

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const getStatuses = async () => {
    loader.value = true;
    try {
      const response = await axios.get('/api/statuses');
      statuses.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
    }
    loader.value = false;
  };

  const getTargets = async () => {
    loader.value = true;
    try {
      const response = await axios.get('/api/targets');
      targets.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
    }
    loader.value = false;
  };

  const addNewTarget = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('/api/targets', data);
      targets.value = [...targets.value, response.data].sort((a ,b) => a.id - b.id);
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  }

  const getServices = async () => {
    loader.value = true;
    try {
      const response = await axios.get('/api/services');
      services.value = response.data.sort((a ,b) => a.id - b.id);
      services.value.forEach( item => {
        item.selected = false;
      });
    } catch (error) {
      console.log(error);
    }
    loader.value = false;
  };

  const addNewService = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('/api/services', data);
      services.value = [...services.value, {...response.data, selected: false}].sort((a ,b) => a.id - b.id);
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  };

  const editService = async (data, id) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('/api/services/' + id + '/update', data);
      const newServices = services.value.filter(item => item.id !== id);
      services.value = [...newServices, {...response.data, selected: false}].sort((a ,b) => a.id - b.id);
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  };

  const deleteServices = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('/api/services/delete', {ids: data});
      services.value = response.data.sort((a ,b) => a.id - b.id);
      services.value.forEach( item => {
        item.selected = false;
      });
    } catch (error) {
      console.log('error ', error);
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
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
  }

  const getLevels = async () => {
    loader.value = true;
    try {
      const response = await axios.get('/api/levels');
      levels.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
    }
    loader.value = false;
  };

  const addNewLevel = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('/api/levels', data);
      levels.value = [...levels.value, response.data].sort((a ,b) => a.id - b.id);
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  }

  const clearErrorsState = () => {
    errors.value = {}
  };

  return {
    totalCountErrors,
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
    clearErrorsState,
  }
});