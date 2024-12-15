<template>
  <div class="form" ref="formElem">
    <div class="form-header">Изменение семьи</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-group">
            <label class="input-label">Название</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': familyStore.errors?.name }"
                v-model="form.name" 
                id="name"
            >
            <div class="input-error" v-if="familyStore.errors?.name">
              {{ familyStore.errors?.name[0] }}
            </div>
        </div>
        <div class="form-group">
            <label class="input-label">Глава семьи</label>
            <InputSelector 
                :text="mainPersonName" 
                :id=form.head_id 
                :data="filteredPersons" 
                :parentElem="formElem"
                :disabled="isChangesNotPossible"
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
                  :disabled="isChangesNotPossible"
                  @selectItem="onSelectCandidate"
              />
            </div> 
            <div class="form-group">
              <button @click.prevent="onAddToFamily" :disabled = "isChangesNotPossible" class="btn btn-blue">В семью</button>
            </div>  
          </div>
          <div class="table2x">
            <div v-if ="!loader" class="group-items">
              <div class="group-item" v-if = "form.candidates.length" v-for ="candidate in form.candidates" :key="candidate.id">
                <div class="group-avatar" :style = "{ backgroundImage : 'url(' + getImgPath(candidate.image_url) +')' }"></div>
                <div class="group-name">{{ candidate.Name }}</div>
                  <div v-if = "!isChangesNotPossible" class="group-delete" @click="onDeleteCandidate(candidate.persone_id)">
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
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': familyStore.errors?.discription }"
                v-model="form.discription" 
                id="first_name"
            >
            <div class="input-error" v-if="familyStore.errors?.discription">
              {{ familyStore.errors?.discription[0] }}
            </div>
        </div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Сохранить изменения?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click="onEditFamily" class="btn btn-blue" :disabled="familyStore.loader">{{ familyStore.loader ? 'Изменение...': 'Изменить'}}</button>
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
  import { ref, reactive, onBeforeMount, computed } from 'vue';
  import { useFamilyStore } from '@/stores/familyStore';
  import { usePeopleStore } from '@/stores/peopleStore';
  import InputSelector from '@/components/ui/InputSelector.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';

  const props = defineProps({
    id: { type: Number, default: null },
    isChangesNotPossible: { type: Boolean, default: true },
  });

  const emits = defineEmits(['toggleModal']);

  const familyStore = useFamilyStore();
  const peopleStore = usePeopleStore();

  const loader = ref(true);
  const confirmWindow = ref(false);
  const form = reactive({
    name: '',
    discription: '',
    head_id: null,
    candidates: [],
    disable_change: true,
  });
  const formElem = ref(null);
  // const family = ref(null);
  const mainPersonName = ref('');
  const candidateId = ref(null);

  const curFamily = computed(() =>{
    if (props.id) {
      const filtered = familyStore.families.filter(item => item.id === props.id);
      return filtered[0];
    } else {
      return familyStore.oneFamily;
    }
  })

  const filteredPersons = computed(() => {
    const heads = [];
    peopleStore.peoples.forEach(item => {
      if (item.family?.head_id && item.family?.head_id != form.head_id) heads.push({persone_id: item.family.head_id});
    });
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

  const onEditFamily = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await familyStore.editFamily(form, props.id);
    if (!familyStore.totalCountErrors) {
      emits('toggleModal');
    }
    confirmWindow.value = false;
    loader.value = false;
  };

  onBeforeMount( async () => {
    loader.value = true;
    familyStore.clearErrorsState();
    await peopleStore.getAllPeople();
    // family.value = familyStore.families.filter(item => item.id === props.id)[0];

    form.name = curFamily.value.name;
    form.discription = curFamily.value.discription;
    form.id = curFamily.value.id;
    form.head_id = curFamily.value.head_id;
    form.disable_change = props.isChangesNotPossible;
    const isPersone = peopleStore.peoples.filter(item => item.id == curFamily.value.head_id);
    if (isPersone.length) mainPersonName.value = isPersone[0].first_name + ' ' + isPersone[0].name;

    peopleStore.peoples.filter(item => item.family_id == curFamily.value.id && item.id != curFamily.value.head_id).forEach(item=> {
      const newCandidate = { persone_id: item.id, Name: item.first_name + ' ' + item.name, image_url: item.image_url }
      form.candidates.push(newCandidate);
    })

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