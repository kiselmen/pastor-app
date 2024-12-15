import { ref } from 'vue';
import { defineStore } from 'pinia';

export const useViewStore = defineStore('viewStore', () => {

  const prihodFilterMask = ref(null);
  const targetFilterMask = ref(null);
  const searchFilterMask = ref(null);
  const serviceFilterMask = ref(null);
  const startDate = ref(null);
  const endDate = ref(null);

  const allowFilterData = [
    {name: 'prihod'}, {name: 'target'}, {name: 'service'}, {name: 'search'},
  ];

  const setPrihodFilterMask = (mask) => {
    prihodFilterMask.value = mask;
  };

  const setTargetFilterMask = (mask) => {
    targetFilterMask.value = mask;
  };

  const setSearchFilterMask = (mask) => {
    searchFilterMask.value = mask;
  };

  const setServiceFilterMask = (mask) => {
    serviceFilterMask.value = mask;
  };

  const setDateParameters = (start, end) => {
    startDate.value = start;
    endDate.value = end;
  }

  return {
    prihodFilterMask,
    targetFilterMask,
    searchFilterMask,
    serviceFilterMask,
    allowFilterData,
    startDate,
    endDate,    
    setPrihodFilterMask,
    setTargetFilterMask,
    setSearchFilterMask,
    setServiceFilterMask,
    setDateParameters,
  };

});
