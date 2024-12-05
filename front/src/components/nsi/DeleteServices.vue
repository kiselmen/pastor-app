<template>
  <div class="form" ref="formElem">
    <div class="form-header">Удаление вида служения</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
          <div class="form-text">Удалить служения {{ service }}?</div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Удалить запись?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-row form-bottom">
      <button @click.prevent="onDeleteService" class="btn btn-warning" :disabled="loader">{{ loader ? 'Удаление...': 'Удалить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-blue" :disabled="loader">{{ loader ? 'Отмена...': 'Отменить'}}</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>
</template>

<script setup>
  import { useNsiStore } from '@/stores/nsiStore';
  import { onBeforeMount, ref } from 'vue';

  const props = defineProps({
    id: {type: Array, default: new Array()},
  });

  const nsiStore = useNsiStore();

  const service = ref('');
  // const idArray = ref([]);
  const loader = ref(false);
  const confirmWindow = ref(false);

  const emits = defineEmits(['toggleModal']);

  const onDeleteService = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await nsiStore.deleteServices(props.id);
    if (!nsiStore.totalCountErrors) {
      emits('toggleModal');
    }
    loader.value = false;
    confirmWindow.value = false;
  };

  onBeforeMount(() => {
    loader.value = true;
    nsiStore.clearErrorsState();
    const filtered = nsiStore.services.filter( item => {
      let isFind = false;
      props.id.forEach(elem =>{
        if (elem === item.id) isFind = true;
      });
      return isFind;
      // item.selected === true
    });
    
    filtered.forEach(item => {
      service.value = service.value + ', ' + item.name;
      // idArray.value.push(item.id);
    });
    if (filtered.length) service.value.slice(2);
    loader.value = false;
  })

</script>
