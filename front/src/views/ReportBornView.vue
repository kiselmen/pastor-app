<template>
  <div class="card-container" >
    <div class="form-row">
      <div class="form-filter">
        <label class="checkbox-control">
          <input type="checkbox" v-model="inverseTypeReport" @change="onTypeReport"/>
          Родились
        </label>
        <label class="checkbox-control">
          <input type="checkbox" v-model="typeReport" @change="onInverseTypeReport"/>
          Умерли
        </label>
      </div>
    </div>
    <div class="form-row">
      <div class="form-filter">
        <div class="form-group">
          <label class="input-label">Начало</label>
          <input 
            type="date"
            class="input-box"
            v-model="dateStart"
          />
        </div>  
        <div class="form-group">
          <label class="input-label">Конец</label>
          <input 
            type="date"
            class="input-box"
            v-model="dateEnd"
          />
        </div>

        <div class="form-buttons">
          <button @click.prevent="onCreateReport" class="btn btn-blue" :disabled="loader">{{ loader ? 'Формирование...': 'Сформировать'}}</button>
          <button @click.prevent="onPrintReport" class="btn btn-blue" :disabled="loader||!peopleStore.peoples.length">{{ loader ? 'Печать...': 'Печать'}}</button>
        </div>  
      </div>
    </div>
  </div>
  <div v-if="peopleStore.peoples?.length" class="card-container" id="print-content">
    <div class="card-name">
      <!-- <div class="form-row"> -->
        {{ !typeReport ? 'Рождены': 'Умерли' }} в период с {{ new Date(dateStart).toLocaleString("ru", minOptions) }} по {{ new Date(dateEnd).toLocaleString("ru", minOptions) }} 
      <!-- </div> -->
    </div>
    <div class="card-info">
      сформировано {{ new Date().toLocaleString("ru", maxOptions) }}
    </div>
    <div class="card-table">
      <Table
        :tableHeadNames = "tableHeadNames"
        :tableHeadID = "tableHeadID"
        :tableData = "peopleStore.peoples"
      />
    </div>
  </div>
</template>
<script setup>

  import { ref, onBeforeMount, onUnmounted } from 'vue';
  import { usePeopleStore } from '@/stores/peopleStore';
  import html2pdf from 'html2pdf.js';
  import Table from '@/components/ui/Table.vue';

  const dateStart = ref(null);
  const dateEnd = ref(null);
  const loader = ref(false);
  const peopleStore = usePeopleStore();
  const typeReport = ref(false);
  const inverseTypeReport = ref(true);
  const tableHeadNames = ref(['N', 'Фамилия', 'Имя', 'Отчество', 'День рождения', 'Умер']);
  const tableHeadID = ref(['id', 'name', 'first_name', 'patronymic', 'birthday_date', 'death_date']);
  const minOptions = {  year: 'numeric', month: 'long', day: 'numeric'};
  const maxOptions = {  year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric'};

  const onCreateReport = async () => {
    loader.value = true;
    console.log('typeReport.value ', typeReport.value);
    
    await peopleStore.getBornPeople(dateStart.value, dateEnd.value, typeReport.value);
    loader.value = false;
  }

  const onPrintReport = () => {
    html2pdf(document.getElementById('print-content'), {
      margin: 1,
      filename: 'report.pdf',
    });
  }

  const onTypeReport = () => {
    typeReport.value = !typeReport.value;
    inverseTypeReport.value = !typeReport.value;
  }

  const onInverseTypeReport = () => {
    inverseTypeReport.value = !inverseTypeReport.value;
    typeReport.value = !inverseTypeReport.value;
  }

  onBeforeMount(async () => {
    peopleStore.clearPeopleState();
    const toDay = new Date();
    const thisYear = String(toDay.getFullYear());
    const thisMonth = String(toDay.getMonth());
    const thisDay = String(toDay.getDate());
    typeReport.value = false;
    inverseTypeReport.value = !typeReport.value;
    dateStart.value = thisYear + '-01-01';
    dateEnd.value = thisYear + (thisMonth.length === 1 ? '-0': '-') + thisMonth + (thisDay.length === 1 ? '-0': '-') + thisDay;
    // console.log(thisMonth, dateStart.value, dateEnd.value);
  });

  onUnmounted(() => {
    peopleStore.clearPeopleState();
  })

</script>

<style lang="scss" scoped>
</style>