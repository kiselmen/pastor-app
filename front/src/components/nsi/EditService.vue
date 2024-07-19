<template>
  <div class="form" ref="formElem">
    <div class="form-header">Изменение вида служения</div>
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
            :text ="form.statusName"
            :id   ="form.status_id"
            :data ="nsiStore.statuses"
            :parentElem = "formElem"
            @selectItem="onStatusSelect"
          />
        </div>
      </div>
      <div class="form-buttons">
        <button @click.prevent="onEditService" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': 'Сохранить'}}</button>
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
  import InputSelector from '@/components/ui/InputSelector.vue';

  const props = defineProps({
    id: {type: Number, default: null},
  });

  const nsiStore = useNsiStore();

  const form = reactive({
    name: '',
    discription: '',
  });

  const emits = defineEmits(['toggleModal']);

  const formElem = ref(null);
  const loader = ref(false);
  const confirmWindow = ref(false);

  const onEditService = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await nsiStore.editService(form, props.id);
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
    loader.value = true;
    nsiStore.clearErrorsState();
    const filtered = nsiStore.services.filter( item => item.id === props.id)[0];
    form.id = filtered.id;
    form.name = filtered.name;
    form.discription = filtered.discription;
    form.status_id = filtered.status_id;
    const isStatus = nsiStore.statuses.filter(item => item.id === form.status_id);
    form.statusName = isStatus.length ? isStatus[0].name : 'Не определено'; 
    loader.value = false;
  })

</script>
