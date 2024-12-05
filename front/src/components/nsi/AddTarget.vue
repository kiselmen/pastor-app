<template>
  <div class="form">
    <div class="form-header">Добавление целевой группы</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-group">
            <label class="input-label">Целевая группа</label>
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
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Сохранить запись?</div>
      </div>
    </div>
    <div  v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onCreateTarget" class="btn btn-blue" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Сохранение...': 'Сохранить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>
</template>

<script setup>
  import { useNsiStore } from '@/stores/nsiStore';
  import { ref, reactive, onBeforeMount } from 'vue';

  const nsiStore = useNsiStore();
  const form = reactive({
    name: '',
    discription: '',
  });
  const emits = defineEmits(['toggleModal']);
  const loader = ref(false);
  const confirmWindow = ref(false);

  const onCreateTarget = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await nsiStore.addNewTarget(form);
    console.log('errors', nsiStore.totalCountErrors);
    if (!nsiStore.totalCountErrors) {
      emits('toggleModal');
    }
    loader.value = false;
    confirmWindow.value = false;
  };

  onBeforeMount(() => {
    nsiStore.clearErrorsState();
  })

</script>
