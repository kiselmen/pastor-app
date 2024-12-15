<template>
  <div class="container">
    <div class="form-group">
      <span class="input-name">с</span>
      <input 
        type="date"
        class="input-box"
        v-model="startDate"
        id="startDate"
      >
      <span class="input-name">по</span>
      <input 
        type="date"
        class="input-box"
        v-model="endDate" 
        id="endDate"
      >
      <button @click.prevent="onChangeStartDate" :disabled="props.isBtnDisabled" class="btn btn-blue">Отобрать</button>

    </div>
  </div>
</template>

<script setup>
  import { onBeforeMount, ref } from 'vue';

  const props = defineProps({
    start: '',
    end: '',
    isBtnDisabled: { type: Boolean, default: true },
  });

  const startDate = ref('');
  const endDate = ref('');

  const emits = defineEmits(['updatePeriod']);

  const onChangeStartDate = () => {
    const params = { startDate: startDate.value, endDate: endDate.value };
    emits('updatePeriod', params );
  };

  const initStart = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = '01';
    return year + '-' + month + '-' + day;
  }

  const initEnd = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();
    const lastDayOfMonth = new Date(year, month + 1, 0); 
    const day = String(lastDayOfMonth.getDate()).padStart(2, '0'); 
    const monthString = String(month + 1).padStart(2, '0');
    return year + '-' + monthString + '-' + day;       

  }

  onBeforeMount(() => {
    if (props.start) {
      startDate.value = props.start;
    } else {
      startDate.value = initStart();
    }
    if (props.end) {
      endDate.value = props.start;
    } else {
      endDate.value = initEnd();
    }
  });
</script>

<style lang="scss" scoped>

  .container {
    display: inline-block;
  }

  .container .input-box {
    width: 120px;
    margin-right: 10px;
  }

  .input-name {
    margin: 0 5px;
  }

  .form-group {
    margin-top: 0; 
  }

</style>