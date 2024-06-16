<template>
  <h2 class="card-name">Приходы</h2>
  <div class="card-container" v-if="loader">
    <div class="card-text">Loading...</div>
  </div>
  <div class="card-container" v-if="!loader">
    <div class="form-group">
        <button 
          @click="openActionModal('addPrihod')" 
          type="button" 
          class="btn btn-blue" 
        >
            Создать
        </button>
    </div>
    <div class="card-items">

      <PrihodItem
        class="card-item"
        @editPrihod = "onEditPrihod"
        v-for="prihod in prihodStore.prihods"
        :key = prihod.Id
        :prihod = prihod
      />
    </div>
  </div>
  <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddPrihod v-if="action === 'addPrihod'"
          @toggle-modal="isModalAction = false"
      />
      <EditPrihod v-if="action === 'editPrihod'"
          @toggle-modal="isModalAction = false"
          :id="activePrihod"
      />
  </ModalWrapper>
</template>

<script setup>
  import { ref, computed, watch, onBeforeMount } from 'vue';
  import { usePrihodStore } from "@/stores/prihodStore";
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import PrihodItem from '@/components/prihod/PrihodItem.vue';
  import AddPrihod from '@/components/prihod/AddPrihod.vue';
  import EditPrihod from '@/components/prihod/EditPrihod.vue';

  const prihodStore = usePrihodStore();

  const isModalAction = ref(false);
  const action = ref('');
  const activePrihod = ref(null);
  const loader = ref(false);

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  }

  const onEditPrihod = (id) => {
    // console.log('On edit click ', id);
    action.value = 'editPrihod';
    activePrihod.value = id;
    isModalAction.value = true;
  }

  onBeforeMount(async () => {
    loader.value = true;
    await prihodStore.getPrihods();
    loader.value = false;
  });

</script>

<style lang="scss" scoped>

</style>