<template>
  <div class="people-container" v-if="!loader">
    <div class="form-group">
        <button 
          @click="openActionModal('addPersone')" 
          type="button" 
          class="btn btn-blue" 
        >
            Создать
        </button>
    </div>
    <div class="people-items">

      <PersoneItem 
        class="people-item"
        @editPeron = "onEditPersone"
        v-for="persone in peopleStore.peoples"
        :key = persone.Id
        :persone = persone
      />
    </div>
  </div>
  <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddPersone v-if="action === 'addPersone'"
          @toggle-modal="isModalAction = false"
      />
      <EditPersone v-if="action === 'editPersone'"
          @toggle-modal="isModalAction = false"
          :id="activePersone"
      />
</ModalWrapper>
</template>

<script setup>
  import { ref, computed, watch, onBeforeMount } from 'vue';
  import { usePeopleStore } from "@/stores/peopleStore";
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import AddPersone  from '@/components/people/AddPersone.vue';
  import EditPersone  from '@/components/people/EditPersone.vue';
  import PersoneItem from '@/components/people/PersoneItem.vue';

  const peopleStore = usePeopleStore();

  const loader = ref(false);
  const isModalAction = ref(false);
  const action = ref('');
  const activePersone = ref(null);

  const openActionModal = (actionValue) => {
    console.log('actionValue ', actionValue);
    action.value = actionValue;
    isModalAction.value = true;
  };

  const onEditPersone = (id) => {
    console.log('On edit click ', id);
    action.value = 'editPersone';
    activePersone.value = id;
    isModalAction.value = true;
  }

  const onCloseModal = () => {
    isModalAction = false;
    action.value = null;
    activePersone.value = null;
  }

  onBeforeMount(async () => {
    loader.value = true;
    await peopleStore.getAllPeople();
    loader.value = false;
  });


</script>

<style lang="scss" scoped>
  .people {
    &-container {
      padding: 0 10px;
      width: 100%;
    }
    &-items {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: flex-start;
      gap: 20px;
    }
    &-item {
      flex-basis: 24%;
    }
  }
</style>