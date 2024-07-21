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
      <TargetFilter 
        @changeFilter="onChangeTargetFilterMask"
      />
      <ServiceFilter 
        @changeFilter="onChangeServiceFilterMask"
      />
      <div class="form-search">
        <input 
          type="text"
          class="input-box"
          v-model="search"
        />
        <div class="form-icons">
          <div class="form-icon-delete">
            <!-- <RouterLink :to="'/peoples'"> -->
              <DismissIcon 
                  v-if = "search"
                  @click="onClearSearchFilterMask"
              />
            <!-- </RouterLink>   -->
          </div>  
          <div class="form-icon-search">
            <!-- <RouterLink :to="'/peoples?search=' + search"> -->
              <SearchLupaIcon 
                  @click="onChangeSearchFilterMask"
              />
            <!-- </RouterLink>   -->
          </div>  
        </div>
      </div>
    </div>  
    <div class="card-items">
      <PersoneItem 
        v-for="persone in peopleStore.peoples"
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
  import { useRoute, useRouter } from 'vue-router';
  import { usePeopleStore } from '@/stores/peopleStore';
  import { useUserStore } from '@/stores/userStore';
  import { useViewStore } from '@/stores/viewStore';  
  import { usePrihodStore } from '@/stores/prihodStore';
  import { useNsiStore } from '@/stores/nsiStore';

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
  import TargetFilter from '@/components/nsi/TargetFilter.vue';
  import ServiceFilter from '@/components/nsi/ServiceFilter.vue';

  const route = useRoute();
  const router = useRouter();
  const peopleStore = usePeopleStore();
  const userStore = useUserStore();
  const viewStore = useViewStore();
  const prihodStore = usePrihodStore();
  const nsiStore = useNsiStore();

  const loader = ref(false);
  const isModalAction = ref(false);
  const action = ref('');
  const activePersone = ref(null);
  const search = ref('');

  const isAvailableAdd = computed(()=> {
    let isAdmin = false;
    userStore.user?.permition?.forEach(permition => {
      if (permition.type == 0 || permition.type == 1) isAdmin = true;
    });
    return isAdmin;
  });

  watch( () => route.query, () => {
    // console.log('watch query');
    setQueryParameters();
    getPeoplesFromAPI();
  }, { deep: true });

  const activeUser = computed(() => {
    if (activePersone.value) {
      return peopleStore.peoples.filter(item => item.id == activePersone.value)[0].user_id;
    } else {
      return null;
    }
  });

  const createURL = (key, value) => {
    let url = '';
    viewStore.allowFilterData.forEach(item => {
      if (item.name === key) {
        if (value) url = url + '&_' + key + '=' + value;
      } else {
        const storeKey = item.name + 'FilterMask';
        console.log('storeKey ', viewStore[storeKey]);
        const storeValue = viewStore[storeKey];
        if (storeValue) url = url + '&_' + item.name + '=' + storeValue;
      }
    });
    if (url) url = '?' + url.substring(1);
    console.log('url ', url);
    return url;
  };

  const onChangePrihodFilterMask = (mask) => {
    const URL = createURL('prihod', mask);
    router.push(URL);
  };

  const onChangeTargetFilterMask = (mask) => {
    const URL = createURL('target', mask);
    router.push(URL);
  };

  const onChangeServiceFilterMask = (mask) => {
    const URL = createURL('service', mask);
    router.push(URL);
  }

  const onChangeSearchFilterMask = () => {
    const URL = createURL('search', search.value);
    router.push(URL);
  };

  const onClearSearchFilterMask = () => {
    search.value = ''
    const URL = createURL('search', search.value);
    router.push(URL);
  };

  const setQueryParameters = () => {
    const query = route.query;

    const prihodID = query._prihod;
    const isPrihodFilterPresent = prihodStore.prihods.filter(item => item.id == prihodID).length;
    if (isPrihodFilterPresent) {
      viewStore.setPrihodFilterMask(prihodID);
    } else {
      viewStore.setPrihodFilterMask(null);
    }

    const targetID = query._target;
    const isTargetFilterPresent = nsiStore.targets.filter(item => item.id == targetID).length;
    if (isTargetFilterPresent) {
      viewStore.setTargetFilterMask(targetID);
    } else {
      viewStore.setTargetFilterMask(null);
    }

    const serviceID = query._service;
    const isServiceFilterPresent = nsiStore.services.filter(item => item.id == serviceID).length;
    if (isServiceFilterPresent) {
      viewStore.setServiceFilterMask(serviceID);
    } else {
      viewStore.setServiceFilterMask(null);
    }
    const searchStr = query._search;
    if (searchStr) {
      search.value = searchStr;
      viewStore.setSearchFilterMask(searchStr);
    } else {
      search.value = '';
      viewStore.setSearchFilterMask(null);
    }
  };

  const getPeoplesFromAPI = async () => {
    loader.value = true;
    // console.log('getPeoplesFromAPI');
    await peopleStore.getAllPeople();
    loader.value = false;
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

  onBeforeMount(async () => {
    // console.log('PeopleListView mount');
    loader.value = true;
    await router.isReady();
    await prihodStore.getPrihods();
    await nsiStore.getTargets();
    await nsiStore.getServices();
    setQueryParameters();
    await getPeoplesFromAPI();
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
    cursor: pointer;
  }
  .form-icon-delete {
    cursor: pointer;
  }
</style>