<template>
  <div v-if="props.id" class="form">
    <div class="form-header">Регистрация пользователя</div>
    <div v-if="!loader&&!confirmWindow&&formStep === 0" class="card-name">Регистрационные данные</div>
    <div v-if="!loader&&!confirmWindow&&formStep === 1" class="card-name">Права доступа</div>
    <div v-if="loader" class="form-text">Loading...</div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container" ref="formElem" >
      <div v-if="formStep === 0" class = "table1x">
        <div class="form-group">
            <label class="input-label">Email</label>
            <input 
                type="email"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': userStore.errors?.email }"
                v-model="form.email" 
                id="email"
                disabled
            >
            <div class="input-error" v-if="userStore.errors?.email">
              {{ userStore.errors?.email[0] }}
            </div>
        </div>
        <div class="form-group">
            <label class="input-label">Имя</label>
            <input 
                type="text"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': userStore.errors?.name }"
                v-model="form.name" 
                id="name"
                disabled
            >
            <div class="input-error" v-if="userStore.errors?.name">
              {{ userStore.errors?.name[0] }}
            </div>
        </div>
        <div class="form-group">
          <label class="input-label">Пароль</label>
          <input
              :type = "showPassword === 1 ? 'text': 'password'"
              autocomplete = "off"
              class="input-box"
              :class="{ 'is-invalid': userStore.errors?.password }"
              id="password"
              v-model="form.password"
            />
            <div class="input-icon" @click=setShowPassword(1)>
              <IconClosedEye v-if="showPassword === 1"/>
              <IconEye v-if="showPassword !== 1"/>
            </div>
            <div class="input-error" v-if="userStore.errors?.password">
              {{ userStore.errors?.password[0] }}
            </div>
        </div>
        <div class="form-group">
          <label class="input-label">Подтверждение</label>
          <input
              :type = "showConfirm === 1 ? 'text': 'password'"
              autocomplete = "off"
              class="input-box"
              :class="{ 'is-invalid': userStore.errors?.password_confirmation }"
              id="confirm"
              v-model="form.password_confirmation"
            />
            <div class="input-icon" @click=setShowConfirm(1)>
              <IconClosedEye v-if="showConfirm === 1"/>
              <IconEye v-if="showConfirm !== 1"/>
            </div>
            <div class="input-error" v-if="userStore.errors?.password_confirmation">
              {{ userStore.errors?.password_confirmation[0] }}
            </div>
        </div>
      </div>
      <div v-if="formStep === 1" class = "table2x">
        <div class="form-group">
          <label class="input-label">Список служений</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="types"
            :parentElem = "formElem"
            @selectItem="onTypeSelect"
          />
        </div>
        <div class="form-group" v-if="typeID == 1">
          <label class="input-label">Список участков</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="prihodStore.prihods"
            :parentElem = "formElem"
            @selectItem="onSourceSelect"
          />
        </div>
        <div class="form-group" v-if="typeID == 2">
          <label class="input-label">Список служений</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="nsiStore.services"
            :parentElem = "formElem"
            @selectItem="onSourceSelect"
          />
        </div>
        <div class="form-group">
          <button @click.prevent="onPermitionAdd" class="btn btn-blue">Добавить</button>
        </div>  
      </div>
      <div v-if="formStep === 1" class="table2x">
        <div v-if ="!loader" class="group-items">
          <div class="group-item" v-if = "permitions.length" v-for ="permition in permitions" >
            <div class="group-name">{{ permition.name + ' ' + permition.source_name }}</div>
              <div class="group-delete" @click="onDeletePermition(permition.id, permition.source_id)">
                <DismissIcon/>
              </div>
            </div> 
        </div>
      </div>

      <div  v-if="formStep === maxFormSteps" class="form-buttons">
        <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0||loader">Назад</button>
        <button @click.prevent="onCreateUser" class="btn btn-blue" :disabled="loader">{{ loader ? 'Сохранение...': 'Регистрация'}}</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray" :disabled="loader">Отмена</button>
      </div>
      <div v-if="formStep !== maxFormSteps" class="form-buttons">
        <button @click.prevent="onMoveToStep('-')" class="btn btn-blue" :disabled="formStep <= 0">Назад</button>
        <button @click.prevent="onMoveToStep('+')" class="btn btn-blue" :disabled="formStep >= maxFormSteps">Вперед</button>
        <button @click.prevent="emits('toggleModal')" class="btn btn-gray" :disabled="loader">Отмена</button>
      </div>
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container">
      <div class = "table1x">
        <div class="form-text">Создать пользователя?</div>
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
  import { useUserStore } from '@/stores/userStore';
  import { useNsiStore } from '@/stores/nsiStore';
  import { useMsgStore } from '@/stores/msgStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import { usePeopleStore } from '@/stores/peopleStore'
  import IconClosedEye from '@/components/icons/IconClosedEye.vue';
  import IconEye from '@/components/icons/IconEye.vue';
  import InputSelector from '@/components/ui/InputSelector.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';

  const props = defineProps({
    id: null,
  });

  const emits = defineEmits(['toggleModal']);

  const userStore = useUserStore();
  const msgStore = useMsgStore();
  const nsiStore = useNsiStore();
  const prihodStore = usePrihodStore();
  const peopleStore = usePeopleStore();

  const loader = ref(false);
  const confirmWindow = ref(false);
  const form = reactive({
    persone_id: props.id,
    email: '',
    name: '',
    password: '',
    password_confirmation: '',
    permitions: [],
  });

  const showPassword = ref(null);
  const showConfirm = ref(null);
  const formStep = ref(0);
  const maxFormSteps = 1;
  const formElem = ref(null);
  const permitions = ref([]);
  const typeID = ref(null);
  const sourceID = ref(null);
  const types = [
    {id: 0, name: 'Пастор'},
    {id: 1, name: 'Дьякон'},
    {id: 2, name: 'Руководитель служения'},
  ];

  const curPersone = computed(() => {
    return peopleStore.peoples.filter(item => item.id == props.id)[0];    
  });

  const onTypeSelect = (id) => {
    typeID.value = id;
  };

  const onSourceSelect = (id) => {
    sourceID.value = id;
  };

  const onPermitionAdd = () => {
    const newPermition = JSON.parse(JSON.stringify(types.filter(item => item.id === typeID.value)))[0];
    if (typeID.value === 0 ) {
      newPermition.source_id = null;
      newPermition.source_name = '';
    } else if (typeID.value === 1 ) {
      const curPrihod = prihodStore.prihods.filter(item => item.id === sourceID.value)[0];
      newPermition.source_id = curPrihod.id;
      newPermition.source_name = '(' + curPrihod.name + ' ' + curPrihod.number +')';
    } else if (typeID.value === 2 ) {
      const curService = nsiStore.services.filter(item => item.id === sourceID.value)[0];
      newPermition.source_id = curService.id;
      newPermition.source_name = '(' + curService.name +')';
    }
    const isAlreadeyPresent = permitions.value.filter(item => item.id === newPermition.id && item.source_id === newPermition.source_id);
    if (!isAlreadeyPresent.length) permitions.value.push(newPermition);
  };

  const onDeletePermition = (id, source_id) => {
    permitions.value = permitions.value.filter(item => {
      return !(item.id == id && item.source_id == source_id)
    })
  };

  const onMoveToStep = (type) => {
    if (type === "-") {
      formStep.value--;
    } else if (type === '+') {
      formStep.value++;
    }
  };

  const onCreateUser = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    form.permitions = permitions.value;
    await userStore.registerPersonAsUser(form);
    if (!userStore.totalCountErrors) {
      emits('toggleModal');
    } else {
      msgStore.addMessage({name: 'Некорректные данные в форме', icon: 'error'});
    }
    confirmWindow.value = false;
    loader.value =false;
  }

  const setShowPassword = (field) => {
    showPassword.value = showPassword.value === field ? null: field;
  };
  const setShowConfirm = (field) => {
    showConfirm.value = showConfirm.value === field ? null: field;
  };

  onBeforeMount( async () => {
    loader.value = true;
    if (curPersone.value.user_id) {
      setTimeout(() => {
        msgStore.addMessage({name: 'Порльзователь уже существует', icon: 'warning'});
        emits('toggleModal');     
      }, 0);
    } else {
      const isPermition = userStore.user.permition;
      if (isPermition.length) {
        await nsiStore.getServices();
        await prihodStore.getPrihods();
        form.email = curPersone.value.email;
        form.name = curPersone.value.first_name + ' ' + curPersone.value.name;
      } else {
        setTimeout(() => {
          msgStore.addMessage({name: 'Не достаточно прав доступа', icon: 'warning'});
          emits('toggleModal');     
        }, 0);
      }
    }
    loader.value = false;
  })
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
  }
</style>