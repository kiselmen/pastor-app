import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import axios from 'axios';
import { usePeopleStore } from '@/stores/peopleStore';
import { useMsgStore } from '@/stores/msgStore';

export const useFamilyStore = defineStore('familyStore', () => {

  const peopleStore = usePeopleStore();
  const msgStore = useMsgStore();

  const families = ref([]);
  const loader = ref(false);
  const errors = ref({})

  const totalCountErrors = computed(() => Object.keys(errors.value).length);

  const getAllFamilies = async () => {
    loader.value = true;
    try {
      const response = await axios.get('api/families');
      families.value = response.data.sort((a ,b) => a.id - b.id);
    } catch (error) {
      console.log(error);
      msgStore.addMessage({name: error.message, icon: 'error'});
    }
    loader.value = false;
  }; // with permitions

  const addNewFamily = async (data) => {
    loader.value = true;
    try {
      errors.value = {};
      const response = await axios.post('api/families', data);
      families.value = [...families.value, response.data].sort((a ,b) => a.name - b.name);
      msgStore.addMessage({name: 'Семья: "' + response.data.name + '", добавлена.', icon: 'done'});
      peopleStore.getAllPeople();
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

  const editFamily = async (familyData) => {
    loader.value = true;
    try {
      errors.value = {};
      const id = Number(familyData.id);
      const response = await axios.post('api/families/' + id + '/update', familyData);
      const newFamilies = families.value.filter(item => item.id !== id);
      families.value = [...newFamilies, response.data].sort((a ,b) => a.id - b.id)
      msgStore.addMessage({name: 'Семья: "' + response.data.name + '", изменена.', icon: 'done'});
      peopleStore.getAllPeople();
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

  const clearErrorsState = () => {
    errors.value = {}
  };

  return {
    totalCountErrors,
    families,
    loader,
    errors,
    getAllFamilies,
    addNewFamily,
    editFamily,
    clearErrorsState,
  }
});