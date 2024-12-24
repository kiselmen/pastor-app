<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление прихожанина</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow&&formStep === 0" class="card-name">Общая информация</div>
    <div v-if="!loader&&!confirmWindow&&formStep === 1" class="card-name">Семья</div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div v-if="formStep === 0" class = "table1x">
          <div class="form-group">
            <label class="input-label">Основание</label>
            <InputSelector
                text = "Выберите операцию"
                :id   = form.action_id
                :data ="actionsData"
                :parentElem = "formElem"
                @selectItem="onActionSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.action_id">
              {{ peopleStore.errors?.action_id[0] }}
            </div>
          </div>
      </div>    
      <div v-if="formStep === 0" class = "table2x">
        <div class="form-group" v-if="curAction !== null">
            <label class="input-label">EMail</label>
            <input 
                type="email"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.email }"
                v-model="form.email" 
                id="email"
            >
            <div class="input-error" v-if="peopleStore.errors?.email">
              {{ peopleStore.errors?.email[0] }}
            </div>
        </div>
        <div class="form-group" v-if="curAction !== null">
            <label class="input-label">Пол</label>
            <InputSelector
                text ="Выберите пол"
                :id   = form.sex_id
                :data ="nsiStore.sexes"
                :parentElem = "formElem"
                @selectItem="onSexSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.prihod_id">
              {{ peopleStore.errors?.prihod_id[0] }}
            </div>
        </div>
        <div class="form-group" v-if="curAction !== null">
            <label class="input-label">Участок</label>
            <InputSelector
                text ="Участок"
                :id   = "form.prihod_id"
                :data ="avalablePrihods"
                :parentElem = "formElem"
                @selectItem="onPrihodSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.prihod_id">
              {{ peopleStore.errors?.prihod_id[0] }}
            </div>
        </div>
        <div class="form-group" v-if="curAction === 3 && curAction !== null">
            <label class="input-label">Причина переезда</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.discription }"
                v-model="form.discription" 
                id="discription"
            >
            <div class="input-error" v-if="peopleStore.errors?.discription">
              {{ peopleStore.errors?.discription[0] }}
            </div>
        </div>
        <div class="form-group" v-if="curAction !== null">
            <FileUpload
              v-model="image"
              @uploadImage="onImageUploaded"
              @clearImage="onImageCleared"
            />
        </div>
        <div class="form-row" v-if="curAction !== null">
          <div class="form-group">
            <label class="input-label">Дата рождения</label>
            <input 
                type="date"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.birthday_date }"
                v-model="form.birthday_date" 
                id="birthday_date"
            >
            <div class="input-error" v-if="peopleStore.errors?.birthday_date">
              {{ peopleStore.errors?.birthday_date[0] }}
            </div>
          </div>
          <div class="form-group" v-if="curAction === 0 || curAction === 2 || curAction === 3">
            <label class="input-label">Дата крещения</label>
            <input 
                type="date"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.baptism_date }"
                v-model="form.baptism_date" 
                id="baptism_date"
            >
            <div class="input-error" v-if="peopleStore.errors?.baptism_date">
              {{ peopleStore.errors?.baptism_date[0] }}
            </div>
          </div>
          <div class="form-group" v-if="curAction === 0">
            <label class="input-label">Дата смерти</label>
            <input 
                type="date"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.death_date }"
                v-model="form.death_date" 
                id="death_date"
            >
            <div class="input-error" v-if="peopleStore.errors?.death_date">
              {{ peopleStore.errors?.death_date[0] }}
            </div>
          </div>
        </div>
      </div>
      <div v-if="formStep === 0" class="table2x">

        <div class="form-group" v-if="curAction !== null">
            <label class="input-label">Фамилия</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.name }"
                v-model="form.name" 
                id="name"
            >
            <div class="input-error" v-if="peopleStore.errors?.name">
              {{ peopleStore.errors?.name[0] }}
            </div>
        </div>
        <div class="form-group" v-if="curAction !== null">
            <label class="input-label">Имя</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.first_name }"
                v-model="form.first_name" 
                id="first_name"
            >
            <div class="input-error" v-if="peopleStore.errors?.first_name">
              {{ peopleStore.errors?.first_name[0] }}
            </div>
        </div>
        <div class="form-group" v-if="curAction !== null">
            <label class="input-label">Отчество</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.patronymic }"
                v-model="form.patronymic" 
                id="patronymic"
            >
            <div class="input-error" v-if="peopleStore.errors?.patronymic">
              {{ peopleStore.errors?.patronymic[0] }}
            </div>
        </div>
        <div class="form-row" v-if="curAction !== null">
          <div class="form-group">
              <label class="input-label">Индекс</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.live_index }"
                  v-model="form.live_index" 
                  id="live_index"
              >
              <div class="input-error" v-if="peopleStore.errors?.live_index">
                {{ peopleStore.errors?.live_index[0] }}
              </div>
          </div>
          <div class="form-group">
              <label class="input-label">Город</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.live_town }"
                  v-model="form.live_town" 
                  id="live_town"
              >
              <div class="input-error" v-if="peopleStore.errors?.live_town">
                {{ peopleStore.errors?.live_town[0] }}
              </div>
          </div>
        </div>
        <div class="form-row" v-if="curAction !== null">
          <div class="form-group">
              <label class="input-label">Улица</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.live_street }"
                  v-model="form.live_street" 
                  id="live_street"
              >
              <div class="input-error" v-if="peopleStore.errors?.live_street">
                {{ peopleStore.errors?.live_street[0] }}
              </div>
          </div>
          <div class="form-group">
              <label class="input-label">Дом</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.live_house }"
                  v-model="form.live_house" 
                  id="live_house"
              >
              <div class="input-error" v-if="peopleStore.errors?.live_house">
                {{ peopleStore.errors?.live_house[0] }}
              </div>
          </div>
          <div class="form-group">
              <label class="input-label">Квартира</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.live_flat }"
                  v-model="form.live_flat" 
                  id="live_flat"
              >
              <div class="input-error" v-if="peopleStore.errors?.live_flat">
                {{ peopleStore.errors?.live_flat[0] }}
              </div>
          </div>
        </div>
        <div class="form-row" v-if="curAction !== null">
          <div class="form-group">
            <label class="input-label">Дом телефон</label>
            <input 
                type="phone"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.home_phone }"
                v-model="form.home_phone" 
                id="home_phone"
            >
            <div class="input-error" v-if="peopleStore.errors?.home_phone">
              {{ peopleStore.errors?.home_phone[0] }}
            </div>
          </div>
          <div class="form-group">
            <label class="input-label">Мобильный телефон</label>
            <input 
                type="phone"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.mobile_phone }"
                v-model="form.mobile_phone" 
                id="mobile_phone"
            >
            <div class="input-error" v-if="peopleStore.errors?.mobile_phone">
              {{ peopleStore.errors?.mobile_phone[0] }}
            </div>
          </div>
        </div>
      </div>
      <div v-if="formStep === 0" class="table1x">
        <div class="form-group" v-if="curAction !== null">
          <label class="input-label">Описание</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.discription }"
                v-model="form.discription" 
                id="discription"
            >
            <div class="input-error" v-if="peopleStore.errors?.discription">
              {{ peopleStore.errors?.discription[0] }}
            </div>
        </div>            
      </div>
      <div v-if="formStep === 1" class="table1x">
          <div>
            <label class="checkbox-control">
              <input type="checkbox" v-model="isOpenNewFamily" @change="onClearFamilyData"/>
              Новая семья (Только для главы семьи)
            </label>
          </div>
          <KeepAlive>
            <div class="form-group" v-if="families?.length && !isOpenNewFamily">
              <label class="input-label">Семья</label>
              <InputSelector
                  text ="Выберите семью"
                  :id   = form.family_id
                  :data ="families"
                  :parentElem = "formElem"
                  @selectItem="onFamilySelect"
                />
              <div class="input-error" v-if="peopleStore.errors?.family_id">
                {{ peopleStore.errors?.family_id[0] }}
              </div>
            </div>
          </KeepAlive>
          <div class="form-group" v-if="nsiStore.relations?.length && !isOpenNewFamily">
            <label class="input-label">Отношение</label>
            <InputSelector
                text = "Выберите отношение"
                :id   = form.relation_id
                :data ="sexRelations"
                :parentElem = "formElem"
                @selectItem="onRelationSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.relation_id">
              {{ peopleStore.errors?.relation_id[0] }}
            </div>
          </div>

          <div v-if="isOpenNewFamily" class="form-group">
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
          <div v-if="isOpenNewFamily" class="form-group">
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
        <div class="form-text">Создать прихожанина?</div>
      </div>
    </div>
    <div v-if="formStep === maxFormSteps&&!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
      <button @click.prevent="onCreatePerson" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': 'Сохранить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div v-if="formStep !== maxFormSteps&&!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
      <button @click.prevent="onMoveToStep('+')" class="btn btn-blue" :disabled="formStep >= maxFormSteps || curAction === null">Вперед</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>
