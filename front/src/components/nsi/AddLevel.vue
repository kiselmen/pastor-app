<template>
  <div class="form">
    <div class="form-header">Добавление церковная дисциплины (уровни)</div>
    <form @submit.prevent="onCreateTarget" class="form-container section-container">
      <div class = "table1x">
        <div class="form-group">
            <label class="input-label">Церковная дисциплина</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': nsiStore.errors?.name }"
                v-model="form.name" 
                id="name"
            >
            <div class="input-error" v-if="nsiStore.errors?.name">
              {{ nsiStore.errors?.name[0] }}
            </div>
        </div>
        <div class="form-group">
            <label class="input-label">Описание</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': nsiStore.errors?.discription }"
                v-model="form.discription" 
                id="first_name"
            >
            <div class="input-error" v-if="nsiStore.errors?.discription">
              {{ nsiStore.errors?.discription[0] }}
            </div>
        </div>
      </div>
      <div class="form-buttons">
        <button type="submit" class="btn btn-blue" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Сохранение...': 'Сохранить'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
    </form>
  </div>
</template>

<script setup>
  import { useNsiStore } from '@/stores/nsiStore';
  import { reactive, onBeforeMount } from 'vue';

  const nsiStore = useNsiStore();
  const form = reactive({
    name: '',
    discription: '',
  });
  const emits = defineEmits(['toggleModal']);

  const onCreateTarget = async () => {
    await nsiStore.addNewLevel(form);
    console.log('errors', nsiStore.totalCountErrors);
    if (!nsiStore.totalCountErrors) {
      emits('toggleModal');
    }
  };

  onBeforeMount(() => {
    nsiStore.clearErrorsState();
  })

</script>
