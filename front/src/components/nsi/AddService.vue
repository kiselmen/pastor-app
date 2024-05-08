<template>
  <div class="form" ref="formElem">
    <div class="form-header">Добавление вид служения</div>
    <form @submit.prevent="onCreateService" class="section-container form-container">
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

          <div class="form-group">
            <button type="submit" class="btn btn-blue" :disabled="nsiStore.loader">{{ nsiStore.loader ? 'Сохранение...': 'Сохранить'}}</button>
          </div>
      </div>
    </form>
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

  const onCreateService = async () => {
    await nsiStore.addNewService(form);
    // console.log('errors', nsiStore.totalCountErrors);
    if (!nsiStore.totalCountErrors) {
      emits('toggleModal');
    }
  };

  const onStatusSelect = (id) => {
    form.status_id = id;
  }

  onBeforeMount(() => {
    nsiStore.clearErrorsState();
  })

</script>
