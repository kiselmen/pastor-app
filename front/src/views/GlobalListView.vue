<template>
  <h2 class="card-name">
      <span>
          Журнал операций
      </span>
      <DataSelector
          :start = "viewStore.startDate"
          :end = "viewStore.endDate"
          :isBtnDisabled = "loader"
          @updatePeriod = "onUpdatePeriod"
      />
  </h2>
  <div class="card-container" v-if="loader">
    <div class="card-text">Loading...</div>
  </div>
  <div class="card-container" v-if="!loader">
    <div class="card-table">
      <Table
        :tableHeadNames = "tableHeadNames"
        :tableHeadID = "tableHeadID"
        :tableData = "nsiStore.globalActions"
        :hasActiveBtn = "hasActions"
        :isAllRowSelected = "isAllRowSelected"
        :actions = "actions"
        @rowAction = "onRowAction"
      />
    </div>
    <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <ShowAction v-if="action === 'showAction'"
          :item = "activeItem" 
          @toggle-modal="isModalAction = false"
      />
    </ModalWrapper>
  </div>    
</template>

<script setup>
  import { ref, onBeforeMount, computed, watch } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import { useUserStore } from '@/stores/userStore.js';
  import { useViewStore } from '@/stores/viewStore';  
  import { useNsiStore } from '@/stores/nsiStore';
  import { useFamilyStore } from '@/stores/familyStore.js';
  import Table from '@/components/ui/Table.vue';
  import ShowAction from '@/components/nsi/ShowAction.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import DataSelector from '@/components/nsi/DataSelector.vue'
  
  const route = useRoute();
  const router = useRouter();
  const viewStore = useViewStore();
  const userStore = useUserStore();
  const familyStore = useFamilyStore();
  const nsiStore = useNsiStore();

  const tableHeadNames = ref(['N', 'Дата', 'Автор', 'Описание']);
  const tableHeadID = ref(['id', 'date', 'UserName', 'discription']);
  const isModalAction = ref(false);
  const action = ref('');
  const isAllRowSelected = ref(false);
  const activeID = ref(null);
  const loader = ref(false);

  const onRowAction = (actionID, rowID) => {
    if (actionID === 0) {
      action.value = 'showAction';
      activeID.value = rowID;
      isModalAction.value = true;
    }  
  };

  const hasActions = computed(() => {
    const isPermition = userStore.user.permition;
    return Boolean(isPermition.length);
  });

  const actions = computed(() => {
    const isPermition = userStore.user.permition;
    if (isPermition.length) {
      return   [
        {id: 0, name: "Показать", type: 0},
      ];
    } else {
      return [];
    }
  });

  const activeItem = computed(() => {
    const isPresent = nsiStore.globalActions.filter(item => item.id == activeID.value);
    if (isPresent.length) return isPresent[0];
    return null; 
  });

  watch( () => route.query, async () => {
    loader.value = true;
    setQueryParameters();
    await getGlobalActionsFromAPI();
    loader.value = false;
  }, { deep: true });

  const setQueryParameters = () => {
    const query = route.query;

    let startFromQuery = query._start;
    if (!startFromQuery) {
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = '01';
      startFromQuery = year + '-' + month + '-' + day;
    }

    let endFromQuery = query._end;
    if (!endFromQuery) {
      const now = new Date();
      const year = now.getFullYear();
      const month = now.getMonth();
      const lastDayOfMonth = new Date(year, month + 1, 0); 
      const day = String(lastDayOfMonth.getDate()).padStart(2, '0'); 
      const monthString = String(month + 1).padStart(2, '0');
      endFromQuery = year + '-' + monthString + '-' + day;       
    }

    viewStore.setDateParameters(startFromQuery, endFromQuery);
  };

  const onUpdatePeriod = (params) => {
    const start = params.startDate;
    const end = params.endDate;
    let URL = 'global-list?' + '_start=' + start + '&_end=' + end;
    router.push(URL);
  };

  const getGlobalActionsFromAPI = async () => {
    await nsiStore.getGlobalActions();
  };
  
  onBeforeMount(async () => {
    loader.value = true;
    await router.isReady();
    setQueryParameters();
    await getGlobalActionsFromAPI();
    await familyStore.getAllFamilies();
    loader.value = false;
  });

</script>

<style lang="scss" scoped>
  .table th, .table td {
    text-align: left;
  }
</style>