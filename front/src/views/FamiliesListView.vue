<template>
  <h2 class="card-name">Семьи</h2>
  <div v-if="loader" class="card-container">
    <div class="card-text">Loading...</div>
  </div>
  <div v-if="!loader" class="card-container">
    <div class="form-row">
      <button 
        @click="openActionModal('addFamily')" 
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
            <RouterLink :to="'/families'">
              <DismissIcon v-if = "search" @click="search=''"/>
            </RouterLink>  
          </div>  
          <div class="form-icon-search">
            <RouterLink :to="'/families?search=' + search">
              <SearchLupaIcon />
            </RouterLink>  
          </div>  
        </div>
      </div>
    </div>
    <div class="card-items">

      <FamilyItem 
        class="card-item"
        @editFamily = "onEditFamily"
        v-for="family in filteredFamilies"
        :key = family.Id
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
  </ModalWrapper>
</template>

<script setup>
  import { ref, watch, onBeforeMount } from 'vue';
  import { useRoute, useRouter } from 'vue-router'
  import { useFamilyStore } from '@/stores/familyStore.js';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import AddFamily  from '@/components/family/AddFamily.vue';
  import EditFamily  from '@/components/family/EditFamily.vue';
  import FamilyItem  from '@/components/family/FamilyItem.vue';
  import SearchLupaIcon from '@/components/icons/IconSearchLupa.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue'

  const route = useRoute();
  const router = useRouter();
  const familyStore = useFamilyStore();

  const loader = ref(false);
  const isModalAction = ref(false);
  const action = ref('');
  const activeFamily = ref(null);
  const search = ref('');
  const filteredFamilies = ref([]);

  watch( () => route.query, () => {
    search.value = route.query.search;
    onFilterFamilies();
  }, { deep: true });

  watch(() => familyStore.families, () => {
    onFilterFamilies();
  }, { deep: true })

  const onFilterFamilies = () => {
    if (search.value) {
      filteredFamilies.value =  familyStore.families.filter(item => item.name.toLowerCase().indexOf(search.value.toLowerCase()) >= 0 ? true: false)
    } else {
      filteredFamilies.value = [...familyStore.families];
    }
  };

  const openActionModal = (actionValue) => {
    action.value = actionValue;
    isModalAction.value = true;
  };

  const onEditFamily = (id) => {
    action.value = 'editFamily';
    activeFamily.value = id;
    isModalAction.value = true;
  };

  onBeforeMount(async () => {
    loader.value = true;
    // console.log(familyStore);
    await familyStore.getAllFamilies();
    await router.isReady();
    search.value = route.query.search;
    onFilterFamilies();
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