<template>
  <div class="form" ref="formElem">
    <div class="form-header">Создание семьи</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow&&formStep === 0" class="card-name">Супруг</div>
    <div v-if="!loader&&!confirmWindow&&formStep === 1" class="card-name">Супруга</div>
    <div  v-if="!loader&&!confirmWindow&&formStep === 0" class="form-container section-container form-middle">
      <div class = "table1x">
          <div class="form-group" v-if="!isManMissing">
            <label class="input-label">Cупруг</label>
            <InputSelector
                text = "Выберите супруга"
                :id   = manID
                :data ="menWithoutPair"
                :parentElem = "formElem"
                @selectItem="onManSelect"
              />
              <div class="input-error" v-if="peopleStore.errors?.man_id">
                {{ peopleStore.errors?.man_id[0] }}
              </div>
          </div>
          <div v-if="!isWomanMissing">
            <label class="checkbox-control">
              <input type="checkbox" v-model="isManMissing"/>
              Ввести нового
            </label>
          </div>
      </div>
      <div v-if="formStep === 0 && isManMissing" class = "table2x">
          <div class="form-group">
              <label class="input-label">EMail</label>
              <input 
                  type="email"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_email }"
                  v-model="form.man_email" 
                  id="man_email"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_email">
                {{ peopleStore.errors?.man_email[0] }}
              </div>
          </div>
          <div class="form-group">
              <FileUpload
                v-model="manImage"
                @uploadImage="onManImageUploaded"
                @clearImage="onManImageCleared"
              />
          </div>
      </div>
      <div v-if="formStep === 0 && isManMissing" class = "table2x">
          <div class="form-group">
              <label class="input-label">Имя</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_first_name }"
                  v-model="form.man_first_name" 
                  id="man_first_name"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_first_name">
                {{ peopleStore.errors?.man_first_name[0] }}
              </div>
          </div>
          <div class="form-group">
              <label class="input-label">Фамилия</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_name }"
                  v-model="form.man_name" 
                  id="man_name"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_name">
                {{ peopleStore.errors?.man_name[0] }}
              </div>
          </div>
          <div class="form-group">
              <label class="input-label">Отчество</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_patronymic }"
                  v-model="form.man_patronymic" 
                  id="man_patronymic"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_patronymic">
                {{ peopleStore.errors?.man_patronymic[0] }}
              </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="input-label">Дата рождения</label>
              <input 
                  type="date"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_birthday_date }"
                  v-model="form.man_birthday_date" 
                  id="man_birthday_date"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_birthday_date">
                {{ peopleStore.errors?.man_birthday_date[0] }}
              </div>
            </div>
            <div class="form-group">
              <label class="input-label">Дата крещения</label>
              <input 
                  type="date"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_baptism_date }"
                  v-model="form.man_baptism_date" 
                  id="man_baptism_date"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_baptism_date">
                {{ peopleStore.errors?.man_baptism_date[0] }}
              </div>
            </div>
          </div>            
          <div class="form-group">
              <label class="input-label">Адрес</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_live_addres }"
                  v-model="form.man_live_addres" 
                  id="man_live_addres"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_live_addres">
                {{ peopleStore.errors?.man_live_addres[0] }}
              </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="input-label">Дом телефон</label>
              <input 
                  type="phone"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_home_phone }"
                  v-model="form.man_home_phone" 
                  id="man_home_phone"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_home_phone">
                {{ peopleStore.errors?.man_home_phone[0] }}
              </div>
            </div>
            <div class="form-group">
              <label class="input-label">Мобильный телефон</label>
              <input 
                  type="phone"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.man_mobile_phone }"
                  v-model="form.man_mobile_phone" 
                  id="man_mobile_phone"
              >
              <div class="input-error" v-if="peopleStore.errors?.man_mobile_phone">
                {{ peopleStore.errors?.man_mobile_phone[0] }}
              </div>
            </div>
          </div>
      </div>
    </div>      
    <div  v-if="!loader&&!confirmWindow&&formStep === 1" class="form-container section-container form-middle">
      <div class = "table1x">
          <div class="form-group" v-if="!isWomanMissing">
            <label class="input-label">Cупругa</label>
            <InputSelector
                text = "Выберите супругу"
                :id   = womanID
                :data ="womenWithoutPair"
                :parentElem = "formElem"
                @selectItem="onWomanSelect"
              />
              <div class="input-error" v-if="peopleStore.errors?.woman_id">
                {{ peopleStore.errors?.woman_id[0] }}
              </div>
          </div>
          <div  v-if="!isManMissing">
            <label class="checkbox-control">
              <input type="checkbox" v-model="isWomanMissing" @change="onToggleWomanMissing"/>
              Ввести новую
            </label>
          </div>
      </div>    
      <div v-if="formStep === 1 && isWomanMissing" class = "table2x">
          <div class="form-group">
              <label class="input-label">EMail</label>
              <input 
                  type="email"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_email }"
                  v-model="form.woman_email" 
                  id="woman_email"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_email">
                {{ peopleStore.errors?.woman_email[0] }}
              </div>
          </div>
          <div class="form-group">
              <FileUpload
                v-model="womanImage"
                @uploadImage="onWomanImageUploaded"
                @clearImage="onWomanImageCleared"
              />
          </div>
      </div>
      <div v-if="formStep === 1 && isWomanMissing" class = "table2x">
          <div class="form-group">
              <label class="input-label">Имя</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_first_name }"
                  v-model="form.woman_first_name" 
                  id="woman_first_name"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_first_name">
                {{ peopleStore.errors?.woman_first_name[0] }}
              </div>
          </div>
          <div class="form-group">
              <label class="input-label">Фамилия</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_name }"
                  v-model="form.woman_name" 
                  id="woman_name"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_name">
                {{ peopleStore.errors?.woman_name[0] }}
              </div>
          </div>
          <div class="form-group">
              <label class="input-label">Отчество</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_patronymic }"
                  v-model="form.woman_patronymic" 
                  id="woman_patronymic"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_patronymic">
                {{ peopleStore.errors?.woman_patronymic[0] }}
              </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="input-label">Дата рождения</label>
              <input 
                  type="date"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_birthday_date }"
                  v-model="form.woman_birthday_date" 
                  id="woman_birthday_date"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_birthday_date">
                {{ peopleStore.errors?.woman_birthday_date[0] }}
              </div>
            </div>
            <div class="form-group">
              <label class="input-label">Дата крещения</label>
              <input 
                  type="date"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_baptism_date }"
                  v-model="form.woman_baptism_date" 
                  id="woman_baptism_date"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_baptism_date">
                {{ peopleStore.errors?.woman_baptism_date[0] }}
              </div>
            </div>
          </div>            
          <div class="form-group">
              <label class="input-label">Адрес</label>
              <input 
                  type="text"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_live_addres }"
                  v-model="form.woman_live_addres" 
                  id="woman_live_addres"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_live_addres">
                {{ peopleStore.errors?.woman_live_addres[0] }}
              </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="input-label">Дом телефон</label>
              <input 
                  type="phone"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_home_phone }"
                  v-model="form.woman_home_phone" 
                  id="woman_home_phone"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_home_phone">
                {{ peopleStore.errors?.woman_home_phone[0] }}
              </div>
            </div>
            <div class="form-group">
              <label class="input-label">Мобильный телефон</label>
              <input 
                  type="phone"
                  autocomplete = "off"
                  class="input-box"
                  :class="{ 'is-invalid': peopleStore.errors?.woman_mobile_phone }"
                  v-model="form.woman_mobile_phone" 
                  id="woman_mobile_phone"
              >
              <div class="input-error" v-if="peopleStore.errors?.woman_mobile_phone">
                {{ peopleStore.errors?.woman_mobile_phone[0] }}
              </div>
            </div>
          </div>
      </div>
    </div>
    <div  v-if="!loader&&!confirmWindow&&formStep === 2" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-group" v-if="!isManMissing">
            <label class="input-label">Фамилия супруга в семье</label>
            <input 
                type="email"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.man_new_name }"
                v-model="form.man_new_name" 
                id="man_new_name"
            >
            <div class="input-error" v-if="peopleStore.errors?.man_new_name">
              {{ peopleStore.errors?.man_new_name[0] }}
            </div>
        </div>
        <div class="form-group" v-if="!isWomanMissing">
            <label class="input-label">Фамилия супруги в семье</label>
            <input 
                type="email"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.woman_new_name }"
                v-model="form.woman_new_name" 
                id="woman_new_name"
            >
            <div class="input-error" v-if="peopleStore.errors?.woman_new_name">
              {{ peopleStore.errors?.woman_new_name[0] }}
            </div>
        </div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Оформить союз?</div>
      </div>
    </div>
    <div class="form-buttons form-bottom" v-if="formStep === maxFormSteps&&!confirmWindow">
      <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
      <button @click.prevent="onMergePersons" class="btn btn-blue" :disabled="loader">{{ loader ? 'Оформление...': 'Оформить'}}</button>
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
  import { reactive, ref, onBeforeMount, computed } from 'vue';
  import { usePeopleStore } from '@/stores/peopleStore';
  import { useMsgStore } from '@/stores/msgStore';

  import InputSelector from '@/components/ui/InputSelector.vue';
  import FileUpload from '@/components/ui/FileUpload.vue';

  const peopleStore = usePeopleStore();
  const msgStore = useMsgStore();

  const emits = defineEmits(['toggleModal']);

  const formElem = ref(null);
  const loader = ref(true);
  const confirmWindow = ref(false);
  const formStep = ref(0);
  const maxFormSteps = ref(2);
  const manID = ref(null);
  const womanID = ref(null);
  const isManMissing = ref(false);
  const isWomanMissing = ref(false);
  const form = reactive({
    man_email: '',
    man_first_name: '',
    man_name: '',
    man_patronymic: '',
    man_birthday_date: '',
    man_baptism_date: '',
    man_death_date: '',
    man_image_url: null,
    man_live_addres: '',
    man_home_phone: '',
    man_mobile_phone: '',
    man_sex: 'Муж',
    man_sex_id: 1,

    woman_email: '',
    woman_first_name: '',
    woman_name: '',
    woman_patronymic: '',
    woman_birthday_date: '',
    woman_baptism_date: '',
    woman_death_date: '',
    woman_image_url: null,
    woman_live_addres: '',
    woman_home_phone: '',
    woman_mobile_phone: '',
    woman_sex: 'Жен',
    woman_sex_id: 2,

    man_new_name: '',
    woman_new_name: '',
    // prihod: '',
    // prihod_id: null,
  });
  const manImage = ref();
  const womanImage = ref();

  const peoplesWithoutPair = computed(() => {
    const avalablePeoples = peopleStore.allPeoples.filter(onePersone => !onePersone.Pair.length && !onePersone.Under18 && onePersone.death_date === null);
    return avalablePeoples;
  });

  const menWithoutPair = computed(() => {
    return peoplesWithoutPair.value.filter(item => item.sex_id === 1).filter(item => {
      return isWomanMissing.value ? item.family.head_id === item.id : true;
    });
  });

  const womenWithoutPair = computed(() => {
    return peoplesWithoutPair.value.filter(item => item.sex_id === 2).filter(item => {
      return isManMissing.value ? item.family.head_id === item.id : true;
    });
  });

  const manHasChildrenMore18YearsOld = computed(() => {
    if (isManMissing.value) {
      return false;
    } else {
      const persone = peopleStore.allPeoples.filter(item => item.id == manID.value)[0];
      if (!persone) return false;
      return Boolean(persone.ChildrenMore18YearsOld.length);
    }
  });

  const womanHasChildrenMore18YearsOld = computed(() => {
    if (isManMissing.value) {
      return false;
    } else {
      const persone = peopleStore.allPeoples.filter(item => item.id == womanID.value)[0];
      if (!persone) return false;
      return Boolean(persone.ChildrenMore18YearsOld.length);
    }
  });

  const onManSelect = (id) => {
    manID.value = id;
    const currentPerson = peopleStore.allPeoples.filter(item => item.id === manID.value)[0];
    form.man_new_name = currentPerson.name; 
  };

  const onWomanSelect = (id) => {
    womanID.value = id;
    const currentPerson = peopleStore.allPeoples.filter(item => item.id === womanID.value)[0];
    form.woman_new_name = currentPerson.name; 
  };

  const onToggleWomanMissing = () => {
    if (!isManMissing.value) {
      const hasOwnFamily = peopleStore.allPeoples.
        filter(item => item.id === manID.value).
        filter(item => item.family?.head_id === item.id);
      if (!hasOwnFamily.length) {
        msgStore.addMessage({name: 'У супруга нет свой семьи выделите его в отдельную семью', icon: 'warning'});
        manID.value = null;
      }
    }  
  };

  const onManImageUploaded = (data) => {
    manImage.value = data;
  };

  const onWomanImageUploaded = (data) => {
    womanImage.value = data;
  };

  const onManImageCleared = () => {
    manImage.value = null;
  };

  const onWomanImageCleared = () => {
    womanImage.value = null;
  };

  const onMoveToStep = (type) => {
    if (type === "-") {
      formStep.value--;
    } else if (type === '+') {
      formStep.value++;
    }
  };

  const onMergePersons = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };

  const onConfirmAction = async () => {
    peopleStore.clearErrorsState();
    if (manHasChildrenMore18YearsOld.value || womanHasChildrenMore18YearsOld.value) {
      msgStore.addMessage({name: 'У супругов имеются совершеннолетние дети, выделите их в отдельную семью', icon: 'warning'});
      confirmWindow.value = false;
    } else if (!isManMissing.value && !isWomanMissing.value) {
      const hasOwnFamily = peopleStore.allPeoples.
        filter(item => item.id === manID.value || item.id === womanID.value).
        filter(item => item.family?.head_id === item.id);
      if (!hasOwnFamily.length) {
        msgStore.addMessage({name: 'Ни у одного из супругов нет свой семьи выделите одного из них в отдельную семью', icon: 'warning'});
        confirmWindow.value = false;
      }
    } else {
      loader.value = true;
      let formData = new FormData();
      if (isManMissing.value) {
        // console.log('Вводим в ручную мужа');
        formData.append('man_email', form.man_email);
        formData.append('man_first_name', form.man_first_name);
        formData.append('man_name', form.man_name);
        formData.append('man_patronymic', form.man_patronymic);
        formData.append('man_birthday_date', form.man_birthday_date);
        formData.append('man_baptism_date', form.man_baptism_date);
        formData.append('man_death_date', form.man_death_date);
        formData.append('man_live_addres', form.man_live_addres);
        formData.append('man_home_phone', form.man_home_phone);
        formData.append('man_mobile_phone', form.man_mobile_phone);
        formData.append('man_relation_id', 0);
        formData.append('man_sex_id', 1);
        formData.append('man_discription', '');
        if (manImage.value) {
          const fileName = manImage.value.name;
          const fileData = manImage.value;
          formData.append('man_image', fileData, fileName);
        }
      } else {
        formData.append('man_id', manID.value);
      }
      if (isWomanMissing.value) {
        // console.log('Вводим в ручную жену');
        formData.append('woman_email', form.woman_email);
        formData.append('woman_first_name', form.woman_first_name);
        formData.append('woman_name', form.woman_name);
        formData.append('woman_patronymic', form.woman_patronymic);
        formData.append('woman_birthday_date', form.woman_birthday_date);
        formData.append('woman_baptism_date', form.woman_baptism_date);
        formData.append('woman_death_date', form.woman_death_date);
        formData.append('woman_live_addres', form.woman_live_addres);
        formData.append('woman_home_phone', form.woman_home_phone);
        formData.append('woman_mobile_phone', form.woman_mobile_phone);
        formData.append('woman_relation_id', 1);
        formData.append('woman_sex_id', 2);
        formData.append('woman_discription', '');
        if (womanImage.value) {
          const fileName = womanImage.value.name;
          const fileData = womanImage.value;
          formData.append('woman_image', fileData, fileName);
        }
      } else {
        formData.append('woman_id', womanID.value);
      }
      formData.append('man_new_name', form.man_new_name);
      formData.append('woman_new_name', form.woman_new_name);

      await peopleStore.mergePersons(formData);
      console.log('totalCountErrors', peopleStore.totalCountErrors);
      if (!peopleStore.totalCountErrors) {  
        emits('toggleModal');
      }
      confirmWindow.value = false;
      loader.value = false;
    }
    // console.log('confirm');
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