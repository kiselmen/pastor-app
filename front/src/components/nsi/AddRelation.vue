<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление отношения</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-group">
            <label class="input-label">Отношение</label>
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
          <label class="input-label">Пол</label>
          <InputSelector
            text  = "Выберите пол"
            :id   = form.sex_id
            :data ="nsiStore.sexes"
            :parentElem = "formElem"
            @selectItem="onSexSelect"
          />
          <div class="input-error" v-if="nsiStore.errors?.sex_id">
            {{ nsiStore.errors?.sex_id[0] }}
          </div>
        </div>
        <div class="form-group">
          <label class="input-label">Половинка пары</label>
          <InputSelector
            text  = "Выберите тип"
            :id   = form.pair
            :data ="[{id: 1, name: 'Да'}, {id: 0, name: 'Нет'}]"
            :parentElem = "formElem"
            @selectItem="onPairSelect"
          />
          <div class="input-error" v-if="nsiStore.errors?.pair">
            {{ nsiStore.errors?.pair[0] }}
          </div>
        </div>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Сохранить запись?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onCreateRelation" class="btn btn-blue" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Сохранение...': 'Сохранить'}}</button>
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
  import { ref, reactive, onBeforeMount } from 'vue';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const nsiStore = useNsiStore();

  const emits = defineEmits(['toggleModal']);

  const form = reactive({
    name: '',
    discription: '',
    sex_id: null,
    pair: null,
  });

  const loader = ref(false);
  const confirmWindow = ref(false);
  const formElem = ref(null);

  const onSexSelect = (id) => {
    form.sex_id = id;
  };

  const onPairSelect = (id) => {
    form.pair = id;
  };

  const onCreateRelation = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await nsiStore.addNewRelation(form);
    console.log('errors', nsiStore.totalCountErrors);
    if (!nsiStore.totalCountErrors) {
      emits('toggleModal');
    }
    loader.value = false;
    confirmWindow.value = false;
  };

  onBeforeMount(() => {
    nsiStore.clearErrorsState();
  })

</script>
