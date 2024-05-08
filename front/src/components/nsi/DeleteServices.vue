<template>
  <div class="form" ref="formElem">
    <div class="form-header">Удаление вида служения</div>
    <form class="section-container form-container" >
      <div class = "table1x">
          <div class="form-text">Удалить служения {{ service }}?</div>
          <div class="form-row">
            <button @click.prevent="onDeleteService" type="submit" class="btn btn-warning" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Удаление...': 'Удалить'}}</button>
            <button @click.prevent="onCancelDelete" type="submit" class="btn btn-blue" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Отмена...': 'Отменить'}}</button>
          </div>
      </div>
    </form>
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
  const idArray = ref([]);

  const emits = defineEmits(['toggleModal']);

  const onDeleteService = async () => {

    await nsiStore.deleteServices(props.id);
    // console.log('errors', nsiStore.totalCountErrors);
    if (!nsiStore.totalCountErrors) {
      emits('toggleModal');
    }
  };

  const onCancelDelete = () => {
    emits('toggleModal');
  }

  onBeforeMount(() => {
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
  })

</script>
