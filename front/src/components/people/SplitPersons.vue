<template>
  <div class="form" ref="formElem">
    <div class="form-header">Развод семьи</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow&&formStep === 0" class="card-name">Супруг, супруга</div>
    <div v-if="!loader&&!confirmWindow&&formStep === 1" class="card-name">Новая семья</div>
    <div v-if="!loader&&!confirmWindow&&formStep === 0" class="form-container section-container form-middle">
      <div class = "table1x">
          <div class="form-group">
            <label class="input-label">Отделяемый</label>
            <InputSelector
                text = "Выберите отделяемого"
                :id   = personeID
                :data ="peopleWithPair"
                :parentElem = "formElem"
                @selectItem="onPersoneSelect"
              />
              <div class="input-error" v-if="peopleStore.errors?.man_id">
                {{ peopleStore.errors?.man_id[0] }}
              </div>
          </div>

          <div class="form-container section-container" v-if = "personeID !== null">
            <div class="table2x">
              <div class="form-group">
                <label class="input-label">Ребенок</label>
                <InputSelector
                    text = "Ребенок"
                    :id   ="childID"
                    :data ="allChildrens"
                    :parentElem = "formElem"
                    @selectItem="onChildSelect"
                />
              </div> 
              <div class="form-group">
                <button @click.prevent="onAddtoNewFamily" :disabled="!childID" class="btn btn-blue">К отделяемому</button>
              </div>  
            </div>
            <div class="table2x">
              <div v-if ="!loader" class="group-items">
                <div class="group-item" v-if = "childrensToMove.length" v-for ="candidate in childrensToMove" :key = "candidate.id">
                  <div class="group-avatar" :style = "{ backgroundImage : 'url(' + getImgPath(candidate.image_url) +')' }"></div>
                  <div class="group-name">{{ candidate.FullName }}</div>
                    <div class="group-delete" @click="onDeleteCandidate(candidate.id)">
                      <DismissIcon/>
                    </div>
                  </div> 
              </div>
            </div>
          </div>  
      </div>
    </div>
    <div v-if="!loader&&!confirmWindow&&formStep === 1" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-group">
            <label class="input-label">Название семьи</label>
            <input 
                type="text" 
                autocomplete="off" 
                class="input-box" 
                :class="{ 'is-invalid': peopleStore.errors?.family_name }"
                v-model="form.family_name" id="name">
            <div class="input-error" v-if="peopleStore.errors?.family_name">
                {{ peopleStore.errors?.family_name[0] }}
            </div>
        </div>
        <div class="form-group">
            <label class="input-label">Описание семьи</label>
            <input 
                type="text" 
                autocomplete="off" 
                class="input-box"
                :class="{ 'is-invalid': 
                peopleStore.errors?.family_discription }" 
                v-model="form.family_discription" 
                id="first_name">
            <div class="input-error" v-if="peopleStore.errors?.family_discription">
                {{ peopleStore.errors?.family_discription[0] }}
            </div>
        </div>
      </div>  
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Оформить развод?</div>
      </div>
    </div>
    <div class="form-buttons form-bottom" v-if="formStep === maxFormSteps&&!confirmWindow">
      <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
      <button @click.prevent="onSplitPersons" class="btn btn-blue" :disabled="loader">{{ loader ? 'Оформление...': 'Оформить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div class="form-buttons form-bottom" v-if="formStep !== maxFormSteps&&!confirmWindow">
      <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
      <button @click.prevent="onMoveToStep('+')" class="btn btn-blue" :disabled="formStep >= maxFormSteps">Вперед</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div class="form-buttons form-bottom" v-if="!loader&&confirmWindow">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>  

</template>

<script setup>
  import getImgPath from '@/utils/imagePlugin.js';
  import { reactive, ref, onBeforeMount, computed } from 'vue';
  import { usePeopleStore } from '@/stores/peopleStore';
  import { useMsgStore } from '@/stores/msgStore';

  import InputSelector from '@/components/ui/InputSelector.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';

  const peopleStore = usePeopleStore();
  const msgStore = useMsgStore();

  const emits = defineEmits(['toggleModal']);

  const formElem = ref(null);
  const loader = ref(true);
  const confirmWindow = ref(false);
  const formStep = ref(0);
  const maxFormSteps = ref(1);
  const personeID = ref(null);
  const childID = ref(null);
  const childrensToMove = ref([]);
  const form = reactive({
    family_name: '',
    family_discription: '',
  });

  const peopleWithPair = computed(() => {
    return peopleStore.allPeoples.filter(item => item.Pair.length);
  });

  const persone = computed(() => {
    return peopleStore.allPeoples.filter(item => item.id === personeID.value)[0];
  });

  const allChildrens = computed(() => {
    return persone.value.AllChildrens
      .filter(child => {
        let isNotPresent = true;
        childrensToMove.value.forEach(childToMove => {
          if (childToMove.id === child.id) isNotPresent =false;
        });
        return isNotPresent;
      })
      .map(item => {
        return { ...item, FullName: item.name + ' ' + item.first_name }
      });
  });

  const personeHasChildrenMore18YearsOld = computed(() => {
      if (!persone.value) return false;
      return Boolean(persone.value.ChildrenMore18YearsOld.length);
  });

  const onPersoneSelect = (id) => {
    personeID.value = id;
    if (id === null) {
      childrensToMove.value = [];
    }
  };

  const onChildSelect = (id) => {
    childID.value = id;
  };

  const onAddtoNewFamily = () => {
    childrensToMove.value.push(allChildrens.value.filter(item => item.id === childID.value)[0]);
    childID.value = null;
  };

  const onDeleteCandidate = (id) => {
    childrensToMove.value = [...childrensToMove.value.filter(item => item.id !== id)];
  };

  const onMoveToStep = (type) => {
    if (type === "-") {
      formStep.value--;
    } else if (type === '+') {
      formStep.value++;
    }
  };

  const onSplitPersons = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };

  const onConfirmAction = async () => {
    peopleStore.clearErrorsState();
    if (personeHasChildrenMore18YearsOld.value) {
      msgStore.addMessage({name: 'У супругов имеются совершеннолетние дети, выделите их в отдельную семью', icon: 'warning'});
      confirmWindow.value = false;
    } else {
      loader.value = true;

      let formData = {
        family_name         : form.family_name,
        family_discription  : form.family_discription,
        persone_id          : personeID.value,
        childrens           : childrensToMove.value,
      };
      await peopleStore.splitPersons(formData);

      if (!peopleStore.totalCountErrors) {  
        emits('toggleModal');
      }
      confirmWindow.value = false;
      loader.value = false;

    }
  };

  onBeforeMount(async () => {
    await peopleStore.getAllPeopleForSelect();
    loader.value = false;
  });

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
