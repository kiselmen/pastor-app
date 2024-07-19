<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление участка</div>
    <div v-if="loader" class="form-text">Loading...</div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-group">
          <label class="input-label">Имя участка</label>
          <input 
              type="text"
              autocomplete = "off"
              class="input-box"
              :class="{ 'is-invalid': prihodStore.errors?.name }"
              v-model="form.name" 
              id="name"
          />
          <div class="input-error" v-if="prihodStore.errors?.name">
            {{ prihodStore.errors?.name[0] }}
          </div>
        </div>
        <div class="form-group">
          <label class="input-label">Описание</label>
          <input 
              type="text"
              autocomplete = "off"
              class="input-box"
              :class="{ 'is-invalid': prihodStore.errors?.discription }"
              v-model="form.discription" 
              id="discription"
          />
          <div class="input-error" v-if="prihodStore.errors?.discription">
            {{ prihodStore.errors?.discription[0] }}
          </div>
        </div>
        <div class="form-group">
          <label class="input-label">Номер участка</label>
          <input 
              type="number"
              autocomplete = "off"
              class="input-box"
              :class="{ 'is-invalid': prihodStore.errors?.number }"
              v-model="form.number" 
              id="number"
          />
          <div class="input-error" v-if="prihodStore.errors?.number">
            {{ prihodStore.errors?.number[0] }}
          </div>
        </div>
      </div>
      <div class="form-buttons">
        <button @click.prevent="onCreatePrihod" class="btn btn-blue" :disabled="loader">{{ loader ? 'Добавление...' : 'Добавить'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-text">Создать участок?</div>
        <div class="form-buttons">
          <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
          <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { usePrihodStore } from '@/stores/prihodStore';
  import { reactive, onBeforeMount, ref } from 'vue';

  const prihodStore = usePrihodStore();

  const form = reactive({
    name: '',
    discription: '',
    number: 0,
  });

  const emits = defineEmits(['toggleModal']);

  const loader = ref(false);
  const confirmWindow = ref(false);
  const formElem = ref(null);

  const onCreatePrihod = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await prihodStore.addNewPrihod(form);
    if (!prihodStore.totalCountErrors) {
      emits('toggleModal');
    }
    confirmWindow.value = false;
    loader.value = false;
  };

  onBeforeMount(() => {
    prihodStore.clearErrorsState();
  })

</script>
