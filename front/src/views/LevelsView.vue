<template>
  <h2 class="level-name">Целевый группы</h2>
  <div class="level-container" v-if="!useNsiStore.loader">
    <div class="form-group">
        <button 
          @click="openActionModal('addLevel')" 
          type="button" 
          class="btn btn-blue" 
        >
            Создать
        </button>
    </div>
    <div class="level-table">
      <Table
        :tableHeadNames = "tableHeadNames"
        :tableHeadID = "tableHeadID"
        :tableData = "nsiStore.levels"
      />
    </div>
    <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddLevel v-if="action === 'addLevel'"
          @toggle-modal="isModalAction = false"
      />
    </ModalWrapper>
  </div>    
</template>

<script setup>
  import Table from '@/components/ui/Table.vue';
  import { useNsiStore } from '@/stores/nsiStore.js';
  import { ref, onBeforeMount } from 'vue';
  import AddLevel from '@/components/nsi/AddLevel.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';

  const nsiStore = useNsiStore();

  const tableHeadNames = ref(['N', 'Целевые группы', 'Описание']);
  const tableHeadID = ref(['id', 'name', 'discription']);
  const isModalAction = ref(false);
  const action = ref('');

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  };

  onBeforeMount(async () => {
    await nsiStore.getLevels();
  })

</script>

<style lang="scss">
  .level {
    &-container {
      padding: 0 10px;
      width: 100%;
    }
    &-name {
      background-color: var(--bs-gray-200);
      color: var(--bs-primary);
      margin: 10px;
      padding: 10px;
      border-radius: 3px;
    }
    &-table {
      margin-top: 20px;
    }
  }
</style>