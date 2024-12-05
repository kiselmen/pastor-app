<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление семьи</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class="table1x">
        <div class="form-group">
            <label class="input-label">Название</label>
            <input 
                type="text" 
                autocomplete="off" 
                class="input-box" 
                :class="{ 'is-invalid': familyStore.errors?.name }"
                v-model="form.name" id="name">
            <div class="input-error" v-if="familyStore.errors?.name">
                {{ familyStore.errors?.name[0] }}
            </div>
        </div>
        <div class="form-group">
            <label class="input-label">Глава семьи</label>
            <InputSelector 
                :text="mainPersonName" 
                :id=null 
                :data="filteredPersons" 
                :parentElem="formElem"
                @selectItem="onMainPersonSelect" />
            <div class="input-error" v-if="familyStore.errors?.head_id">
                {{ familyStore.errors?.head_id[0] }}
            </div>
        </div>
        <div class="form-container section-container" v-if = "filteredPersons">
          <div class="table2x">
            <div class="form-group">
              <label class="input-label">Член семьи</label>
              <InputSelector
                  :text = "''"
                  :id   ="candidateId"
                  :data ="filteredPersons"
                  :parentElem = "formElem"
                  @selectItem="onSelectCandidate"
              />
            </div> 
            <div class="form-group">
              <button @click.prevent="onAddToFamily" class="btn btn-blue">В семью</button>
            </div>  
          </div>
          <div class="table2x">
            <div v-if ="!loader" class="group-items">
              <div class="group-item" v-if = "form.candidates.length" v-for ="candidate in form.candidates" >
                <div class="group-avatar" :style = "{ backgroundImage : 'url(' + getImgPath(candidate.image_url) +')' }"></div>
                <div class="group-name">{{ candidate.Name }}</div>
                  <div class="group-delete" @click="onDeleteCandidate(candidate.persone_id)">
                    <DismissIcon/>
                  </div>
                </div> 
            </div>
          </div>
        </div>  
        <div class="form-group">
            <label class="input-label">Описание</label>
            <input 
                type="text" 
                autocomplete="off" 
                class="input-box"
                :class="{ 'is-invalid': 
                familyStore.errors?.discription }" 
                v-model="form.discription" 
                id="first_name">
            <div class="input-error" v-if="familyStore.errors?.discription">
                {{ familyStore.errors?.discription[0] }}
            </div>
        </div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Создать семью?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onCreateFamily" class="btn btn-blue" :disabled="familyStore.loader">{{ familyStore.loader ? 'Добавление...' : 'Добавить' }}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>
</template>

<script setup>

  import getImgPath from '@/utils/imagePlugin.js';
  import { ref, reactive, computed, onBeforeMount } from 'vue';
  import { useFamilyStore } from '@/stores/familyStore';
  import { usePeopleStore } from '@/stores/peopleStore';
  import InputSelector from '@/components/ui/InputSelector.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';

  const emits = defineEmits(['toggleModal']);

  const familyStore = useFamilyStore();
  const peopleStore = usePeopleStore();

  const loader = ref(false);
  const confirmWindow = ref(false);
  const form = reactive({
    name: '',
    discription: '',
    head_id: null,
    candidates: [],
  });
  const formElem = ref(null);
  const mainPersonName = ref('');
  const candidateId = ref(null);

  const filteredPersons = computed(() => {
    const heads = [];
    peopleStore.peoples.forEach(item => {
      if (item.family?.head_id) heads.push({persone_id: item.family.head_id});
    })
    return peopleStore.peoples.filter(item => {
      let isAvailable = true;
      heads.forEach(elem => {
        if (elem.persone_id === item.id) isAvailable = false;
      });
      return isAvailable;
    })
  });

  const onSelectCandidate = (id) => {
    candidateId.value = id;
  };

  const onAddToFamily = () => {
    const isPresent = form.candidates.filter(item => item.persone_id === candidateId.value);
    if (!isPresent.length) {
      const personeToAdd = filteredPersons.value.filter(item => item.id === candidateId.value);
      form.candidates = [
        ...form.candidates, {
          persone_id: personeToAdd[0].id, 
          Name: personeToAdd[0].first_name + ' ' + personeToAdd[0].name, 
          image_url: personeToAdd[0].image_url
        }
      ];
    }
  };

  const onDeleteCandidate = (id) => {
    const isNotPresent = form.candidates.filter(item => item.persone_id !== id);
    form.candidates = [...isNotPresent];
  };


  const onMainPersonSelect = (id) => {
    const isPersone = peopleStore.peoples.filter(item => item.id === id);
    if (isPersone.length) mainPersonName.value = isPersone[0].first_name + ' ' + isPersone[0].name;
    form.head_id = id;
  };

  const onCreateFamily = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await familyStore.addNewFamily(form);
    console.log('errors', familyStore.totalCountErrors);
    if (!familyStore.totalCountErrors) {
      emits('toggleModal');
    }
    confirmWindow.value = false;
    loader.value = false;
  };

  onBeforeMount(() => {
    loader.value = true;
    familyStore.clearErrorsState();
    peopleStore.getAllPeople();
    loader.value = false;
  })

</script>

<style lang="scss" scoped>
  .section {
    &-container {
      width: 100%;
      display: flex;
      gap: 10px;
    }
  }

  .form-container {
    margin-top: 0;
  }

  .table2x{
    flex-basis: 48%;
    padding: 0 0;
    max-width: 750px;
  }
</style>