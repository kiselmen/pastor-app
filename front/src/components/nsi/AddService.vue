<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление вид служения</div>
    <div v-if="loader" class="form-text">Loading...</div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-group">
            <label class="input-label">Вид служения</label>
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
        <div class="form-group">
            <label class="input-label">Статус</label>
            <InputSelector
              :text ="form.status"
              :id   = null
              :data ="nsiStore.statuses"
              :parentElem = "formElem"
              @selectItem="onStatusSelect"
            />
        </div>
      </div>
      <div class="form-buttons">
        <button @click.prevent="onCreateService" class="btn btn-blue" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Сохранение...': 'Сохранить'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-text">Сохранить запись?</div>
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
  import InputSelector from '@/components/ui/InputSelector.vue';

  const nsiStore = useNsiStore();

  const form = reactive({
    name: '',
    discription: '',
  });

  const emits = defineEmits(['toggleModal']);

  const formElem = ref(null);
  const loader = ref(false);
  const confirmWindow = ref(false);

  const onCreateService = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    confirmWindow.value = true;
    await nsiStore.addNewService(form);
    if (!nsiStore.totalCountErrors) {
      emits('toggleModal');
    }
    loader.value = false;
    confirmWindow.value = false;
  };

  const onStatusSelect = (id) => {
    form.status_id = id;
  }

  onBeforeMount(() => {
    nsiStore.clearErrorsState();
  })

</script>
