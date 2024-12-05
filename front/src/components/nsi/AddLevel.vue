<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление церковная дисциплины (уровни)</div>
    <!-- <div v-if="loader" class="form-text">Loading...</div> -->
    <div v-if="!loader&&!confirmWindow" class="form-container section-container">
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
                id="discription"
            >
            <div class="input-error" v-if="nsiStore.errors?.discription">
              {{ nsiStore.errors?.discription[0] }}
            </div>
        </div>
        <div class="form-group">
            <label class="input-label">Цвет</label>
            <input 
                type="color"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': nsiStore.errors?.color }"
                v-model="form.color" 
                id="color"
            >
            <div class="input-error" v-if="nsiStore.errors?.color">
              {{ nsiStore.errors?.color[0] }}
            </div>
        </div>
      </div>
      <div class="form-buttons">
        <button @click.prevent="onCreateLevel" class="btn btn-blue" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Сохранение...': 'Сохранить'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-text">Изменить запись?</div>
        <div class="form-buttons">
        <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
        <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { useNsiStore } from '@/stores/nsiStore';
  import { reactive, onBeforeMount, ref } from 'vue';

  const nsiStore = useNsiStore();
  const form = reactive({
    name: '',
    discription: '',
    color: null,
  });
  const emits = defineEmits(['toggleModal']);

  const formElem = ref(null);
  const loader = ref(false);
  const confirmWindow = ref(false);

  const onCreateLevel = async () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };

  const onConfirmAction = async () => {
    loader.value = true;
    await nsiStore.addNewLevel(form);
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
