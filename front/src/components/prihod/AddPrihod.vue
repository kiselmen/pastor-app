<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление участка</div>
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
        <div class="form-group">
          <label class="input-label">Тип участка</label>
          <InputSelector
              text = "Тип"
              :id   = form.is_global
              :data ="prihodTypes"
              :parentElem = "formElem"
              @selectItem="onTypeSelect"
            />
          <div class="input-error" v-if="prihodStore.errors?.is_global">
            {{ prihodStore.errors?.is_global[0] }}
          </div>
        </div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Создать участок?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onCreatePrihod" class="btn btn-blue" :disabled="loader">{{ loader ? 'Добавление...' : 'Добавить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
    </div>
    <div  v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>
</template>

<script setup>
  import { usePrihodStore } from '@/stores/prihodStore';
  import { reactive, onBeforeMount, ref, computed } from 'vue';

  import InputSelector from '@/components/ui/InputSelector.vue';

  const prihodStore = usePrihodStore();

  const form = reactive({
    name: '',
    discription: '',
    number: 0,
    is_global: 0,
  });

  const emits = defineEmits(['toggleModal']);

  const loader = ref(false);
  const confirmWindow = ref(false);
  const formElem = ref(null);

  const prihodTypes = [
    { id: 0, name: 'Локальная церковь' },
    { id: 1, name: 'Другие церкви' },
  ];

  const onTypeSelect = (id) => {
    form.is_global = id;
  };

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
