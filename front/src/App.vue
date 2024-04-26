<template>
  <Loading v-if="loader"/>
  <Logo v-if="userStore.authenticated"/>
  <SideBar v-if="userStore.authenticated"/>
  <TopBar v-if="userStore.authenticated"/>

  <div :class="menuStore.contentClasses">
    <RouterView />
  </div>
</template>

<script setup>
  import Logo from '@/components/top-bar/Logo.vue';
  import SideBar from '@/components/side-bar/SideBar.vue';
  import TopBar from '@/components/top-bar/TopBar.vue';
  import Loading from '@/components/ui/Loading.vue';
  import { onBeforeMount, ref, onBeforeUnmount } from 'vue';
  import { RouterView } from 'vue-router';
  import { useUserStore } from '@/stores/userStore';
  import { useMenuStore } from '@/stores/menuStore';

  const menuStore = useMenuStore();
  const userStore = useUserStore();

  let localStoreWatcher = null;
  const loader = ref(false);

  const checkLocalStorage = () => {
    const isToken = localStorage.getItem("authToken");
    if (!isToken) {
      if (userStore.authenticated) {
        userStore.clearLoginState();
      }
    }
  }

  onBeforeMount(async () => {
    loader.value = true;
    if (localStorage.getItem('authToken')) {
      await userStore.signIn();
    }
    localStoreWatcher = setInterval(() => checkLocalStorage(), 2000);    
    loader.value = false;
  });

  onBeforeUnmount(() => {
    clearInterval(localStoreWatcher);
  });

</script>

<style lang="scss" scope>
  .body {
    margin: 0;
    color: var(--bs-body-color);
    text-align: var(--bs-body-text-align);
    background-color: var(--bs-body-bg);
    -webkit-text-size-adjust: 100%;
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    font-family: "Rubik", sans-serif;
    font-size: 15px;
  }
  .content {
    position: relative;
    overflow: hidden;
    min-height: 100vh;
    margin-left: 230px;
    padding-top: 64px;
    transition: 0.5s ease-in-out;
    &-off {
      margin-left: 0;
    }    
  }
</style>