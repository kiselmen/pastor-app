<template>
  <h2 class="card-name">Семьи</h2>
  <div v-if="loader" class="card-container">
    <div class="card-text">Loading...</div>
  </div>
  <div v-if="!loader" class="card-container">
    <div class="form-row">
      <!-- <button 
        @click="openActionModal('addFamily')" 
        type="button" 
        class="btn btn-blue" 
      >
          Создать
      </button> -->
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
              <DismissIcon 
                v-if = "search" 
                @click="onClearSearchFilterMask"
              />
          </div>  
          <div class="form-icon-search">
              <SearchLupaIcon 
                @click="onChangeSearchFilterMask"
              />
          </div>  
        </div>
      </div>
    </div>
    <div class="card-items">

      <FamilyItem 
        class="card-item"
        @editFamily = "onEditFamily"
        @moveFamily = "onMoveFamily"
        v-for="family in familyStore.families"
        :key = family.id
        :family = family
      />
    </div>
  </div>
  <ModalWrapper
      :is-modal-active="isModalAction"
      @close-modal="isModalAction = false"
    >
      <AddFamily v-if="action === 'addFamily'"
          @toggle-modal="isModalAction = false"
      />
      <EditFamily v-if="action === 'editFamily'"
          @toggle-modal="isModalAction = false"
          :id="activeFamily"
      />
      <MoveFamily v-if="action === 'moveFamily'"
          @toggle-modal="isModalAction = false"
          :id="activeFamily"
      />
  </ModalWrapper>
</template>

<script setup>
  import { createURL } from '@/utils/helper.js';
  import { ref, watch, onBeforeMount } from 'vue';
  import { useRoute, useRouter } from 'vue-router'
  import { useFamilyStore } from '@/stores/familyStore.js';
  import { useViewStore } from '@/stores/viewStore';  
  import { usePrihodStore } from '@/stores/prihodStore';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import AddFamily  from '@/components/family/AddFamily.vue';
  import EditFamily  from '@/components/family/EditFamily.vue';
  import MoveFamily  from '@/components/family/MoveFamily.vue';
  import FamilyItem  from '@/components/family/FamilyItem.vue';
  import SearchLupaIcon from '@/components/icons/IconSearchLupa.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue'
  import PrihodFilter from '@/components/prihod/PrihodFilter.vue';

  const route = useRoute();
  const router = useRouter();
  const familyStore = useFamilyStore();
  const viewStore = useViewStore();
  const prihodStore = usePrihodStore();

  const loader = ref(false);
  const isModalAction = ref(false);
  const action = ref('');
  const activeFamily = ref(null);
  const search = ref('');

  watch( () => route.query, async () => {
    // console.log('watch query');
    loader.value = true;
    setQueryParameters();
    await getFamiliesFromAPI();
    loader.value = false;
  }, { deep: true });

  const onChangePrihodFilterMask = (mask) => {0
    const URL = createURL('prihod', mask, viewStore);
    console.log('URL ', URL);
    
    router.push(URL);
  };

  const onChangeSearchFilterMask = () => {
    const URL = createURL('search', search.value, viewStore);
    router.push(URL);
  };

  const onClearSearchFilterMask = () => {
    search.value = ''
    const URL = createURL('search', search.value, viewStore);
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

    const searchStr = query._search;
    if (searchStr) {
      search.value = searchStr;
      viewStore.setSearchFilterMask(searchStr);
    } else {
      search.value = '';
      viewStore.setSearchFilterMask(null);
    }
  };

  const getFamiliesFromAPI = async () => {
    loader.value = true;
    await familyStore.getAllFamilies();
    loader.value = false;
  };

  const onEditFamily = (id) => {
    action.value = 'editFamily';
    activeFamily.value = id;
    isModalAction.value = true;
  };

  const onMoveFamily = (id) => {
    action.value = 'moveFamily';
    activeFamily.value = id;
    isModalAction.value = true;
  };

  onBeforeMount(async () => {
    loader.value = true;
    await router.isReady();
    await prihodStore.getPrihods();
    setQueryParameters();
    await getFamiliesFromAPI();
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