</template>

<script setup>
  import { usePeopleStore } from '@/stores/peopleStore';
  import { useFamilyStore } from '@/stores/familyStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import { useNsiStore } from '@/stores/nsiStore';
  import { useUserStore } from '@/stores/userStore';
  import { reactive, ref, onBeforeMount, computed } from 'vue';
  import FileUpload from '@/components/ui/FileUpload.vue';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const props = defineProps({
    id: { type: Number, default: null },
    isCopyProccess: { type: Boolean, default: false },
  });

  const peopleStore = usePeopleStore();
  const prihodStore = usePrihodStore();
  const nsiStore = useNsiStore();
  const familyStore = useFamilyStore();
  const userStore = useUserStore();

  const form = reactive({
    email: '',
    first_name: '',
    name: '',
    patronymic: '',
    birthday_date: '',
    baptism_date: '',
    death_date: '',
    live_index: '',
    live_town: '',
    live_street: '',
    live_house: '',
    live_flat: '',
    home_phone: null,
    mobile_phone: null,
    discription: '',
    prihod: '',
    prihod_id: null,
    family_id: null,
    relation_id: null,
    action: '',
    action_id: null,
    sex: '',
    sex_id: null,
    family_name: '',
    family_discription: null,
    family_head_id: null,
    family_candidates: [],
  });

  const formElem = ref(null);
  const image = ref();
  const emits = defineEmits(['toggleModal']);
  const loader = ref(false);
  const confirmWindow = ref(false);
  // const family = ref(null);
  const formStep = ref(0);
  const maxFormSteps = 1;
  const isOpenNewFamily = ref(false);
  const curAction = ref(null);

  const actionsData = [
    {id: 0, name: 'Ввод начальных данных'},
    {id: 1, name: 'Рождение'},
    {id: 2, name: 'Вхождение в веру'},
    {id: 3, name: 'Переехал из другой церкви'},
  ];

  const avalablePrihods = computed(() => {
    let isAdmin = false;
    const prihodsIDs = [];
    userStore.user?.permition?.forEach(permition => {
      if (permition.type == 0) isAdmin = true;
      if (permition.type == 1) prihodsIDs.push(permition.source_id);
    });
    if (isAdmin) {
      return prihodStore.prihods
    } else {
      return prihodStore.prihods.filter(item => prihodsIDs.filter(id => id == item.id).length);
    }
  });

  const sexRelations = computed(() => {
    return form.sex_id !== null ? nsiStore.relations.filter(item => item.sex_id == form.sex_id): nsiStore.relations;
  });

  const families = computed(() => {
    const rebuildedFamilies = [];
    familyStore.families.forEach(item => {
      const newFamilyItem = {};
      newFamilyItem.id = item.id;
      newFamilyItem.name = item.name;
      newFamilyItem.image_url = item.head ? item.head.image_url: null;
      rebuildedFamilies.push(newFamilyItem);
    });
    return rebuildedFamilies;
  });

  const onImageUploaded = (data) => {
    image.value = data;
  };

  const onImageCleared = () => {
    image.value = null;
  };

  const onPrihodSelect = (id) => {
    form.prihod_id = id;
  };

  const onActionSelect = (id) => {
    form.action_id = id;
    if (form.action_id !== null) {
      form.action = actionsData.filter(item => item.id == id)[0].name;
    }
    curAction.value = id;
  }

  const onSexSelect = (id) => {
    form.sex_id = id;
  }

  const onMoveToStep = (type) => {
    if (type === "-") {
      formStep.value--;
    } else if (type === '+') {
      formStep.value++;
    }
  };

  const onFamilySelect = (id) => {
    // family.value = familyStore.families.filter(item => item.id === id)[0];
    form.family_id = id;
  };

  const onClearFamilyData = () => {
    if (isOpenNewFamily.value) {
      form.family_id = null;
      form.relation_id = 0;
    } else {
      form.relation_id = null;
    }
  };

  const onRelationSelect = (id) => {
    form.relation_id = id;
  };

  const onCreatePerson = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    peopleStore.clearErrorsState();
    let formData = new FormData();
    formData.append('email', form.email);
    formData.append('first_name', form.first_name);
    formData.append('name', form.name);
    formData.append('patronymic', form.patronymic);
    formData.append('birthday_date', form.birthday_date);
    formData.append('baptism_date', form.baptism_date);
    formData.append('death_date', form.death_date);
    formData.append('live_index', form.live_index);
    formData.append('live_town', form.live_town);
    formData.append('live_street', form.live_street);
    formData.append('live_house', form.live_house);
    formData.append('live_flat', form.live_flat);
    formData.append('home_phone', form.home_phone);
    formData.append('mobile_phone', form.mobile_phone);
    formData.append('discription', form.discription);
    formData.append('prihod_id', form.prihod_id);
    formData.append('action_id', form.action_id);
    formData.append('family_id', form.family_id);
    if (form.family_id === null) {
      formData.append('relation_id', form.sex_id == 1 ? 0: 1);
    } else {
      formData.append('relation_id', form.relation_id);
    }
    formData.append('sex_id', form.sex_id);
    formData.append('discription', form.discription);
    if (image.value) {
      const fileName = image.value.name;
      const fileData = image.value;
      formData.append('image', fileData, fileName);
    }
    formData.append('family_name', form.family_name);
    formData.append('family_discription', form.family_discription);
    formData.append('family_head_id', form.family_head_id);
    formData.append('family_candidates', form.family_candidates);

    await peopleStore.addNewPersone(formData);
    console.log('errors', peopleStore.totalCountErrors);
    if (!peopleStore.totalCountErrors) {
      emits('toggleModal');
    }
    confirmWindow.value = false;
    loader.value = false;
  };

  onBeforeMount(async () => {
    loader.value = true;
    peopleStore.clearErrorsState();0
    await prihodStore.getPrihods();
    await familyStore.getAllFamilies();
    if (props.isCopyProccess) {
      let persone = null; 
      if (props.id) {
        persone = peopleStore.peoples.filter(item => item.id === props.id)[0];
      } else {
        persone = peopleStore.onePersone;
      }
      form.email = persone.email;
      form.first_name = persone.first_name;
      form.name = persone.name;
      form.patronymic = persone.patronymic;
      form.birthday_date = persone.birthday_date;
      form.baptism_date = persone.baptism_date ? persone.baptism_date: '';
      form.death_date = persone.death_date ? persone.death_date: '';
      // form.image_url = persone.image_url;
      form.live_index = persone.live_index;
      form.live_town = persone.live_town;
      form.live_street = persone.live_street;
      form.live_house = persone.live_house;
      form.live_flat = persone.live_flat;
      form.home_phone = persone.home_phone;
      form.mobile_phone = persone.mobile_phone;
      form.discription = persone.discription;
      form.prihod = persone.PrihodName;
      form.prihod_id = persone.prihod_id;
      // form.target = persone.TargetName;
      // form.target_id = persone.target_id;
      form.sex = persone.SexName;
      form.sex_id = persone.sex_id;
      form.family_id = persone.family_id;
    }
    loader.value = false;
  })

</script>

<style lang="scss" scoped>

  .people {
    border: 1px solid var(--color-background);
    border-radius: 10px;
    margin: 20px;

    &-container {
      max-width: 1440px;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
      margin: 0 auto;
      margin-top: 30px;
    }
  }
  .section {
    &-container {
      width: 100%;
      display: flex;
    }
  }

  .table2x{
    flex-basis: 50%;
    padding: 0 20px;
    max-width: 750px;
    @media (max-width: 1080px) {
      flex-basis: 100%;
    }  
  }

</style>