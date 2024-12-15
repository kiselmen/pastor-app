<template>
  <div class="form" ref="formElem">
    <div class="form-header">Оформление переезда</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-group">
          <label class="input-label">Основание</label>
          <InputSelector
              text = "Выберите операцию"
              :id   = form.action_id
              :data ="actionsData"
              :parentElem = "formElem"
              @selectItem="onActionSelect"
            />
          <div class="input-error" v-if="familyStore.errors?.action_id">
            {{ familyStore.errors?.action_id[0] }}
          </div>
        </div>
        <div class="form-group">
            <label class="input-label">Участок</label>
            <InputSelector
                text = "Участок"
                :id   = form.prihod_id
                :data ="avalablePrihods"
                :parentElem = "formElem"
                @selectItem="onPrihodSelect"
              />
            <div class="input-error" v-if="familyStore.errors?.prihod_id">
              {{ familyStore.errors?.prihod_id[0] }}
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
      <button @click.prevent="onMoveFamily" class="btn btn-blue" :disabled="loader">Переместить</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>  
</template>

<script setup>

  import { ref, reactive, computed, onBeforeMount } from 'vue';
  import { useFamilyStore } from '@/stores/familyStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const props = defineProps({
    id: { type: Number, default: null },
  });

  const emits = defineEmits(['toggleModal']);

  const familyStore = useFamilyStore();
  const prihodStore = usePrihodStore();

  const formElem = ref(null);
  const loader = ref(false);
  const confirmWindow = ref(false);
  const curAction = ref(null);
  const form = reactive({
    id: '',
    action_id: null,
    action_name: '',
    prihod_id: null,
  });

  const actionsData = computed(() => {
    return [
      {id: 9, name: 'Смена участка'},
      {id: 14, name: 'Выбытие в другую церковь'},
    ]
  });

  const avalablePrihods = computed(() => {
    if (curAction.value == 14) {
      return prihodStore.prihods.filter(item => item.is_global);
    } else {
      return prihodStore.prihods.filter(item => !item.is_global);
    }
  });

  const onPrihodSelect = (id) => {
    form.prihod_id = id;
  };

  const onActionSelect = (id) => {
    form.action_id = id;
    if (form.action_id !== null) {
      form.action_name = actionsData.value.filter(item => item.id == id)[0].name;
    }
    curAction.value = id;
  }

  const onMoveFamily = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await familyStore.moveFamily(form);
    if (!familyStore.totalCountErrors) {
      emits('toggleModal');
    }
    confirmWindow.value = false;
    loader.value = false;
  };

  onBeforeMount( async () => {
    loader.value = true;
    familyStore.clearErrorsState();
    await prihodStore.getPrihods();
    
    if (props.id) {
      form.id = props.id;
    }
    loader.value = false;
  });
</script>

<style lang="scss" scoped>
  .section {
    &-container {
      width: 100%;
      display: flex;
      gap: 10px;
    }
  }

  .form-container {
    margin-top: 0;
  }

  .table2x{
    flex-basis: 48%;
    padding: 0 0;
    max-width: 750px;
  }
</style>