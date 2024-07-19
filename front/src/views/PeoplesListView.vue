<template>
  <h2 class="card-name">Прихожане</h2>
  <div v-if="loader" class="card-container">
    <div class="card-text">Loading...</div>
  </div>
  <div v-if="!loader" class="card-container" >
    <div class="form-row">
      <button 
        v-if = 'isAvailableAdd'
        @click="openActionModal('addPersone')" 
        type="button" 
        class="btn btn-blue" 
      >
          Создать
      </button>
      <PrihodFilter 
        @changeFilter="onChangePrihodFilterMask"
      />
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
        @editPersone = "onEditPersone"
        @editPersoneServices = "onEditPersonServices"
        @editPersoneLevels = "onEditPersonLevels"
        @registerNewUser = "onRegisterNewUser"
        @changeUserPass = "onChangePersonePass"
        @changeUserPermitions = "onChangePersonePermitions"
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
      <EditPersoneLevels v-if="action === 'editPersoneLevels'"
          @toggle-modal="isModalAction = false"
          :id="activePersone"
      />
      <EditPersoneServices v-if="action === 'editPersoneServices'"
          @toggle-modal="isModalAction = false"
          :id="activePersone"
      />
      <RegisterNewUser v-if="action === 'registerNewUser'"
          @toggle-modal="isModalAction = false"
          :id="activePersone"
      />
      <ChangeUserPass v-if="action === 'changeUserPass'"
          @toggle-modal="isModalAction = false"
          :id="activeUser"
      />
      <ChangeUserPermitions v-if="action === 'changeUserPermitions'"
          @toggle-modal="isModalAction = false"
          :id="activeUser"
      />
  </ModalWrapper>
</template>

<script setup>
  import { ref, watch, onBeforeMount, computed, onUpdated } from 'vue';
  import { useRoute, useRouter } from 'vue-router'
  import { usePeopleStore } from "@/stores/peopleStore";
  import { useUserStore } from "@/stores/userStore";
  import { useViewStore } from "@/stores/viewStore";  
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import AddPersone  from '@/components/people/AddPersone.vue';
  import EditPersone  from '@/components/people/EditPersone.vue';
  import PersoneItem from '@/components/people/PersoneItem.vue';
  import EditPersoneServices from '@/components/nsi/EditPersoneServices.vue';
  import EditPersoneLevels from '@/components/nsi/EditPersoneLevels.vue';
  import RegisterNewUser from '@/components/nsi/RegisterNewUser.vue';
  import ChangeUserPass from '@/components/nsi/ChangeUserPass.vue';
  import ChangeUserPermitions from '@/components/nsi/ChangeUserPermitions.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';
  import SearchLupaIcon from '@/components/icons/IconSearchLupa.vue';
  import PrihodFilter from '@/components/prihod/PrihodFilter.vue';

  const route = useRoute();
  const router = useRouter();
  const peopleStore = usePeopleStore();
  const userStore = useUserStore();
  const viewStore = useViewStore();

  const loader = ref(false);
  const isModalAction = ref(false);
  const action = ref('');
  const activePersone = ref(null);
  const search = ref('');
  const filteredPeoples = ref([]);

  const isAvailableAdd = computed(()=> {
    let isAdmin = false;
    userStore.user?.permition?.forEach(permition => {
      if (permition.type == 0 || permition.type == 1) isAdmin = true;
    });
    return isAdmin;
  });

  watch( () => route.query, () => {
    search.value = route.query.search;
    console.log('watch query');
    onFilterPeoples();
  }, { deep: true });

  watch(() => peopleStore.peoples, () => {
    onFilterPeoples();
  }, { deep: true });

  const activeUser = computed(() => {
    if (activePersone.value) {
      return peopleStore.peoples.filter(item => item.id == activePersone.value)[0].user_id;
    } else {
      return null;
    }
  });

  const onChangePrihodFilterMask = (mask) => {
    console.log('onChangePrihodFilterMask ', mask);
    router.push('peoples?_prihod=' + mask);
  };

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
  };

  const onEditPersonLevels = (id) => {
    action.value = 'editPersoneLevels';
    activePersone.value = id;
    isModalAction.value = true;
  };

  const onRegisterNewUser = (id) => {
    action.value = 'registerNewUser';
    activePersone.value = id;
    isModalAction.value = true;
  };

  const onChangePersonePass = (id) => {
    action.value = 'changeUserPass';
    activePersone.value = id;
    isModalAction.value = true;
  };

  const onChangePersonePermitions = (id) => {
    action.value = 'changeUserPermitions';
    activePersone.value = id;
    isModalAction.value = true;
  };

  // onUpdated(() => {
  //   console.log('on change', route.query);
  //   const query = route.query;
  //   if (query._prihod) viewStore.setPrihodFilerMask(query._prihod);
  // });

  onBeforeMount(async () => {
    loader.value = true;
    await router.isReady();
    const query = route.query;
    search.value = query.search;
    console.log('onBeforeMount ', query._prihod);
    if (query._prihod) viewStore.setPrihodFilerMask(query._prihod);
    await peopleStore.getAllPeople();
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