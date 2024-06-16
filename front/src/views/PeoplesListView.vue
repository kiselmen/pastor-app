<template>
  <h2 class="card-name">Прихожане</h2>
  <div v-if="loader" class="card-container">
    <div class="card-text">Loading...</div>
  </div>
  <div v-if="!loader" class="card-container" >
    <div class="form-row">
      <button 
        @click="openActionModal('addPersone')" 
        type="button" 
        class="btn btn-blue" 
      >
          Создать
      </button>
      <div class="form-search">
        <input 
          type="text"
          class="input-box"
          v-model="search"
        />
        <div class="form-icons">
          <div class="form-icon-delete">
            <RouterLink :to="'/peoples'">
              <DismissIcon v-if = "search" @click="search=''"/>
            </RouterLink>  
          </div>  
          <div class="form-icon-search">
            <RouterLink :to="'/peoples?search=' + search">
              <SearchLupaIcon />
            </RouterLink>  
          </div>  
        </div>
      </div>
    </div>  
    <div class="card-items">
      <PersoneItem 
        v-for="persone in filteredPeoples"
        class="card-item"
        @editPerson = "onEditPersone"
        @editPersonServices = "onEditPersonServices"
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
      <EditPersoneServices v-if="action === 'editPersoneServices'"
          @toggle-modal="isModalAction = false"
          :id="activePersone"
      />
  </ModalWrapper>
</template>

<script setup>
  import { ref, watch, onBeforeMount } from 'vue';
  import { useRoute, useRouter } from 'vue-router'
  import { usePeopleStore } from "@/stores/peopleStore";
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import AddPersone  from '@/components/people/AddPersone.vue';
  import EditPersone  from '@/components/people/EditPersone.vue';
  import PersoneItem from '@/components/people/PersoneItem.vue';
  import EditPersoneServices from '@/components/nsi/EditPersoneServices.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';
  import SearchLupaIcon from '@/components/icons/IconSearchLupa.vue';

  const route = useRoute();
  const router = useRouter();
  const peopleStore = usePeopleStore();

  const loader = ref(false);
  const isModalAction = ref(false);
  const action = ref('');
  const activePersone = ref(null);
  const search = ref('');
  const filteredPeoples = ref([]);

  watch( () => route.query, () => {
    search.value = route.query.search;
    onFilterPeoples();
  }, { deep: true });

  watch(() => peopleStore.peoples, () => {
    onFilterPeoples();
  }, { deep: true })

  const onFilterPeoples = () => {
    if (search.value) {
      filteredPeoples.value =  peopleStore.peoples.filter(item => item.name.toLowerCase().indexOf(search.value.toLowerCase()) >= 0 ? true: false)
    } else {
      filteredPeoples.value = [...peopleStore.peoples];
    }
  };

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  };

  const onEditPersone = (id) => {
    action.value = 'editPersone';
    activePersone.value = id;
    isModalAction.value = true;
  };

  const onEditPersonServices = (id) => {
    action.value = 'editPersoneServices';
    activePersone.value = id;
    isModalAction.value = true;
  }

  onBeforeMount(async () => {
    loader.value = true;
    await peopleStore.getAllPeople();
    await router.isReady();
    search.value = route.query.search;
    console.log('Mount ', route.query);
    onFilterPeoples();
    loader.value = false;
  });

</script>

<style lang="scss" scoped>
  .input-box {
    width: 300px;
    padding-right: 55px;
  }
  .form-icon-search {
    background-color: var(--bs-primary);
    width: 100%;
    height: 100%;
    padding: 5px 5px 0 5px;
  }
</style>