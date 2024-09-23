<template>
  <div v-if="curPersone.id" class="form">
    <div class="form-header">Дисциплина {{ curPersone.name }} {{ curPersone.first_name }} {{ curPersone.patronymic }}</div>
    <div v-if="loader" class="form-text">Loading...</div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div v-if="curPersone.plevel.length" class="card-table">
          <Table
            :tableHeadNames = "tableHeadNames"
            :tableHeadID = "tableHeadID"
            :tableData = "curPersone.plevel"
            :hasActiveBtn = "hasActions"
            :isAllRowSelected = "isAllRowSelected"
            :actions = "actions"
            @selectRow = "onSelectRow"
            @selectAllRows = "onSelectAllRows"
            @rowAction = "onRowAction"
            @allRowAction = "onAllRowAction"
          />
        </div>
        <div v-if="!curPersone.plevel.length" class="card-table">Нарушений нет</div>
      </div>  
      <div v-if="!isFormOpen" class="form-buttons">
        <button @click.prevent="onOpenForm('add')" class="btn btn-blue" >{{ formType == 'add' ? 'Новая запись': 'Изминить' }}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray">Отмена</button>
      </div>
    </div>
    <div v-if = "!loader&&isFormOpen&&!confirmWindow" class="form-container section-container">
      <!-- <div class="form-line"></div> -->
      <div class = "table1x">
        <div class="form-text">{{formType == 'add'? 'Добавление дисциплины' : 'Изменение дисциплины'}}</div>
        <div class="form-row">
          <div class="form-group">
            <label class="input-label">Дата</label>
            <input 
                type="date"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.date }"
                v-model="form.date" 
                id="date"
            >
            <div class="input-error" v-if="peopleStore.errors?.date">
              {{ peopleStore.errors?.date[0] }}
            </div>
          </div>
          <div class="form-group">
              <label class="input-label">Список дисциплин</label>
              <InputSelector
                :text = "levelText"
                :id   ="form.level_id"
                :data ="nsiStore.levels"
                @selectItem="onLevelSelect"
              />
              <div class="input-error" v-if="peopleStore.errors?.level_id">
                {{ peopleStore.errors?.level_id[0] }}
              </div>
          </div>
        </div>
        <div class="form-group">
            <label class="input-label">Описание</label>
            <textarea 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': peopleStore.errors?.discription }"
                v-model="form.discription" 
                id="discription"
            />
            <div class="input-error" v-if="peopleStore.errors?.discription">
              {{ peopleStore.errors?.discription[0] }}
            </div>
        </div>
      </div>
      <div class="form-buttons">
        <button @click.prevent="onCreatePLevel" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': formType == 'add' ? 'Добавить' : 'Изменить'}}</button>
        <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
      </div>
    </div>
    <div v-if = "!loader&&confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-text">{{ confirmMessage }}</div>
        <div class="form-buttons">
        <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
        <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
      </div>
      </div>
    </div>
  </div>

</template>

