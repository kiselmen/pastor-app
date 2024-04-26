<template>
  <div class="logo" :class="menuStore.logoClasses">
    <h1 class="logo-container">
      <RouterLink to="/" class="logo-name">Пастор</RouterLink>
    </h1>
    <Burger/>
  </div>
</template>

<script setup>
  import Burger from '@/components/top-bar/Burger.vue'
  import { useMenuStore } from '@/stores/menuStore';
  // import { useUserStore } from "@/stores/userStore";

  const isLogin = localStorage.getItem('authToken');
  const menuStore = useMenuStore();

  // const { authenticated } = useUserStore;

  // console.log('authenticated ', authenticated, useUserStore.signIn);
</script>

<style lang="scss" scoped>
  .logo {
    position: fixed;
    top: 0;
    left: 0;
    width: 230px;
    z-index: 10;
    transition: 0.5s ease-in-out;      
    background-color: var(--bs-primary);
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    &-container {
      width: 100%;
      height: 64px;
      padding: 9px 0 10px 10px;
      font-weight: bold;
      font-size: 30px;
      transition: 0.5s ease-in-out;
      color: var(--bs-white);
    }
    &-off {
      left: -175px;
    }
    &-name {
      font-size: 36px;
      font-weight: 400px;
      margin: auto;
    }
  }
  a.router-link-exact-active, a.router-link-active, a.router-link, a {
    color: var(--bs-white);
  }

</style>