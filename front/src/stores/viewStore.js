import { ref } from 'vue';
import { defineStore } from 'pinia';

export const useViewStore = defineStore('viewStore', () => {

  const prihodFilerMask = ref(null);

  const setPrihodFilerMask = (mask) => {
    prihodFilerMask.value = mask;
  };

  return {
    prihodFilerMask,
    setPrihodFilerMask,
  };

});
