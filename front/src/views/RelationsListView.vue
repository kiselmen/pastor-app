<template>
  <h2 class="card-name">Отношения</h2>
  <div class="card-container" v-if="loader">
    <div class="card-text">Loading...</div>
  </div>
  <div class="card-container" v-if="!loader">
    <div class="card-header">
      <div class="form-group">
          <button 
            @click="openActionModal('addTarget')" 
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
        :tableData = "nsiStore.relations"
      />
    </div>
    <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddRelation v-if="action === 'addTarget'"
          @toggle-modal="isModalAction = false"
      />
    </ModalWrapper>
  </div>    
</template>

<script setup>
  import { useNsiStore } from '@/stores/nsiStore.js';
  import { ref, onBeforeMount } from 'vue';
  import Table from '@/components/ui/Table.vue';
  import AddRelation from '@/components/nsi/AddRelation.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';

  const nsiStore = useNsiStore();

  const tableHeadNames = ref(['N', 'Целевые группы', 'Описание', 'Пол', 'Половинка пары']);
  const tableHeadID = ref(['id', 'name', 'discription', 'SexName', 'pair']);
  const isModalAction = ref(false);
  const action = ref('');
  const loader = ref(false);

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  };

  onBeforeMount(async () => {
    loader.value = true;
    await nsiStore.getRelations();
    loader.value = false;
  })

</script>

<style lang="scss">
</style>