<script setup>
  import { reactive, ref, computed, onBeforeMount } from 'vue';
  import { useNsiStore } from '@/stores/nsiStore';
  import { usePeopleStore } from '@/stores/peopleStore';
  import { useMsgStore } from '@/stores/msgStore';
  import { useUserStore } from '@/stores/userStore';
  import Table from '@/components/ui/Table.vue';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const props = defineProps({
    id: null,
  });

  const nsiStore = useNsiStore();
  const peopleStore = usePeopleStore();
  const msgStore = useMsgStore();
  const userStore = useUserStore();

  const form = reactive({
    id: null,
    date: null,
    level_id: null,
    people_id: null,
    discription: '',
  });
  const loader = ref(false);
  const isFormOpen = ref(false);
  const formType = ref('add'); // 'add' - добавить, 'edit'- изменить
  const tableHeadNames = ref(['Дата', 'Уровень', 'Описание', 'Автор']);
  const tableHeadID = ref(['date', 'LevelName', 'discription', 'UserName']);
  const isAllRowSelected = ref(false);
  const levelText = ref('');
  const confirmWindow = ref(false);
  const confirmMessage = ref('');
  const rowID = ref(null);
  const actionID = ref(null);
  const isOneRowAction = ref(true);

  const emits = defineEmits(['toggleModal']);

  const curPersone = computed(() => {
    if (props.id) {
      const filtered = peopleStore.peoples.filter(item => item.id == props.id);
      form.people_id = filtered[0].id;
      return filtered[0];
    } else {
      form.people_id = peopleStore.onePersone.id
      return peopleStore.onePersone;
    }
  });
  
  const onOpenForm = (type) => {
    formType.value = type;
    isFormOpen.value = true;
  };

  const onLevelSelect = (id) => {
    form.level_id = id;
  };

  const hasActions = computed(() => {
    return true;
  });

  const actions = computed(() => {
    const isPermition = userStore.user.permition;
    if (isPermition.length) {
      return   [
        {id: 0, name: "Изменить", type: 0},
        {id: 1, name: "Удалить", type: 1},
      ];
    } else {
      return [];
    }
  })
  
  const onSelectRow = (value, rowID) => {
    const filtered = curPersone.value.plevel.filter( item => item.id === rowID)[0];
    filtered['selected'] = value;
  };

  const onSelectAllRows = (value) => {
    curPersone.value.plevel.forEach(item => item['selected'] = value);
    isAllRowSelected.value = value;
    isFormOpen.value = false;
  };

  const clearConfirmData = () => {
    confirmWindow.value = false;
    confirmMessage.value = '';
    actionID.value = null;
    rowID.value = null;
    isOneRowAction.value = true;
    isFormOpen.value = false;
    form.id = null;
    form.date = null;
    form.level_id = null;
    form.discription = '';
    levelText.value = '';
    formType.value = 'add';
  };

  const onCancelAction = () => {
    clearConfirmData();
  };

  const clearFormFields = () => {
    isFormOpen.value = false;
    form.id = null;
    form.date = null;
    form.level_id = null;
    form.people_id = curPersone.value.id;
    form.discription = '';
    levelText.value = '';
    formType.value = 'add';
  };

  const onConfirmAction = async () => {
    loader.value = true;
    if (actionID.value === 0) {
      if (formType.value === 'add') {
        await peopleStore.addPersonLevel(form, props.id);
        if (peopleStore.totalCountErrors === 0) {
          clearFormFields();
        }
      } else if (formType.value === 'edit') {
        await peopleStore.editPersonLevel(form, props.id);
        if (peopleStore.totalCountErrors === 0) {
          clearFormFields();
        }
      }
    } else if (actionID.value === 1) {
      if (isOneRowAction.value) {
        await peopleStore.deletePersonLevels([rowID.value], curPersone.value.id, props.id);
      } else {
        const IDs = [];
        curPersone.value.plevel.forEach(item => {
          if (item.selected === true) {
            IDs.push(item.id);
          }
        });
        if (IDs.length) {
          await peopleStore.deletePersonLevels(IDs, curPersone.value.id, props.id);
        } else {
          msgStore.addMessage({name: 'Нет выбраных строк для выполнения опреации', icon: 'warning'});
        }
      }
      clearFormFields();
    }
    clearConfirmData();
    loader.value = false;
  };

  const onRowAction = async (action, row) => {
    if (action === 0) {
      formType.value = 'edit';
      isFormOpen.value = true;
      const curLevel = curPersone.value.plevel.filter(item => item.id == row)[0];
      form.id = curLevel.id;
      form.date = curLevel.date;
      form.level_id = curLevel.level_id;
      form.people_id = curLevel.people_id;
      form.discription = curLevel.discription;
      levelText.value = curLevel.LevelName;
    } else if (action === 1) {
      confirmWindow.value = true;
      const curLevel = curPersone.value.plevel.filter(item => item.id == row)[0];
      confirmMessage.value = 'Удалить ' + curLevel.LevelName + ' от ' + curLevel.date + '?';
      actionID.value = action;
      rowID.value = row;
      isOneRowAction.value = true;
    }
  };

  const onAllRowAction = (action) => {
    if (action === 1) {
      confirmWindow.value = true;
      confirmMessage.value = 'Удалить выбраные записи?';
      actionID.value = action;
      rowID.value = null;
      isOneRowAction.value = false;
    }
  }

  const onCreatePLevel = async () => {
    confirmWindow.value = true;
    actionID.value = 0;
    rowID.value = null;
    isOneRowAction.value = true;
    if (formType.value === 'add') {
      confirmMessage.value = 'Добавить запись?';
    } else if (formType.value === 'edit') {
      confirmMessage.value = 'Изменить запись?';
    }
  };

  onBeforeMount( async () => {
    loader.value = true;
    await nsiStore.getLevels();
    nsiStore.clearErrorsState();
    loader.value = false;
  });

</script>

<style lang="scss" scoped>
  .form-container {
    margin-top: 10px;
  }
  
</style>