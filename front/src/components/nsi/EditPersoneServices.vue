<template>
  <div class="form" ref="formElem">
    <div class="form-header">Служения</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class="table2x">
        <div class="form-group">
          <label class="input-label">Список служений</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="nsiStore.services"
            :parentElem = "formElem"
            @selectItem="onServiceSelect"
          />
        </div>
        <div class="form-group">
          <button @click.prevent="onServiceAdd" class="btn btn-blue">Добавить</button>
        </div>  
      </div>
      <div class="table2x">
        <div v-if ="!loader" class="group-items">
          <div class="group-item" v-if = "pservices.length" v-for ="service in pservices" >
            <div class="group-name">{{ service.ServiceName }}</div>
              <div class="group-delete" @click="onDeleteService(service.service_id)">
                <DismissIcon/>
              </div>
            </div> 
        </div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Сохранить изменения?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onSaveServices" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': 'Сохранить'}}</button>
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
  import { usePeopleStore } from '@/stores/peopleStore';
  import { onBeforeMount, ref, computed } from 'vue';
  import InputSelector from '@/components/ui/InputSelector.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';

  const props = defineProps({
    id: null,
  });

  const emits = defineEmits(['toggleModal'])

  const nsiStore = useNsiStore();
  const peopleStore = usePeopleStore();

  const loader = ref(false);
  const confirmWindow = ref(false);
  const formElem = ref(null);
  // const persone = ref({});
  const pservices = ref([]); 
  const serviseId = ref(null);

  const curPersone = computed(() => {
    if (props.id) {
      const filtered = peopleStore.peoples.filter(item => item.id == props.id);
      return filtered[0];
    } else {
      return peopleStore.onePersone;
    }
  });
  
  const onServiceSelect = (id) => {
    serviseId.value = id;
  };

  const onServiceAdd = () => {
    const isPresent = pservices.value.filter(item => item.service_id === serviseId.value);
    if (!isPresent.length) {
      const serviceToAdd = nsiStore.services.filter(item => item.id === serviseId.value);
      pservices.value = [...pservices.value, {service_id: serviceToAdd[0].id, ServiceName: serviceToAdd[0].name, people_id: curPersone.value.id}];
    }
  };

  const onDeleteService = (id) => {
    const isNotPresent = pservices.value.filter(item => item.service_id !== id);
    pservices.value = [...isNotPresent];
  };

  const onSaveServices = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await peopleStore.updatePersoneServices(curPersone.value.id, pservices.value, props.id);
    loader.value = false;
    confirmWindow.value = false;
    emits('toggleModal');
  };

  onBeforeMount( async () => {
    loader.value = true;
    // await nsiStore.getServices();8
    pservices.value = [...curPersone.value.pservice];
    loader.value = false;
  });

</script>

<style lang="scss" scoped>
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
    @media (max-width: 454px) {
      flex-basis: 100%;
    }  
  }

</style>