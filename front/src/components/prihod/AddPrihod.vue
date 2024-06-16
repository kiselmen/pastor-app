<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление прихода</div>
    <div class="form-text" v-if="prihodStore.loader">Loading...</div>
    <form @submit.prevent="onCreatePrihod" class="form-container section-container" v-if="!prihodStore.loader">
      <div class = "table1x">
        <div class="form-group">
          <label class="input-label">Имя прихода</label>
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
          <label class="input-label">Номер прихода</label>
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
        <button type="submit" class="btn btn-blue" :disabled="prihodStore.loader">{{ prihodStore.loader ? 'Сохранение...': 'Сохранить'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
    </form>
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

  const formElem = ref(null);

  const onCreatePrihod = async () => {
    await prihodStore.addNewPrihod(form);
    // console.log('errors', nsiStore.totalCountErrors);
    if (!prihodStore.totalCountErrors) {
      emits('toggleModal');
    }
  };

  onBeforeMount(() => {
    prihodStore.clearErrorsState();
  })

</script>
