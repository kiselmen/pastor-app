<template>
  <div class="people">
    <form @submit.prevent="onCreatePerson" class="section-container people-container">
          <div class = "ProfileLogo table2x">
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
                <FileUpload
                  v-model="image"
                  @uploadImage="onImageUploaded"
                  @clearImage="onImageCleared"
                />
            </div>
          </div>
          <div class="table2x">

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
                    :class="{ 'is-invalid': peopleStore.errors?.second_name }"
                    v-model="form.second_name" 
                    id="first_name"
                >
                <div class="input-error" v-if="peopleStore.errors?.second_name">
                  {{ peopleStore.errors?.second_name[0] }}
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
            <div class="form-group">
              <button type="submit" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': 'Сохранить'}}</button>
            </div>
          </div>
    </form>
  </div>
</template>

<script setup>
  import { usePeopleStore } from '@/stores/peopleStore';
  import { reactive, ref, onBeforeMount } from 'vue';
  import FileUpload from '@/components/ui/FileUpload.vue';

  const peopleStore = usePeopleStore();
  const form = reactive({
    email: '',
    first_name: '',
    second_name: '',
    patronymic: '',
    birthday_date: '',
    baptism_date: '',
    death_date: '',
    live_addres: '',
    home_phone: '',
    mobile_phone: '',
  });
  const image = ref();
  const emits = defineEmits(['toggleModal']);
  const loader = ref(false);

  const onImageUploaded = (data) => {
    // console.log('onImageUploaded ', data);
    image.value = data;
  };

  const onImageCleared = () => {
    image.value = null;
    // console.log('onImageCleared ');
  };

  const onCreatePerson = async () => {
    loader.value = true;
    let formData = new FormData();
    formData.append('email', form.email);
    formData.append('first_name', form.first_name);
    formData.append('second_name', form.second_name);
    formData.append('patronymic', form.patronymic);
    formData.append('birthday_date', form.birthday_date);
    formData.append('baptism_date', form.baptism_date);
    formData.append('death_date', form.death_date);
    formData.append('live_addres', form.live_addres);
    formData.append('home_phone', form.home_phone);
    formData.append('mobile_phone', form.mobile_phone);
    if (image.value) {
      const fileName = image.value.name;
      const fileData = image.value;
      formData.append('image', fileData, fileName);
    } 
    await peopleStore.addNewPersone(formData);
    console.log('errors', peopleStore.totalCountErrors);
    if (!peopleStore.totalCountErrors) {
      emits('toggleModal');
    }
    loader.value = false;
  };

  onBeforeMount(() => {
    peopleStore.clearErrorsState();
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

</style>@/stores/peopleStore