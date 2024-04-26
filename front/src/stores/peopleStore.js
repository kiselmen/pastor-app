import { ref, computed } from 'vue';
// import { useRouter } from 'vue-router';
import { defineStore } from 'pinia';
import axios from 'axios';

export const usePeopleStore = defineStore('peopleStore', () => {
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
    }
    loader.value = false;
  }

  const addNewPersone = async (personeData) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('/api/peoples', personeData);
      peoples.value = [...peoples.value, response.data].sort((a ,b) => a.id - b.id);
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  }

  const editPersone = async (personeData) => {
    loader.value = true;
    try {
      errors.value = {};
      const id = Number(personeData.get('id'));
      const response = await axios.post('/api/peoples/' + id + '/update', personeData);
      console.log(response.data.id);
      const newPeoples = peoples.value.filter(item => item.id !== id);
      peoples.value = [...newPeoples, response.data].sort((a ,b) => a.id - b.id)
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response?.data?.errors;
      }
    }
    loader.value = false;
  }

  const clearErrorsState = () => {
    errors.value = {}
  }

  return { 
    peoples,
    errors,
    totalCountErrors,
    getAllPeople,
    addNewPersone,
    editPersone,
    clearErrorsState,
  }  
})