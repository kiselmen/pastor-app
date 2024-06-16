<template>
  <div class="form">
    <div class="form-header">Редактирование прихожанина {{ form.name }} {{ form.first_name }} {{ form.patronymic }}</div>
    <div v-if="loader" class="form-text">Loading...</div>
    <div v-if="!loader&&formStep === 0" class="card-name">Общая информация</div>
    <div v-if="!loader&&formStep === 1" class="card-name">Семья</div>
    <div class="form-container section-container" ref="formElem"  v-if="!loader">
      <div v-if="formStep === 0" class = "table2x">
        <!-- <div class="card-box"> -->
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
          <div class="form-group" >
            <label class="input-label">Приход</label>
            <InputSelector
                :text ="form.prihod"
                :id   = form.prihod_id
                :data ="prihodStore.prihods"
                :parentElem = "formElem"
                @selectItem="onPrihodSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.prihod_id">
              {{ peopleStore.errors?.prihod_id[0] }}
            </div>
          </div>
          <div class="form-group">
            <label class="input-label">Целевая группа</label>
            <InputSelector
                :text ="form.target"
                :id   = null
                :data ="nsiStore.targets"
                :parentElem = "formElem"
                @selectItem="onTargetSelect"
              />
            <div class="input-error" v-if="peopleStore.errors?.target_id">
              {{ peopleStore.errors?.target_id[0] }}
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
        <!-- </div> -->
      </div>
      <div v-if="formStep === 0" class="table2x">
        <!-- <div class="card-box"> -->
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
            <div class="form-group">
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
          <div class="form-group">
              <label class="input-label">Адрес</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.live_addres }"
                  v-model="form.live_addres" 
                  id="live_addres"
              >
              <div class="input-error" v-if="peopleStore.errors?.live_addres">
                {{ peopleStore.errors?.live_addres[0] }}
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
        <!-- </div> -->
      </div>
      <div v-if="formStep === 1" class="table1x">
          <div class="card-row">
            <DateItem>
              <template #icon>
                <SupportIcon />
              </template>
              <template #heading>
                {{ family.name }}
              </template>
            </DateItem>
          </div>
          <div class="form-group" v-if="familyStore.families?.length">
            <label class="input-label">Семья</label>
            <InputSelector
                :text ="family.name"
                :id   = form.family_id
                :data ="familyStore.families"
                :parentElem = "formElem"
                @selectItem="onFamilySelect"
              />
            <div class="input-error" v-if="familyStore.errors?.family_id">
              {{ peopleStore.errors?.family_id[0] }}
            </div>
          </div>
          <!-- <div class="card-box">
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
          </div> -->

      </div>
      <div class="form-buttons" v-if="formStep === maxFormSteps">
        <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
        <button @click.prevent="onEditPerson" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': 'Сохранить'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
      <div class="form-buttons" v-if="formStep !== maxFormSteps">
        <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
        <button @click.prevent="onMoveToStep('+')" class="btn btn-blue" :disabled="formStep >= maxFormSteps">Вперед</button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { usePeopleStore } from '@/stores/peopleStore';
  import { useFamilyStore } from '@/stores/familyStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import { useNsiStore } from '@/stores/nsiStore';
  import { reactive, ref, onBeforeMount } from 'vue';
  import FileUpload from '@/components/ui/FileUpload.vue';
  import InputSelector from '@/components/ui/InputSelector.vue';
  import SupportIcon from '@/components/icons/IconSupport.vue';
  import DateItem from '@/components/people/DateItem.vue';

  const props = defineProps({
    id: { type: Number, default: null },
  })

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
    live_addres: '',
    home_phone: '',
    mobile_phone: '',
    prihod: '',
    prihod_id: null,
    family_id: null,
    target: '',
    target_id: null,
  });
  const image = ref();
  const emits = defineEmits(['toggleModal']);
  const loader = ref(true);
  const persone = ref(null);
  const formElem = ref(null);
  const family = ref(null);
  const formStep = ref(0);
  const maxFormSteps = 1;

  const onImageUploaded = (data) => {
    image.value = data;
  };

  const onImageCleared = () => {
    image.value = null;
  };

  const onPrihodSelect = (id) => {
    form.prihod_id = id;
  };

  const onTargetSelect = (id) => {
    form.target_id = id;
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

  const onEditPerson = async () => {
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
    formData.append('live_addres', form.live_addres);
    formData.append('home_phone', form.home_phone);
    formData.append('mobile_phone', form.mobile_phone);
    formData.append('id', props.id);
    formData.append('prihod_id', form.prihod_id);
    formData.append('target_id', form.target_id);
    formData.append('family_id', form.family_id);
    if (image.value) {
      const fileName = image.value.name;
      const fileData = image.value;
      formData.append('image', fileData, fileName);
    } 
    await peopleStore.editPersone(formData);
    if (!peopleStore.totalCountErrors) {
      emits('toggleModal');
    }
    loader.value = false;
  };

  onBeforeMount( async () => {
    loader.value = true;
    peopleStore.clearErrorsState();
    await prihodStore.getPrihods();
    persone.value = peopleStore.peoples.filter(item => item.id === props.id)[0];

    await nsiStore.getTargets();
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
    form.live_addres = persone.value.live_addres;
    form.home_phone = persone.value.home_phone;
    form.mobile_phone = persone.value.mobile_phone;
    form.id = props.id;
    form.prihod = persone.value.PrihodName;
    form.prihod_id = persone.value.prihod_id;
    form.target = persone.value.TargetName;
    form.target_id = persone.value.target_id;
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
  }

  .card-box {
    padding: 10px;
  }

  .form-buttons {
    padding-top: 20px;
    margin: 0 auto;
  }

</style>