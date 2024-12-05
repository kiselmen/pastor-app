<template>
  <div class="form" ref="formElem">
    <div class="form-header">Выбор операции</div>
    <div class="card-name">Операции</div>
    <div class="form-container section-container form-middle">
      <div class = "table1x">
          <div class="form-group">
            <label class="input-label">Операция</label>
            <InputSelector
                text = "Выберите операцию"
                :id   = actionID
                :data ="actions"
                :parentElem = "formElem"
                @selectItem="onActionSelect"
              />
          </div>
      </div>    
    </div>
    <div class="form-buttons form-bottom">
      <button @click.prevent="onAccept" class="btn btn-blue" :disabled="actionID === null">Выбрать</button>
      <button @click.prevent="emits('cancelSelect')" class="btn btn-gray">Отмена</button>
    </div>
  </div>  
</template>

<script setup>
  import { ref } from 'vue';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const props = defineProps({
    actions: { type: Array, default: [] },
  })

  const emits = defineEmits(['acceptSelect', 'cancelSelect']);

  const formElem = ref(null);
  const actionID = ref(null);
  // const localActions = ref([]);

  const onActionSelect = (action) => {
    actionID.value = action;
  };

  const onAccept = () => {
    emits('acceptSelect', actionID.value);
  }

</script>

<style lang="scss" scoped>
</style>