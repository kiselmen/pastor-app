<template>
  <div v-if="curPersone.user_id" class="form" ref="formElem" >
    <div class="form-header">{{ curPersone.name }} {{ curPersone.first_name }} {{ curPersone.patronymic }}</div>
    <div class="card-name">Новый пароль</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
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
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Сменить пароль?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onChangePassword" class="btn btn-blue" :disabled="loader">{{ loader ? 'Смена...': 'Сменить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray" :disabled="loader">Отмена</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>  
</template>

<script setup>
  import { reactive, ref, computed, onBeforeMount } from 'vue';
  import { useUserStore } from '@/stores/userStore';
  import { usePeopleStore } from '@/stores/peopleStore';
  import IconClosedEye from '@/components/icons/IconClosedEye.vue';
  import IconEye from '@/components/icons/IconEye.vue';

  const props = defineProps({
    id: null,
  });

  const emits = defineEmits(['toggleModal']);

  const userStore = useUserStore();
  const peopleStore = usePeopleStore();

  const loader = ref(false);
  const confirmWindow = ref(false);
  const form = reactive({
    user_id: props.id ? props.id: peopleStore.onePersone.user_id,
    name: '',
    password: '',
    password_confirmation: '',
  });

  const showPassword = ref(null);
  const showConfirm = ref(null);
  const formElem = ref(null);

  const curPersone = computed(() => {
    if (props.id) {
      const filtered = peopleStore.peoples.filter(item => item.user_id == props.id);
      return filtered[0];
    } else {
      return peopleStore.onePersone;
    }
  });
  
  const setShowPassword = (field) => {
    showPassword.value = showPassword.value === field ? null: field;
  };
  const setShowConfirm = (field) => {
    showConfirm.value = showConfirm.value === field ? null: field;
  };

  const onChangePassword = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await userStore.changeUserPassword(form);
      if (userStore.totalCountErrors === 0) {
        emits('toggleModal');
      }
    loader.value = false;
    confirmWindow.value = false;
  }

  onBeforeMount(() => {
    // console.log('curPersone ', curPersone.value);
    form.name = curPersone.value.name + ' ' + curPersone.value.first_name + ' ' + curPersone.value.patronymic;    
  });

</script>