<template>
  <h2 class="card-name">Виды служения</h2>
  <div class="card-container" v-if="loader">
    <div class="card-text">Loading...</div>
  </div>
  <div class="card-container" v-if="!loader">
    <div class="card-header">
      <div class="form-group">
          <button 
            @click="openActionModal('addService')" 
            type="button" 
            class="btn btn-blue" 
          >
              Создать
          </button>
      </div>
    </div>
    <div class="card-table">
      <Table
        :tableHeadNames = "tableHeadNames"
        :tableHeadID = "tableHeadID"
        :tableData = "nsiStore.services"
        :hasActiveBtn = "hasActions"
        :isAllRowSelected = "isAllRowSelected"
        :actions = "actions"
        @selectRow = "onSelectRow"
        @selectAllRows = "onSelectAllRows"
        @rowAction = "onRowAction"
        @allRowAction = "onAllRowAction"
      />
    </div>
    <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddService v-if="action === 'addService'"
          @toggle-modal="isModalAction = false"
      />
      <EditService v-if="action === 'editService'"
          :id = "activeID"
          @toggle-modal="isModalAction = false"
      />
      <DeleteServices v-if="action === 'deleteServices'"
          :id = "activeID"
          @toggle-modal="isModalAction = false"
      />
    </ModalWrapper>
  </div>    
</template>

<script setup>
  import Table from '@/components/ui/Table.vue';
  import { useNsiStore } from '@/stores/nsiStore.js';
  import { useUserStore } from '@/stores/userStore.js';
  import { ref, onBeforeMount, watch, computed } from 'vue';
  import AddService from '@/components/nsi/AddService.vue';
  import EditService from '@/components/nsi/EditService.vue';
  import DeleteServices from '@/components/nsi/DeleteServices.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  // import EditDuotoneIcon from '@/components/icons/IconEditDuotone.vue';

  const nsiStore = useNsiStore();
  const userStore = useUserStore();

  const tableHeadNames = ref(['N', 'Виды служения', 'Описание', 'Статус']);
  const tableHeadID = ref(['id', 'name', 'discription', 'StatusName']);
  const isModalAction = ref(false);
  const action = ref('');
  const isAllRowSelected = ref(false);
  const activeID = ref(null);
  const loader = ref(false);

  const onSelectRow = (value, rowID) => {
    nsiStore.updateServiceAttribute(rowID, 'selected', value);
  };

  const onSelectAllRows = (value) => {
    nsiStore.updateServiceAttributeAllRows('selected', value);
    isAllRowSelected.value = value;
  }

  const onRowAction = (actionID, rowID) => {
    if (actionID === 0) {
      action.value = 'editService';
      activeID.value = rowID;
      isModalAction.value = true;
    } else if (actionID === 1) {
      action.value = 'deleteServices';
      activeID.value = [rowID];
      isModalAction.value = true;
    }
  }

  const onAllRowAction = (actionID) => {
    if (actionID === 1) {
      action.value = 'deleteServices';
      const IDs = [];
      nsiStore.services.forEach(item => {
        if (item.selected === true) {
          IDs.push(item.id);
        }
      });
      activeID.value = IDs;
      isModalAction.value = true;
    }
  }

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  };

  const hasActions = computed(() => {
    const isPermition = userStore.user.permition;
    return Boolean(isPermition.length);
  })

  const actions = computed(() => {
    const isPermition = userStore.user.permition;
    if (isPermition.length) {
      return   [
        {id: 0, name: "Изменить", type: 0},
        // {id: 1, name: "Удалить", type: 1},
      ];
    } else {
      return [];
    }
  })
  
  onBeforeMount(async () => {
    loader.value = true;
    await nsiStore.getServices();
    loader.value = false;
  });

</script>

<style lang="scss">
</style>