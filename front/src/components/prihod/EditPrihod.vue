<template>
  <div class="form" ref="formElem">
    <div class="form-header">Редактирование участка</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
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
            >
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
            >
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
            >
            <div class="input-error" v-if="prihodStore.errors?.number">
              {{ prihodStore.errors?.number[0] }}
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
      <button @click.prevent="onEditPrihod" class="btn btn-blue" :disabled="prihodStore.loader">{{ prihodStore.loader ? 'Изменение...': 'Изменить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>
</template>

<script setup>
  import { usePrihodStore } from '@/stores/prihodStore';
  import { reactive, onBeforeMount, ref } from 'vue';

  const props = defineProps({
    id: { type: Number, default: null },
  })

  const emits = defineEmits(['toggleModal']);

  const prihodStore = usePrihodStore();

  const form = reactive({
    name: '',
    discription: '',
    number: 0,
  });

  const prihod = ref(null);
  const loader = ref(false);
  const confirmWindow = ref(false);
  const formElem = ref(null);

  const onEditPrihod = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    let formData = new FormData();
    formData.append('name', form.name);
    formData.append('number', form.number);
    formData.append('discription', form.discription);
    formData.append('id', props.id);
    await prihodStore.editPrihod(formData);
    if (!prihodStore.totalCountErrors) {
      emits('toggleModal');
    }
    confirmWindow.value = false;
    loader.value = false;
  };

  onBeforeMount(() => {
    loader.value = true;
    prihodStore.clearErrorsState();
    // prihodStore.getPrihods();
    prihod.value = prihodStore.prihods.filter(item => item.id === props.id)[0];

    form.name = prihod.value.name;
    form.number = prihod.value.number;
    form.discription = prihod.value.discription;
    loader.value = false;
  })

</script>
