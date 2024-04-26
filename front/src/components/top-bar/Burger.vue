<template>
  <button :class="menuStore.burgerClasses" @click="menuStore.onBurgerClick">
    <div class="line line1"></div>
    <div class="line line2"></div>
    <div class="line line3"></div>
  </button>
</template>

<script setup>
  import { useMenuStore } from "@/stores/menuStore";

  const menuStore = useMenuStore();

</script>

<style lang="scss" scoped>
  .line {
    width: 100%;
    height: 2px;
    background: var(--bs-white);
    margin: auto 0;
    z-index: 10;
  }

  .burger {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    height: 40px;
    width: 30px;
    margin-right: 20px;
    background-color: var(--bs-primary);
    cursor: pointer;
    transition: all 0.3s ease-in-out;
  }

  .burger.open{
    transform: translateY(7%) rotateZ(-90deg);
    // transition: all 1s ease-in-out;
  }

  // .burger.open > .line{
  //   transition: all 0.3s ease-in-out;
  // }

</style>