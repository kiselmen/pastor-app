<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
const router = useRouter();
const form = reactive({
    email: '',
    password: ''
});
const message = ref();
function submit() {
    message.value = '';
    axios.post('login', form)
    .then(response => {
        localStorage.setItem('token', response.data.token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        router.push({ name: 'user' });
    })
    .catch(error => {
        if (error.response.status === 422) {
            message.value = error.response.data.message;
        }
    })
    .finally(() => form.password = '');
}
</script>

<template>
  <div>
      <p v-if="message" class="error">{{ message }}</p>
      <form @submit.prevent="submit" class="login">
          <div class="form-group">
              <label>Email</label>
              <input v-model="form.email" type="text" class="form-input">
          </div>
          <div class="form-group">
              <label>Password</label>
              <input v-model="form.password" type="password" class="form-input">
          </div>
          <div class="form-group">
              <button type="submit" class="form-input">Login</button>
          </div>
      </form>
    </div>
  </template>

<style>
.login {
    font-size: 1.2em;
    display: flex;
    flex-direction: column;
    gap: 1em;
}
.form-group {
    display: flex;
    flex-direction: column;
}
.form-input {
    padding: 0.5em;
    font-size: 1em;
}
.error {
    color: red;
    font-size: 1em;
}
</style>