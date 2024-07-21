<template>
  <div class="login-container">
    <h2>Вход</h2>
    <!-- <div class=""> -->
      <form @submit.prevent="onLogin" class="login-form">
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
            <button type="submit" class="btn btn-blue" :disabled="userStore.loader">{{ userStore.loader ? 'Загрузка...': 'Вход'}}</button>
        </div>
      </form>
    <!-- </div> -->
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
      password: ''
  });
  const showPassword = ref(null);

  const setShowPassword = (field) => {
    showPassword.value = showPassword.value === field ? null: field;
  };

  function onLogin() {
      userStore.loginUser(form);
  }
</script>

<style scoped lang="scss">

  .content {
    align-items: center;
    flex-direction: column;
    justify-content: center;
  }
  .login {
    &-container {
      font-size: 1.2em;
      display: flex;
      flex-direction: column;
      gap: 1em;
      width: 350px;
      margin: 0 auto;
      margin-top: 150px;
    }
    &-form {
      width: 100%;
      text-align: center;
    }
  }

  .form {
    &-group{
      margin-bottom: 25px;
      position: relative;
    }
  }

  .btn {
    width: 100%;
  }


</style>@/stores/userStore