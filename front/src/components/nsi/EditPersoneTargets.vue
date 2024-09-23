<template>
  <div class="form">
    <div class="form-header">Целевые группы</div>
    <div v-if="loader" class="form-text">Loading...</div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container" ref="formElem">
      <div class="table2x">
        <div class="form-group">
          <label class="input-label">Целевые группы</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="nsiStore.targets"
            :parentElem = "formElem"
            @selectItem="onTargetSelect"
          />
        </div>
        <div class="form-group">
          <button @click.prevent="onTargetAdd" class="btn btn-blue">Добавить</button>
        </div>  
      </div>
      <div class="table2x">
        <div v-if ="!loader" class="group-items">
          <div class="group-item" v-if = "ptargets.length" v-for ="target in ptargets" >
            <div class="group-name">{{ target.TargetName }}</div>
              <div class="group-delete" @click="onDeleteTarget(target.target_id)">
                <DismissIcon/>
              </div>
            </div> 
        </div>
      </div>
      <div class="form-buttons">
        <button @click.prevent="onSaveTargets" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': 'Сохранить'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-text">Сохранить изменения?</div>
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
  const ptargets = ref([]); 
  const targetId = ref(null);

  const curPersone = computed(() => {
    if (props.id) {
      const filtered = peopleStore.peoples.filter(item => item.id == props.id);
      return filtered[0];
    } else {
      return peopleStore.onePersone;
    }
  });
  
  const onTargetSelect = (id) => {
    targetId.value = id;
  };

  const onTargetAdd = () => {
    const isPresent = ptargets.value.filter(item => item.target_id === targetId.value);
    if (!isPresent.length) {
      const targetToAdd = nsiStore.targets.filter(item => item.id === targetId.value);
      ptargets.value = [...ptargets.value, {target_id: targetToAdd[0].id, TargetName: targetToAdd[0].name, people_id: curPersone.value.id}];
    }
  };

  const onDeleteTarget = (id) => {
    const isNotPresent = ptargets.value.filter(item => item.target_id !== id);
    ptargets.value = [...isNotPresent];
  };

  const onSaveTargets = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await peopleStore.updatePersoneTargets(curPersone.value.id, ptargets.value, props.id);
    loader.value = false;
    confirmWindow.value = false;
    emits('toggleModal');
  };

  onBeforeMount( async () => {
    loader.value = true;
    await nsiStore.getTargets();
    ptargets.value = [...curPersone.value.ptarget];
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