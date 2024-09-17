<template>
  <div class="topbar" :class="menuStore.topBarClasses">
    <Avatar/>

  </div>
</template>

<script setup>
  import { useMenuStore } from '@/stores/menuStore';
  import Avatar from '@/components/avatar/Avatar.vue';

  const menuStore = useMenuStore();
</script>

<style lang="scss" scoped>
  .topbar {
    position: fixed;
    top: 0;
    left: 230px;
    right: 0;
    height: 64px;
    padding: 0 20px;
    display: flex;
    justify-content: right;
    transition: all 0.2s ease-in-out;
    z-index: 98;
    margin-left: auto;
    align-items: center;
    background-color: var(--bs-primary);
    transition: 0.5s ease-in-out;    &-off{
      left: 55px;
    }
  }

</style>