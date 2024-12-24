<template>
  <div class="form" ref="formElem">
    <div class="form-header">Редактирование прихожанина {{ form.name }} {{ form.first_name }} {{ form.patronymic }}</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow&&formStep === 0" class="card-name">Общая информация</div>
    <div v-if="!loader&&!confirmWindow&&formStep === 1" class="card-name">Семья</div>
    <div  v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
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
      <div v-if="formStep === 0 && curAction === 4" class = "table2x">
          <div class="form-group">
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
          <div class="form-group">
            <label class="input-label">Пол</label>
            <InputSelector
                text = "Пол"
                :id   = form.sex_id
                :data ="nsiStore.sexes"
                :parentElem = "formElem"
                @selectItem="onSexSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.sex_id">
              {{ peopleStore.errors?.sex_id[0] }}
            </div>
          </div>
          <div class="form-group">
              <FileUpload v-if="persone"
                v-model="image"
                :prevImage = "persone.image_url"
                @uploadImage="onImageUploaded"
                @clearImage="onImageCleared"
              />
          </div>
          <div class="form-row">
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
            <div class="form-group">
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
          </div>            
      </div>
      <div v-if="formStep === 0 && curAction === 4" class = "table2x">
          <div class="form-group">
              <label class="input-label">Фамилия</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.name }"
                  v-model="form.name" 
                  id="first_name"
              >
              <div class="input-error" v-if="peopleStore.errors?.name">
                {{ peopleStore.errors?.name[0] }}
              </div>
          </div>
          <div class="form-group">
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
          <div class="form-group">
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
          <div class="form-row">
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
          <div class="form-row">
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
          <div class="form-row">
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
      <div v-if="formStep === 0 && curAction === 4" class="table1x">
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
          <div class="card-row">
            <DateItem>
              <template #icon>
                <SupportIcon />
              </template>
              <template #heading>
                {{ family?.name }}
              </template>
            </DateItem>
          </div>
          <div class="form-group" v-if="families?.length">
            <label class="input-label">Семья</label>
            <InputSelector
                :text ="family?.name"
                :id   = form.family_id
                :data ="families"
                :parentElem = "formElem"
                @selectItem="onFamilySelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.family_id">
              {{ peopleStore.errors?.family_id[0] }}
            </div>
          </div>
      </div>
      <div v-if="formStep === 0 && curAction === 5" class = "table1x">
        <div class="form-group" v-if="curAction === 5 && curAction !== null">
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
      <div v-if="formStep === 0 && curAction === 6" class = "table1x">
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
      <div v-if="formStep === 0 && (curAction === 9 || curAction === 14)" class = "table1x">
        <div class="form-group">
            <label class="input-label">Участок</label>
            <InputSelector
                text = "Участок"
                :id   = form.prihod_id
                :data ="avalablePrihods"
                :parentElem = "formElem"
                @selectItem="onPrihodSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.prihod_id">
              {{ peopleStore.errors?.prihod_id[0] }}
            </div>
        </div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Сохранить изменения?</div>
      </div>
    </div>
    <div v-if="(formStep === maxFormSteps&&!confirmWindow) && (curAction !== null)" class="form-buttons form-bottom">
      <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
      <button @click.prevent="onEditPerson" class="btn btn-blue" :disabled="loader || curAction === null">{{ loader ? 'Сохранение...': 'Сохранить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div v-if="(formStep !== maxFormSteps&&!confirmWindow) || (curAction === null)" class="form-buttons form-bottom">
      <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
      <button @click.prevent="onMoveToStep('+')" class="btn btn-blue" :disabled="formStep >= maxFormSteps || curAction === null">Вперед</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>
</template>

<script setup>
  import { reactive, ref, onBeforeMount, computed } from 'vue';
  import { usePeopleStore } from '@/stores/peopleStore';
  import { useFamilyStore } from '@/stores/familyStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import { useNsiStore } from '@/stores/nsiStore';

  import FileUpload from '@/components/ui/FileUpload.vue';
  import InputSelector from '@/components/ui/InputSelector.vue';
  import SupportIcon from '@/components/icons/IconSupport.vue';
  import DateItem from '@/components/people/DateItem.vue';

  const props = defineProps({
    id: { type: Number, default: null },
  });

  const peopleStore = usePeopleStore();
  const prihodStore = usePrihodStore();
  const nsiStore = useNsiStore();
  const familyStore = useFamilyStore();

  const form = reactive({
    email: '',
    first_name: '',
    name: '',
    patronymic: '',
    birthday_date: '',
    baptism_date: '',
    death_date: '',
    image_url: null,
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
    action: '',
    action_id: null,
    sex: '',
    sex_id: null,
    family_name: '',
    family_discription: '',
    family_head_id: null,
    family_candidates: [],
    pair: '',
    pair_id: null,
    familyNameAction_id: 0,
    discription: '',
    family_name: '',
    new_pair_name: '',
  });
  const image = ref();
  const emits = defineEmits(['toggleModal']);
  const loader = ref(true);
  const confirmWindow = ref(false);
  const persone = ref(null);
  const formElem = ref(null);
  const family = ref(null);
  const formStep = ref(0);
  // const maxFormSteps = ref(0);
  const curAction = ref(null);
  const isCloseNewFamilyName = ref(false);

  const hasPair = computed(() => {
    return persone.value.Pair.length;
  });

  const isUnder18 = computed(() => {
    return persone.value.Under18;
  });

  const isHeadOfFamily = computed(() => {
    const family = persone.value.family;
    if (family) {
      if (family.head_id === persone.value.id) return true;
    }
    return false;
  });

  const actionsData = computed(() => {
    if (hasPair.value) {
      return [
        {id: 4, name: 'Изменить данные'},
        {id: 5, name: 'Оформить смерть'},
        {id: 9, name: 'Сменить участок'},
        {id: 14, name: 'Выбытие в другую церковь'},
      ]
    } else 
      if (isUnder18.value) {
        return [
          {id: 4, name: 'Изменить данные'},
          {id: 5, name: 'Оформить смерть'},
          {id: 9, name: 'Сменить участок'},
          {id: 14, name: 'Выбытие в другую церковь'},
        ]
      } else {
        if (isHeadOfFamily.value) {
          return [
            {id: 4, name: 'Изменить данные'},
            {id: 5, name: 'Оформить смерть'},
            {id: 9, name: 'Сменить участок'},
            {id: 14, name: 'Выбытие в другую церковь'},
          ]
        } else {
          return [
            {id: 4, name: 'Изменить данные'},
            {id: 5, name: 'Оформить смерть'},
            {id: 6, name: 'Выделить в новую семью'},
            {id: 9, name: 'Сменить участок'},
            {id: 14, name: 'Выбытие в другую церковь'},
          ]
        }
      }
    
  });

  const avalablePrihods = computed(() => {
    if (curAction.value == 14) {
      return prihodStore.prihods.filter(item => item.is_global);
    } else {
      return prihodStore.prihods.filter(item => !item.is_global);
    }
  });

  const maxFormSteps = computed(() => {
    return curAction.value === 4 ? 1: 0;
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
      form.action = actionsData.value.filter(item => item.id == id)[0].name;
    }
    curAction.value = id;
  }

  const onSexSelect = (id) => {
    form.sex_id = id;
  };
  
  const onMoveToStep = (type) => {
    if (type === "-") {
      formStep.value--;
    } else if (type === '+') {
      formStep.value++;
    }
  };

  const onFamilySelect = (id) => {
    family.value = familyStore.families.filter(item => item.id === id)[0];
    form.family_id = id;
  };

  const onEditPerson = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    let formData = new FormData();
    formData.append('email', form.email);
    formData.append('first_name', form.first_name);
    formData.append('name', form.name);
    formData.append('patronymic', form.patronymic);
    formData.append('birthday_date', form.birthday_date);
    formData.append('baptism_date', form.baptism_date);
    formData.append('death_date', form.death_date ? form.death_date: '');
    formData.append('image_url', form.image_url);
    formData.append('live_index', form.live_index);
    formData.append('live_town', form.live_town);
    formData.append('live_street', form.live_street);
    formData.append('live_house', form.live_house);
    formData.append('live_flat', form.live_flat);
    formData.append('home_phone', form.home_phone);
    formData.append('mobile_phone', form.mobile_phone);
    formData.append('discription', form.discription);
    formData.append('id', form.id);
    formData.append('prihod_id', form.prihod_id);
    formData.append('action_id', form.action_id);
    formData.append('action', form.action);
    formData.append('family_id', form.family_id);
    formData.append('family_name', form.family_name);
    formData.append('family_discription', form.family_discription);
    formData.append('sex_id', form.sex_id);
    formData.append('pair', form.pair);
    formData.append('pair_id', form.pair_id);
    formData.append('familyNameAction_id', form.familyNameAction_id);
    formData.append('family_name', form.family_name);
    if (isCloseNewFamilyName.value) {
      formData.append('new_pair_name', '');      
    } else {
      formData.append('new_pair_name', form.new_pair_name);      
    }
    
    if (image.value) {
      const fileName = image.value.name;
      const fileData = image.value;
      formData.append('image', fileData, fileName);
    } 
    await peopleStore.editPersone(formData, props.id);
    if (!peopleStore.totalCountErrors) {
      emits('toggleModal');
    }
    console.log(formData);
    
    confirmWindow.value = false;
    loader.value = false;
  };

  onBeforeMount( async () => {
    loader.value = true;
    peopleStore.clearErrorsState();
    await prihodStore.getPrihods();
    
    if (props.id) {
      persone.value = peopleStore.peoples.filter(item => item.id === props.id)[0];
    } else {
      persone.value = peopleStore.onePersone;
    }
    // console.log('persone ', persone.value);

    // await nsiStore.getTargets();
    await familyStore.getAllFamilies();
    family.value = familyStore.families.filter(item => item.id === persone.value.family_id)[0];

    form.email = persone.value.email;
    form.first_name = persone.value.first_name;
    form.name = persone.value.name;
    form.patronymic = persone.value.patronymic;
    form.birthday_date = persone.value.birthday_date;
    form.baptism_date = persone.value.baptism_date;
    form.death_date = persone.value.death_date;
    form.image_url = persone.value.image_url;
    form.live_index = persone.value.live_index;
    form.live_town = persone.value.live_town;
    form.live_street = persone.value.live_street;
    form.live_house = persone.value.live_house;
    form.live_flat = persone.value.live_flat;
    form.home_phone = persone.value.home_phone;
    form.mobile_phone = persone.value.mobile_phone;
    form.discription = persone.value.discription;
    if (props.id) {
      form.id = props.id;
    } else {
      form.id = peopleStore.onePersone.id;
    }
    form.prihod = persone.value.PrihodName;
    form.prihod_id = persone.value.prihod_id;
    // form.target = persone.value.TargetName;
    // form.target_id = persone.value.target_id;
    form.sex = persone.value.SexName;
    form.sex_id = persone.value.sex_id;
    form.family_id = persone.value.family_id;
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

  .card-box {
    padding: 10px;
  }

  .form-buttons {
    padding-top: 20px;
    margin: 0 auto;
  }

</style>