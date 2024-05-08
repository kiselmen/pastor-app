<template>
  <h2 class="people-name">Прихожане</h2>
  <div class="people-container" v-if="!peopleStore.loader">
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
    console.log(peopleStore.loader);
    await peopleStore.getAllPeople();
  });


</script>

<style lang="scss" scoped>
  .people {
    &-container {
      padding: 0 10px 10px 10px;
      width: 100%;
    }
    &-name {
      background-color: var(--bs-gray-200);
      color: var(--bs-primary);
      margin: 10px;
      padding: 10px;
      border-radius: 3px;
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