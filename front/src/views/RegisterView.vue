<template>
  <div class="login-container">
    <h2>Регистрация</h2>
    <div class="">
      <form @submit.prevent="submit" class="login-form">
        <div class="form-group">
            <label class="input-label">Email</label>
            <input 
                type="email"
                autocomplete = "off"
                class="input-box"
                :class="{ 'is-invalid': userStore.errors?.email }"
                v-model="form.email" 
                id="email"
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
        <div class="form-group">
            <button type="submit" class="btn btn-blue" :disabled="userStore.loader">{{ userStore.loader ? 'Загрузка...': 'Регистрация'}}</button>
        </div>
      </form>
    </div>
    <!-- <p v-if="message" class="error">{{ message }}</p> -->
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useUserStore } from '@/stores/userStore';
import IconClosedEye from '@/components/icons/IconClosedEye.vue';
import IconEye from '@/components/icons/IconEye.vue';

const userStore = useUserStore();
const form = reactive({
    email: '',
    name: '',
    password: '',
    password_confirmation: '',
});
const showPassword = ref(null);
const showConfirm = ref(null);

const setShowPassword = (field) => {
  showPassword.value = showPassword.value === field ? null: field;
};
const setShowConfirm = (field) => {
  showConfirm.value = showConfirm.value === field ? null: field;
};

function submit() {
    userStore.createNewUser(form);
}
</script>

<style scoped lang="scss">

  .login {
    &-container {
      width: 350px;
      font-size: 1.2em;
      display: flex;
      flex-direction: column;
      gap: 1em;
      margin: auto;
    }
    &-form {
      width: 100%;
      text-align: center;
    }
  }

  .btn {
    width: 100%;
  }


</style>@/stores/userStore