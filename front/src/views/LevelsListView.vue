<template>
  <h2 class="card-name">Целевые группы</h2>
  <div class="card-container" v-if="loader">
    <div class="card-text">Loading...</div>
  </div>
  <div class="card-container" v-if="!loader">
    <div class="card-header">
      <div class="form-group">
          <button 
            @click="openActionModal('addLevel')" 
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
        :tableData = "nsiStore.levels"
        :hasActiveBtn = "hasActions"
        :actions = "actions"
        @rowAction = "onRowAction"
      />
    </div>
    <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddLevel v-if="action === 'addLevel'"
          @toggle-modal="isModalAction = false"
      />
      <EditLevel v-if="action === 'editLevel'"
          :id = "activeID"
          @toggle-modal="isModalAction = false"
      />
    </ModalWrapper>
  </div>    
</template>

<script setup>
  import { ref, onBeforeMount, computed } from 'vue';
  import { useNsiStore } from '@/stores/nsiStore.js';
  import { useUserStore } from '@/stores/userStore.js';
  import Table from '@/components/ui/Table.vue';
  import AddLevel from '@/components/nsi/AddLevel.vue';
  import EditLevel from '@/components/nsi/EditLevel.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';

  const nsiStore = useNsiStore();
  const userStore = useUserStore();

  const tableHeadNames = ref(['N', 'Целевые группы', 'Описание', 'Цвет']);
  const tableHeadID = ref(['id', 'name', 'discription', 'color']);
  const isModalAction = ref(false);
  const action = ref('');
  const loader = ref(false);
  const activeID = ref(null);

  const hasActions = computed(() => {
    const isPermition = userStore.user.permition;
    return Boolean(isPermition.length);
  })

  const actions = computed(() => {
    const isPermition = userStore.user.permition;
    if (isPermition.length) {
      return   [
        {id: 0, name: "Изменить", type: 0},
      ];
    } else {
      return [];
    }
  })
  
  const onRowAction = (actionID, rowID) => {
    if (actionID === 0) {
      action.value = 'editLevel';
      activeID.value = rowID;
      isModalAction.value = true;
    }
  }

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  };

  onBeforeMount(async () => {
    loader.value = true;
    await nsiStore.getLevels();
    loader.value = false;
  })

</script>

<style lang="scss">
</style>